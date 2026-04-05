<?php

namespace App\Http\Controllers\Api;

use App\Events\EmailAccountStatusChange;
use App\Events\NewEmailReceived;
use App\Http\Controllers\Controller;
use App\Http\Requests\WorkerAccountErrorRequest;
use App\Http\Requests\WorkerHeartbeatRequest;
use App\Http\Requests\WorkerLeaseRequest;
use App\Http\Requests\WorkerMessageRequest;
use App\Jobs\OutlookSubscriptionHandleJob;
use App\Models\EmailAccount;
use App\Repositories\Facades\EmailAccountFacade;
use App\Repositories\Facades\EmailMessageFacade;
use App\Repositories\Facades\WorkerFacade;
use App\Services\ProxyGenerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WorkerController extends Controller
{
    public function lease(WorkerLeaseRequest $request)
    {
        try {
            $leases = WorkerFacade::assignLeasesToWorker($request->worker_node, $request->validated());
            ProxyGenerationService::generateProxy($leases);

            return $this->sendResponse(['leases' => $leases], 'Leases assigned successfully');
        } catch (\Throwable $th) {
            Log::error('Worker lease assignment error: ' . $th->getMessage());

            return $this->sendError('Failed to assign leases', 500);
        }
    }

    public function heartbeat(WorkerHeartbeatRequest $request)
    {
        try {
            WorkerFacade::updateWorkerHeartbeat($request->worker_node, $request->all());

            return $this->sendResponse(['message' => 'Heartbeat updated'], 'Heartbeat successful');
        } catch (\Throwable $th) {
            Log::error('Worker heartbeat error: ' . $th->getMessage());

            return $this->sendError('Failed to update heartbeat', 500);
        }
    }

    public function auth(Request $request)
    {
        try {
            $workerNode = WorkerFacade::auth($request->node_id);
            if ($workerNode) {
                return $this->sendResponse(['worker_node' => $workerNode], 'Authentication successful');
            } else {
                return $this->sendError('Authentication failed', 401);
            }
        } catch (\Throwable $th) {
            Log::error('Worker authentication error: ' . $th->getMessage());

            return $this->sendError('Failed to authenticate worker', 500);
        }
    }

    public function messages(WorkerMessageRequest $request)
    {
        try {
            $data = $request->validated();
            $emailMessage = EmailMessageFacade::create($data);
            if (! $emailMessage) {
                return $this->sendError('Failed to create message', 500);
            }
            event(new NewEmailReceived($emailMessage->user_id, $emailMessage->email_account_id, $emailMessage->subject, $emailMessage->from, $emailMessage->received_at));
            return $this->sendResponse(['id' => $emailMessage->id], 'Message created successfully');
        } catch (\Throwable $th) {

            return $this->sendError('Failed to create message', 500);
        }
    }

    public function accountError(WorkerAccountErrorRequest $request)
    {
        try {
            $emailAccount = EmailAccount::find($request->validated('email_account_id'));

            if (! $emailAccount) {
                return $this->sendError('Failed to save err', 500);
            }

            $lease = $emailAccount->leases()->first();

            // Failsafe in case there is no lease attached
            if (! $lease) {
                return $this->sendError('No active lease found', 500);
            }

            $key = 'email_account_error_count_' . $emailAccount->id;
            $shouldUpdate = false;

            // Check if error is NOT within 5 mins (> 5 mins)
            if ($lease->leased_at->diffInMinutes(now()) > 5) {

                // Add the cache key if it doesn't exist, expiring in 20 minutes
                Cache::add($key, 0, now()->addMinutes(20));
                $errorCount = Cache::increment($key);

                // Check if we hit the 3 error threshold
                if ($errorCount >= 3) {
                    $shouldUpdate = true;
                    Cache::forget($key); // Clean up the cache once we take action
                }
            } else {
                // If the error IS within 5 minutes, update immediately
                $shouldUpdate = true;
            }

            // Execute the update only if conditions are met
            if ($shouldUpdate) {
                $emailAccount->leases()->delete();

                $success = EmailAccountFacade::update($emailAccount, [
                    'status' => EmailAccount::ERROR_STATUS,
                    'desired_state' => EmailAccount::INACTIVE_STATE,
                ]);

                event(new EmailAccountStatusChange($emailAccount->username, EmailAccount::ERROR_STATUS, $emailAccount->user_id));

                return $success
                    ? $this->sendResponse([], 'account err successfully')
                    : $this->sendError('Failed to save err', 500);
            }

            // Return success if we just logged the error to cache but didn't update yet
            return $this->sendResponse([], 'Error logged to cache, threshold not yet met');
        } catch (\Throwable $th) {
            Log::error('Worker account error handling error: ' . $th->getMessage());
            return $this->sendError('Failed to save err', 500);
        }
    }

    public function microsoftAutomationError(WorkerAccountErrorRequest $request)
    {
        try {
            $emailAccount = EmailAccount::find($request->validated('email_account_id'));

            if (! $emailAccount) {
                return $this->sendError('Failed to save err', 500);
            }

            $emailAccount->status = EmailAccount::UNVERIFIED_STATUS;
            $emailAccount->desired_state = EmailAccount::INACTIVE_STATE;            
            $success = $emailAccount->saveQuietly();

            event(new EmailAccountStatusChange($emailAccount->username, EmailAccount::UNVERIFIED_STATUS, $emailAccount->user_id));

            return $success
                ? $this->sendResponse([], 'account err successfully')
                : $this->sendError('Failed to save err', 500);
        } catch (\Throwable $th) {
            Log::error('Worker microsoft automation error handling error: ' . $th->getMessage());
            return $this->sendError('Failed to save err', 500);
        }
    }

    public function microsoftAutomationSuccess(Request $request)
    {
        try {
            $emailAccount = EmailAccount::find($request->email_account_id);

            if (! $emailAccount) {
                return $this->sendError('Failed to save err', 500);
            }

            if ($emailAccount->desired_state == EmailAccount::ACTIVE_STATE) {
                OutlookSubscriptionHandleJob::dispatch(EmailAccount::ACTIVE_STATE, $emailAccount->id);
            }
            return $this->sendResponse([], 'account finished successfully');
        } catch (\Throwable $th) {
            Log::error('Worker microsoft automation success handling error: ' . $th->getMessage());
            return $this->sendError('Failed to save finish', 500);
        }
    }
}

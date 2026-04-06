<?php

namespace App\Http\Controllers\Api\User;

use App\DTOs\ChargeRequestDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTransactionRequest;
use App\Repositories\Facades\TransactionFacade;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Get paginated list of user's transactions.
     */
    public function index(Request $request)
    {
        $filters = $request->input('filters', []);
        $filters['per_page'] = $request->input('per_page', 20);
        $filters['user_id'] = $request->user()->id;

        $transactions = TransactionFacade::getPaginated(
            $filters
        );

        return $this->sendResponse($transactions, 'Transactions fetched successfully');
    }
}

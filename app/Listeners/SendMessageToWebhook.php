<?php

namespace App\Listeners;

use App\Events\MessageReceived;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendMessageToWebhook implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(MessageReceived $event): void
    {
       $user = $event->message->orderItem->user;
       if ($user->webhook_url) {
           $data = [
               'phone_number_id' => $event->message->orderItem->id,
               'phone_number' => $event->message->orderItem->phone_number,
               'service_name' => $event->message->orderItem->service_name,
               'message' => $event->message->message,
           ];
           try {
               Http::post($user->webhook_url, $data);
           } catch (\Throwable $th) {
               Log::error('Error sending message to webhook', [
                   'exception' => $th->getMessage(),
               ]);
           }
       }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Actions\CacheCounterAction;
use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\PhoneMessage;
use App\Repositories\Facades\OrderItemFacade;
use Illuminate\Http\Request;

class MessagesWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $phone = OrderItem::where('external_order_id', $request->request_id)->first();
        if ($phone) {
            OrderItemFacade::update($phone, ['status' => 'completed']);
            $message = PhoneMessage::create([
                'order_item_id' => $phone->id,
                'message' => $request->message
            ]);
            CacheCounterAction::execute(
                'pending_numbers_' . $phone->user_id,
                1,
                'decrement'
            );
            event(new \App\Events\MessageReceived($message));
        }
        return response()->json(['message' => 'message recieved']);
    }
}

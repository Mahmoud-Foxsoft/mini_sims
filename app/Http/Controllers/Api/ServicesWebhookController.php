<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Services\PhoneServiceService;
use Illuminate\Http\Request;

class ServicesWebhookController extends Controller
{
    public function handle(Request $request)
    {
        PhoneServiceService::forgetCache();
        event(new \App\Events\ServicesUpdated());
        return response()->json(['message' => 'services recieved']);
    }
}

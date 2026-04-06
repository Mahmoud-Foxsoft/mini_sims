<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use App\Models\ContactMessage;
use App\Repositories\Facades\ContactFacade;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $messages = ContactFacade::getAllMessages((array) $request->input('filters'));
        ContactFacade::markAsRead($messages->getCollection());
        return $this->sendResponse($messages, 'Contact Messages recieved successfully');
    }

    public function store(ContactRequest $request)
    {
        ContactFacade::createMessage($request->validated());
        return $this->sendResponse([], 'message send succefully');
    }

    public function destroy(ContactMessage $contactMessage)
    {
        ContactFacade::deleteMessage($contactMessage);
        return $this->sendResponse([], 'message deleted');
    }

    public function deleteAllMessages()
    {
        ContactFacade::deleteAllMessages();
        return $this->sendResponse([], 'all messages deleted');
    }
}

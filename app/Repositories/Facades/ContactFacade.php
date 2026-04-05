<?php


namespace App\Repositories\Facades;

use App\Repositories\Services\ContactService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Contracts\Pagination\LengthAwarePaginator getAllMessages(?array $filters = null)
 * @method static \App\Models\ContactMessage|null createMessage(array $data)
 * @method static bool markAsRead(\Illuminate\Support\Collection $contactMessages)
 * @method static bool deleteMessage(\App\Models\ContactMessage $contactMessage)
 * @method static bool deleteAllMessages()
 *
 * @see \App\Repositories\Services\ContactService
 */
class ContactFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ContactService::class;
    }
}

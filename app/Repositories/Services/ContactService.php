<?php

namespace App\Repositories\Services;

use App\Models\ContactMessage;
use App\Repositories\Interfaces\ContactInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ContactService
{
    public function __construct(protected ContactInterface $repo) {}

    public function getAllMessages(?array $filters): LengthAwarePaginator
    {
        return $this->repo->getAllMessages($filters);
    }

    public function createMessage(array $data): ?ContactMessage
    {
        return $this->repo->createMessage($data);
    }

    public function markAsRead(Collection $contactMessages): bool
    {
        return $this->repo->markAsRead($contactMessages);
    }

    public function deleteMessage(ContactMessage $contactMessage): bool
    {
        return $this->repo->deleteMessage($contactMessage);
    }

    public function deleteAllMessages() : bool {
        return $this->repo->deleteAllMessages();
    }
}

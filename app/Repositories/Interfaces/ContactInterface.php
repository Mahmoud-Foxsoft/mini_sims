<?php

namespace App\Repositories\Interfaces;

use App\Models\ContactMessage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ContactInterface
{

    public function getAllMessages(?array $filters): LengthAwarePaginator;

    public function createMessage(array $data): ?ContactMessage;

    public function markAsRead(Collection $contactMessages): bool;

    public function deleteMessage(ContactMessage $contactMessage): bool;

    public function deleteAllMessages(): bool;

}

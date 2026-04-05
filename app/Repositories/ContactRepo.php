<?php

namespace App\Repositories;


use App\Models\Admin;
use App\Models\ContactMessage;
use App\Repositories\Interfaces\ContactInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ContactRepo implements ContactInterface
{
    public function getAllMessages(?array $filters): LengthAwarePaginator
    {
        return ContactMessage::when(
            $filters['name'] ?? null,
            fn($query, $name) => $query->where('name', 'like', "%$name%")
        )->when(
            $filters['is_read'] ?? null,
            function ($query,) use ($filters) {
                if ($filters['is_read'] === 'true') {
                    $query->where('is_read', true);
                }
            }
        )->when(
            $filters['email'] ?? null,
            fn($query, $email) => $query->whereHas('user', fn($q) => $q->where('email', 'like', "%$email%"))
        )->latest()->paginate(20);
    }

    public function createMessage(array $data): ?ContactMessage
    {
        try {
            return ContactMessage::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'message' => $data['message'],
            ]);
        } catch (\Throwable $th) {
            Log::error('Db Create Error', [$th->getMessage()]);
            return null;
        }
    }

    public function markAsRead(Collection $contactMessages): bool
    {
        if ($contactMessages->isNotEmpty()) {
            $ids = $contactMessages->pluck('id');
            return ContactMessage::whereIn('id', $ids)->update(['is_read' => true]);
        }
        return false;
    }

    public function deleteMessage(ContactMessage $contactMessage): bool
    {
        return $contactMessage->delete();
    }

    public function deleteAllMessages(): bool
    {
        try {
            ContactMessage::where('created_at', '<=', now())->delete();
            return true;
        } catch (\Throwable $th) {
            Log::error('Db delete all Error', [$th->getMessage()]);
            return false;
        }
    }
}

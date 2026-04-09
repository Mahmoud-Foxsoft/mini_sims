<?php

namespace App\Http\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Kreait\Firebase\Contract\Messaging as MessagingContract;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseMessagingService
{
    protected MessagingContract $messaging;

    public function __construct(Factory $factory)
    {
        $credentials = $this->resolveCredentialsPath(config('firebase.credentials'));
        $this->messaging = $factory->withServiceAccount($credentials)->createMessaging();
    }


    protected function resolveCredentialsPath(?string $path): string
    {
        if (! $path) {
            throw new \RuntimeException('Firebase credentials path is not configured.');
        }

        $isAbsolute = Str::startsWith($path, ['/', '\\']) || preg_match('/^[A-Za-z]:\\\\/', $path);

        return $isAbsolute ? $path : base_path($path);
    }

    public function sendToToken(string $token, string $title, string $body, array $data = []): ?array
    {
        return $this->sendToTokens([$token], $title, $body, $data);
    }

    public function sendToTokens(array $tokens, string $title, string $body, array $data = []): ?array
    {
        $tokens = collect($tokens)->filter()->unique()->values();

        if ($tokens->isEmpty()) {
            return null;
        }

        $results = [
            'successes' => 0,
            'failures' => 0,
            'failed_tokens' => [],
        ];

        $tokens->chunk(500)->each(function (Collection $chunk) use (&$results, $title, $body, $data) {
            $message = CloudMessage::new()
                ->withNotification(Notification::create($title, $body))
                ->withData($this->stringifyData($data));

            try {
                $report = $this->messaging->sendMulticast($message, $chunk->all());
                $results['successes'] += $report->successes()->count();
                $results['failures'] += $report->failures()->count();
                $failedTokens = $report->failures()->map(fn ($failure) => $failure->target()->value());
                $results['failed_tokens'] = array_values(array_unique(array_merge($results['failed_tokens'], $failedTokens->all())));
            } catch (\Throwable $exception) {
                Log::error('Firebase messaging failed', ['error' => $exception->getMessage()]);
            }
        });

        return $results;
    }

    protected function stringifyData(array $data): array
    {
        $stringified = [];
        foreach ($data as $key => $value) {
            if (is_array($value) || is_object($value)) {
                $stringified[$key] = json_encode($value);
            } else {
                $stringified[$key] = (string) $value;
            }
        }

        return $stringified;
    }
}

<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use \App\Notifications\EmailVerifyNotification;
use Illuminate\Support\Facades\Cache;

class EmailVerifyNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    /**
    * Create a new job instance.
    *
    * @return void
    */
    public function __construct(User $user) {
        $this->user = $user;
    }

    /**
    * Execute the job.
    *
    * @return void
    */
    public function handle()
    {
        $this->user->notify(new EmailVerifyNotification($this->user));
        Cache::increment('otp_attempts_' . $this->user->email);
    }
}

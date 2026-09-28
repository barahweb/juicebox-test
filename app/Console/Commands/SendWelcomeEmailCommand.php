<?php

namespace App\Console\Commands;

use App\Jobs\SendWelcomeEmailJob;
use App\Models\Entity\User;
use Illuminate\Console\Command;

class SendWelcomeEmailCommand extends Command
{
    protected $signature = 'email:send-welcome {email}';

    protected $description = 'Dispatch job pengiriman welcome email ke user tertentu (untuk testing)';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if (!$user) {
            $this->error("User dengan email {$this->argument('email')} tidak ditemukan.");

            return self::FAILURE;
        }

        SendWelcomeEmailJob::dispatch($user);

        $this->info("Job kirim welcome email untuk {$user->email} berhasil di-dispatch.");

        return self::SUCCESS;
    }
}

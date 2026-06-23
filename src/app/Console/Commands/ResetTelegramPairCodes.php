<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ResetTelegramPairCodes extends Command
{
    protected $signature = 'telegram:reset-pair-codes';

    protected $description = 'Regenerate Telegram pairing codes for all users every 5 minutes';

    public function handle(): int
    {
        $users = User::all();
        $count = 0;

        foreach ($users as $user) {
            do {
                $code = 'TG-' . Str::upper(Str::random(6));
            } while (User::where('telegram_pair_code', $code)->exists());

            $user->updateQuietly(['telegram_pair_code' => $code]);
            $count++;
        }

        $this->info("Regenerated Telegram pairing codes for {$count} users.");
        return Command::SUCCESS;
    }
}

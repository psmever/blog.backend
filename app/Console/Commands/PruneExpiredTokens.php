<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Sanctum\PersonalAccessToken;

class PruneExpiredTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tokens:prune-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired Sanctum personal access tokens';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $deleted = PersonalAccessToken::query()
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->delete();

        $this->info(sprintf('Pruned %d expired personal access token(s).', $deleted));

        return self::SUCCESS;
    }
}

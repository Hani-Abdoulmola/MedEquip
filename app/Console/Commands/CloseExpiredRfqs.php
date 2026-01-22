<?php

namespace App\Console\Commands;

use App\Models\Rfq;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CloseExpiredRfqs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rfqs:close-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Close RFQs that have passed their deadline';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Closing expired RFQs...');

        $closed = \App\Services\RfqWorkflowService::closeExpiredRfqs();

        if ($closed > 0) {
            $this->info("✅ Closed {$closed} expired RFQ(s).");
            Log::info("Closed {$closed} expired RFQ(s) via scheduled command.");
        } else {
            $this->info('ℹ️  No expired RFQs to close.');
        }

        return Command::SUCCESS;
    }
}

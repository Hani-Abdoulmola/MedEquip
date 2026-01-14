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

        $closedCount = Rfq::where('status', 'open')
            ->whereNotNull('deadline')
            ->where('deadline', '<', now())
            ->update([
                'status' => 'closed',
                'closed_at' => now(),
            ]);

        if ($closedCount > 0) {
            $this->info("Closed {$closedCount} expired RFQ(s).");
            Log::info("Closed {$closedCount} expired RFQ(s) via scheduled command.");
        } else {
            $this->info('No expired RFQs to close.');
        }

        return Command::SUCCESS;
    }
}

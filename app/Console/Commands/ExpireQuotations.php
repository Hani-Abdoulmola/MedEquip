<?php

namespace App\Console\Commands;

use App\Services\QuotationWorkflowService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExpireQuotations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quotations:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire quotations that have passed their validity date or belong to closed RFQs';

    /**
     * Execute the console command.
     */
    public function handle(QuotationWorkflowService $workflowService)
    {
        $this->info('Expiring quotations...');

        $expired = $workflowService->expireQuotations();

        if ($expired > 0) {
            $this->info("✅ Expired {$expired} quotation(s).");
            Log::info("Expired {$expired} quotation(s) via scheduled command.");
        } else {
            $this->info('ℹ️  No quotations to expire.');
        }

        return Command::SUCCESS;
    }
}

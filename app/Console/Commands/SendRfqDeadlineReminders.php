<?php

namespace App\Console\Commands;

use App\Services\RfqWorkflowService;
use Illuminate\Console\Command;

class SendRfqDeadlineReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rfqs:send-reminders {--hours=24 : Hours before deadline to send reminder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send deadline reminders for RFQs approaching their deadline';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = (int) $this->option('hours');
        
        $this->info("Sending reminders for RFQs expiring in {$hours} hours...");
        
        $sent = RfqWorkflowService::sendDeadlineReminders($hours);
        
        if ($sent > 0) {
            $this->info("✅ Sent {$sent} reminder(s).");
        } else {
            $this->info('ℹ️  No reminders to send.');
        }
        
        return Command::SUCCESS;
    }
}

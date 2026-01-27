<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class CheckAuthorizationErrors extends Command
{
    protected $signature = 'rbac:check-logs {--hours=24 : Number of hours to check back}';
    protected $description = 'Check logs for authorization errors (403, permission denied, etc.)';

    public function handle(): int
    {
        $hours = (int) $this->option('hours');
        $this->info("🔍 Checking logs for authorization errors (last {$hours} hours)");
        $this->newLine();

        $logPath = storage_path('logs/laravel.log');
        
        if (!File::exists($logPath)) {
            $this->warn('⚠️  Log file not found: ' . $logPath);
            return Command::SUCCESS;
        }

        $logContent = File::get($logPath);
        
        // Search for authorization-related errors
        $patterns = [
            '/403|Forbidden/i' => '403 Forbidden errors',
            '/Unauthorized/i' => 'Unauthorized access attempts',
            '/Permission denied/i' => 'Permission denied errors',
            '/authorization/i' => 'Authorization-related messages',
            '/Gate::deny/i' => 'Gate denial messages',
            '/Access denied/i' => 'Access denied messages',
        ];

        $foundErrors = [];
        $lines = explode("\n", $logContent);
        
        // Get recent lines (last N hours)
        $cutoffTime = now()->subHours($hours);
        $recentLines = [];
        $currentDate = null;

        foreach ($lines as $line) {
            // Check if line starts with date
            if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $matches)) {
                $lineDate = \Carbon\Carbon::parse($matches[1]);
                if ($lineDate->gte($cutoffTime)) {
                    $currentDate = $lineDate;
                    $recentLines[] = $line;
                } else {
                    $currentDate = null;
                }
            } elseif ($currentDate && $currentDate->gte($cutoffTime)) {
                $recentLines[] = $line;
            }
        }

        $recentLogContent = implode("\n", $recentLines);

        foreach ($patterns as $pattern => $description) {
            $matches = [];
            preg_match_all($pattern, $recentLogContent, $matches, PREG_OFFSET_CAPTURE);
            
            $count = count($matches[0]);
            if ($count > 0) {
                $foundErrors[$description] = $count;
            }
        }

        if (empty($foundErrors)) {
            $this->info('✅ No authorization errors found in logs');
        } else {
            $this->warn('⚠️  Found authorization-related entries:');
            foreach ($foundErrors as $description => $count) {
                $this->line("  - {$description}: {$count} occurrence(s)");
            }
            $this->newLine();
            $this->info('💡 Tip: Review these entries to ensure they are expected (e.g., legitimate access denials)');
        }

        // Check for specific RBAC-related errors
        $rbacPatterns = [
            '/user_type_id.*===.*1/i' => 'user_type_id === 1 checks (should not exist)',
            '/Gate::before.*failed/i' => 'Gate::before() failures',
        ];

        $rbacIssues = [];
        foreach ($rbacPatterns as $pattern => $description) {
            if (preg_match($pattern, $recentLogContent)) {
                $rbacIssues[] = $description;
            }
        }

        if (!empty($rbacIssues)) {
            $this->error('❌ Potential RBAC issues found:');
            foreach ($rbacIssues as $issue) {
                $this->error("  - {$issue}");
            }
        } else {
            $this->info('✅ No RBAC implementation issues detected');
        }

        return Command::SUCCESS;
    }
}

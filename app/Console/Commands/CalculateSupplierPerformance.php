<?php

namespace App\Console\Commands;

use App\Models\Supplier;
use App\Services\SupplierPerformanceService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CalculateSupplierPerformance extends Command
{
    protected $signature = 'suppliers:calculate-performance {--period=current_month}';
    protected $description = 'Calculate supplier performance metrics';

    protected SupplierPerformanceService $performanceService;

    public function __construct(SupplierPerformanceService $performanceService)
    {
        parent::__construct();
        $this->performanceService = $performanceService;
    }

    public function handle(): int
    {
        $this->info('📊 Calculating supplier performance metrics...');

        // Determine period
        [$periodStart, $periodEnd] = $this->getPeriod();

        $this->info("Period: {$periodStart->format('Y-m-d')} to {$periodEnd->format('Y-m-d')}");

        // Get all active and verified suppliers
        $suppliers = Supplier::where('status', 'verified')
            ->orWhere('status', 'active')
            ->get();

        $this->info("Found {$suppliers->count()} suppliers");

        $progressBar = $this->output->createProgressBar($suppliers->count());
        $progressBar->start();

        $calculated = 0;
        foreach ($suppliers as $supplier) {
            try {
                $this->performanceService->calculateMetrics($supplier, $periodStart, $periodEnd);
                $calculated++;
            } catch (\Exception $e) {
                $this->error("Failed for supplier {$supplier->id}: {$e->getMessage()}");
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Calculate rankings
        $this->info('📈 Calculating rankings...');
        $this->performanceService->calculateRankings($periodStart, $periodEnd);

        $this->info("✅ Successfully calculated metrics for {$calculated}/{$suppliers->count()} suppliers");

        // Show top performers
        $topPerformers = $this->performanceService->getTopPerformers($periodStart, $periodEnd, 5);
        if ($topPerformers->count() > 0) {
            $this->newLine();
            $this->info('🏆 Top Performers:');
            foreach ($topPerformers as $index => $metric) {
                $this->line(sprintf(
                    '%d. %s - Score: %.2f (Rank: #%d)',
                    $index + 1,
                    $metric->supplier->company_name ?? 'N/A',
                    $metric->overall_score,
                    $metric->overall_rank ?? 0
                ));
            }
        }

        return Command::SUCCESS;
    }

    protected function getPeriod(): array
    {
        $period = $this->option('period');

        return match ($period) {
            'current_month' => [now()->startOfMonth(), now()->endOfMonth()],
            'last_month' => [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()],
            'current_quarter' => [now()->startOfQuarter(), now()->endOfQuarter()],
            'last_quarter' => [now()->subQuarter()->startOfQuarter(), now()->subQuarter()->endOfQuarter()],
            'current_year' => [now()->startOfYear(), now()->endOfYear()],
            default => [now()->startOfMonth(), now()->endOfMonth()],
        };
    }
}

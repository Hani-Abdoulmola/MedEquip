<?php

namespace App\Console\Commands;

use App\Mail\AbandonedCartReminder;
use App\Models\BuyerCart;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendAbandonedCartReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cart:send-abandoned-reminders {--type=all : Type of reminder to send (24h, 72h, 7d, or all)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email reminders for abandoned shopping carts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');
        $totalSent = 0;

        $this->info('🔍 Checking for abandoned carts...');

        if ($type === 'all' || $type === '24h') {
            $totalSent += $this->send24HourReminders();
        }

        if ($type === 'all' || $type === '72h') {
            $totalSent += $this->send72HourReminders();
        }

        if ($type === 'all' || $type === '7d') {
            $totalSent += $this->send7DayReminders();
        }

        $this->info("✅ Sent {$totalSent} abandoned cart reminders");
        
        return Command::SUCCESS;
    }

    /**
     * Send 24-hour reminders (carts updated 24h ago).
     */
    private function send24HourReminders(): int
    {
        $carts = BuyerCart::with(['buyer.user', 'items'])
            ->where('is_active', true)
            ->whereHas('items') // Has items
            ->whereBetween('updated_at', [
                now()->subHours(25), // 25 hours ago
                now()->subHours(23), // 23 hours ago
            ])
            ->whereDoesntHave('buyer', function ($q) {
                // Don't send if buyer has created RFQ in last 24h
                $q->whereHas('rfqs', function ($rfqQuery) {
                    $rfqQuery->where('created_at', '>=', now()->subHours(24));
                });
            })
            ->get();

        return $this->sendReminders($carts, '24h');
    }

    /**
     * Send 72-hour reminders (carts updated 3 days ago).
     */
    private function send72HourReminders(): int
    {
        $carts = BuyerCart::with(['buyer.user', 'items'])
            ->where('is_active', true)
            ->whereHas('items')
            ->whereBetween('updated_at', [
                now()->subHours(73),
                now()->subHours(71),
            ])
            ->whereDoesntHave('buyer', function ($q) {
                // Don't send if buyer has created RFQ in last 3 days
                $q->whereHas('rfqs', function ($rfqQuery) {
                    $rfqQuery->where('created_at', '>=', now()->subDays(3));
                });
            })
            ->get();

        return $this->sendReminders($carts, '72h');
    }

    /**
     * Send 7-day reminders (carts about to expire).
     */
    private function send7DayReminders(): int
    {
        $carts = BuyerCart::with(['buyer.user', 'items'])
            ->where('is_active', true)
            ->whereHas('items')
            ->whereBetween('updated_at', [
                now()->subDays(8),
                now()->subDays(6),
            ])
            ->whereDoesntHave('buyer', function ($q) {
                // Don't send if buyer has created RFQ in last 7 days
                $q->whereHas('rfqs', function ($rfqQuery) {
                    $rfqQuery->where('created_at', '>=', now()->subDays(7));
                });
            })
            ->get();

        return $this->sendReminders($carts, '7d');
    }

    /**
     * Send reminder emails for a collection of carts.
     */
    private function sendReminders($carts, string $reminderType): int
    {
        $sent = 0;

        foreach ($carts as $cart) {
            try {
                if ($cart->buyer && $cart->buyer->user && $cart->buyer->user->email) {
                    // Check if we've already sent this type of reminder
                    $alreadySent = DB::table('abandoned_cart_reminders')
                        ->where('cart_id', $cart->id)
                        ->where('reminder_type', $reminderType)
                        ->exists();

                    if ($alreadySent) {
                        continue;
                    }

                    // Send email
                    Mail::to($cart->buyer->user->email)
                        ->send(new AbandonedCartReminder($cart, $reminderType));

                    // Track that we sent this reminder
                    DB::table('abandoned_cart_reminders')->insert([
                        'cart_id' => $cart->id,
                        'buyer_id' => $cart->buyer_id,
                        'reminder_type' => $reminderType,
                        'sent_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $sent++;
                    $this->line("📧 Sent {$reminderType} reminder to {$cart->buyer->user->email}");
                }
            } catch (\Throwable $e) {
                Log::error('Failed to send abandoned cart reminder', [
                    'cart_id' => $cart->id,
                    'buyer_id' => $cart->buyer_id,
                    'reminder_type' => $reminderType,
                    'error' => $e->getMessage(),
                ]);
                $this->error("❌ Failed to send reminder for cart {$cart->id}: {$e->getMessage()}");
            }
        }

        return $sent;
    }
}

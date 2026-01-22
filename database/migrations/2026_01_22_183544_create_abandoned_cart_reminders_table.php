<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('abandoned_cart_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')
                ->constrained('buyer_carts')
                ->cascadeOnDelete()
                ->comment('FK -> buyer_carts.id');
            $table->foreignId('buyer_id')
                ->constrained('buyers')
                ->cascadeOnDelete()
                ->comment('FK -> buyers.id');
            $table->enum('reminder_type', ['24h', '72h', '7d'])
                ->comment('Type of reminder sent');
            $table->timestamp('sent_at')->comment('When the reminder was sent');
            $table->timestamps();

            // Indexes
            $table->index(['cart_id', 'reminder_type']);
            $table->index('sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abandoned_cart_reminders');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_trackings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_id')->constrained('deliveries')->cascadeOnDelete();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            
            // Status tracking
            $table->enum('status', [
                'pending',
                'confirmed',
                'preparing',
                'shipped',
                'in_transit',
                'out_for_delivery',
                'delivered',
                'failed',
                'returned'
            ])->default('pending');
            
            // Location tracking
            $table->string('current_location')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Carrier information
            $table->string('carrier_name')->nullable()->comment('Delivery company name');
            $table->string('tracking_number')->nullable()->unique();
            $table->string('driver_name')->nullable();
            $table->string('driver_phone')->nullable();
            $table->string('vehicle_info')->nullable();
            
            // Timeline
            $table->timestamp('estimated_delivery_at')->nullable();
            $table->timestamp('actual_delivery_at')->nullable();
            $table->timestamp('last_updated_at')->nullable();
            
            // Delivery details
            $table->text('delivery_instructions')->nullable();
            $table->string('delivery_address')->nullable();
            $table->string('delivery_city')->nullable();
            
            // Notifications sent
            $table->json('notifications_sent')->nullable()->comment('Track which notifications were sent');
            
            // Events timeline (JSON)
            $table->json('events')->nullable()->comment('Delivery status events log');
            
            // Proof of delivery
            $table->string('signature_image')->nullable();
            $table->json('delivery_photos')->nullable();
            $table->text('delivery_notes')->nullable();
            $table->string('received_by')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('delivery_id');
            $table->index('order_id');
            $table->index('status');
            $table->index('tracking_number');
            $table->index('estimated_delivery_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_trackings');
    }
};

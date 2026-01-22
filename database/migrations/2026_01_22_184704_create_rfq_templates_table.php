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
        Schema::create('rfq_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyer_id')
                ->constrained('buyers')
                ->cascadeOnDelete()
                ->comment('FK -> buyers.id');
            $table->string('name')->comment('Template name');
            $table->text('description')->nullable()->comment('Template description');
            $table->enum('category', ['general', 'emergency', 'recurring', 'department', 'project', 'custom'])
                ->default('custom')
                ->comment('Template category');
            $table->string('department')->nullable()->comment('Department/division');
            $table->integer('default_deadline_days')->default(7)->comment('Default deadline in days');
            $table->boolean('is_public')->default(true)->comment('Public or private RFQ by default');
            $table->boolean('is_shared')->default(false)->comment('Shared with organization');
            $table->integer('use_count')->default(0)->comment('Number of times used');
            $table->timestamp('last_used_at')->nullable()->comment('Last time template was used');
            $table->timestamps();

            // Indexes
            $table->index(['buyer_id', 'category']);
            $table->index('last_used_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rfq_templates');
    }
};

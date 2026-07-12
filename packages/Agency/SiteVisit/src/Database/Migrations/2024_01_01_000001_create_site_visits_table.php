<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_visits', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');

            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            $table->text('address');
            $table->date('preferred_date');
            $table->string('preferred_time_slot', 50);
            $table->text('notes')->nullable();

            $table->enum('status', ['requested', 'assigned', 'scheduled', 'completed', 'cancelled'])
                  ->default('requested');

            $table->unsignedBigInteger('assigned_rep_id')->nullable();
            $table->foreign('assigned_rep_id')->references('id')->on('admins')->onDelete('set null');

            $table->unsignedBigInteger('assigned_by_id')->nullable();
            $table->foreign('assigned_by_id')->references('id')->on('admins')->onDelete('set null');

            $table->timestamp('assigned_at')->nullable();

            $table->date('scheduled_date')->nullable();
            $table->string('scheduled_time', 50)->nullable();

            $table->timestamp('completed_at')->nullable();
            $table->text('completion_notes')->nullable();

            $table->timestamps();

            // Indexes for common queries
            $table->index('status');
            $table->index('customer_id');
            $table->index('assigned_rep_id');
            $table->index('preferred_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_visits');
    }
};

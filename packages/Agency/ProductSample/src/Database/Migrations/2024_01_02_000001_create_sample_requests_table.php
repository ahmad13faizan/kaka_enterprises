<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sample_requests', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');

            $table->unsignedInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            $table->unsignedInteger('quantity')->default(1);
            $table->text('shipping_address');

            $table->enum('status', ['pending', 'approved', 'rejected', 'shipped', 'delivered'])
                  ->default('pending');

            $table->text('rejection_reason')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->string('tracking_info', 255)->nullable();
            $table->timestamp('delivered_at')->nullable();

            $table->timestamps();

            $table->index('customer_id');
            $table->index('product_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sample_requests');
    }
};

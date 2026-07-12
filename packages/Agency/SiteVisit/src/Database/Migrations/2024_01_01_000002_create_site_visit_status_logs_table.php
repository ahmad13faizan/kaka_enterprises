<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_visit_status_logs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('site_visit_id');
            $table->foreign('site_visit_id')->references('id')->on('site_visits')->onDelete('cascade');

            $table->string('previous_status', 20);
            $table->string('new_status', 20);

            $table->unsignedInteger('changed_by_id');
            $table->foreign('changed_by_id')->references('id')->on('admins')->onDelete('cascade');

            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('site_visit_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_visit_status_logs');
    }
};

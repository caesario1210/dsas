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
        Schema::create('etl_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained('sales_periods')->onDelete('cascade');
            $table->integer('rows_uploaded')->default(0);
            $table->integer('rows_imported')->default(0);
            $table->integer('rows_duplicate')->default(0);
            $table->integer('rows_missing')->default(0);
            $table->integer('rows_failed')->default(0);
            $table->json('validation_errors')->nullable();
            $table->timestamps();
            
            $table->index('period_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etl_logs');
    }
};

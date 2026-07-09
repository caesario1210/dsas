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
        Schema::create('kpi_summary', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained('sales_periods')->onDelete('cascade');
            $table->string('kpi_name', 100);
            $table->string('kpi_value', 255);
            $table->string('filter_type', 50)->nullable();
            $table->string('filter_value', 255)->nullable();
            $table->timestamp('calculated_at');
            $table->timestamps();
            
            $table->index(['period_id', 'kpi_name']);
            $table->index(['period_id', 'filter_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpi_summary');
    }
};

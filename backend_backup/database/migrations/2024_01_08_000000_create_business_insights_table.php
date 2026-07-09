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
        Schema::create('business_insights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained('sales_periods')->onDelete('cascade');
            $table->string('insight_type', 50);
            $table->text('insight_text');
            $table->timestamps();
            
            $table->index('period_id');
            $table->index('insight_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_insights');
    }
};

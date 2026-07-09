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
        Schema::create('sales_periods', function (Blueprint $table) {
            $table->id();
            $table->string('period', 7)->unique();
            $table->date('upload_date');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('restrict');
            $table->integer('total_rows')->default(0);
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->timestamps();
            
            $table->index('period');
            $table->index('status');
            $table->index('uploaded_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_periods');
    }
};

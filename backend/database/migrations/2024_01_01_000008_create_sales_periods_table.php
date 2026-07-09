<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_periods', function (Blueprint $table) {
            $table->id();
            $table->integer('year');
            $table->integer('month');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['draft', 'processing', 'completed', 'failed'])->default('draft');
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('uploaded_at')->nullable();
            $table->integer('total_rows')->default(0);
            $table->integer('imported_rows')->default(0);
            $table->integer('failed_rows')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_periods');
    }
};

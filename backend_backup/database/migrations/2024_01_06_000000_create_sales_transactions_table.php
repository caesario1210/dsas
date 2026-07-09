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
        Schema::create('sales_transactions', function (Blueprint $table) {
            $table->id();
            $table->date('transaction_date');
            $table->string('invoice_no', 50)->unique();
            $table->foreignId('dealer_id')->constrained('dealers')->onDelete('restrict');
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict');
            $table->integer('quantity');
            $table->decimal('unit_price', 15, 2);
            $table->decimal('revenue', 15, 2);
            $table->decimal('cost', 15, 2);
            $table->decimal('profit', 15, 2)->storedAs('revenue - cost');
            $table->decimal('profit_margin', 5, 2)->storedAs('((revenue - cost) / revenue) * 100');
            $table->decimal('target', 15, 2);
            $table->string('sales_person', 100)->default('N/A');
            $table->string('sales_month', 7);
            $table->foreignId('period_id')->constrained('sales_periods')->onDelete('restrict');
            $table->timestamps();
            
            $table->index('invoice_no');
            $table->index('transaction_date');
            $table->index('dealer_id');
            $table->index('product_id');
            $table->index('sales_month');
            $table->index('period_id');
            $table->index(['sales_month', 'dealer_id']);
            $table->index(['sales_month', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_transactions');
    }
};

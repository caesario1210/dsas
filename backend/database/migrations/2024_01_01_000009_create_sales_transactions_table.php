<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_period_id')->constrained('sales_periods')->onDelete('cascade');
            $table->foreignId('dealer_id')->constrained('dealers')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->date('transaction_date');
            $table->string('invoice_no')->unique();
            $table->integer('quantity')->default(0);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('revenue', 15, 2)->default(0);
            $table->decimal('cost', 15, 2)->default(0);
            $table->decimal('profit', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('target', 15, 2)->default(0);
            $table->string('sales_person')->nullable();
            $table->string('sales_month', 7);
            $table->string('dealer_code');
            $table->string('dealer_name');
            $table->string('branch');
            $table->timestamps();
            
            $table->index(['sales_period_id', 'dealer_id', 'product_id']);
            $table->index('transaction_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_transactions');
    }
};

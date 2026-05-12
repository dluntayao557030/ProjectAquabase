<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_ins', function (Blueprint $table) {
            // PK and FK to stock_transactions
            $table->unsignedBigInteger('transaction_id')->primary();
            $table->foreign('transaction_id')
                  ->references('transaction_id')
                  ->on('stock_transactions')
                  ->cascadeOnDelete();

            $table->foreignId('supplier_id')
                  ->nullable()
                  ->constrained('suppliers', 'supplier_id')
                  ->nullOnDelete();
            $table->date('delivery_date')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('receipt_no', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_ins');
    }
};
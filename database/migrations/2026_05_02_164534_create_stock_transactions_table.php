<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id('transaction_id');
            $table->foreignId('supply_id')
                  ->constrained('supplies', 'supply_id')
                  ->restrictOnDelete();
            $table->foreignId('user_id')
                  ->constrained('users', 'user_id')
                  ->restrictOnDelete();
            $table->dateTime('transaction_date');
            $table->unsignedInteger('quantity');
            $table->enum('transaction_type', ['stock_in', 'stock_out']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transactions');
    }
};
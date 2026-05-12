<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_outs', function (Blueprint $table) {
            // PK and FK to stock_transactions
            $table->unsignedBigInteger('transaction_id')->primary();
            $table->foreign('transaction_id')
                  ->references('transaction_id')
                  ->on('stock_transactions')
                  ->cascadeOnDelete();

            // approved_by FK → users
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->foreign('approved_by')
                  ->references('user_id')
                  ->on('users')
                  ->nullOnDelete();

            $table->text('purpose')->nullable();
            $table->text('remarks')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_outs');
    }
};
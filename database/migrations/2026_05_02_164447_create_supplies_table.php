<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('supplies', function (Blueprint $table) {
            $table->id('supply_id');
            $table->foreignId('category_id')
                  ->constrained('categories', 'category_id')
                  ->restrictOnDelete();
            $table->string('supply_name', 200);
            $table->string('supply_img_path')->nullable();
            $table->text('supply_description')->nullable();
            $table->string('unit_measure', 50)->nullable();
            $table->unsignedInteger('current_stock')->default(0);
            $table->unsignedInteger('reorder_level')->default(0);
            $table->enum('status', ['active', 'inactive', 'out_of_stock', 'expired'])
                  ->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplies');
    }
};
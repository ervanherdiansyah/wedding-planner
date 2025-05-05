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
        Schema::create('handover_budget_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_handover_budgets_id')->constrained('category_handovers')->onDelete('cascade'); // Relasi ke budgets
            $table->string('name')->nullable(); // Nama item
            $table->enum('category', ['male', 'female'])->nullable(); // Kategori item (pria/wanita)
            $table->enum('purchase_method', ['online', 'offline'])->nullable(); // Sistem pembelian
            $table->unsignedBigInteger('price')->default(0)->nullable(); // Harga
            $table->string('detail')->nullable(); // Status pembelian
            $table->boolean('status')->default(false)->nullable(); // Status pembelian
            $table->date('purchase_date')->nullable(); // Tanggal pembelian
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('handover_budget_items');
    }
};

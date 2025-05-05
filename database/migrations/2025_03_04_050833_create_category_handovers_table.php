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
        Schema::create('category_handovers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('handover_budgets_id')->constrained('handover_budgets')->onDelete('cascade'); // Relasi ke budgets
            $table->string('title')->nullable(); // Nama item
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_handovers');
    }
};

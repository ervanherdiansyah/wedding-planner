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
        Schema::create('detail_payment_budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('list_budgets_id')->constrained('list_budgets')->onDelete('cascade');
            $table->string('description')->nullable();
            $table->integer('deadline')->nullable();
            $table->integer('paid')->nullable();
            $table->string('payer')->nullable();
            $table->date('date_payment')->nullable();
            $table->string('type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_payment_budgets');
    }
};

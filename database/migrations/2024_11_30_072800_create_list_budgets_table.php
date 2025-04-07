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
        Schema::create('list_budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_budget_id')->constrained('category_budgets')->onDelete('cascade');
            $table->integer('estimated_payment')->nullable();
            $table->string('title')->nullable();
            $table->integer('actual_payment')->nullable();
            $table->integer('difference')->nullable();
            $table->integer('paid')->nullable();
            $table->integer('remaining_payment')->nullable();
            $table->boolean('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('list_budgets');
    }
};

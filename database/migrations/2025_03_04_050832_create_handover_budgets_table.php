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
        Schema::create('handover_budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade'); // Relasi ke projects
            $table->unsignedBigInteger('male_budget')->default(0)->nullable(); // Budget pria (integer)
            $table->unsignedBigInteger('female_budget')->default(0)->nullable(); // Budget wanita (integer)
            $table->unsignedBigInteger('used_budget_male')->default(0)->nullable(); // Budget yang sudah terpakai pria (integer)
            $table->unsignedBigInteger('used_budget_female')->default(0)->nullable(); // Budget yang sudah terpakai wanita (integer)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('handover_budgets');
    }
};

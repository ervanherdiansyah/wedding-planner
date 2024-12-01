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
        Schema::create('family_member_brides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bride_id')->constrained('brides')->onDelete('cascade');
            $table->string('relationship')->nullable();
            $table->string('name_family')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_member_brides');
    }
};
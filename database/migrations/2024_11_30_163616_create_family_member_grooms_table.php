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
        Schema::create('family_member_grooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('groom_id')->constrained('grooms')->onDelete('cascade');
            $table->string('relationship_groom')->nullable();
            $table->string('name_family_groom')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_member_grooms');
    }
};

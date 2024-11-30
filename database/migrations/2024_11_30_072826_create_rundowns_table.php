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
        Schema::create('rundowns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->time('time')->nullable();
            $table->string('title_event')->nullable();
            $table->integer('minute')->nullable();
            $table->text('address')->nullable();
            $table->string('vendor')->nullable();
            $table->text('person_responsible')->nullable();
            $table->text('description')->nullable();
            $table->boolean('status')->default(0);
            $table->string('icon')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rundowns');
    }
};

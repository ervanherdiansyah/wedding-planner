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
        Schema::create('list_vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_vendor_id')->constrained('category_vendors')->onDelete('cascade');
            $table->string('vendor_name')->nullable();
            $table->integer('vendor_price')->nullable();
            $table->string('person_responsible')->nullable();
            $table->string('vendor_contact')->nullable();
            $table->string('social_media')->nullable();
            $table->string('vendor_features')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('list_vendors');
    }
};

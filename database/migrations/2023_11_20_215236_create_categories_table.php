<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('tax', 8, 2);
            $table->boolean('delivery_option');
            $table->boolean('discount_option');
            $table->enum('discount_type', ['percentage', 'fixed'])->nullable();
            $table->decimal('discount_value', 8, 2)->nullable();
            $table->string('thumbnail')->nullable();
            $table->boolean('hidden')->default(false);
            $table->unsignedBigInteger('band_id'); // Add the band_id column
            $table->foreign('band_id')->references('id')->on('bands'); // Add the foreign key constraint
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};

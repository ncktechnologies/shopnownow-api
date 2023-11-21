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
        Schema::create('delivery_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('band_id')->constrained()->onDelete('cascade');
            $table->string('location');
            $table->decimal('price', 8, 2);
            $table->boolean('hidden')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_locations');
    }
};

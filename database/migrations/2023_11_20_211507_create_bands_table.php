<?php

// database/migrations/xxxx_xx_xx_create_bands_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBandsTable extends Migration
{
    public function up()
    {
        Schema::create('bands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('minimum')->nullable();
            $table->string('bulk_discount_percentage')->nullable();
            $table->string('bulk_discount_amount')->default("0");
            $table->string('general_discount')->nullable();
            $table->boolean('discount_enabled')->default(false);
            $table->integer('free_delivery_threshold')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bands');
    }
}


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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->json('product_ids'); // Array of product IDs
            $table->json('quantities'); // Quantity of each product ID
            $table->string('delivery_info'); // JSON for delivery information
            $table->string('delivery_time_slot'); // Delivery time slot (e.g. 9am-12pm)
            $table->decimal('delivery_fee', 10, 2); // Delivery fee (e.g. $5.00
            $table->decimal('price', 10, 2); // Price/amount
            $table->decimal('tax', 10, 2); // Tax amount
            $table->string('order_id'); // Order ID (e.g. #0000001
            $table->string('payment_type');
            $table->string('recipient_name');
            $table->string('recipient_phone');
            $table->string('recipient_email');
            $table->string('status');
            $table->unsignedBigInteger('user_id')->nullable(); // User ID (optional)
            $table->string('coupon_code')->nullable(); // Coupon code (optional)
            $table->date('scheduled_date')->nullable(); // Scheduled date (optional)
            $table->boolean('discount_applied')->default(false); // Discount applied (boolean)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

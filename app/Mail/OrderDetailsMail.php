<?php

// app/Mail/OrderDetailsMail.php


namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product; // Import the Product model

class OrderDetailsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $payment;
    public $productNames; // Add a new property for product names

    /**
     * Create a new message instance.
     *
     * @param Order $order
     * @param Payment $payment
     * @return void
     */
    public function __construct(Order $order, Payment $payment)
    {
        $this->order = $order;
        $this->payment = $payment;

        // Fetch the product names using the product IDs
        $this->productNames = Product::whereIn('id', $this->order->product_ids)
                                     ->pluck('name')
                                     ->toArray();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.order-details')
                    ->with([
                        'order' => $this->order,
                        'payment' => $this->payment,
                        'productNames' => implode(', ', $this->productNames) // Pass the product names to the view
                    ]);
    }
}

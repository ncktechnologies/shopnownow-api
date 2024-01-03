<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product; // Import the Product model

class CustomerOrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $payment;
    public $products; // Add a new property for the products

    public function __construct(Order $order, Payment $payment)
    {
        $this->order = $order;
        $this->payment = $payment;

        // Fetch the products based on the product_ids in the order
        $productIds = json_decode($order->product_ids);
        $this->products = Product::find($productIds);
    }

    public function build()
    {
        return $this->markdown('emails.customer-order-confirmation')
                    ->with([
                        'order' => $this->order,
                        'payment' => $this->payment,
                        'products' => $this->products, // Pass the products to the view
                    ]);
    }
}

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
    public $productNames; // Add a new property for product names

    public function __construct(Order $order, Payment $payment)
    {
        $this->order = $order;
        $this->payment = $payment;

        // Fetch the products based on the product_ids in the order
        $productIds = json_decode($order->product_ids);
        $quantities = json_decode($order->quantities);

        $this->products = Product::find($productIds);

        foreach ($this->products as $index => $product) {
            $product->quantity = $quantities[$index];
        }

        // Convert product_ids string to array
        $productIds = explode(',', $this->order->product_ids);

        // Fetch the product names using the product IDs
        $this->productNames = Product::whereIn('id', $productIds)
            ->pluck('name')
            ->toArray();
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

<?php

// app/Mail/OrderDetailsMail.php


namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;
use App\Models\Payment;

class OrderDetailsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $payment;

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
                        'payment' => $this->payment
                    ]);
    }
}

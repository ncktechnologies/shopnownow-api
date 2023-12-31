<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;
use App\Models\Payment;

class CustomerOrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $payment;

    public function __construct(Order $order, Payment $payment)
    {
        $this->order = $order;
        $this->payment = $payment;
    }

    public function build()
    {
        return $this->markdown('emails.customer-order-confirmation')
                    ->with([
                        'order' => $this->order,
                        'payment' => $this->payment,
                    ]);
    }
}

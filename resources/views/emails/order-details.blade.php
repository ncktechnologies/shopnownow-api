@component('mail::message')
# Order Details

@component('mail::table')
| Customer Order Details | |
| ------------- |-------------|
| Order ID              | {{ $order->id }} |
| Order Status          | {{ $order->status }} |
| Product Names          | @foreach($products as $product)
    {{ $product->name }} ({{ $product->quantity }})@if(!$loop->last), @endif
@endforeach
| Quantities            | {{ $order->quantities }} |
| Delivery Info         | {{ $order->delivery_info }} |
| Delivery Fee          | {{ $order->delivery_fee }} |
| Price                 | {{ $order->price }} |
| Tax                   | {{ $order->tax }} |
| Payment Type          | {{ $order->payment_type }} |
| Recipient Name        | {{ $order->recipient_name }} |
| Recipient Phone       | {{ $order->recipient_phone }} |
| Recipient Email       | {{ $order->recipient_email }} |
| User ID               | {{ $order->user_id }} |
| Delivery Time Slot    | {{ $order->delivery_time_slot }} |
| Coupon Code           | {{ $order->coupon_code }} |
| Scheduled Date        | {{ $order->scheduled_date }} |
| Total Amount          | {{ $order->price + $order->delivery_fee + $order->tax }} |
| ...                   | ... |  <!-- Add more order details here -->

| Payment Details | |
| --------------- |-------------|
| Payment ID                  | {{ $payment->id }} |
| Payment Amount              | {{ $payment->amount }} |
| Total Amount                | {{ $order->price + $order->delivery_fee + $order->tax }} |
| Payment Method              | {{ $payment->payment_type }} |
| User ID                     | {{ $payment->user_id }} |
| Status                      | {{ $payment->status }} |
| Order ID                    | {{ $payment->order_id }} |
| Reference                   | {{ $payment->reference }} |
| Payment Type                | {{ $payment->payment_type }} |
| Payment Gateway             | {{ $payment->payment_gateway }} |
| Payment Gateway Reference   | {{ $payment->payment_gateway_reference }} |
| ...                         | ... |  <!-- Add more payment details here -->
@endcomponent

Thanks,
{{ config('app.name') }}
@endcomponent

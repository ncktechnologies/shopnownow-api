{{-- @php
    $productIds = json_decode($order->product_ids, true);
    $quantities = json_decode($order->quantities, true);
    $products = \App\Models\Product::find($productIds);
@endphp --}}

@component('mail::message')

# <strong>Order Receipt</strong>


# Your order has been completed successfully.


# Order Details


Order ID: {{ $order->order_id }}

Order Status: {{$order->status }}

Date/Time: {{ $order->created_at }}

Product Names & Quantities :
@foreach($products as $product)
    {{ $product->name }} ({{ $product->quantity }})@if(!$loop->last), @endif
@endforeach


Delivery Address: {{ $order->delivery_info }}

Delivery Time Slot: {{ $order->delivery_time_slot }}

Delivery Date: {{ $order->created_at }}

Scheduled Date: {{ $order->scheduled_date }}

Recipient Name: {{ $order->recipient_name }}

Recipient Phone: {{ $order->recipient_phone }}

Recipient Email: {{ $order->recipient_email }}

Product Price: N{{ $order->price }}

Tax: N{{ $order->tax }}

Delivery Fee: N{{ $order->delivery_fee }}

Total Price: N{{ $order->price }}

Payment Type: {{ $order->payment_type }}

Coupon Code: {{ $order->coupon_code }}


Thank you

<strong>ShopNowNow</strong>

# *Enjoy Free Delivery N25,000 Orders and above!*


Email: support@shopnownow.co or visit our contact page
{{-- Disclaimer:
Information contained in this email is confidential and intended for the addressee only. Any dissemination, distribution, copying or use of this communication without prior permission from the addressee is strictly prohibited. If you are not the intended recipient of this communication, please delete it permanently without copying, disclosing or otherwise using its contents, and notify shopnownow immediately. --}}
@endcomponent

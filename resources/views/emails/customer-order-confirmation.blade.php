{{-- @php
    $productIds = json_decode($order->product_ids, true);
    $quantities = json_decode($order->quantities, true);
    $products = \App\Models\Product::find($productIds);
@endphp --}}

@component('mail::message')

<strong>Order Receipt</strong>
# Your order has been completed successfully.

Order Details:

Order ID: {{ $order->order_id }}

Order Status: {{$order->status }}

Date/Time: {{ $order->created_at }}

Details:
@foreach($products as $index => $product)
    {{ $product->name }} ({{ $quantities[$index] }})@if(!$loop->last), @endif
@endforeach

Total: N{{ $order->price }}

Selected Delivery Method: {{$order->delivery_info }}

Selected Delivery Time: {{ $order->delivery_time_slot }}

Address: {{ $order->delivery_info }}

Customer Details

Customer: {{ $order->recipient_name }}

Email: {{ $order->recipient_email }}

Phone: {{ $order->recipient_phone }}

Your order will be delivered accordingly.

<strong>Enjoy Free Delivery for N25,000 Orders and above!</strong>

Thank you

ShopNowNow

Email: support@shopnownow.co or visit our contact page
Disclaimer:
Information contained in this email is confidential and intended for the addressee only. Any dissemination, distribution, copying or use of this communication without prior permission from the addressee is strictly prohibited. If you are not the intended recipient of this communication, please delete it permanently without copying, disclosing or otherwise using its contents, and notify shopnownow immediately.
@endcomponent

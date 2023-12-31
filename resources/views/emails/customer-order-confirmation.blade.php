{{-- @php
    $productIds = json_decode($order->product_ids, true);
    $quantities = json_decode($order->quantities, true);
    $products = \App\Models\Product::find($productIds);
@endphp --}}

@component('mail::message')
# Your order has been completed successfully.

Order Details: {{ $order->order_id }}

Date/Time: {{ $order->created_at }}

Details:
{{-- @foreach($products as $index => $product)
    {{ $product->name }} x{{ $quantities[$index] }}: N{{ $product->price * $quantities[$index] }}
@endforeach --}}

Total: N{{ $order->price }}

Selected Delivery Method: {{ json_decode($order->delivery_info, true)['method'] }}

Selected Delivery Time: {{ $order->delivery_time_slot }}

Address: {{ json_decode($order->delivery_info, true)['address'] }}

Customer Details

Customer: {{ $order->recipient_name }}

Email: {{ $order->recipient_email }}

Phone: {{ $order->recipient_phone }}

Your order will be delivered accordingly.

<strong>Enjoy Free Delivery for N25,0000 Orders and above!</strong>

Thank you

ShopNowNow

Email: support@shopnownow.co or visit our contact page
Disclaimer:
Information contained in this email is confidential and intended for the addressee only. Any dissemination, distribution, copying or use of this communication without prior permission from the addressee is strictly prohibited. If you are not the intended recipient of this communication, please delete it permanently without copying, disclosing or otherwise using its contents, and notify shopnownow immediately.
@endcomponent

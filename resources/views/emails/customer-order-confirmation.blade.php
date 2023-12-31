@component('mail::message')
# Your order has been completed successfully.

Order Details: {{ $order->id }}

Date/Time: {{ $order->created_at }}

Details:
@foreach($order->products as $product)
{{ $product->name }} x{{ $product->pivot->quantity }}: N{{ $product->pivot->price }}
@endforeach

Total: N{{ $order->total }}

Selected Delivery Method: {{ $order->delivery_method }}

Selected Delivery Time: {{ $order->delivery_time }}

Address: {{ $order->address }}

Customer Details

Customer: {{ $order->user->name }}

Email: {{ $order->user->email }}

Phone: {{ $order->user->phone }}

Your order will be delivered accordingly.


<strong>Enjoy Free Delivery for N25,0000 Orders and above!</strong>

Thank you

ShopNowNow

Email: support@shopnownow.co or visit our contact page
Disclaimer:
Information contained in this email is confidential and intended for the addressee only. Any dissemination, distribution, copying or use of this communication without prior permission from the addressee is strictly prohibited. If you are not the intended recipient of this communication, please delete it permanently without copying, disclosing or otherwise using its contents, and notify shopnownow immediately.
@endcomponent

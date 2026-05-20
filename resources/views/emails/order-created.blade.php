<!DOCTYPE html>
<html>
<head>
    <title>Order Created</title>
</head>
<body>
    <h2>Hello {{ $order->customer_name }}</h2>

    <p>Your order has been successfully created.</p>

    <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
    <p><strong>Product:</strong> {{ $order->product_name }}</p>
    <p><strong>Total:</strong> {{ $order->total_price }}</p>

    <p>Thank you for shopping with us!</p>
</body>
</html>
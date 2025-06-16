<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Order Confirmation</title>
    </head>
    <body>
        <h1>Thank you for your order, {{ $order->first_name ?? 'Customer' }}!</h1>
        <p>Your order number is: <strong>{{ $order->order_number }}</strong></p>
        <p>We'll notify you once your items are on the way.</p>
    </body>
</html>
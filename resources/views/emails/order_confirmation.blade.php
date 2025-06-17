<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Order Confirmation</title>
    </head>
    <body>
        <h1>Thanks for your order {{ $order->first_name ?? 'Customer' }}!</h1>
        <p>Your order number is: <strong>{{ $order->order_number }}</strong></p>
        <p>We'll let you know once your order is on way.</p>
        <br>
        <p>Please note this site is only meant as a demonstration of skills and any orders will be immediately cancelled and no money taken.</p>
        <p>If you have any concerns please contact us at help@hire-tom.co.uk</p>
    </body>
</html>
<!DOCTYPE html>
<html>

<head>

    <title>Purchase Confirmation</title>
</head>

<body>
    <h2>Thank you for your purchase!</h2>

    <p>Dear {{ $user->name }},</p>

    <p>We are pleased to confirm that your purchase has been successful.</p>

    <h3>Order Details:</h3>

    <ul>
        @foreach ($items as $item)
        <li>{{ $item['titre']}} -{{$item['type']}} -{{ $item['price'] }}</li>
        @endforeach
    </ul>

    <p>Total Amount: {{ $total }}</p>

    <p>Thank you for choosing our service. If you have any questions or need further assistance, please feel free to contact our support team.</p>

    <p>Best regards,</p>
    <p>The Well-Being Platform Team</p>
</body>

</html>
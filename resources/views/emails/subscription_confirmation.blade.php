<!DOCTYPE html>
<html>
<head>
    <title>Subscription Confirmation</title>
</head>
<body>
    <h1>Subscription Confirmation</h1>
    
    <p>Hello {{ $user->name }},</p>
    
    <p>Thank you for subscribing to our service. Your subscription is confirmed.</p>
    
    <p>Subscription Details:</p>
    <ul>
        <li>Subscription :{{ $subscription->name }}</li>
        <li>Duration :{{ $subscription->duration }}</li>
        <li>Start Date: {{ $subscriber->start_date }}</li>
        <li>End Date: {{ $subscriber->end_date }}</li>
    </ul>
</body>
</html>

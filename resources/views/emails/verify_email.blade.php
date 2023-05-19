<!DOCTYPE html>
<html>
<head>
    <title>Email Verification</title>
</head>
<body>
    <h2>Email Verification</h2>
    <p>Dear {{ $user->name }},</p>
    <p>Please click the following link to verify your email address:</p>
    <a href="{{ $verificationUrl }}">Verify Email</a>
</body>
</html>

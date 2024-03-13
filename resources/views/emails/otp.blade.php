<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your OTP Code</title>
    <style>
        /* Add your email styles here */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            text-align: center;
        }

        p {
            color: #666;
            font-size: 16px;
            line-height: 1.6;
        }

        .otp-code {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin-top: 20px;
            margin-bottom: 30px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Your OTP Code</h1>
        <p>Here is your one-time password (OTP) code for your process:</p>
        <div class="otp-code">{{ $otp }}</div>
        <p>Please use this code to complete process. This code will expire after a certain period of
            time.</p>
    </div>
</body>

</html>

<!DOCTYPE html>
<html>
<head>
    <title>Exception Alert</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }
        .container {
            background-color: #fff;
            width: 80%;
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        h1 {
            color: #fa5252;
        }
        p {
            font-size: 16px;
            line-height: 1.5;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            text-align: center;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>An Exception Occurred!</h1>
        <p><strong>Message:</strong> {{ $message }}</p>
        <p><strong>File:</strong> {{ $file }}</p>
        <p><strong>Line:</strong> {{ $line }}</p>
        <div class="footer">
            This is an automated message, please do not reply.
        </div>
    </div>
</body>
</html>

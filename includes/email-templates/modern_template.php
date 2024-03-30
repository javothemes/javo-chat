<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{title}}</title>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background-color: #f2f4f6;
            color: #51545E;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 40px;
            background-color: #ebeef3;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #eaeaea;
        }
        .content {
            padding-top: 30px;
            text-align: center;
            background-color: #ebeef3;
        }
        a {
            color: #000;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            font-size: 12px;
            color: #a2a2a2;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 20px;
            background-color: #0045ff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
        }
        .icon {
            padding: 10px;
            background-color: #eaeaea;
            border-radius: 50%;
            display: inline-block;
            margin-bottom: 20px;
        }

        h2 {
            font-size: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <span class="icon">🔔</span>
            <p>Oh well, there's more...</p>
        </div>
        <div class="content">
            <h1>Hi "John Doe"</h1>
            <p>{{title}}</p>
            <div style="padding: 20px; background-color: #f9f9f9; margin: 20px 0; border-radius: 5px;">
                <img src="https://source.unsplash.com/random/100x100/?person" alt="User Image" style="border-radius: 50%;">
                <h2>Logan Paul</h2>
                <p>{{content}}</p>
            </div>
            <a href="#" class="button">REPLY NOW</a>
        </div>
        <div class="footer">
            © Notify Inc. | 123 Broadway, Suite 1230 | New York, NY 000123, USA.<br>
            <a href="#">View Web Version</a> | <a href="#">Email Preferences</a> | <a href="#">Privacy Policy</a><br>
            If you have any questions please contact us at <a href="mailto:support@email.com">support@email.com</a><br>
            Unsubscribe from our mailing lists
            <p>
                <a href="https://www.google.com/url?q=http://www.facebook.com&sa=D&ust=1584959908820000">Facebook</a> | 
                <a href="https://twitter.com">Twitter</a> | 
                <a href="https://www.pinterest.com">Pinterest</a> | 
                <a href="https://www.instagram.com">Instagram</a> | 
                <a href="https://www.linkedin.com">LinkedIn</a>
            </p>
            <img src="https://source.unsplash.com/random/48x48/?logo,google" alt="Google Play">
            <img src="https://source.unsplash.com/random/48x48/?logo,apple" alt="App Store">
        </div>
    </div>
</body>
</html>

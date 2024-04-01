<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>You're Invited!</title>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Jost', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: #333333;
        }
        .email-container {
            background-color: #f5f5f5;
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            border: 2px solid #131314;
            text-align: center;
            padding: 20px;
        }
        .email-container .email-header {
            background-color: #f8f8f8;
            padding: 20px;
            font-size: 24px;
            font-weight: bold;
        }
        .email-container .email-content {
            font-weight: 600;
        }
        .email-container .email-body {
            padding: 0px 20px;
        }
        .email-container .email-footer {
            background-color: #292a2c;
            padding: 15px 20px;
            font-size: 12px;
            color: #bcc0cb;
            margin: 15px 0 0 0;
        }
        .email-container .email-footer a{
               color: #bcc0cb;
        }
        .email-container .button {
            background-color: #000000;
            color: #ffffff;
            padding: 7px 30px;
            text-decoration: none;
            margin: 35px 0 20px;
            display: inline-block;
            border-radius: 0;
        }
        .email-container .invite-code {
            font-weight: bold;
            font-size: 12px;
            margin: 20px 0;
        }

        .footer-content {
            display: flex;
            justify-content: space-evenly;
            align-items: center;
            padding-top: 20px;
        }

        .email-container .footer-content a{
            color: #000;
        }
        .email-container .social-icons {
            margin: 10px 0;
        }
        .email-container .social-icon {
            height: 24px;
            width: 24px;
            margin: 0px;
            text-decoration: none;
        }
        .email-container .company-info {
            font-size: 11px;
            font-weight: 400;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            {{title}}
        </div>
        <div class="email-body">
            <div class="email-content">{{content}}</div>
            <a href="<?php echo esc_url(home_url('/')); ?>" class="button">Check My Chat Box</a>
            
            <div style="text-align: center; margin: 0px 0;">
                <img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . 'public/images/icon-msg.png'; ?>" width="150" alt="message">
            </div>

            <div class="invite-code">The best directory site!  Join us if you have not.</div>

            <div class="footer-content">
                <div class="company-info">Street address<br>Phone number</div>
                <div class="social-icons">
                    <a href="#" class="social-icon">
                        <img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . 'public/images/icon-facebook.png'; ?>" alt="Facebook">
                    </a>
                    <a href="#" class="social-icon">
                        <img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . 'public/images/icon-twitterx.png'; ?>" alt="Twitter">
                    </a>
                    <a href="#" class="social-icon">
                        <img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . 'public/images/icon-instagram.png'; ?>" alt="Instagram">
                    </a>
                </div>
                <div>
                    <a href="<?php echo esc_url(home_url('/')); ?>">Visit site</a>
                </div>

            </div>
        </div>
        <div class="email-footer">
            Copyright by Your site. <a href="#">Explore</a>
        </div>
    </div>
</body>
</html>

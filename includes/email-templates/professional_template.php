<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>New Message Alert</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f2f4f6;
        margin: 0;
        padding: 0;
    }
    .email-container {
        max-width: 600px;
        margin: 0 auto;
        background: #ffffff;
    }
    .email-header {
        background: #373B53;
        padding: 20px;
        color: #ffffff;
        text-align: center;
    }
    .email-body {
        padding: 20px;
        text-align: center;
    }
    .email-footer {
        background: #eaeaea;
        padding: 20px;
        text-align: center;
        font-size: 12px;
        color: #51545E;
    }
    .button {
        background-color: #FFD700;
        color: #ffffff;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        display: inline-block;
        margin-top: 20px;
    }
    .profile-pic {
        border-radius: 50%;
        width: 100px;
        height: 100px;
        margin: 10px auto;
        display: block;
    }
    .social-icons img {
        margin: 0 5px;
    }
    .app-icons img {
        margin: 0 5px;
        height: 40px;
    }
</style>
</head>
<body>
<div class="email-container">
    <div class="email-header">
        <!-- Logo or Title -->
        <h1>Lotus</h1>
        <!-- View in browser link, usually this would be a hyperlink -->
        <p>View this email in your browser</p>
    </div>
    <div class="email-body">
        <!-- Content -->
        <h2>Hi "Anna Daly"</h2>
        <h3>{{title}}</h3>
        <!-- User Image -->
        <img src="https://source.unsplash.com/random/100x100/?face" alt="Anna Daly" class="profile-pic">
        <!-- Username -->
        <p>@Anna Daly</p>
        <!-- Message Preview -->
        <p>"{{content}}"</p>
        <!-- Call to action -->
        <a href="#" class="button">JOIN ACCOUNT</a>
    </div>
    <div class="email-footer">
        <!-- Footer Content -->
        <p>Address name St 12, City Name, State, Country Name</p>
        <p>(738) 479-6719 - (369) 718-1973</p>
        <p>info@website.com - www.website.com</p>
        <!-- Social Media Icons -->
        <div class="social-icons">
            <img src="https://source.unsplash.com/random/48x48/?social,facebook" alt="Facebook">
            <img src="https://source.unsplash.com/random/48x48/?social,instagram" alt="Instagram">
            <img src="https://source.unsplash.com/random/48x48/?social,twitter" alt="Twitter">
            <img src="https://source.unsplash.com/random/48x48/?social,pinterest" alt="Pinterest">
            <img src="https://source.unsplash.com/random/48x48/?social,linkedin" alt="LinkedIn">
        </div>
        <!-- App Store Icons -->
        <div class="app-icons">
            <img src="https://source.unsplash.com/random/48x48/?logo,apple" alt="App Store">
            <img src="https://source.unsplash.com/random/48x48/?logo,google" alt="Google Play">
        </div>
        <!-- Unsubscribe and Other Links -->
        <p>
            UNSUBSCRIBE | WEB VERSION | SEND TO A FRIEND
        </p>
    </div>
</div>
</body>
</html>

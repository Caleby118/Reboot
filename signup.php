<?php 
session_start();
include_once('incl/connect.php');
checkLogin();
if(isset($_POST['reg']) & !empty($_POST['reg'])) {
    $userName = $_POST['userName'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm-password'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    $post = new User();
    echo $post->signUp($userName, $email, $password, $confirmPassword, $ip, $firstName, $lastName);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/logreg.css">
    <script src="js/script.js" defer></script>
    <link
  rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css"
/>

<script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/355bb21480.js" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Teko:wght@500&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <title>REBOOT - Sign up</title>
</head>
<body>
    <div class="sign-wrap">
        <div class="sign-logo"><a href="index.html" class="logo-link" style="text-decoration: none;">r<span class="glitch">e</span>boot</a></div>
        

        <div class="sign-container">
            <div class="sign-img">
                <img loading="lazy" src="assets/images/sign/2.jpg" class="img" alt="">
            </div>
            <div class="sign-main">
            <?php if(!empty($errors)) {
                    echo '<ul class="error">';
                    foreach($errors as $error) {
                        echo '<li>'. $error .'</li>';
                    }
                    echo '</ul>';
                }

                if(!empty($messages)) {
                    echo '<div class="error">';
                    foreach($messages as $message) {
                        echo $message;
                    }
                    echo '</div>';
                }
                ?>   
                <div class="sub-heading" style="margin: 0 0 1rem;"></div>
                <form action="" method="post" class="login">
                    <div class="sub-heading" style="margin: 0 0 1rem; font-size: 1.3rem;">Your details.</div>
                    <div class="user-details">
                        <input type="text" name="firstName" class="input-text" placeholder="First name" style="display: inline-block; width: 50%; margin-right: 1%" required>
                        <input type="text" name="lastName" class="input-text" placeholder="First name" style="display: inline-block; width: 50%;" required>
                    </div>
                    <input type="email" name="email" class="input-text" placeholder="Email" required>
                    <div class="sub-heading" style="margin: 0 0 1rem; font-size: 1.3rem;">Username and Password.</div>
                    <input type="text" name="userName" class="input-text" placeholder="Username" required>
                    <input type="password" name="password" class="input-text" placeholder="Password" required>
                    <input type="password" name="confirm-password" class="input-text" placeholder="Confirm Password" required>
                    <!-- <button name="reg" class="primary login-btn hero-btn">Sign up</button> -->
                    <input type="submit" name="reg" class="primary-a login-btn hero-btn" value="Sign up">
                    <div class="divider">or</div>
                    <a href="login.php" class="secondary-a link login-btn hero-btn">Log in</a>
                </form>
            </div>
        </div>        
    </div>
   
    
</body>
</html>
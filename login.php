<?php 
session_start();
include_once('incl/connect.php');
checkLogin();
if(isset($_POST['login']) & !empty($_POST['login'])) {
    $user = $_POST['user'];
    $password = $_POST['password'];
    $ip = "1";
    $post = new User();
    echo $post->signIn($user, $password);
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
    <title>REBOOT - Log in</title>
</head>
<body>
        <div class="sign-logo"><a href="index.html" class="logo-link" style="text-decoration: none;">r<span class="glitch">e</span>boot</a></div>
       

    <div class="sign-container">
        <div class="sign-img">
            <img loading="lazy" src="assets/images/sign/1.jpg" class="img" alt="">
        </div>
        <div class="sign-main">
            <div class="sub-heading" style="margin: 0 0 1rem;">Login.</div>
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
            <form action="" method="post" class="login">
                <input type="text" name="user" class="input-text" placeholder="Username/Email" value="<?php echo (isset($user))?$user:'';?>" required>
                <input type="password" name="password" class="input-text" placeholder="Password" required>
                <div class="form-text"><a href="#">Forgot password?</a></div>
                <input type="submit" name="login" class="primary-a login-btn hero-btn" value="Log in">
                <div class="divider">or</div>
                <a href="signup.php" class="secondary-a link login-btn hero-btn">Sign up</a>
            </form>
        </div>
    </div>
    
</body>
</html>
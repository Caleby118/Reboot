<?php 
session_start();
include_once('incl/connect.php');
if(isset($_SESSION['id']) & !empty($_SESSION['id'])){
    $id = $_SESSION['id'];
}
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script src="assets/js/script.js" defer></script>
    <title>Document</title>
</head>
<body>
    <header>
        <?php include_once('incl/nav.php'); ?>
    </header>

    <div class="flex inner-container">

        <div class="flex column sidebar" id="sidebar">
            <div class="header">Explore</div>
            <ul class="flex column">
                <li class="flex active"><a href="https://caleb.wtf/reboot/new/products.php" style="display: flex; align-items: center;"><span class="grid icon">🏠</span>Home</a></li>
                <li class="flex"><a href="https://caleb.wtf/reboot/new/products.php" style="display: flex; align-items: center;"><span class="grid icon">🔎</span>Search</a></li>
                <li class="flex"><a href="https://caleb.wtf/reboot/new/chat.php" style="display: flex; align-items: center;"><span class="grid icon">💬</span>Chat</a></li>
            </ul>
            <?php if(isset($_SESSION['login']) && !empty($_SESSION['login'])) { ?>
            <div class="flex signout">
                <a href="https://caleb.wtf/reboot/new/logout.php" class="flex"><span class="grid icon">🚪</span>Sign out</a>
            </div>
            <?php } ?>
        </div>

        <div class="grid main-content">
            <div class="div1 header"><div class="flex header">⚡ Whats new?</div></div>
            <div class="capsule-horizontal capsule-background div2" >
                <div class="flex content">
                    Shop Winter deals
                </div>
            </div>
            <div class="capsule-horizontal capsule-background div3" style="--bg-img: url('../images/main-grid/div3.jpg');"> </div>
            <div class="capsule-vertical capsule-background div4" style="--bg-img: url('../images/main-grid/div4.jpg');"> </div>
            <div class="capsule-vertical capsule-background div5" style="--bg-img: url('../images/main-grid/shoes.jpg');">
            <div class="flex content">
                
            </div>
            </div>
            <div class="capsule-horizontal capsule-background div6" style="--bg-img: url('../images/main-grid/div6.jpg');"> </div>
            <div class="capsule-horizontal capsule-background div7" style="--bg-img: url('../images/main-grid/div7.jpg');"> </div>
            <div class="capsule-vertical capsule-background div8"  style="--bg-img: url('../images/main-grid/div8.jpg');"> </div>
            <div class="capsule-vertical capsule-background div9"> </div>
        </div>     

    </div>
</body>

</html>
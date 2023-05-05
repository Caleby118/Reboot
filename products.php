<?php 
session_start();
include_once('incl/connect.php');
include_once('incl/classes/Class.Products.php');
$products = new Products();

if(isset($_SESSION) && isset($_SESSION['login'])) {

    if(isset($_SESSION['id']) & !empty($_SESSION['id'])){
        $id = $_SESSION['id'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://caleb.wtf/reboot/new/assets/css/style.css">
    <link rel="stylesheet" href="https://caleb.wtf/reboot/new/assets/css/profile.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script src="https://caleb.wtf/reboot/new/assets/js/script.js" defer></script>
    <title>Search</title>
</head>
<body>
<header>
        <?php include_once('incl/nav.php'); ?>
    </header>
    <div class="flex column profile-container">
        <div class="flex column sidebar-res" id="sidebar">
            <div class="header">Explore</div>
            <ul class="flex column">
                <li class="flex"><a href="https://caleb.wtf/reboot/new/products.php" style="display: flex; align-items: center;"><span class="grid icon">🏠</span>Home</a></li>
                <li class="flex active"><a href="https://caleb.wtf/reboot/new/products.php" style="display: flex; align-items: center;"><span class="grid icon">🔎</span>Search</a></li>
                <li class="flex"><a href="https://caleb.wtf/reboot/new/chat.php" style="display: flex; align-items: center;"><span class="grid icon">💬</span>Chat</a></li>
            </ul>
            <?php if(isset($_SESSION['login']) && !empty($_SESSION['login'])) { ?>
            <div class="flex signout">
                <a href="https://caleb.wtf/reboot/new/logout.php" class="flex"><span class="grid icon">🚪</span>Sign out</a>
            </div>
            <?php } ?>
        </div>

        <div class="flex column profile-main">
            <div class="flex main-header">
                <div class="title">
                    <?php if(isset($_GET['q']) && !empty($_GET['q'])) {
                        echo "Searching for: ".$_GET['q'];
                    }
                    ?>
                </div>
            </div>
            <div class="grid profile-grid">

                <?php
                if(isset($_GET['q']) && !empty($_GET['q'])) {
                    $searchTerm = $_GET['q'];
                    $products->getSearchedProducts($searchTerm);    
                }else{
                    echo $products->getAllProducts(); 
                }
                ?>

            </div>
        </div>
    </div>

    </div>
</body>
</html>
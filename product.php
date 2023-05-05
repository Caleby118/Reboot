<?php
session_start();
include_once('incl/connect.php');
include_once('incl/classes/Class.Products.php');
if(isset($_SESSION) && isset($_SESSION['login'])) {

    if(isset($_SESSION['id']) & !empty($_SESSION['id'])){
        $id = $_SESSION['id'];
    }
}
if(isset($_GET['id']) && !empty($_GET['id'])) {
    $pageId = $_GET['id'];
}
$product = new Products();
$product->getProduct($pageId);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://caleb.wtf/reboot/new/assets/css/style.css">
    <link rel="stylesheet" href="https://caleb.wtf/reboot/new/assets/css/product.css">
    <script src="https://cy118.brighton.domains/projects/reboot/js/script.js" defer></script>
    <script src="https://cy118.brighton.domains/projects/reboot/js/popup.js" defer></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <title>Reboot - Home</title>
</head>

<body>

<?php include('incl/nav.php'); ?>
<div class="flex column sidebar-res" id="sidebar">
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
    <div class="product-container">
        <div class="product-images">
            <div class="user-info">
                <div class="user-profile-img pp-sm" style="--profilePic: url('<?php echo $product->getProfilePic(); ?>')">

                </div>
                <div class="user-info-details">
                    <div><a href="#" style="text-decoration: none;"><?php echo $product->getUserName(); ?></a></div>
                    <div>Brighton, UK</div>
                </div>
            </div>
            <div class="prod">
                <img loading="lazy" src="<?php echo $product->getImage(); ?>" class="img prod-img" alt="">
              </div>

        </div>

        <div class="product-info">
            <div class="sub-heading">
                
            </div>
            <div class="product-description">
                <table class="product-info-table">

                    <?php if (!empty($product->getBrand())) { ?>
                    <tr>
                        <th class="product-table-header">Size</th>
                        <td class="product-table-content"><?php echo $product->getSize(); ?></td>   
                    </tr>
                    <?php } ?>

                    <?php if (!empty($product->getBrand())) { ?>
                    <tr>
                        <th class="product-table-header">Brand</th>
                        <td class="product-table-content"><?php echo $product->getBrand(); ?></td>   
                    </tr>
                    <?php } ?>

                </table>
            </div>
            <div class="product-actions">
                <div class="product-price">&#163;<?php echo $product->getPrice(); ?></div>
                <?php 
                if(isset($_POST['buy'])) {
                    echo $product->buyProduct($pageId, $id);
                }
                ?>
                <?php if($product->getStatus() == 0) { ?>
                    <form action="" method="post">
                        <input type="submit" name="buy" class="primary login-btn hero-btn"  style="width: 100%;" value="Buy Now">                    
                    </form>
                <?php }else{ ?>
                    <h2>Sold</h2>
                <?php } ?>
            </div>

            <pre><?php echo $product->getDescription(); ?></pre>
        </div>
    </div>

    </div>
    <footer>
        <div class="footer-section">
            <a href="#">Privacy policy</a>
            <a href="#">Cookie policy</a>
            <a href="#">Terms & conditions</a>
            <a href="#">Frequently asked questions</a>
            </ul>
        </div>
        <div class="footer-section">
            <i class="fa-brands fa-facebook"></i>
            <i class="fa-brands fa-instagram"></i>
            <i class="fa-brands fa-youtube"></i>
        </div>
    </footer>
    <div id="productPop" class="prodimgpop">
        <div class="popup-img">
          <img id="popID" class="img">
        </div>
      </div>
</body>

</html>
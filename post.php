<?php 
session_start();
if(isset($_SESSION) && isset($_SESSION['login'])) {
include_once('incl/connect.php');
include_once('incl/classes/Class.Products.php');
if(isset($_SESSION['id'])  && !empty($_SESSION['id'])) {
    $id = $_SESSION['id'];
}
}else{
    echo "<script> window.location = 'https://www.caleb.wtf/reboot/new/login.php'</script>";
}
?>
<?php 
if(isset($_POST['submit']) && !empty($_POST['submit'])) {
    $id = $_SESSION['id'];
    $productType = $_POST['productType'];
    $product = $_POST['product'];
    $description = $_POST['description'];
    $size = $_POST['productSize'];
    $brand = $_POST['brand'];
    $price = $_POST['price'];
    $uploadPic = new Products();
    echo $uploadPic->listProduct($id, $productType, $product, $description, $size, $brand, $price, $_FILES["fileToUpload"]);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://caleb.wtf/reboot/new/assets/css/style.css">
    <script src="https://caleb.wtf/reboot/new/assets/js/script.js" defer></script>
    <script src="https://cy118.brighton.domains/projects/reboot/js/script.js" defer></script>
    <script src="https://cy118.brighton.domains/projects/reboot/js/popup.js" defer></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <title>Reboot - Home</title>
</head>

<body>

<?php include('incl/nav.php'); ?>

    <div class="flex column post-container">

        <div class="flex column sidebar-res" id="sidebar">
            <div class="header">Explore</div>
            <ul class="flex column">
                <li class="flex"><a href="https://caleb.wtf/reboot/new/products.php" style="display: flex; align-items: center;"><span class="grid icon">🏠</span>Home</a></li>
                <li class="flex"><a href="https://caleb.wtf/reboot/new/products.php" style="display: flex; align-items: center;"><span class="grid icon">🔎</span>Search</a></li>
                <li class="flex"><a href="https://caleb.wtf/reboot/new/chat.php" style="display: flex; align-items: center;"><span class="grid icon">💬</span>Chat</a></li>
            </ul>
            <?php if(isset($_SESSION['login']) && !empty($_SESSION['login'])) { ?>
            <div class="flex signout">
                <a href="https://caleb.wtf/reboot/new/logout.php" class="flex"><span class="grid icon">🚪</span>Sign out</a>
            </div>
            <?php } ?>
        </div>
        <div class="flex header">
            <div class="text"></div>
        </div>
        <div class="flex column form-area">
            <form action="" method="post" class="flex column"  enctype="multipart/form-data">
                <label for="images">Select up to 5 images.</label>
                <input type="file" name="fileToUpload" id="fileToUpload" accept="image/png/jpg/jpeg">
                <label for="productType">What type of product are you selling?</label>
                <select name="productType" id="productType" onchange="typeChange(this.value)">
                    <option value="0">Catagory</option>
                    <option value="1">Clothing</option>
                    <option value="2">Tutoring</option>
                    <option value="3">Other</option>
                </select>

                <select name="product" id="clothingType" style="display: none"></select>
                <select name="tutoring" id="tutoring" style="display: none"></select>


                <label for="description">Add a description.</label>
                <textarea name="description" id="description" cols="30" rows="10" required></textarea>
                <label for="productSize" id="productSizeLabel" style="display: none">Size</label>
                <select name="productSize" id="productSize" style="display: none" required>
                    <option value="Extra small">XS</option>
                    <option value="Small">S</option>
                    <option value="Medium">M</option>
                    <option value="Large">L</option>
                    <option value="Extra large">XL</option>
                    <option value="XXL">XXL</option>
                </select>
                <label for="brand" style="display: none;">Brand</label>
                <input type="text" name="brand" id="brand" placeholder="Brand" style="display: none;">
                <label for="price">Price</label>
                <input type="number" id="price" name="price" placeholder="£" autocomplete="off" required>
                <input type="submit" name="submit" value="List">
            </form>
        </div>
    </div>

    </div>
</body>
<script>
const productType = document.getElementById("productType");
const clothingType = document.getElementById("clothingType");
const tutoring = document.getElementById("tutoring");
const size = document.getElementById("productSize");
const brand = document.getElementById("brand");
function typeChange(elm) {
    if(elm == 1) {
        tutoring.style.display = "none";
        clothingType.style.display = "block";
        size.style.display = "block";
        brand.style.display = "block";
        clothingType.innerHTML = '<option value="1">T-Shirts & Vests</option><option value="2">Shirts</option><option value="3">Jackets & Coats</option><option value="4">Hoodies & Sweatshirts</option><option value="5">Trousers</option><option value="6">Dress</option><option value="7">Skirt</option><option value="8">Shoes</option>';
    } else if(elm == 2) {
        tutoring.style.display = "block";
        tutoring.innerHTML = '<option value="1">Subject</option><option value="1">Selected</option><option value="1">Selected</option><option value="1">Selected</option><option value="1">Selected</option><option value="1">Selected</option>';
    } else if (elm == 3) {
        tutoring.style.display = "none";
        clothingType.style.display = "none";
    } else {
        tutoring.style.display = "none";
        clothingType.style.display = "none";
    }
    
}
</script>
</html>
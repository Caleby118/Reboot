<?php
session_start();
include_once "Class.Database.php";
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
class Products extends Database {
    public function listProduct($id, $productType, $product, $description, $size, $brand, $price) {
        $error = false;
        if(empty($productType)) {
            $error = true;
            $error[] = "Product type";
            echo $productType;
        }
        if(empty($product)) {
            $error = true;
            $errors[] = "Product";
            echo $product;
        }
        if(empty($description)) {
            $errors[] = "Description";
            echo $description;
        }
        if(empty($errors)) {
            $pageId = round(microtime(true));
            $sql = "INSERT INTO products (productID, sellerID, productType, product, description, size, brand, price) 
                    VALUES (:productID, :sellerID, :productType, :product, :description, :size, :brand, :price)";
            $stmt = $this->connect()->prepare($sql);
            $res = $stmt->execute(array(':productID' => $pageId,
                                        ':sellerID' => $id,
                                        ':productType' => $productType,
                                        ':product' => $product,
                                        ':description' => $description,
                                        ':size' => $size,
                                        ':brand' => $brand,
                                        ':price' => $price
                                        ))or die(print_r($stmt->errorInfo(), true));;
            if($res) {
                $uploadOk = 1;
                require '../vendor/autoload.php';
        
            
                // AWS Info
                $bucketName = 'reboot-media';
                $IAM_KEY = 'key';
                $IAM_SECRETKEY = 'secretkey';
            
                // Connect to AWS
                try {
                    $s3 = S3Client::factory(
                        array(
                            'credentials' => array(
                                'key' => $IAM_KEY,
                                'secret' => $IAM_SECRET
                            ),
                            'version' => 'latest',
                            'region'  => 'eu-west-2'
                        )
                    );
                } catch (Exception $e) {
                    die("Error: " . $e->getMessage());
                }
        
                $allowedTypes = array('jpg', 'jpeg', 'png');
                $fileExtension = strtolower(pathinfo($_FILES["fileToUpload"]["name"],PATHINFO_EXTENSION));
                echo $fileExtension;
                
                // Check if the file is a valid image type
                if (!in_array($fileExtension, $allowedTypes)) {
                    echo "Error: Invalid file type. Please upload a JPG or PNG file.";
                    $uploadOk = 0;
                    exit;
                }
            
                // Set the maximum size of the compressed image
                $maxWidth = 1000;
                $maxHeight = 1000;
            
                // Get the uploaded file
                $file = $_FILES['fileToUpload']['tmp_name'];
            
                // Get the image dimensions
                list($width, $height) = getimagesize($file);
            
                // Calculate the new dimensions
                $ratio = min($maxWidth/$width, $maxHeight/$height);
                $newWidth = intval($width * $ratio);
                $newHeight = intval($height * $ratio);
            
                // Create a new image with the new dimensions
                $newImage = imagecreatetruecolor($newWidth, $newHeight);
            
                // Load the original image
                $origImage = imagecreatefromjpeg($file);
            
                // Copy and resize the original image to the new image
                imagecopyresampled($newImage, $origImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            
                // Save the compressed image to a file
                $newName = round(microtime(true));
                $compressedFilename = $newName.'.'.pathinfo($_FILES["fileToUpload"]["name"],PATHINFO_EXTENSION);
                if (!imagejpeg($newImage, $compressedFilename, 80)) {
                    echo "Error: Failed to save compressed image.";
                    $uploadOk = 0;
                }
        
                // For this, I would generate a unqiue random string for the key name. But you can do whatever.
                $pathInS3 = 'https://s3.eu-west-2.amazonaws.com/' . $bucketName . '/' . $compressedFilename;
            
                if($uploadOk = 1) {
                    $sql = "INSERT INTO product_images (postID, imageUrl) VALUES (:id, :imageUrl)";
                    $stmt = $this->connect()->prepare($sql);
                    $values = array(
                                    ':imageUrl' => "https://reboot-media.s3.eu-west-2.amazonaws.com/product-pics/".$newName.'.'.$fileExtension,
                                    ':id' => $pageId
                                    );
                    $res = $stmt->execute($values)or die(print_r($stmt->errorInfo(), true));;
                    if($res) {
                        
                        try {
                            // Uploaded:
                            $file = $_FILES["fileToUpload"]['tmp_name'];
                    
                            $s3->putObject(
                                array(
                                    'Bucket'=>$bucketName,
                                    'Key' =>  'product-pics/'.$compressedFilename,
                                    'SourceFile' => $compressedFilename,
                                    'StorageClass' => 'REDUCED_REDUNDANCY'
                                )
                            );
                            $uploaded = 1;
                        } catch (S3Exception $e) {
                            $uploaded = 0;
                            die('Error:' . $e->getMessage());
                        } catch (Exception $e) {
                            $uploaded = 0;
                            die('Error:' . $e->getMessage());
                        }
        
                        if ($uploaded == 1) {
                            echo "<script>
                            window.location = 'https://www.caleb.wtf/reboot/new/product/".$pageId."';
                            </script>";
                                
                            // Delete the compressed image file from the server
                            unlink($compressedFilename);
                        } else {
                            echo "Error uploading your file.";
                        }
                    } else {
                        echo "Error uploading file into database.";
                    }
                }
            }else{
                echo "Error inserting data";
            }
        }

    }
    public function buyProduct($pageId, $id) {
        $sql = "SELECT *
                FROM products p
                WHERE productID = ? AND status = 0";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute(array($pageId));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $count = $stmt->rowCount();
        if($count == 1) {
            $sql = "SELECT funds
                    FROM user_info
                    WHERE userID = ?";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute(array($id));
            $fundsRes = $stmt->fetch(PDO::FETCH_ASSOC);
            $funds = $fundsRes['funds'];
            $price = $row['price'];
            $sellerID = $row['sellerID'];
            $productID = $row['productID'];
            if($funds > $price) {
                $sql = "UPDATE products
                        SET status = 1
                        WHERE productID = ?";
                $stmt = $this->connect()->prepare($sql);
                $res = $stmt->execute(array($pageId));
                if($res) {
                    $newFunds = $funds - $price;
                    $sql = "UPDATE user_info
                            SET funds = :funds
                            WHERE userID = :id";
                    $stmt = $this->connect()->prepare($sql);
                    $updateFunds = $stmt->execute(array(':funds' => $newFunds,
                                                        ':id' => $id));
                    if($updateFunds) {
                        $sql = "UPDATE user_info
                                SET funds = funds + :price
                                WHERE userID = :id";
                        $stmt = $this->connect()->prepare($sql);
                        $updateSeller = $stmt->execute(array(':price' => $price,
                                                             ':id' => $sellerID));
                        if($updateSeller) {
                            $sql = "INSERT INTO sales (productID, buyerID, sellerID) VALUES (:productID, :buyerID, :sellerID)";
                            $stmt = $this->connect()->prepare($sql);
                            $updateSale = $stmt->execute(array('productID' => $productID, 'buyerID' => $id, 'sellerID' => $sellerID));
                            if($updateSeller) {
                                echo "Payment complete";
                            } else {
                                echo "Error updating sales table, purchase still completed";
                            }
                        }else{
                            echo "Payment failed";
                        }
                    }else{
                        echo "Failed to update funds";
                    }
                }else{
                    echo "Error updating product";
                }
                
            } else {
                echo 'Insufficient Funds';
            }
        } else {
            echo 'Product not found';
        }
    }
    public function getAllProducts() {
        $sql = "SELECT *
                FROM products p
                INNER JOIN product_images ON p.productID = product_images.postID
                ORDER BY p.datePosted DESC";
        $stmt = $this->connect()->prepare($sql);
        $res = $stmt->execute();
      
        if($res) {
          while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $image = $row['imageUrl'];
            $product_title = $row['product'];
            $product_description = $row['description'];
            $product_price = $row['price'];
            $product_id = $row['productID'];
            switch($product_title) {
              case '1':
                $product_title = 'T-Shirts & Vests';
                break;
              case '2':
                $product_title = 'Shirts';
                break;
              case '3':
                $product_title = 'Jackets & Coats';
                break;
              case '4':
                $product_title = 'Hoodies & Sweatshirts';
                break;
              case '5':
                $product_title = 'Trousers';
                break;
              case '6':
                $product_title = 'Dress';
                break;
              case '7':
                $product_title = 'Skirt';
                break;
                case '7':
                  $product_title = 'Shoes';
                  break;
              default:
                $product_title = 'unknown';
            }
            $product_descriptionR = ucfirst($product_description);
            (strlen($product_description) > 24) ? $product_description = substr($product_descriptionR, 0, 24).'...' : $product_description = $product_descriptionR;
      
            echo '<div>
                    <a href="https://www.caleb.wtf/reboot/new/product/'.$product_id.'" class="link">
                      <div class="card">
                        <div class="card-header">
                          <img src="'.$image.'" class="card-img">
                        </div>
                        <span class="new">
                          New
                        </span>
                        <span class="card-price">
                          £'.$product_price.'
                        </span>
                        <a href="https://www.caleb.wtf/reboot/new/product/'.$product_id.'" class="grid link button">View</a>
                        <div class="card-body">
                          <div class="card-title">
                            '.$product_title.'
                          </div>
                          <div class="card-text">
                            '.$product_description.'
                          </div>
                        </div>
                      </div>
                    </a>
                  </div>';
          }
        }
      }

      public function getSearchedProducts($searchTerm) {
        $sql = "SELECT *
        FROM products p
        INNER JOIN product_images ON p.productID = product_images.postID
        WHERE CONCAT(p.description, p.brand, p.size) LIKE :searchTerm
        ORDER BY p.datePosted DESC";
$stmt = $this->connect()->prepare($sql);
$res = $stmt->execute(array(':searchTerm' => '%' . $searchTerm . '%'));
$count = $stmt->rowCount();
        if($count == 0) {
            echo 'No results found';
        }
        if($res) {
          while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $image = $row['imageUrl'];
            $product_title = $row['product'];
            $product_description = $row['description'];
            $product_price = $row['price'];
            $product_id = $row['productID'];
            switch($product_title) {
              case '1':
                $product_title = 'T-Shirts & Vests';
                break;
              case '2':
                $product_title = 'Shirts';
                break;
              case '3':
                $product_title = 'Jackets & Coats';
                break;
              case '4':
                $product_title = 'Hoodies & Sweatshirts';
                break;
              case '5':
                $product_title = 'Trousers';
                break;
              case '6':
                $product_title = 'Dress';
                break;
              case '7':
                $product_title = 'Skirt';
                break;
                case '7':
                  $product_title = 'Shoes';
                  break;
              default:
                $product_title = 'unknown';
            }
            $product_descriptionR = ucfirst($product_description);
            (strlen($product_description) > 24) ? $product_description = substr($product_descriptionR, 0, 24).'...' : $product_description = $product_descriptionR;
      
            echo '<div>
                    <a href="https://www.caleb.wtf/reboot/new/product/'.$product_id.'" class="link">
                      <div class="card">
                        <div class="card-header">
                          <img src="'.$image.'" class="card-img">
                        </div>
                        <span class="new">
                          New
                        </span>
                        <span class="card-price">
                          £'.$product_price.'
                        </span>
                        <a href="https://www.caleb.wtf/reboot/new/product/'.$product_id.'" class="grid link button">View</a>
                        <div class="card-body">
                          <div class="card-title">
                            '.$product_title.'
                          </div>
                          <div class="card-text">
                            '.$product_description.'
                          </div>
                        </div>
                      </div>
                    </a>
                  </div>';
          }
        }
      }

    public function getProduct($pageId) {
        $sql = "SELECT u.productID, u.sellerID, u.productType, u.product, u.description, u.price, u.size, u.brand, u.status, user_info.profilePic, user_login.userName, product_images.imageUrl
                FROM products u
                INNER JOIN user_info ON u.sellerID = user_info.userID
                INNER JOIN user_login ON u.sellerID = user_login.userID
                INNER JOIN product_images ON u.productID = product_images.postID
                WHERE u.productID = :id";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute(array(':id' => $pageId))or die(print_r($stmt->errorInfo(), true));;
        $row = $stmt->fetch();

        $this->pageid = $row['postID'];
        $this->userName = $row['userName'];
        $this->profilePic = $row['profilePic'];
        $this->description = $row['description'];
        $this->image = $row['imageUrl'];
        $this->price = $row['price'];
        $this->size = $row['size'];
        $this->brand = $row['brand'];
        $this->status = $row['status'];
    }

    public function getUserID(){
        return $this->pageid;
    }
    public function getUserName(){
        return $this->userName;
    }
    public function getProfilePic(){
        return $this->profilePic;
    }
    public function getDescription(){
      return $this->description;
    }
    public function getImage(){
        return $this->image;
    }
    public function getSize(){
        return $this->size;
    }
    public function getBrand(){
        return $this->brand;
    }
    public function getPrice(){
        return $this->price;
    }
    public function getStatus(){
        return $this->status;
    }
}

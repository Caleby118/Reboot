<?php
session_start();
include_once "Class.Database.php";
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
class Settings extends Database {
    public function newUpdateProfilePic($id) {
        $uploadOk = 1;
        require '../.././vendor/autoload.php';

    
        // AWS Info
        $bucketName = 'reboot-media';
        $IAM_KEY = 'AKIAUGLI7NB6NDRZ2GH3';
        $IAM_SECRET = '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';
    
        // Connect to AWS
        try {
            // You may need to change the region. It will say in the URL when the bucket is open
            // and on creation.
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
            // We use a die, so if this fails. It stops here. Typically this is a REST call so this would
            // return a json object.
            die("Error: " . $e->getMessage());
        }

        $allowedTypes = array('jpg', 'jpeg', 'png');
        $fileExtension = strtolower(pathinfo($_FILES["fileToUpload"]["name"],PATHINFO_EXTENSION));
        
        // Check if the file is a valid image type
        if (!in_array($fileExtension, $allowedTypes)) {
            echo "Error: Invalid file type. Please upload a JPG or PNG file.";
            $uploadOk = 0;
            exit;
        }
    
        // Set the maximum size of the compressed image
        $maxWidth = 500;
        $maxHeight = 500;
    
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
            $sql = "UPDATE user_info SET profilePic = :profilePic, updated = now() WHERE userID = :id";
            $stmt = $this->connect()->prepare($sql);
            $values = array(
                            ':profilePic' => "https://reboot-media.s3.eu-west-2.amazonaws.com/profile-pics/".$newName.'.'.$fileExtension,
                            ':id' => $id
                            );
            $res = $stmt->execute($values)or die(print_r($stmt->errorInfo(), true));;
            if($res) {
                
                try {
                    // Uploaded:
                    $file = $_FILES["fileToUpload"]['tmp_name'];
            
                    $s3->putObject(
                        array(
                            'Bucket'=>$bucketName,
                            'Key' =>  'profile-pics/'.$compressedFilename,
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
                    echo "Profile picture updated.";
                        
                    // Delete the compressed image file from the server
                    unlink($compressedFilename);
                } else {
                    echo "Error uploading your file.";
                }
            } else {
                echo "Error uploading file into database.";
            }
        }
        
    }
    function changePassword($id, $password, $newPassword, $confirmNewPassword) {
        $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        if(empty($password)) { http_response_code(400); echo "Please enter your old password."; exit;};
        if(empty($newPassword)) { http_response_code(400); echo "Please enter your new password."; exit;};
        if(empty($confirmNewPassword)) { http_response_code(400); echo "Please confirm your new password."; exit;};
        if($newPassword != $confirmNewPassword) {
            http_response_code(400); 
            echo "Passwords do not match";
            exit;
        } else {
            $passHash = password_hash($newPassword, PASSWORD_DEFAULT);
        }
        if(empty($errors)) {
            $sql = "SELECT *
                    FROM user_login 
                    WHERE userID = :id";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute(array(':id' => $id))or die(print_r($stmt->errorInfo(), true));;
            $count = $stmt->rowCount();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            if($count == 1) {
                if(password_verify($password, $res['password'])) {
                    $sql = "UPDATE user_login
                            SET `password` = :passHash
                            WHERE userID = :id";
                    $stmt = $this->connect()->prepare($sql);
                    $values = array(':passHash' => $passHash,
                                    ':id' => $id
                                    );
                    $res = $stmt->execute($values)or die(print_r($stmt->errorInfo(), true));;  
                    if($res) {
                        http_response_code(200); 
                        echo "Password updated";
                        exit;
                    } else {
                        http_response_code(400); echo "Error updating password.";
                        exit;
                    }
                } else {
                    http_response_code(400); echo "Your password is incorrect.";
                    exit;
                }
            } else {
                http_response_code(400); echo "Failed to find user.";
                exit;
            }
        }
        
    }
    function updateEmail($id, $email, $newEmail, $password) {
        $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        if(empty($email)){ http_response_code(400); echo "Please enter your current email address."; exit;}
        if(empty($newEmail)){ http_response_code(400); echo "Please enter your new email address."; exit;}
        if(empty($password)){ http_response_code(400); echo "Please enter your password."; exit;}
        
        if(empty($errors)) {
            $sql = "SELECT *
                    FROM user_login
                    WHERE email = :email";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute(array(':email' => $email));
            $count = $stmt->rowCount();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($count == 1){
                if(password_verify($password, $res['password'])){
                    $sql = "SELECT *
                            FROM user_login
                            WHERE email = :newEmail";
                    $stmt = $this->connect()->prepare($sql);
                    $stmt->execute(array(':newEmail' => $newEmail));
                    $count = $stmt->rowCount();
                    $res = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($count == 0){
                        $sql = "UPDATE user_login
                                SET email = :newEmail
                                WHERE userID = :id";
                        $stmt = $this->connect()->prepare($sql);
                        $res = $stmt->execute(array(':newEmail' => $newEmail,
                        ':id' => $id
                        ))or die(print_r($stmt->errorInfo(), true));;
                        if($res) {
                            http_response_code(200);
                            echo "Email updated";
                            exit;
                        }

                    } else {
                        http_response_code(400);
                        echo "This email address is already in use.";
                        exit;
                    }       
                }else{
                    http_response_code(400);
                    echo "The password you entered is incorrect.";
                    exit;
                }
            } else {
                http_response_code(400);
                echo "Your email address is incorrect.";
                exit;
            }
        }
    }
    function updateChatSettings($id, $optionValue) {
        $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        if(!isset($optionValue)) {
            http_response_code(400); 
            var_dump($optionValue); 
            echo "Please select an option."; 
            $error[]=""; 
            exit;
        }
        $options = array(0, 1, 2, 3);
        if (!in_array($optionValue, $options)) {
            http_response_code(400);
            echo "An error has occurred.";
            $error[] = "";
            exit;
        }
        if(empty($errors)) {
            $sql = "UPDATE user_info
                    SET chat = :optionValue
                    WHERE userID = :id";
            $stmt = $this->connect()->prepare($sql);
            $values = array(':optionValue' => $optionValue,
                            ':id' => $id
                            );
            $res = $stmt->execute($values)or die(print_r($stmt->errorInfo(), true));;  
            if($res) {
                http_response_code(200); 
                echo "Chat setting updated.";
                exit;
            } else {
                http_response_code(400); echo "Error changing chat setting.";
                exit;
            }
        }
    }
    
}
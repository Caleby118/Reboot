<?php
include_once('connect.php');

class User extends Database {
    public function signUp() {
        if(isset($_POST['reg']) & !empty($_POST['reg'])) {
            if(empty($_POST['userName']) || strlen($_POST['userName']) < 5) {
                $errors[]="Your username must be atleast 5 characters."; 
            }
            else {
                $sql = "SELECT * 
                        FROM user_login
                        WHERE userName=?";
                $stmt =$this->connect()->prepare($sql);
                $stmt->execute(array($_POST['userName']));
                $count = $stmt->rowCount();
                if($count == 1) {
                    $errors[]="This username is already taken!";
                }
            }
            if(empty($_POST['email'])){ $errors[]="Please enter a valid email address";}
            else {
                $sql = "SELECT * user_login
                        WHERE email=?";
                $stmt =$this->connect()->prepare($sql);
                $stmt->execute(array($_POST['email']));
                $count = $stmt->rowCount();
                if($count == 1) {
                    $errors[]="This email is already registered!";
                }
            }
            if(empty($_POST['password']) || strlen($_POST['password']) < 6) {
                $errors[]="Your password must contain atleast 6 characters."; }
            else {
                if(empty($_POST['confirmPassword'])) {
                    $errors[]="Please confirm your password."; 
                } else {
                    if($_POST['password'] == $_POST['confirmPassword']) {
                        $passHash = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    } else {
                        $errors[]="Passwords do not match.";
                    }
                }
            }
              
            if(empty($errors)) {
                $sql = "INSERT INTO users_login (userName, email, `password`) VALUES (:userName, :email, :passHash)";
                $stmt =$this->connect()->prepare($sql);
                $values = array(':userName'     => $_POST['userName'],
                                ':email'        => $_POST['email'],
                                ':passHash'     => $passHash
                                );
                $res = $stmt->execute($values)or die(print_r($stmt->errorInfo(), true));;
                
                if($res) {
                    $messages[]="Registration was successful you will be redirected in 5 seconds.";
                    echo("<script>
                    setTimeout(function(){
                        window.location.href = 'index.php';
                     }, 5000);
                    </script>");
                }else{
                    $errors[]="Something went wrong!";
                    
                }
                
            }
        }
    }
    
}
echo 'Hello';
?>
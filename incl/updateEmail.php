<?php   
session_start();
if(isset($_SESSION['id']) & !empty($_SESSION['id'])){
    include_once('connect.php');
    include_once('classes/Class.Settings.php');
    $id = $_SESSION['id'];
    $email = $_POST['email'];
    $newEmail = $_POST['newEmail'];
    $password = $_POST['password'];
    $user = new Settings();
    echo $user->updateEmail($id, $email, $newEmail, $password);
}
?>
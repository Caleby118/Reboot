<?php   
session_start();
if(isset($_SESSION['id']) & !empty($_SESSION['id'])){
    include_once('connect.php');
    include_once('classes/Class.Settings.php');
    $id = $_SESSION['id'];
    $password = $_POST['password'];
    $newPassword = $_POST['newPassword'];
    $confirmNewPassword = $_POST['confirmNewPassword'];
    $user = new Settings();
    echo $user->changePassword($id, $password, $newPassword, $confirmNewPassword);
}
?>
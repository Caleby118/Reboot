<?php   
session_start();
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
if(isset($_SESSION['id']) & !empty($_SESSION['id'])){
    include_once('connect.php');
    include_once('classes/Class.Settings.php');
    $id = $_SESSION['id'];
    $optionValue = $_POST["option"];
    $user = new Settings();
    echo $user->updateChatSettings($id, $optionValue);
}
?>
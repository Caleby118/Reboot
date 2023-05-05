<?php
session_start();
include_once('connect.php');
include_once('classes/Class.Settings.php');
$id = $_SESSION['id'];
$uploadPic = new Settings();
$uploadPic->newUpdateProfilePic($id, $_FILES["fileToUpload"]);
?>
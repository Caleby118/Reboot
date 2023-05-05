<?php
session_start();
include_once("connect.php");
include_once ("classes/Class.Profile.php"); 
$id = $_SESSION['id'];
$pageId = $_POST['following_id'];
 
        $chat = new Profile();
        $chat->follow($id, $pageId);  
  
?>
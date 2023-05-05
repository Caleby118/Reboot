<?php
session_start();
ini_set('max_execution_time', 30);
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

if(isset($_SESSION) && isset($_SESSION['login'])) { 
include_once("connect.php");
include_once ("classes/Class.Chat.php"); 
    $id = $_SESSION['id'];
    $receiverID = $_POST['getid'];
    $limit = $_POST['limit'];
    $start = $_POST['start'];
    if($limit == NULL) {
        $limit = 5;
    }
    if($start == NULL) {
        $start = 0;
    }
 
        $chat = new SendChat();
        $chat->getChat($id, $receiverID, $limit, $start);  
} else {
    echo ' <script> location.replace("https://cy118.brighton.domains/projects/reboot/login.php"); </script>';
}   
?>
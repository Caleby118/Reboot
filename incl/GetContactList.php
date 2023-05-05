<?php 
    session_start();
    ini_set('max_execution_time', 30);
    if(isset($_SESSION) && isset($_SESSION['login'])) { 
    $id = $_SESSION['id'];
    include_once("classes/Class.Chat.php");
    $chatInfo = new SendChat();
    $chatInfo->contactList($id); 
} else {
    echo ' <script> location.replace("https://cy118.brighton.domains/projects/reboot/login.php"); </script>';
} 
?>
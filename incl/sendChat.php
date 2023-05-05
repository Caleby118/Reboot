<?php
session_start();
include_once("connect.php");
include_once("classes/Class.Chat.php");
if(isset($_SESSION) && isset($_SESSION['login'])) {
    if(isset($_SESSION['id']) & !empty($_SESSION['id'])){
        $id = $_SESSION['id'];
        $error = [];
        $receiverID = $_POST['getid'];
        $message = $_POST['chatInput'];
        if(empty($message)) {
            $error[] = "Message is empty";
        }
        if(empty($error)) {
            http_response_code(201);
            $chat = new SendChat();
            echo $chat->sendMessage($id, $receiverID, $message);        
        } else {
            http_response_code(400);
            echo json_encode(['Error' => $error]);
            exit;
        }        
        
    }
}
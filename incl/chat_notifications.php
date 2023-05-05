<?php
session_start();
header("Content-Type: text/event-stream");
header("Cache-Control: no-cache");
header("Access-Control-Allow-Origin: *");

include_once('connect.php');

function sendNotifications() {
    $user = new User();
    $id = $_SESSION['id']; // change this to the ID of the logged in user

    $notifications = $user->getUnseenChatNotifications($id);
    var_dump($notifications);

    if (!empty($notifications)) {
        echo "data: " . json_encode($notifications) . "\n\n";
        ob_flush();
        flush();
    }
}

while (true) {
    sendNotifications();
    sleep(10);
}
?>
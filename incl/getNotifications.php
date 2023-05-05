<?php
session_start();
$id = $_SESSION['id'];
include_once('connect.php');
$notifList = new User();
$list = $notifList->getUnseenChatNotifications($id);

if (!empty($list)) {
    foreach ($list as $notification) {
        $message = $notification['message'];
        $totalCount = $notification['count'];
        if ($totalCount > 1) {
            $message = $message;
        }
        echo $message;
    }
} else {
    echo "<p>No new chat notifications</p>";
}
?>
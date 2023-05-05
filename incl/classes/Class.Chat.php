<?php
session_start();
include_once "Class.Database.php";
function whens($datetime) {   

    define("SECOND", 1);
    define("MINUTE", 60 * SECOND);
    define("HOUR", 60 * MINUTE); define("DAY", 24 * HOUR);
    define("MONTH", 30 * DAY); $delta = time() - strtotime($datetime);

    // convert

    if($delta < 1 * MINUTE) { return $delta == 1 ? "Just now" : " Just now"; }
    if($delta < 2 * MINUTE) { return "a minute ago"; } if($delta < 45 * MINUTE) { return floor($delta / MINUTE)." minutes ago"; }
    if($delta < 90 * MINUTE) { return "an hour ago"; } if($delta < 24 * HOUR) { return floor($delta / HOUR)." hours ago"; }
    if($delta < 48 * HOUR) { return "yesterday"; } if($delta < 30 * DAY) { return floor($delta / DAY)." days ago"; }
    if($delta < 12 * MONTH) { $months = floor($delta / DAY / 30); return $months <= 1 ? "one month ago" : $months." months ago"; }
    else { $years = floor($delta / DAY / 365); return $years <= 1 ? "one year ago" : $years." years ago"; }

}
class SendChat extends Database {

    public function sendMessage($id, $receiverID, $message): Array {
        $messages = [];
        $sql = "INSERT INTO chat (senderID , receiverID, `message`)
                VALUES (:senderID, :receiverID, :message)";
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindValue(":senderID", $id, PDO::PARAM_INT);
        $stmt->bindValue(":receiverID", $receiverID, PDO::PARAM_INT);
        $stmt->bindValue(":message", $message, PDO::PARAM_STR);
        $res = $stmt->execute()or die(print_r($stmt->errorInfo(), true));;
        if($res) {
            echo json_encode(['Message' => 'Message sent',
                              'Code' => 201]);
            http_response_code(201);
            exit;
        } else {
            http_response_code(500);
            $message = "Failed to send message";
            echo json_encode(['Error' => $messages]);
            exit;
        }

    }

    public function getChat($id, $receiverID): array {
        $message = [];
        $sql = "SELECT *
                FROM chat
                INNER JOIN user_info ON user_info.userID = chat.senderID
                WHERE (senderID = :senderID OR senderID = :receiverID) AND (receiverID = :receiverID2 OR receiverID = :senderID2)
                ORDER BY chat.messageID DESC
                LIMIT 50";
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindValue(":senderID", $id, PDO::PARAM_STR);
        $stmt->bindValue(":senderID2", $id, PDO::PARAM_STR);
        $stmt->bindValue(":receiverID2", $receiverID, PDO::PARAM_STR);
        $stmt->bindValue(":receiverID", $receiverID, PDO::PARAM_STR);
        $res = $stmt->execute()or die(print_r($stmt->errorInfo(), true));;
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $count = $stmt->rowCount();
        if($count > 0) {
            foreach($row as $msg) {
                $datetime = $msg['sentAt'];
                if($msg['userID'] == $_SESSION['id']) {
                    $bubble = "<div class='flex chat'style='flex-direction: row-reverse;'>
                                <p class='chat sender'>".htmlspecialchars($msg['message'])."</p>
                                <span class='chat-date'>".whens($datetime)."</span>
                               </div>";
                } else {
                    $bubble = " <div class='flex chat'>
                                    <span class='chat-date'>".whens($datetime)."</span>
                                    <p class='chat receiver'>".htmlspecialchars($msg['message'])."</p>
                                </div>";
                }
                echo $bubble;
            }
        }
        if($count == 0) {
            echo '<div class="grid no-chat"><div>Send a message to start a chat!</div>';
            exit;
        }
        if($res) {
            $sql = "SELECT seen
                    FROM chat
                    WHERE senderID = :receiverID AND receiverID = :senderID AND seen = 0";
            $stmt = $this->connect()->prepare($sql);
            $stmt->bindValue(':senderID', $id, PDO::PARAM_INT);
            $stmt->bindValue(':receiverID', $receiverID, PDO::PARAM_INT);
            $stmt->execute();
            $countNotif = $stmt->rowCount();
            $this->countNotif = $countNotif;
            if($countNotif >= 1) {
                $sql = "UPDATE chat 
                        SET seen = 1
                        WHERE senderID = :receiverID AND receiverID = :senderID AND seen = 0";
                $stmt = $this->connect()->prepare($sql);
                $stmt->bindValue(':senderID', $id, PDO::PARAM_INT);
                $stmt->bindValue(':receiverID', $receiverID, PDO::PARAM_INT);
                $res = $stmt->execute()or die(print_r($stmt->errorInfo(), true));;
                if($res) {
                    $message = "<script>
                    console.log('notf send');
                    </script>";  
                } else {
                    $message = "<script>
                    console.log('fail');
                    </script>";  
                }
                echo $message;
            }
            return $row;
        }
        $this->count = $count;
    }
    public function getNotif($id, $receiverID) {
        $sql = "SELECT seen
                FROM chat
                WHERE senderID = :senderID AND receiverID = :receiverID AND seen = 0";
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindValue(':senderID', $receiverID, PDO::PARAM_STR);
        $stmt->bindValue(':receiverID', $id, PDO::PARAM_STR);
        $stmt->execute();
        $countNotif = $stmt->rowCount();
        if($countNotif > 0) {
            return $countNotif;
        } else {
            return $countNotif;
        }
    }
    public function contactList($id) {
        $contacts_query = "
            SELECT u.`userID`, u.`userName`, u.`email`, c.`message`, c.`sentAt`, c.`seen`, i.`profilePic`
            FROM (
                SELECT `user_id`, MAX(`messageID`) AS `latest_message_id`
                FROM (
                    SELECT `senderID` AS `user_id`, `messageID` FROM `chat` WHERE `receiverID` = :user_id
                    UNION
                    SELECT `receiverID` AS `user_id`, `messageID` FROM `chat` WHERE `senderID` = :user_id2
                ) AS `temp`
                GROUP BY `user_id`
            ) AS `m`
            INNER JOIN `user_login` AS `u` ON `m`.`user_id` = `u`.`userID`
            INNER JOIN `user_info` AS `i` ON `m`.`user_id` = `i`.`userID`
            INNER JOIN `chat` AS `c` ON `m`.`latest_message_id` = `c`.`messageID`
            ORDER BY `c`.`messageID` DESC
        ";
    
        $user_id = $id; // Replace with the ID of the logged-in user
        $user_id2 = $id; // Replace with the ID of the logged-in user
        $contacts_stmt = $this->connect()->prepare($contacts_query);
        $contacts_stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
        $contacts_stmt->bindParam(':user_id2', $user_id2, PDO::PARAM_STR);
        $contacts_stmt->execute();
    
        $contacts = array();
        while ($row = $contacts_stmt->fetch(PDO::FETCH_ASSOC)) {
            $contacts[] = $row;
        }
    
        // Display the contact list
        foreach ($contacts as $contact) {
            $message = $contact['message'];
            $sentAt = $contact['sentAt'];
            $receiverID = $contact['userID'];
            $userName = $contact['userName'];
            $userNameR = ucfirst($userName);
            (strlen($userNameR) > 14) ? $userName = substr($userNameR, 0, 14).'...' : $userName = $userNameR;
            echo ' <div class="flex contact" data-chat-id="'.$contact['userID'].'"> 
            <a style="text-decoration: none;" data-chat-id="'.$contact['userID'].'" href="https://caleb.wtf/reboot/new/chat/'.$contact['userID'].'"><div><img src="'.$contact['profilePic'].'" class="pp pp-md circle outline"></div></a>
                   <div class="flex column contact-text">
                       <span class="name">'.htmlspecialchars($userName).'</span>';
                               $result = ucfirst($message);
                               
       
                           (strlen($result) > 28) ? $message = substr($result, 0, 28).'...' : $message = $result;
                           $datetime = $contact['sentAt'];
                           echo '<span class="message">'.htmlspecialchars($message).'</span>
                                 <span class="date">'.whens($datetime).'</span>';
                           if($this->getNotif($id, $receiverID) > 0) {
                                echo '</div> <span class="grid notification">';
                                echo $this->getNotif($id, $receiverID);                  
                                echo '</span> </div>';
                           } else {
                                echo '</div> <span class="grid notification-none">';                
                                echo '</span> </div>';
                           }
        }

    }

    public function getChatUser($receiverId) {
        $sql = "SELECT u.userID, u.userName, user_info.profilePic, u.created
                FROM user_login u
                INNER JOIN user_info ON u.userID = user_info.userID
                WHERE u.userID = :id";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute(array(':id' => $receiverId))or die(print_r($stmt->errorInfo(), true));;
        $row = $stmt->fetch();

        $this->id = $row['userID'];
        $this->userName = $row['userName'];
        $this->profilePic = $row['profilePic'];
        $this->created = $row['created'];
    }

    public function getUserID(){
        return $this->id;
    }
    public function getUserName(){
        return $this->userName;
    }
    public function getProfilePic(){
        return $this->profilePic;
    }
    public function getCreated(){
        return $this->created;
    }

}

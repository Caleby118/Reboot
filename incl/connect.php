<?php
include_once "classes/Class.Database.php";
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
function when($datetime) {   

    define("SECOND", 1);
    define("MINUTE", 60 * SECOND);
    define("HOUR", 60 * MINUTE); define("DAY", 24 * HOUR);
    define("MONTH", 30 * DAY); $delta = time() - strtotime($datetime);

    // convert

    if($delta < 1 * MINUTE) { return $delta == 1 ? "now" : " now"; }
    if($delta < 2 * MINUTE) { return "a minute ago"; } if($delta < 45 * MINUTE) { return floor($delta / MINUTE)."m"; }
    if($delta < 90 * MINUTE) { return "an hour ago"; } if($delta < 24 * HOUR) { return floor($delta / HOUR)."h"; }
    if($delta < 48 * HOUR) { return "yesterday"; } if($delta < 30 * DAY) { return floor($delta / DAY)."d"; }
    if($delta < 12 * MONTH) { $months = floor($delta / DAY / 30); return $months <= 1 ? "one month ago" : $months." months ago"; }
    else { $years = floor($delta / DAY / 365); return $years <= 1 ? "one year ago" : $years." years ago"; }

}
class User extends Database {
    function signUp($userName, $email, $password, $confirmPassword, $ip, $firstName, $lastName) {
        $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        global $errors;
        global $messages;
        $UUID = random_bytes(10);
        $newID = bin2hex($UUID);
        if(empty($newID)) { $errors[]= "Issue with UID generation, please try again.";
        } else {
            $sql = "SELECT * 
            FROM user_login
            WHERE userID=?";
            $stmt =$this->connect()->prepare($sql);
            $stmt->execute(array($newID));
            $count = $stmt->rowCount();
            if($count == 1) {
                $errors[]="UID error, please try again.";
            }
        }
        if(empty($firstName)) {
            $errors[]="Please enter your first name."; 
        }
        if(empty($lastName)) {
            $errors[]="Please enter your last name."; 
        }
        if(empty($userName) || strlen($userName) < 4) {
            $errors[]="Your username must be atleast 4 characters."; 
        }
        else {
            $sql = "SELECT * 
                    FROM user_login
                    WHERE userName=?";
            $stmt =$this->connect()->prepare($sql);
            $stmt->execute(array($userName));
            $count = $stmt->rowCount();
            if($count == 1) {
                $errors[]="This username is already taken!";
            }
        }
        if(empty($email)){ $errors[]="Please enter a valid email address";}
        else {
            $sql = "SELECT * 
                    FROM user_login
                    WHERE email=?";
            $stmt =$this->connect()->prepare($sql);
            $stmt->execute(array($email));
            $count = $stmt->rowCount();
            if($count == 1) {
                $errors[]="This email is already registered!";
            }
        }
        if(empty($password) || strlen($password) < 6) {
            $errors[]="Your password must contain atleast 6 characters."; }
        else {
            if(empty($confirmPassword)) {
                $errors[]="Please confirm your password."; 
            } else {
                if($password == $confirmPassword) {
                    $passHash = password_hash($password, PASSWORD_DEFAULT);
                } else {
                    $errors[]="Passwords do not match.";
                }
            }
        }
            
        if(empty($errors)) {
            $sql = "INSERT INTO user_login (userID, userName, email, `password`, ip) VALUES (:userID, :userName, :email, :passHash, :ip)";
            $stmt = $this->connect()->prepare($sql);
            $values = array(':userID'       => $newID,
                            ':userName'     => $userName,
                            ':email'        => $email,
                            ':passHash'     => $passHash,
                            ':ip'           => $ip
                            );
            $res = $stmt->execute($values)or die(print_r($stmt->errorInfo(), true));;
            
            if($res) {
                $sql = "SELECT userID
                        FROM user_login
                        WHERE email = :email";
                $stmt = $this->connect()->prepare($sql);
                $stmt->execute(array(':email' => $email))or die(print_r($stmt->errorInfo(), true));;
                $row = $stmt->fetch();
                $id = $row['userID'];

                $sql2 = "INSERT INTO user_info (userID, firstName, lastName) VALUES (:id, :firstName, :lastName)";
                $stmt2 = $this->connect()->prepare($sql2);
                $values = array(':id'        => $id,
                                ':firstName' => $firstName,
                                ':lastName'  => $lastName
                                );
                $res2 = $stmt2->execute($values)or die(print_r($stmt->errorInfo(), true));;
                if($res2) {
                    $messages[]="Registration was successful you will be redirected in 5 seconds.";
                    $user = $email;
                    $password = $password;
                    $post = new User();
                    echo $post->signIn($user, $password);
                } else {
                    $errors[]="Issue with user info input";
                }
            }else{
                $errors[]="Something went wrong!";
                
            }
            
        }   
    }
    
    function signIn($user, $password) {
        global $errors;
        global $messages;
        $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        if(empty($user)){ $errors[]= "Please enter a username/email.";}
        if(empty($password)){ $errors[]= "Please enter your password.";}
        if(empty($errors)){
            //checks login info
                $sql = "SELECT * FROM user_login WHERE ";
                if(filter_var($user, FILTER_VALIDATE_EMAIL)){
                    $sql .="email=?";
                }else{
                    $sql .="userName=?";
                }
                $stmt = $this->connect()->prepare($sql);
                $stmt->execute(array($user));
                $count = $stmt->rowCount();
                $res = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($count == 1){
                    if(password_verify($password, $res['password'])){
                        // regenerate session id
                        session_regenerate_id();
                        $_SESSION['login'] = true;
                        $_SESSION['id'] = $res['userID'];
                        $_SESSION['last_login'] = time();
                        
                        //redirect to members page
                        $messages[]= "Login successful, redirecting in 5 seconds";
                        echo ' <script> location.replace("https://caleb.wtf/reboot/new/profile/'.$res['userID'].'"); </script>';
                    
                    }else{
                        $errors[]= "Incorrect email and/or password.";
                    }
                }else{
                    $errors[]= "This user does not exist.";
                }
                            
        }
    }
    function changePassword($id, $password, $newPassword, $confirmNewPassword) {
        $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        global $errors;
        global $messages;

        if(empty($password)) { $errors[]= "Please enter your old password.";};
        if(empty($newPassword)) { $errors[]= "Please enter your new password.";};
        if(empty($confirmNewPassword)) { $errors[]= "Please confirm your new password.";};
        if($newPassword != $confirmNewPassword) {
            $errors[]= "Passwords do not match";
        } else {
            $passHash = password_hash($newPassword, PASSWORD_DEFAULT);
        }
        if(empty($errors)) {
            $sql = "SELECT *
                    FROM user_login 
                    WHERE userID = :id";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute(array(':id' => $id))or die(print_r($stmt->errorInfo(), true));;
            $count = $stmt->rowCount();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            if($count == 1) {
                if(password_verify($password, $res['password'])) {
                    $sql = "UPDATE user_login
                            SET `password` = :passHash
                            WHERE userID = :id";
                    $stmt = $this->connect()->prepare($sql);
                    $values = array(':passHash' => $passHash,
                                    ':id' => $id
                                    );
                    $res = $stmt->execute($values)or die(print_r($stmt->errorInfo(), true));;  
                    if($res) {
                        $alertType = 1;
                        $alertText = "Your password has been changed.";
                        echo("<script>
                        setTimeout(function(){
                            window.location.href = 'settings';
                            }, 2000);
                        </script>");
                        return alert($alertType, $alertText);
                    } else {
                        $errors[]= "Error updating password.";
                    }
                } else {
                    $errors[]= "Your password is incorrect.";
                }
            } else {
                $errors[]= "Failed to find user.";
            }
        }
        
    }
    function updateEmail($id, $email, $newEmail, $password) {
        $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        global $errors;
        global $messages;
        $errorStyle = "background-color: #f2dede; border-color: red; color: #a94442; border: 1px solid #ebacac;";
        global $passwordError;
        global $passwordErrorMsg;
        global $emailError;
        global $emailErrorMsg;
        global $newEmailError;
        global $newEmailErrorMsg;
        if(empty($email)){ $emailErrorMsg = "Please enter your current email address."; $emailError = $errorStyle; $errors[]= " ";}
        if(empty($newEmail)){ $newEmailErrorMsg = "Please enter your new email address."; $newEmailError = $errorStyle; $errors[]= " ";}
        if(empty($password)){ $passwordErrorMsg = "Please enter your password."; $passwordError = $errorStyle; $errors[]= " ";}
        
        if(empty($errors)) {
            $sql = "SELECT *
                    FROM user_login
                    WHERE email = :email";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute(array(':email' => $email));
            $count = $stmt->rowCount();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($count == 1){
                if(password_verify($password, $res['password'])){
                    $sql = "SELECT *
                            FROM user_login
                            WHERE email = :newEmail";
                    $stmt = $this->connect()->prepare($sql);
                    $stmt->execute(array(':newEmail' => $newEmail));
                    $count = $stmt->rowCount();
                    $res = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($count == 0){
                        $sql = "UPDATE user_login
                                SET email = :newEmail
                                WHERE userID = :id";
                        $stmt = $this->connect()->prepare($sql);
                        $res = $stmt->execute(array(':newEmail' => $newEmail,
                        ':id' => $id
                        ))or die(print_r($stmt->errorInfo(), true));;
                        if($res) {
                                                //redirect to members page
                            $alertType = 1;
                            $alertText = "Your email address has been updated!";
                            echo("<script>
                            setTimeout(function(){
                                window.location.href = 'settings';
                                }, 2000);
                            </script>");
                            return alert($alertType, $alertText);
                            echo ' <script> location.replace("profile.php?email"); </script>';
                        }

                    } else {
                        $newEmailErrorMsg = "This email address is already in use."; $newEmailError = $errorStyle;
                        $errors[]= " ";
                    }       
                }else{
                    $passwordErrorMsg = "The password you entered is incorrect."; $passwordError = $errorStyle;
                    $errors[]= " ";
                }
            } else {
                $emailErrorMsg = "Your email address is incorrect."; $emailError = $errorStyle;
                $errors[]= " ";
            }
        }
    }
    public function getUser($id) {
        $sql = "SELECT *
                FROM user_login
                INNER JOIN user_info ON user_login.userID = user_info.userID
                WHERE user_login.userID = :id";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute(array(':id' => $id))or die(print_r($stmt->errorInfo(), true));;
        $row = $stmt->fetch();

        $this->id = $row['userID'];
        $this->userName = $row['userName'];
        $this->firstName = $row['firstName'];
        $this->lastName = $row['lastName'];
        $this->email = $row['email'];
        $this->profilePic = $row['profilePic'];
        $this->created = $row['created'];
        $this->chat = $row['chat'];
        $this->funds = $row['funds'];
    }

    public function getUserID(){
        return $this->id;
    }
    public function getUserName(){
        return $this->userName;
    }
    public function getEmail(){
        return $this->email;
    }
    public function getFirstName(){
        return $this->firstName;
    }
    public function getLastName(){
        return $this->lastName;
    }
    public function getProfilePic(){
        return $this->profilePic;
    }
    public function getCreated(){
        return $this->created;
    }
    public function getChat(){
        return $this->chat;
    }
    public function getFunds(){
        return $this->funds;
    }
    public function totalUsers() {
        $sql = "SELECT *
                FROM user_info";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute()or die(print_r($stmt->errorInfo(), true));;
        $count = $stmt->rowCount();
        return $count;
    }
    public function checkID($receiverId) {
        $sql = "SELECT userID
                FROM user_info
                WHERE userID = :receiverID";
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindValue(':receiverID', $receiverId, PDO::PARAM_INT);
        $res = $stmt->execute()or die(print_r($stmt->errorInfo(), true));;
        $count = $stmt->rowCount();
        if($res) {
            if($count == 0) {
                echo("<script>
                setTimeout(function(){
                    window.location.href = 'https://caleb.wtf/reboot/chat.php';
                    }, 0);
                </script>");
            }
        } else {
            exit;
        }
    }
    public function pageCheckID($pageId) {
        $sql = "SELECT userID
                FROM user_info
                WHERE userID = :pageID";
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindValue(':pageID', $pageId, PDO::PARAM_INT);
        $res = $stmt->execute()or die(print_r($stmt->errorInfo(), true));;
        $count = $stmt->rowCount();
        if($res) {
            if($count == 0) {
                echo("<script>
                setTimeout(function(){
                    window.location.href = 'https://caleb.wtf/reboot/new/profile/';
                    }, 0);
                </script>");
            }
        } else {
            exit;
        }
    }

    public function getUnseenChatNotifications($id) {
        $sql = "SELECT senderID as userID, userName, sentAt, COUNT(*) as count, 'chat' as type
        FROM chat
        JOIN user_login ON chat.senderID = user_login.userID
        WHERE receiverID = :id AND seen = 0
        GROUP BY senderID, sentAt
        
        UNION
        
        SELECT buyerID as userID, userName, sentAt, COUNT(*) as count, 'product' as type
        FROM sales
        JOIN user_login ON sales.buyerID = user_login.userID
        WHERE sellerID = :id2 AND seen = 0
        GROUP BY buyerID, sentAt
        
        ORDER BY sentAt DESC";
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':id2', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $notifications = [];
        $lastNotification = null;
        
        foreach ($result as $row) {
            $type = $row['type'];
            $count = $row['count'];
            $senderID = $row['userID'];
            $timestamp = strtotime($row['sentAt']);
            $username = $row['userName'];
            $datetime = $row['sentAt'];
        
            $message = null;
            $isNewNotification = true;
        
            if ($type === 'chat') {
                $message = "<div class='flex' style='align-items: center; position: relative;'>
                <a href='https://caleb.wtf/reboot/new/chat/{$senderID}' class='flex link'><span class='grid icon'>
                                <span class='material-symbols-outlined'>
                                    chat_bubble
                                </span>
                                </span>Message from {$username}
                                <span class='notification-date'>".when($datetime)."</span></a></div>";
        
                if ($lastNotification !== null && $lastNotification['type'] === 'chat' && $lastNotification['senderID'] === $senderID) {
                    // The last notification was from the same sender
                    $lastNotification['count'] += $count;
                    $totalCount = $lastNotification['count'];
                    $lastNotification['message'] = "<div class='flex' style='align-items: center; position: relative;'>
                    <a href='https://caleb.wtf/reboot/new/chat/{$senderID}' class='flex link'><span class='grid icon'>
                                    <span class='material-symbols-outlined'>
                                        chat_bubble
                                    </span>
                                    </span>Message from {$username}<span class='grid notification-bubble'>{$totalCount}</span>
                                    <span class='notification-date'>".when($datetime)."</span></a></div>";
                    $isNewNotification = false;
                }
            } elseif ($type === 'product') {
                $message = "<div class='flex' style='align-items: center; position: relative;'>
                <a href='https://caleb.wtf/reboot/new/chat/{$senderID}' class='flex link'><span class='grid icon'>
                                <span class='material-symbols-outlined'>
                                    sell
                                </span>
                                </span>Sold to {$username}
                                <span class='notification-date'>".when($datetime)."</span></a></div>";
            }
        
            if ($isNewNotification) {
                $lastNotification = [
                    'message' => $message,
                    'count' => $count,
                    'senderID' => $senderID,
                    'type' => $type,
                    'timestamp' => $timestamp,
                ];
                $notifications[] = $lastNotification;
            } else {
                // update the last notification in the list
                $notifications[count($notifications)-1] = $lastNotification;
            }
        }
        
        
        return $notifications;
    }

}
class Chat extends Database {
    public function sendMessage($id, $receiverId, $message) {
        $sql = "INSERT INTO chat (senderID , receiverID, `message`)
                VALUES (:senderID, :receiverID, :message)";
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindValue(":senderID", $id, PDO::PARAM_INT);
        $stmt->bindValue(":receiverID", $receiverId, PDO::PARAM_INT);
        $stmt->bindValue(":message", $message, PDO::PARAM_STR);
        $res = $stmt->execute()or die(print_r($stmt->errorInfo(), true));;
        if($res) {
            http_response_code(201);
            $message = "<script>
            console.log('Message sent');
            </script>";  
        } else {
            $message = "<script>
            console.log('Message failed to send');
            </script>";  
        }
        return $message;
    }

    public function getChat($id, $receiverId): array {

        $sql = "SELECT *
                FROM chat
                INNER JOIN user_info ON user_info.userID = chat.senderID
                WHERE (senderID = :senderID OR senderID = :receiverID) AND (receiverID = :receiverID2 OR receiverID = :senderID2)
                ORDER BY chat.messageID ASC";
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindValue(":senderID", $id, PDO::PARAM_INT);
        $stmt->bindValue(":senderID2", $id, PDO::PARAM_INT);
        $stmt->bindValue(":receiverID2", $receiverId, PDO::PARAM_INT);
        $stmt->bindValue(":receiverID", $receiverId, PDO::PARAM_INT);
        $res = $stmt->execute()or die(print_r($stmt->errorInfo(), true));;
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $count = $stmt->rowCount();
        if($res) {
            $sql = "SELECT seen
                    FROM chat
                    WHERE senderID = :receiverID AND receiverID = :senderID AND seen = 0";
            $stmt = $this->connect()->prepare($sql);
            $stmt->bindValue(':senderID', $id, PDO::PARAM_INT);
            $stmt->bindValue(':receiverID', $receiverId, PDO::PARAM_INT);
            $stmt->execute();
            $countNotif = $stmt->rowCount();
            $this->countNotif = $countNotif;
            if($countNotif >= 1) {
                $sql = "UPDATE chat 
                        SET seen = 1
                        WHERE senderID = :receiverID AND receiverID = :senderID AND seen = 0";
                $stmt = $this->connect()->prepare($sql);
                $stmt->bindValue(':senderID', $id, PDO::PARAM_INT);
                $stmt->bindValue(':receiverID', $receiverId, PDO::PARAM_INT);
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
    public function contacts($id) {

        $sql = "SELECT DISTINCT receiverID, senderID, user_info.*, user_login.userName
                FROM chat
                INNER JOIN user_info ON user_info.userID = chat.senderID
                INNER JOIN user_login ON user_login.userID = chat.senderID
                WHERE receiverID = :senderID";
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindValue(":senderID", $id, PDO::PARAM_INT);
        $res = $stmt->execute()or die(print_r($stmt->errorInfo(), true));;
        $row = $stmt->fetchAll();
        if($res) {
            return $row;
        }

    }
    public function chatNotification($id, $chatID) {
        $sql = "SELECT seen
                FROM chat
                WHERE senderID = :receiverID AND receiverID = :senderID AND seen = 0";
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindValue(':senderID', $id, PDO::PARAM_INT);
        $stmt->bindValue(':receiverID', $chatID, PDO::PARAM_INT);
        $stmt->execute();
        $countNotif = $stmt->rowCount();

        return $countNotif;
    }
    public function getCount() {
        return $this->count;
    }
    public function getNotifCount() {
        return $this->countNotif;
    }

    public function totalNotifs($id) {
        $sql = "SELECT seen
                FROM chat
                WHERE receiverID = :id AND seen = 0";
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute()or die(print_r($stmt->errorInfo(), true));;
        $count = $stmt->rowCount();

        return $count;
    }

}
function checkLogin() {
    if(isset($_SESSION) && isset($_SESSION['login'])) { 
        echo ' <script> location.replace("https://caleb.wtf/reboot/new/login.php"); </script>';
    }
}
function logout() {
    session_start(); 
    session_unset(); 
    session_destroy(); 
    echo ' <script> location.replace("https://caleb.wtf/reboot/new/login.php"); </script>';
}
function alert($alertType, $alertText) {
    if($alertType == 0) {
        $alertClass = '<div class="alert danger">'.$alertText.'</div>';
    } elseif($alertType == 1) {
        $alertClass = '<div class="alert success">'.$alertText.'</div>';
    }
    return $alertClass;
}
?>
<?php 
session_start();
if(isset($_SESSION) && isset($_SESSION['login'])) {
include_once('incl/connect.php');
if(isset($_GET['id'])) {
    $receiverId = $_GET['id'];
    $idCheck = new User();
    $idCheck->checkID($receiverId);
}
}
if(isset($_SESSION['id']) & !empty($_SESSION['id'])){
    $id = $_SESSION['id'];
} else {
    echo ' <script> location.replace("https://caleb.wtf/reboot/new/login.php"); </script>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://caleb.wtf/reboot/new/assets/css/style.css">
    <link rel="stylesheet" href="https://caleb.wtf/reboot/new/assets/css/chat-main.css">
    <?php 
    if(!isset($_GET['id'])) {
        echo '<link rel="stylesheet" href="https://caleb.wtf/reboot/new/assets/css/chat.css">';
    } else {
        echo '<link rel="stylesheet" href="https://caleb.wtf/reboot/new/assets/css/chat2.css">';
    }
    ?>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script src="https://caleb.wtf/reboot/new/assets/js/script.js" defer></script>
    <?php if(isset($_GET['id'])) { ?>
    <style>
        @media only screen and (max-width: 710px) {
            header {
                display: none;
            }
        }
    </style>
    <?php } ?>
    <title>Document</title>
</head>
<body class="chat-body">
    <header>
        <?php include_once('incl/nav.php'); ?>
    </header>

    <div class="flex chat-container">

    <div class="flex column sidebar-res" id="sidebar">
            <div class="header">Explore</div>
            <ul class="flex column">
                <li class="flex"><a href="https://caleb.wtf/reboot/new/products.php" style="display: flex; align-items: center;"><span class="grid icon">🏠</span>Home</a></li>
                <li class="flex"><a href="https://caleb.wtf/reboot/new/products.php" style="display: flex; align-items: center;"><span class="grid icon">🔎</span>Search</a></li>
                <li class="flex active"><a href="https://caleb.wtf/reboot/new/chat.php" style="display: flex; align-items: center;"><span class="grid icon">💬</span>Chat</a></li>
            </ul>
            <?php if(isset($_SESSION['login']) && !empty($_SESSION['login'])) { ?>
            <div class="flex signout">
                <a href="https://caleb.wtf/reboot/new/logout.php" class="flex"><span class="grid icon">🚪</span>Sign out</a>
            </div>
            <?php } ?>
        </div>

        <div class="flex chat-wrapper" <?php if(!isset($_GET['id'])) { echo 'style="gap: 0;"'; }?>>
            <div class="flex column contacts" id="contacts">
                
            </div>
            <div class="flex column chatbox">
                <div class="flex header">
                <?php
                    if(isset($_GET['id'])) {
                        include_once("incl/classes/Class.Chat.php");
                        $chatInfo = new SendChat();
                        $chatInfo->getChatUser($receiverId);
                    ?>
                    <div><a href="../chat/" style="text-decoration: none;"><span class="material-symbols-outlined">
                        arrow_back_ios_new
                        </span></a></div>
                    <div><a style="text-decoration: none;" href="https://caleb.wtf/reboot/new/profile/<?php echo $chatInfo->getuserID(); ?>"><img src="<?php echo $chatInfo->getProfilePic(); ?>" class="pp pp-lg circle outline"></div>
                    <span class="name">
                        <?php
                            $userName = $chatInfo->getUserName();
                            $userNameR = ucfirst($userName);
                            (strlen($userNameR) > 14) ? $userName = substr($userNameR, 0, 14).'...' : $userName = $userNameR;
                            echo $userName; 
                        ?>
                     </span></a>
                </div>
                <div class="flex column chatroom"  id="chatbox">
                    
                    
            </div>

            <div class="chat-input-area">
                <form action="" method="post" name="getC" id="getC">
                    <input type="text" name="getC" id="getid" value="<?php echo $receiverId; ?>" hidden>
                </form>
                <form action="" method="post" id="sendMsgArea">
                <input type="text" name="id" id="id" value="<?php echo $id; ?>"hidden>
                    <input type="text" name="getid" id="getid" value="<?php echo $receiverId; ?>" hidden>
                <?php
                include_once('incl/classes/Class.Profile.php');
                $pageId = $_GET['id'];
                $pageUser = new Profile();
                $pageUser->getProfileUser($pageId);
                            if($pageUser->getChat() == 0) {
                                //allow messages from everyone
                                if($pageUser->checkFollowsYou($id, $pageId) == 1 || $pageUser->checkFollowsYou($id, $pageId) == 2 || $pageUser->checkFollowsYou($id, $pageId) == 3 || $pageUser->checkFollowsYou($id, $pageId) == 0) {
                                ?>
                                                                <script>
                                    form.onsubmit = (e) => {
                                        e.preventDefault();
                                    }

                                    sendBtn.onclick = () => {
                                        let xhr = new XMLHttpRequest();
                                        xhr.open('POST', "https://caleb.wtf/reboot/new/incl/sendChat.php", true);
                                        xhr.onload = () => {
                                            if(xhr.readyState === XMLHttpRequest.DONE) {
                                                if(xhr.status === 201) {
                                                    let data = xhr.response;
                                                    console.log(data.Code);
                                                    input.value = "";
                                                    getChat();
                                                    getContactList();
                                                }
                                            }
                                        }
                                        let formData = new FormData(form);
                                        xhr.send(formData);
                                    }
                                    </script>
                    <input type="text" name="chatInput" id="chatInput" class="input-text" placeholder="Message..." spellcheck="true" autocomplete="off">
                
                                <?php
                                }

                            } elseif($pageUser->getChat() == 1) {
                                //allow messages from followers
                                if($pageUser->checkFollowsYou($id, $pageId) == 1 || $pageUser->checkFollowsYou($id, $pageId) == 3) {
                                ?>
                                                                <script>
                                    form.onsubmit = (e) => {
                                        e.preventDefault();
                                    }

                                    sendBtn.onclick = () => {
                                        let xhr = new XMLHttpRequest();
                                        xhr.open('POST', "https://caleb.wtf/reboot/new/incl/sendChat.php", true);
                                        xhr.onload = () => {
                                            if(xhr.readyState === XMLHttpRequest.DONE) {
                                                if(xhr.status === 201) {
                                                    let data = xhr.response;
                                                    console.log(data.Code);
                                                    input.value = "";
                                                    getChat();
                                                    getContactList();
                                                }
                                            }
                                        }
                                        let formData = new FormData(form);
                                        xhr.send(formData);
                                    }
                                    </script>
                                                
                    <input type="text" name="chatInput" id="chatInput" class="input-text" placeholder="Message..." spellcheck="true" autocomplete="off">
                                <?php
                                } else {
                                echo "<span class='flex nochat-text'>". $userName . " only accepts chats from followers.</span>";
                                }
                            } elseif($pageUser->getChat() == 2) {
                                //allow messages only if they follow you
                                if($pageUser->checkFollowsYou($id, $pageId) == 2 || $pageUser->checkFollowsYou($id, $pageId) == 3) {
                                ?>
                                                                <script>
                                    form.onsubmit = (e) => {
                                        e.preventDefault();
                                    }

                                    sendBtn.onclick = () => {
                                        let xhr = new XMLHttpRequest();
                                        xhr.open('POST', "https://caleb.wtf/reboot/new/incl/sendChat.php", true);
                                        xhr.onload = () => {
                                            if(xhr.readyState === XMLHttpRequest.DONE) {
                                                if(xhr.status === 201) {
                                                    let data = xhr.response;
                                                    console.log(data.Code);
                                                    input.value = "";
                                                    getChat();
                                                    getContactList();
                                                }
                                            }
                                        }
                                        let formData = new FormData(form);
                                        xhr.send(formData);
                                    }
                                    </script>
                                              
                    <input type="text" name="chatInput" id="chatInput" class="input-text" placeholder="Message..." spellcheck="true" autocomplete="off">
                                <?php
                                } else {
                                    echo "<span class='flex nochat-text'>". $userName . " only accepts chats from people they follow.</span>";
                                }
                            } elseif($pageUser->getChat() == 3) {
                                //allow messages only if you both follow each other
                                if($pageUser->checkFollowsYou($id, $pageId) == 3) {
                                ?>
                                <script>
                                    form.onsubmit = (e) => {
                                        e.preventDefault();
                                    }

                                    sendBtn.onclick = () => {
                                        let xhr = new XMLHttpRequest();
                                        xhr.open('POST', "https://caleb.wtf/reboot/new/incl/sendChat.php", true);
                                        xhr.onload = () => {
                                            if(xhr.readyState === XMLHttpRequest.DONE) {
                                                if(xhr.status === 201) {
                                                    let data = xhr.response;
                                                    console.log(data.Code);
                                                    input.value = "";
                                                    getChat();
                                                    getContactList();
                                                }
                                            }
                                        }
                                        let formData = new FormData(form);
                                        xhr.send(formData);
                                    }
                                    </script>
                                               
                    <input type="text" name="chatInput" id="chatInput" class="input-text" placeholder="Message..." spellcheck="true" autocomplete="off">
                
                                <?php
                                } else {
                                    echo "<span class='flex nochat-text'>You must follow each other to chat.</span>";
                                }
                            }
                        ?>
                        <button type="submit" name="sendMsg" id="sendMsg">Send Message</button hidden>
                        </form>
            </div>

        </div>
        <script>
const form = document.getElementById("sendMsgArea");
const input = document.getElementById("chatInput");
const sendBtn = document.getElementById("sendMsg");
const chatbox = document.getElementById("chatbox");
const contactList = document.getElementById("contacts");
document.getElementById('sendMsg').style.visibility='hidden';

                                    form.onsubmit = (e) => {
                                        e.preventDefault();
                                    }

                                    sendBtn.onclick = () => {
                                        let xhr = new XMLHttpRequest();
                                        xhr.open('POST', "https://caleb.wtf/reboot/new/incl/sendChat.php", true);
                                        xhr.onload = () => {
                                            if(xhr.readyState === XMLHttpRequest.DONE) {
                                                if(xhr.status === 201) {
                                                    let data = xhr.response;
                                                    console.log(data.Code);
                                                    input.value = "";
                                                    getChat();
                                                    getContactList();
                                                }
                                            }
                                        }
                                        let formData = new FormData(form);
                                        xhr.send(formData);
                                    }
                                    
function getChat(chatId) {
    let xhr = new XMLHttpRequest();
    xhr.open('POST', "https://caleb.wtf/reboot/new/incl/getChat.php", true);
    xhr.onload = () => {
        if(xhr.readyState === XMLHttpRequest.DONE) {
            if(xhr.status === 200) {
                let data = xhr.response;
                chatbox.innerHTML = data;
            }
            // After receiving the response, initiate another request
            setTimeout(() => {
                getChat();
            }, 5000);
        }
    }
    xhr.onerror = () => {
        console.log('Error fetching chat content');
    }
    let formData = new FormData(form);
    xhr.send(formData); 
}

window.onload = () => {
    // Get the chat ID from the URL query string, or use a default chat ID
    const urlParams = new URLSearchParams(window.location.search);
    const chatId = urlParams.get('id') || 'default';
    
    // Load the chat content for the specified chat ID
    getChat(chatId);
    
    getContactList();
}
</script>
<?php  } ?>
<script>
contactList = document.getElementById("contacts");

function getContactList() {
    let xhr = new XMLHttpRequest();
    xhr.open('GET', "https://caleb.wtf/reboot/new/incl/GetContactList.php", true);
    xhr.onload = () => {
        if(xhr.readyState === XMLHttpRequest.DONE) {
            if(xhr.status === 200) {
                let dataa = xhr.response;
                contactList.innerHTML = dataa;
                setTimeout(() => {
                getContactList();
            }, 10000);
            }
        }
        }
        xhr.send();  
    }
                setTimeout(() => {
                getContactList();
            }, 10000);
window.onload = () => {
    getContactList();
}
    </script>



    </div>

    </div>

</body>

</html>
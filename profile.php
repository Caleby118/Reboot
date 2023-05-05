<?php 
session_start();
include_once('incl/connect.php');
include_once('incl/classes/Class.Profile.php');
$pageId = $_GET['id'];
$pageUser = new Profile();
$pageUser->getProfileUser($pageId);
if(isset($_SESSION) && isset($_SESSION['login'])) {

    if(isset($_SESSION['id']) & !empty($_SESSION['id'])){
        $id = $_SESSION['id'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://caleb.wtf/reboot/new/assets/css/style.css">
    <link rel="stylesheet" href="https://caleb.wtf/reboot/new/assets/css/profile.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script src="https://caleb.wtf/reboot/new/assets/js/script.js" defer></script>
    <title>Document</title>
</head>
<body>
<header>
        <?php include_once('incl/nav.php'); ?>
    </header>
    <?php if($_GET['id'] == 'edit') { }else{ ?>
    <div class="flex column profile-container">
        <?php } ?>
        <div class="flex column sidebar-res" id="sidebar">
            <div class="header">Explore</div>
            <ul class="flex column">
                <li class="flex"><a href="https://caleb.wtf/reboot/new/products.php" style="display: flex; align-items: center;"><span class="grid icon">🏠</span>Home</a></li>
                <li class="flex"><a href="https://caleb.wtf/reboot/new/products.php" style="display: flex; align-items: center;"><span class="grid icon">🔎</span>Search</a></li>
                <li class="flex"><a href="https://caleb.wtf/reboot/new/chat.php" style="display: flex; align-items: center;"><span class="grid icon">💬</span>Chat</a></li>
            </ul>
            <?php if(isset($_SESSION['login']) && !empty($_SESSION['login'])) { ?>
            <div class="flex signout">
                <a href="https://caleb.wtf/reboot/new/logout.php" class="flex"><span class="grid icon">🚪</span>Sign out</a>
            </div>
            <?php } ?>
        </div>
<?php
    
    if(isset($_GET['id']) && $_GET['id'] != 'edit') {
        if($_GET['id'] == "edit") {

        }else{
            $pageId = $_GET['id'];
            $idCheck = new User();
            $idCheck->pageCheckID($pageId);
        }
    
    ?>
        <div class="flex profile-header">
            <div><img src="<?php echo $pageUser->getProfilePic(); ?>" class="pp pp-xl circle outline"></div>
            <div class="flex column header-text">
                <div class="flex header">
                    <span class="flex name">
                        <?php 
                            $userName = $pageUser->getUserName();
                            $userNameR = ucfirst($userName);
                            (strlen($userNameR) > 14) ? $userName = substr($userNameR, 0, 14).'...' : $userName = $userNameR;
                            echo $userName; 
                        ?>
                    </span>
                    <div class="flex profile-action-container">
                    <?php if(isset($_GET['id']) && $_GET['id'] == $id) { }else{?>
                    <form id="follow-form" method="POST" action="incl/follow.php">
                    <input type="hidden" name="follower_id" value="<?php echo $id; ?>">
                    <input type="hidden" name="following_id" value="<?php echo $pageId; ?>">
                        <button type="submit" class="profile-action" data-id="<?php echo $id; ?>" data-following="<?php echo $pageId; ?>" onclick="toggleFollowButton()" id="follow-button">
                        <?php echo $pageUser->checkFollow($id, $pageId); ?>
                    </button>
</form>     
                        <?php
                            if($pageUser->getChat() == 0) {
                                //allow messages from everyone
                                if($pageUser->checkFollowsYou($id, $pageId) == 1 || $pageUser->checkFollowsYou($id, $pageId) == 2 || $pageUser->checkFollowsYou($id, $pageId) == 3 || $pageUser->checkFollowsYou($id, $pageId) == 0) {
                                ?>
                                <a href="../chat/<?php echo $pageUser->getUserID(); ?>" class="grid link profile-action">Message</a>
                                <?php
                                }

                            } elseif($pageUser->getChat() == 1) {
                                //allow messages from followers
                                if($pageUser->checkFollowsYou($id, $pageId) == 1 || $pageUser->checkFollowsYou($id, $pageId) == 3) {
                                ?>
                                <a href="../chat/<?php echo $pageUser->getUserID(); ?>" class="grid link profile-action">Message</a>
                                <?php
                                }

                            } elseif($pageUser->getChat() == 2) {
                                //allow messages only if they follow you
                                if($pageUser->checkFollowsYou($id, $pageId) == 2 || $pageUser->checkFollowsYou($id, $pageId) == 3) {
                                ?>
                                <a href="../chat/<?php echo $pageUser->getUserID(); ?>" class="grid link profile-action">Message</a>
                                <?php
                                }

                            } elseif($pageUser->getChat() == 3) {
                                //allow messages only if you both follow each other
                                if($pageUser->checkFollowsYou($id, $pageId) == 3) {
                                ?>
                                <a href="../chat/<?php echo $pageUser->getUserID(); ?>" class="grid link profile-action">Message</a>
                                <?php
                                }
                            }
                        ?>
                        <?php } ?>
                        <?php if(isset($_GET['id']) && $_GET['id'] == $id) { ?>
                        <a href="edit/" class="grid link profile-action">
                        <span class="material-symbols-outlined">
edit
</span>
                        </a>     
                        <?php } ?>                   
                    </div>

                </div>
                <span style="color: rgba(255, 255, 255, .7); font-size: var(--fs-200); position: absolute; top: 5px; left: 25px;"><?php if($pageUser->checkFollowsYou($id, $pageId) == 1) { echo "You follow them"; } elseif($pageUser->checkFollowsYou($id, $pageId) == 2) { echo "They follow you"; } elseif($pageUser->checkFollowsYou($id, $pageId) == 3) { echo "You both follow each other"; } else { echo "Neither follow"; }?></span>
                <span class="sub-text">
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. In quae nesciunt nihil obcaecati voluptates iusto natus officia ullam pariatur? Atque!
                </span>
            </div>
            <div class="flex chips">
                <span class="chip">Movies</span>
                <span class="chip">Games</span>
                <span class="chip">Games</span>
                <span class="chip">Games</span>
                
            </div>
        </div>

        <div class="flex column profile-main">
            <div class="flex main-header">
                <div class="title">
                    🛍️ Store
                </div>
                <div class="flex toggles">
                    <div class="grid toggle-left active">
                        Selling
                    </div>
                    <div class="grid toggle-right">
                        Sold
                    </div>
                </div>
            </div>
            <div class="grid profile-grid">

                <?php echo $pageUser->getUserProducts($pageId); ?>

            </div>
        </div>
    </div>
    <?php } ?>
    <?php
        if($_GET['id'] == "edit") {
            if(isset($_SESSION['id']) & !empty($_SESSION['id'])){
                $id = $_SESSION['id'];
            } else {
                echo ' <script> location.replace("https://caleb.wtf/reboot/new/login.php"); </script>';
            }
            
            $id = $_SESSION['id'];
            $user = new User();
            $user->getUser($id);
        ?>
<div class="flex edit-profile-main">

<div class="flex column edit-section">

<form id="upload-form" enctype="multipart/form-data">
                    <!-- <input type="file" name="fileToUpload" id="img"> -->
                    <div class="img-upload">
                        <label for="fileToUpload" class="img-overlay pp-xl circle"><span class="material-symbols-outlined">
upload
</span></label>
                        <input id="fileToUpload" name="fileToUpload" style="position: absolute; visibility:hidden;" type="file" onchange="document.getElementById('upload-btn').click();" accept="image/gif, image/jpeg, image/png">
                        <img src="<?php echo $user->getProfilePic(); ?>" class="pp pp-xl circle" id="img" width="100%" height="100%">    
                    </div>
                    
                        <input type="submit" name="btnSubmit" id="upload-btn" class="primary link" style="display: none;" value="Update picture">
</form>
</div>
                <div class="flex column edit-section">
                    <div class="heading-left">Update email address.</div>
                        <form id="change-email">
                            <input type="email" name="email" id="email" class="input-text" placeholder="Email address" value="<?php echo $user->getEmail(); ?>" readonly="readonly">
                            <input type="email" name="newEmail" id="new-email" class="input-text" placeholder="New email address" required>
                            <input type="password" name="password" id="confirm-email" class="input-text" placeholder="Confirm password" required>
                            <input type="submit" name="updateEmail" id="update-email" class="primary-a" value="Update email">
                        </form>                    
                </div>
                <div class="flex column edit-section">
                    <div class="heading-left">Change password.</div>

                    <form id="change-password">
                        <input type="password" name="password" id="old-password" class="input-text" placeholder="Old password" >
                        <input type="password" name="newPassword" id="new-password" class="input-text" placeholder="New password" required>
                        <input type="password" name="confirmNewPassword" id="confirm-password" class="input-text" placeholder="Confirm new password" required>
                        <input type="submit" name="updatePassword" id="update-password" class="primary-a" value="Change password">
                    </form>                   
                </div>
                    <div class="flex column edit-section">
                        <div class="heading-left">Allow messages from</div>
                        <?php $optionValue = $user->getChat(); ?>
                        <div class="flex checkbox-container" style="align-items: center;">
                            <input type="checkbox" id="option1" name="option" value="0" onchange="toggleCheckboxes(this)" <?php echo ($optionValue == 0) ? 'checked disabled' : ''; ?>>
                            <label for="option1">Everyone</label>                            
                        </div>

                        <div class="flex checkbox-container" style="align-items: center;">
                            <input type="checkbox" id="option2" name="option" value="1" onchange="toggleCheckboxes(this)" <?php echo ($optionValue == 1) ? 'checked disabled' : ''; ?>>
                            <label for="option2">People you follow</label>
                        </div>

                        <div class="flex checkbox-container" style="align-items: center;">
                            <input type="checkbox" id="option3" name="option" value="2" onchange="toggleCheckboxes(this)" <?php echo ($optionValue == 2) ? 'checked disabled' : ''; ?>>
                            <label for="option3">People who follow you</label>
                        </div>

                        <div class="flex checkbox-container" style="align-items: center;">
                            <input type="checkbox" id="option4" name="option" value="3" onchange="toggleCheckboxes(this)" <?php echo ($optionValue == 3) ? 'checked disabled' : ''; ?>>
                            <label for="option4">You both follow each other</label>
                        </div>
                    </div>
                    <div class="flex column edit-section">
                        <input type="submit" name="deletedAccount" class="primary-a danger" value="Deactivate your account"> 
                    </div>

                </div>


<div class="flex alert success" id="message" style="display:none;"></div>

<script>
// JavaScript code
function showSelectedPhoto() {
    var file = document.getElementById("fileToUpload").files[0];
    var reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById("selected-photo").innerHTML = '<img src="' + e.target.result + '" />';
    }
    reader.readAsDataURL(file);
}

const reader = new FileReader();
const fileInput = document.getElementById("fileToUpload");
const img = document.getElementById("img");
reader.onload = e => {
  img.src = e.target.result;
}
fileInput.addEventListener('change', e => {
  const f = e.target.files[0];
  reader.readAsDataURL(f);
})

document.getElementById('upload-form').addEventListener('submit', function(e) {
    e.preventDefault();

    var formData = new FormData(this);

    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if(xhr.readyState === 4) {
            if(xhr.status === 200) {
                document.getElementById('message').innerHTML = xhr.responseText;
                document.getElementById('message').style.display = 'block';
                setTimeout(function(){
                    document.getElementById('message').style.display = 'none';
                }, 5000); // hide message after 5 seconds
            } else {
                document.getElementById('message').innerHTML = "Error: Failed to upload image.";
                document.getElementById('message').style.display = 'block';
            }
            document.getElementById('upload-btn').removeAttribute('disabled');
        } else {
            document.getElementById('upload-btn').setAttribute('disabled', 'disabled');
        }
    };

    xhr.open('POST', 'https://caleb.wtf/reboot/new/incl/uploadProfilePic.php');
    xhr.send(formData);
    document.getElementById('upload-btn').setAttribute('disabled', 'disabled');
});

//change email
document.getElementById('change-email').addEventListener('submit', function(e) {
    e.preventDefault();

    var formData = new FormData(this);

    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if(xhr.readyState === 4) {
            if(xhr.status === 200) {
                document.getElementById('message').innerHTML = xhr.responseText;
                document.getElementById('message').classList.remove('danger');
                document.getElementById('message').classList.add('success');
                document.getElementById('message').style.display = 'block';
                const emailInput = document.getElementById('new-email');
                const newEmail = emailInput.value;
                document.getElementById("email").value = newEmail;
                document.getElementById("new-email").value = "";
                document.getElementById("confirm-email").value = "";
                setTimeout(function(){
                    document.getElementById('message').style.display = 'none';
                }, 5000); // hide message after 5 seconds
            } else {
                document.getElementById('message').innerHTML = xhr.responseText;
                document.getElementById('message').classList.remove('success');
                document.getElementById('message').classList.add('danger');
                document.getElementById('message').style.display = 'block';
                setTimeout(function(){
                    document.getElementById('message').style.display = 'none';
                }, 5000); // hide message after 5 seconds
                }
            document.getElementById('update-email').removeAttribute('disabled');
        } else {
            document.getElementById('update-email').setAttribute('disabled', 'disabled');
        }
    };

    xhr.open('POST', 'https://caleb.wtf/reboot/new/incl/updateEmail.php');
    xhr.send(formData);
    document.getElementById('update-email').setAttribute('disabled', 'disabled');
});

//change password
document.getElementById('change-password').addEventListener('submit', function(e) {
    e.preventDefault();

    var formData = new FormData(this);

    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if(xhr.readyState === 4) {
            if(xhr.status === 200) {
                document.getElementById('message').innerHTML = xhr.responseText;
                document.getElementById('message').classList.remove('danger');
                document.getElementById('message').classList.add('success');
                document.getElementById('message').style.display = 'block';
                document.getElementById("change-password").reset();
                setTimeout(function(){
                    document.getElementById('message').style.display = 'none';
                }, 5000); // hide message after 5 seconds
            } else {
                document.getElementById('message').innerHTML = xhr.responseText;
                document.getElementById('message').classList.remove('success');
                document.getElementById('message').classList.add('danger');
                document.getElementById('message').style.display = 'block';
                setTimeout(function(){
                    document.getElementById('message').style.display = 'none';
                }, 5000); // hide message after 5 seconds
                }
            document.getElementById('update-password').removeAttribute('disabled');
        } else {
            document.getElementById('update-password').setAttribute('disabled', 'disabled');
        }
    };

    xhr.open('POST', 'https://caleb.wtf/reboot/new/incl/updatePassword.php');
    xhr.send(formData);
    document.getElementById('update-password').setAttribute('disabled', 'disabled');
});

//chat settings
function toggleCheckboxes(checkbox) {
  var optionValue = checkbox.value;
  var isChecked = checkbox.checked;
  if (isChecked == false) {
    optionValue = 0;
  }
  console.log(optionValue + ' + ' + isChecked);
  var checkboxes = document.getElementsByName('option');
  for (var i = 0; i < checkboxes.length; i++) {
    if (checkboxes[i] !== checkbox) {
      checkboxes[i].checked = false;
      checkboxes[i].disabled = false;
    }else{
        checkboxes[i].disabled = checkbox.checked;
    }
  }
  
  // send AJAX request to update SQL table
  var xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        document.getElementById('message').innerHTML = xhr.responseText;
        document.getElementById('message').classList.remove('danger');
        document.getElementById('message').classList.add('success');
        document.getElementById('message').style.display = 'block';
        setTimeout(function() {
          document.getElementById('message').style.display = 'none';
        }, 5000); // hide message after 5 seconds
      } else {
        document.getElementById('message').innerHTML = xhr.responseText;
        document.getElementById('message').classList.remove('success');
        document.getElementById('message').classList.add('danger');
        document.getElementById('message').style.display = 'block';
        setTimeout(function() {
          document.getElementById('message').style.display = 'none';
        }, 5000); // hide message after 5 seconds
      }
    }
  };
  xhr.open('POST', 'https://caleb.wtf/reboot/new/incl/updateChatSettings.php');
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.send('option=' + optionValue);
}


</script>

        <?php }
?>

    </div>
</body>
<script>
function toggleFollowButton() {
  event.preventDefault(); // prevent form submission
  
  var form = document.getElementById('follow-form');
  var follower_id = form.elements['follower_id'].value;
  var following_id = form.elements['following_id'].value;
  
  // send AJAX request
  var xhr = new XMLHttpRequest();
  xhr.open('POST', 'https://caleb.wtf/reboot/new/incl/follow.php', true);
  xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhr.onreadystatechange = function() {
    if (xhr.readyState == 4 && xhr.status == 200) {
      // parse JSON response
      var response = JSON.parse(xhr.responseText);
      var status = response.status;
      
      // update button text and style
      var button = document.getElementById('follow-button');
      button.innerText = status == 'following' ? 'Unfollow' : 'Follow';
    }
  };
  xhr.send('follower_id=' + follower_id + '&following_id=' + following_id);
}
    </script>
</html>
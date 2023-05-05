<?php
session_start();
if(isset($_SESSION['id']) & !empty($_SESSION['id'])){
include_once('incl/connect.php');
include_once('incl/classes/Class.Profile.php');
$id = $_SESSION['id'];
$user = new User();
$user->getUser($id);
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
    <link rel="stylesheet" href="https://caleb.wtf/reboot/new/assets/css/profile.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script src="assets/js/script.js" defer></script>
    <title>Document</title>
</head>
<body class="chat-body">
    <header>
        <?php include_once('incl/nav.php'); ?>
    </header>

        <div class="flex column sidebar-res" id="sidebar">
            <div class="header">Explore</div>
            <ul class="flex column">
                <li class="flex"><span class="grid icon">🏠</span>Home</li>
                <li class="flex"><span class="grid icon">🔎</span>Search</li>
                <li class="flex active"><span class="grid icon">💬</span>Chat</li>
                <li class="flex"><span class="grid icon">👘</span>Clothing </li>
            </ul>
            <div class="flex signout">
                <a href="#" class="flex"><span class="grid icon">🚪</span>Sign out</a>
            </div>
        </div>

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

    xhr.open('POST', 'incl/uploadProfilePic.php');
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

    xhr.open('POST', 'incl/updateEmail.php');
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

    xhr.open('POST', 'incl/updatePassword.php');
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
  xhr.open('POST', 'incl/updateChatSettings.php');
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.send('option=' + optionValue);
}


</script>

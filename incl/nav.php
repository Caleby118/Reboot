            <div class="flex navbar">
                <div class="flex start">
                    <div class="sidebar-btn2" id="sidebarBtn">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div><a href="https://caleb.wtf/reboot/new/index.php" class="logo-link">r<span class="glitch">e</span>boot</a>
                </div>
                <div class="flex middle">
                    <span class="material-symbols-outlined" style="margin-top: 0; position: absolute; z-index: 2; margin-left: .5rem;">
                        search
                    </span>
                    <div class="nav-search"><form action="https://caleb.wtf/reboot/new/products.php?q=" method="GET"><input type="text" name="q" id="nav-search" onkeydown="if (event.keyCode == 13) search()" placeholder="T-shirt, shoes, tutoring..."><input type="submit" style="display: none;"></form></div>         
                </div>
                <div class="flex end">
                    <div class="flex actions">
                        <?php 
                            if(isset($_SESSION) && isset($_SESSION['login'])) { 
                                $user = new User();
                                echo $user->getUser($id);
                                $notifications = new Chat();
                                $notifs = $notifications->totalNotifs($id);
                            }
                        ?>
                        <?php if(isset($_SESSION) && isset($_SESSION['login'])) {  ?>
                        <div class="flex notifications" id="notification-button">
                            <a href="https://caleb.wtf/reboot/new/post.php" class="link add-post">
                                <span class="material-symbols-outlined icon-lg">
                                    add_circle
                                </span>
                            </a>
                            <div class="link notification-icon" style="position: relative;">
                            <?php if($notifs > 0) { ?>
                            <span class="grid notification-bubble" style="position: absolute; right: 3px; top: 10px; min-height: 10px; min-width: 10px;"></span>
                            <?php } ?>
                                <span class="material-symbols-outlined icon-lg" style="<?php if($notifs > 0) { echo ' '; } ?>">
                                    notifications
                                </span>
                                <div class="flex column notification-dropdown">
                                <?php
include_once('incl/connect.php');
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

                                </div>
                            </div>
                            <?php } ?>
                        </div>
                        <div class="user-area">
                            <div class="flex login-group" id="login-group">
                            <?php if(isset($_SESSION) && isset($_SESSION['login'])) {  ?>
                                <div><img src="<?php echo $user->getProfilePic(); ?>" class="pp pp-sm circle"></div>
                                <div class="flex nav-login-text">
                                    <span class="user-welcome"><strong>Hello,</strong> <?php echo $user->getFirstName(); ?></span>
                                    <span class="material-symbols-outlined">
                                        expand_more
                                    </span>
                                </div>
                            </div>
                            <div class="flex column dropdown">
                                <a href="https://caleb.wtf/reboot/new/profile/<?php echo $user->getUserID(); ?>" class="flex link">
                                    <span class="material-symbols-outlined">
                                        currency_pound
                                    </span> 
                                    <?php echo $user->getFunds(); ?>
                                </a>
                                <a href="https://caleb.wtf/reboot/new/profile/<?php echo $user->getUserID(); ?>" class="flex link">
                                    <span class="material-symbols-outlined">
                                        account_circle
                                    </span> 
                                    Profile
                                </a>
                                <a href="https://caleb.wtf/reboot/new/chat/" class="flex link">
                                    <span class="grid icon">
                                        <span class="material-symbols-outlined">
                                            chat_bubble
                                        </span>
                                    </span>
                                    Chat <?php if($notifs > 0) { ?><span class="grid notification-bubble"><?php echo $notifs; ?></span><?php } ?>
                                </a>
                                <a href="https://caleb.wtf/reboot/new/profile/edit/" class="flex link">
                                    <span class="material-symbols-outlined">
                                        edit_note
                                    </span>
                                    Edit profile
                                </a>
                                <a href="https://caleb.wtf/reboot/login.php" class="flex link">
                                    <span class="grid icon">
                                        <span class="material-symbols-outlined">
                                            settings
                                        </span>
                                    </span>
                                    Settings
                                </a>
                            </div>
                            <?php } else { ?>
                                <a href="https://caleb.wtf/reboot/new/login.php">Login</a>
                                <a href="https://caleb.wtf/reboot/new/signup.php">Register</a>
                                <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
        <?php if(isset($_SESSION) && isset($_SESSION['login'])) {  ?>
<!--         <div class="flex column mobile-container" id="mobile-container">
            <div class="flex column links-container" id="links-container">
                <a href="https://caleb.wtf/reboot/new/profile/" class="flex link">
                    <span class="material-symbols-outlined">
                        account_circle
                    </span> 
                    Profile
                </a>
                <a href="https://caleb.wtf/reboot/new/chat/" class="flex link">
                    <span class="grid icon">
                        <span class="material-symbols-outlined">
                            chat_bubble
                        </span>
                    </span>
                    
                </a>
                <a href="https://caleb.wtf/reboot/new/profile/edit/" class="flex link">
                    <span class="material-symbols-outlined">
                        edit_note
                    </span>
                    Edit profile
                </a>
                <a href="https://caleb.wtf/reboot/login.php" class="flex link">
                    <span class="grid icon">
                        <span class="material-symbols-outlined">
                            settings
                        </span>
                    </span>
                    Settings
                </a>                
            </div> -->
            <?php } ?>

        </div>
        <script>
            const loginGroup = document.getElementById("login-group");
            const notifButton = document.getElementById("notification-button");
            const mobileContainer = document.getElementById("mobile-container");
            const linksContainer = document.getElementById("links-container");
            
            function addEventListeners() {
            if (window.matchMedia('(max-width: 710px)').matches) {
                const loginGroup = mobileContainer.querySelector('.login-group');
                const notificationButton = mobileContainer.querySelector('.notification-button');

                loginGroup.addEventListener('click', () => {
                    console.log('Notifications clicked');
                    if(mobileContainer.classList.contains("mobile-container-show")) {
                        mobileContainer.classList.remove("mobile-container-show");
                    }else{
                        mobileContainer.classList.add("mobile-container-show");
                    }
                    // Show login links
                    linksContainer.innerHTML = `
                        <a href="#">Login with email</a>
                        <a href="#">Login with Facebook</a>
                        <a href="#">Forgot password?</a>
                    `;
                });

                notificationButton.addEventListener('click', () => {
                    console.log('Notifications clicked');
                    if(mobileContainer.classList.contains("mobile-container-show")) {
                        mobileContainer.classList.remove("mobile-container-show");
                    }else{
                        mobileContainer.classList.add("mobile-container-show");
                    }
                    // Show notifications from PHP file
                    fetch('https://caleb.wtf/reboot/new/incl/getNotifications.php')
                        .then(response => response.text())
                        .then(data => linksContainer.innerHTML = data)
                        .catch(error => console.log(error));
                    });
            }
            }

            addEventListeners();
	</script>
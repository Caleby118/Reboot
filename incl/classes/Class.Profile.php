<?php
session_start();
include_once "Class.Database.php";
class Profile extends Database {
    public function getProfileUser($pageId) {
        $sql = "SELECT u.userID, u.userName, user_info.profilePic, u.created, user_info.chat
                FROM user_login u
                INNER JOIN user_info ON u.userID = user_info.userID
                WHERE u.userID = :id";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute(array(':id' => $pageId))or die(print_r($stmt->errorInfo(), true));;
        $row = $stmt->fetch();

        $this->pageid = $row['userID'];
        $this->userName = $row['userName'];
        $this->profilePic = $row['profilePic'];
        $this->created = $row['created'];
        $this->chat = $row['chat'];
    }

    public function getUserID(){
        return $this->pageid;
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
    public function getChat(){
      return $this->chat;
  }

    public function follow($id, $pageId) {
// check if following relationship exists in database
$stmt = $this->connect()->prepare('SELECT * FROM followers WHERE followerID = ? AND followingID = ?');
$stmt->execute(array($id, $pageId));
$exists = $stmt->rowCount() > 0;

// update or delete following
if ($exists) {
  // unfollow
  $stmt = $this->connect()->prepare('DELETE FROM followers WHERE followerID = ? AND followingID = ?');
  $stmt->execute(array($id, $pageId));
  $status = 'unfollowed';
} else {
  // follow
  $stmt = $this->connect()->prepare('INSERT INTO followers (followerID, followingID) VALUES (?, ?)');
  $stmt->execute(array($id, $pageId));
  $status = 'following';
}

// return JSON response
if (isset($status)) {
    $response = array('status' => $status);
  } else {
    $response = array('error' => 'An error occurred');
  }
header('Content-Type: application/json');
echo json_encode($response);
    }

    public function checkFollow($id, $pageId) {
        $stmt = $this->connect()->prepare('SELECT * FROM followers WHERE followerID = ? AND followingID = ?');
        $stmt->execute(array($id, $pageId));
        $exists = $stmt->rowCount() > 0;
        if ($exists) {
          echo "Unfollow";
        } else {
          echo "Follow";
        }
    }

    public function checkFollowsYou($id, $pageId) {
      $stmt = $this->connect()->prepare('SELECT * FROM followers WHERE followerID = ? AND followingID = ?');
      $stmt->execute(array($pageId, $id));
      $follower = $stmt->fetch(PDO::FETCH_ASSOC);
  
      $stmt = $this->connect()->prepare('SELECT * FROM followers WHERE followerID = ? AND followingID = ?');
      $stmt->execute(array($id, $pageId));
      $following = $stmt->fetch(PDO::FETCH_ASSOC);
  
      if ($follower && $following) {
          // both follow each other
          $result = 3;
      } elseif ($follower) {
          // they follow you
          $result = 2;
      } elseif ($following) {
        // you follow them
        $result = 1;
      } else {
          // user is not following
          $result = 0;
      }
  
      return $result;
  }

  public function getUserProducts($pageId) {
    $sql = "SELECT *
            FROM products p
            INNER JOIN product_images ON p.productID = product_images.postID
            WHERE sellerID = ?
            ORDER BY p.datePosted DESC";
    $stmt = $this->connect()->prepare($sql);
    $res = $stmt->execute(array($pageId));
  
    if($res) {
      while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $image = $row['imageUrl'];
        $product_title = $row['product'];
        $product_description = $row['description'];
        $product_price = $row['price'];
        $product_id = $row['productID'];
        switch($product_title) {
          case '1':
            $product_title = 'T-Shirts & Vests';
            break;
          case '2':
            $product_title = 'Shirts';
            break;
          case '3':
            $product_title = 'Jackets & Coats';
            break;
          case '4':
            $product_title = 'Hoodies & Sweatshirts';
            break;
          case '5':
            $product_title = 'Trousers';
            break;
          case '6':
            $product_title = 'Dress';
            break;
          case '7':
            $product_title = 'Skirt';
            break;
            case '7':
              $product_title = 'Shoes';
              break;
          default:
            $product_title = 'unknown';
        }
        $product_descriptionR = ucfirst($product_description);
        (strlen($product_description) > 24) ? $product_description = substr($product_descriptionR, 0, 24).'...' : $product_description = $product_descriptionR;
  
        echo '<div>
                <a href="https://www.caleb.wtf/reboot/new/product/'.$product_id.'" class="link">
                  <div class="card">
                    <div class="card-header">
                      <img src="'.$image.'" class="card-img">
                    </div>
                    <span class="new">
                      New
                    </span>
                    <span class="card-price">
                      £'.$product_price.'
                    </span>
                    <a href="https://www.caleb.wtf/reboot/new/product/'.$product_id.'" class="grid link button">View</a>
                    <div class="card-body">
                      <div class="card-title">
                        '.$product_title.'
                      </div>
                      <div class="card-text">
                        '.$product_description.'
                      </div>
                    </div>
                  </div>
                </a>
              </div>';
      }
    }
  }
}
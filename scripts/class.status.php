<?php
/**
  * status class
  * dana framework v.3
*/

class status {

  static public function ShowTitle() {
    echo 'MyLocalSmallBusiness &ndash; Control Page';
  }

  static public function ShowUserStatus() {
    $account = account::$instance;
    if ($account->exists) {
      $username = $account->Contact()->GetFieldValue('username');
      $displayname = $account->Contact()->displayname;
      $url = '<a href="logout.php" title="log out">Log Out</a>';
      $msg = "Logged in as: <strong>{$username}</strong> &ndash;&nbsp; Hello {$displayname} &ndash; {$url}";
    } else {
      $url = '<a href="login.php" title="log in">Log In</a>';
      $msg = '<strong>NOT LOGGED IN</strong>&nbsp;- Please ' . $url;
    }
    echo $msg;
  }

  static public function ShowFooter() {
    echo "&copy; Whitedream Software " . date('Y');
  }
}

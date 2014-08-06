<?php
require_once 'define.php';
require_once 'class.table.account.php';

class websitemanager {
  static public $instance = false;
  static public $account;
  private $pagemgrid;
  private $pageid;
  private $guestbook; // = new guestbookprocess({$accountid}, {$pageid});

  function __construct($accountid) {
    self::$account = account::StartInstance($accountid);
  }

  static public function GetInstance() {
    if (!self::$instance) {
      die("Account not assigned / no account found");
      exit;
    }
    return self::$instance;
  }

  static public function SetAccount($accountid) {
    self::$instance = new websitemanager($accountid);
  }

  static public function ProcessPageStats($pagemgrid, $pageid) {
    $obj = self::GetInstance();
    $obj->pagemgrid = $pagemgrid;
    $obj->pageid = $pageid;
    // TODO
  }

  static public function ShowAdvert() {
//    require_once('../scripts/advertclass.php');
    $advertitem = new advertitem();
    $advert = $advertitem->GetRandomAdvert();
    if ($advert) {
      echo $advert->ShowContent();
    }
  }

  static public function ShowBlogSideContent() {
    echo "<p>TODO: Show Blog Side Content</p>\n";
  }

  static public function ShowGallery($galleryid) {
    $gallery = new gallery($galleryid);
    echo $gallery->BuildGallery(self::GetInstance()->pageid);
  }

  static public function ShowGuestBook() {
    $guestbook = self::guestbook;
    $guestbook->BuildPage(self::GetInstance()->rootpath);
  }

  static public function ShowGuestBookSideContent() {
    $guestbook = self::guestbook;
    $guestbook->BuildSideContent();
  }
}

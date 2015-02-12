<?php
namespace dana\webmanager;

//use dana\core;

/**
  * website manager class
  * processes the dynamic parts of the pages of the mini-websites of the account holders
  * @version dana framework v.3
*/

abstract class websitemanager {
// list of types for creating and processing action queries
  const AQ_RID = 'rid'; // show a specific article
  const AQ_ARTCAT = 'cat'; // show a list of articles that match the category
  const AQ_LOGOUT = 'logout'; // log out (key specifies type) eg. v for visitor
  const AQ_NEWSLETTERID = 'nid'; // showing a specific newsletter
  const AQ_CALENDARDATEID = 'calid'; // show calendar entry
  const AQ_VISITORLOGOUT = 'vlo'; // logout for visitor
// post form handlers
  const PFH_GUESTBOOKVISITOR = 'visitorlogin'; // fields: username  password
  const PFH_PRIVATEAREA = 'privatearealogin'; // fields: username  password
// content type for website manager class
  const CTWM_MAINCONTENT = 'mc';
  const CTWM_INITIALCONTENT = 'ic';
  const CTWM_ARTICLES = 'art';
  const CTWM_GUESTBOOK = 'gb';
  const CTWM_NEWSLETTERS = 'nl';
  const CTWM_SOCIALNETWORK = 'sn';
  const CTWM_CALENDAR = 'cal';
  const CTWM_BOOKING = 'bk';
  const CTWM_PRIVATEAREA = 'pa';
  const CTWM_DOWNLOADABLEFILES = 'df';

  static public $instance = false;
  static public $account;
  protected $pgtype; // page type (eg. 'gen'). assigned by derived class
  protected $page; // current page object
  protected $pagemgrid;
  protected $mode; //
  protected $rootpath;
  protected $sourcepath;
//  public $pagemgrid;
  public $pageid;
  public $groupid;

  function __construct($accountid, $pageid, $groupid, $mode, $rootpath, $sourcepath) {
    self::$account = account::StartInstance($accountid);
    $this->pagemgrid = self::$account->GetFieldValue('pagemgrid');
    $this->pageid = $pageid;
    $this->groupid = $groupid;
    $this->mode = $mode;
    $this->rootpath = $rootpath;
    $this->sourcepath =$sourcepath;
    $this->pgtype = $this->GetPageType();
    $this->ProcessActionString();
  }

  abstract protected function GetPageType();
  abstract protected function GetMainContent($groupid);

  static public function GetInstance() {
    if (!self::$instance) {
      die("Account not assigned / no account found"); // TODO: create an error page
      exit;
    }
    return self::$instance;
  }

  static public function Initialise($accountid, $pgtype, $pageid, $groupid, $mode, $rootpath, $sourcepath) {
    switch ($pgtype) {
      case page::PAGETYPE_GENERAL:
        require_once $sourcepath . DIRECTORY_SEPARATOR . 'wsm.generalpage.php';
        self::$instance = new wsm_generalpage($accountid, $pageid, $groupid, $mode, $rootpath, $sourcepath);
        break;
      case page::PAGETYPE_CONTACT:
        require_once $sourcepath . DIRECTORY_SEPARATOR . 'wsm.contactpage.php';
        self::$instance = new wsm_contactpage($accountid, $pageid, $groupid, $mode, $rootpath, $sourcepath);
        break;
      case page::PAGETYPE_GALLERY:
        require_once $sourcepath . DIRECTORY_SEPARATOR . 'wsm.gallerypage.php';
        self::$instance = new wsm_gallerypage($accountid, $pageid, $groupid, $mode, $rootpath, $sourcepath);
        break;
      case page::PAGETYPE_ARTICLE:
        require_once $sourcepath . DIRECTORY_SEPARATOR . 'wsm.articlepage.php';
        self::$instance = new wsm_articlepage($accountid, $pageid, $groupid, $mode, $rootpath, $sourcepath);
        break;
      case page::PAGETYPE_GUESTBOOK:
        require_once $sourcepath . DIRECTORY_SEPARATOR . 'wsm.guestbookpage.php';
        self::$instance = new wsm_guestbookpage($accountid, $pageid, $groupid, $mode, $rootpath, $sourcepath);
        break;
      case page::PAGETYPE_BOOKING:
        require_once $sourcepath . DIRECTORY_SEPARATOR . 'wsm.bookingpage.php';
        self::$instance = new wsm_bookingpage($accountid, $pageid, $groupid, $mode, $rootpath, $sourcepath);
        break;
      case page::PAGETYPE_CALENDAR:
        require_once $sourcepath . DIRECTORY_SEPARATOR . 'wsm.calendarpage.php';
        self::$instance = new wsm_calendarpage($accountid, $pageid, $groupid, $mode, $rootpath, $sourcepath);
        break;
      case page::PAGETYPE_NEWSLETTER:
      case page::PAGETYPE_SOCIALNETWORK:
      //PAGETYPE_PRODUCT
      default:
        break;
    }
  }

  static public function ProcessPageStats() { //$pagemgrid, $pageid) {
/*    $obj = self::GetInstance();
    $obj->pagemgrid = $pagemgrid;
    $obj->pageid = $pageid; */
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

  // display content for the sidebar based on the content type (ct) and page type (pgtype)
  static public function ShowSideContent($ct, $groupid) {
    switch ($ct) {
      case self::CTWM_ARTICLES: // 1
        $ret = self::$instance->GetArticleSidebar();
        break;
      case self::CTWM_GUESTBOOK: // 2
        $ret = self::$instance->GetGuestbookSidebar($groupid);
        break;
      case self::CTWM_NEWSLETTERS: // 3
        $ret = self::$instance->GetNewsletterSidebar();
        break;
      case self::CTWM_SOCIALNETWORK: // 4
        $ret = self::$instance->GetSocialNetworkSidebar();
        break;
      case self::CTWM_CALENDAR: // 5
        $ret = self::$instance->GetCalendarSidebar();
        break;
      case self::CTWM_BOOKING: // 6
        $ret = self::$instance->GetBookingSidebar();
        break;
      case self::CTWM_PRIVATEAREA: // 7
        $ret = self::$instance->GetPrivateAreaSidebar();
        break;
      case self::CTWM_DOWNLOADABLEFILES: // 8
        $ret = self::$instance->GetDownloadableFilesSidebar();
        break;
      case self::CTWM_INITIALCONTENT: // 9        
      default:
        $ret = array();
    }
    echo ArrayToString($ret);
  }

  // display content for the main area based on the content type (ct) and page type (pgtype)
  static public function ShowMainContent($ct, $groupid) {
    switch ($ct) {
      case self::CTWM_MAINCONTENT: // mc
        $ret = self::$instance->GetMainContent($groupid);
        break;
      case self::CTWM_ARTICLES: // art
        $ret = self::$instance->GetArticlesMain();
        break;
      case self::CTWM_GUESTBOOK: // gb
        $ret = self::$instance->GetGuestbookMain($groupid);
        break;
      case self::CTWM_NEWSLETTERS: // nl
        $ret = self::$instance->GetNewsletterMain();
        break;
      case self::CTWM_SOCIALNETWORK: // sn
        $ret = self::$instance->GetSocialNetworkMain();
        break;
      case self::CTWM_CALENDAR: // cal
        $ret = self::$instance->GetCalendarMain();
        break;
      case self::CTWM_BOOKING: // bk
        $ret = self::$instance->GetBookingMain();
        break;
      case self::CTWM_PRIVATEAREA: // pa
        $ret = self::$instance->GetPrivateAreaMain();
        break;
      case self::CTWM_DOWNLOADABLEFILES: // df
        $ret = self::$instance->GetDownloadableFilesMain();
        break;
      case self::CTWM_INITIALCONTENT: // ic
        $ret = self::$instance->GetInitialContentMain();// $this->page->GetFieldValue('initialcontent');
        break;
      default:
        $ret = array();
    }
    echo ArrayToString($ret);
  }

  protected function DoProcessActionString() {
    return '';
  }

  protected function ProcessActionString() {
    $ret = ArrayToString($this->DoProcessActionString());
    if ($ret) {
      echo $ret;
    }
  }

  // make a link to the same page with a action query
  protected function MakeActionLink($aq, $key, $value, $title = false) {
    if (!$title) {
      $title = $value;
    }
    $url = $_SERVER['PHP_SELF'] . "?{$aq}={$key}";
    return "<a title='{$title}' href='{$url}'>{$value}</a>";
  }

  protected function DoGallerySlideShow() {
    $gallery = new gallery($this->page->GetFieldValue('gengalleryid'));
//      $galleryheight = $gallery->GetGalleryHeight();
    return $gallery->BuildSlideShowList();
  }

  protected function GetActionQuery($aq) {
    return GetGet($aq);
  }

  protected function GetSessionValue($session) {
    return (isset($_SESSION[$session])) ? $_SESSION[$session] : false;
  }

  // make a list of items for showing in the sidebar
  protected function MakeSidebarList($title, $list, $aq) {
    $ret = array();
    if ($list) {
      $ret[] = "<h2>{$title}</h2>";
      $ret[] = '<ul>';
      foreach($list as $key => $item) {
        $value = $item['value'];
        $title = $item['title'];
        $content = (isset($item['content'])) ? $item['content'] : '';
        $ret[] = '  <li>' . $this->MakeActionLink($aq, $key, $value, $title) . $content . '</li>';
      }
      $ret[] = '</ul>';
    }
    return $ret;
  }


  // MAIN CONTENT

  // ARTICLES - MAIN
  protected function GetArticlesMain() {
    $ret = array();
    if ($this->pgtype == PAGETYPE_ARTICLE) {
      $rid = $this->GetActionQuery(self::AQ_RID);
      if ($rid) {
        $articles = articleitem::GetArticle($rid);
      } else {
        $cat = $this->GetActionQuery(self::AQ_ARTCAT);
        $articles = articleitem::GetAllCurrentArticles(self::$account->ID(), $cat, $rid);
      }
      // show just a list of categories with an action link
      $list = array();
      foreach($articles as $articleid => $article) {
        $item = articleitem::MakeDisplayItem($article);
        $list[] = ArrayToString($item);
      }
      if ($list) {
        $ret[] = "<section>";
        $ret = array_merge($ret, $list);
        $ret[] = "</section>";
      }
    }
    return $ret;
  }

  // GUESTBOOKS - MAIN
  protected function GetGuestbookMain($groupid) {
    $ret = array();
/*    if ($this->pgtype == PAGETYPE_GUESTBOOK) {
      $ret[] = '<h2>GUESTBOOK MAIN - TODO</h2>';
    }; */
    return $ret;
  }

  // NEWSLETTERS - MAIN
  protected function GetNewsletterMain() {
    $ret = array();
/*    if ($this->pgtype == PAGETYPE_NEWSLETTER) {
      $ret[] = '<h2>NEWSLETTERS MAIN - TODO</h2>';
    }; */
    return $ret;
  }

  // SOCIAL NETWORKS - MAIN
  protected function GetSocialNetworkMain() {
    $ret = array();
    //$ret[] = '<h2>SOCIALNETWORK MAIN - TODO</h2>';
    return $ret;
  }

  // CALENDAR DATES - MAIN
  protected function GetCalendarMain() {
    $ret = array();
/*    if ($this->pgtype == PAGETYPE_CALENDAR) {
      $ret[] = '<h2>CALENDAR MAIN - TODO</h2>';
    }; */
    return $ret;
  }

  // BOOKINGS - MAIN
  protected function GetBookingMain() {
    $ret = array();
/*    if ($this->pgtype == PAGETYPE_BOOKING) {
      $ret[] = '<h3>BOOKINGS MAIN - TODO</h3>';
    }; */
    return $ret;
  }

  // PRIVATE AREAS - MAIN
  protected function GetPrivateAreaMain() {
    $ret = array();
//    $ret[] = '<h3>PRIVATE AREA MAIN - TODO</h3>';
    return $ret;
  }

  // DOWNLOADABLE FILES - MAIN
  protected function GetDownloadableFilesMain() {
    $ret = array();
//    $ret[] = '<h3>DOWNLOADABLE FILES MAIN - TODO</h3>';
    return $ret;
  }

  // INITIAL CONTENT - MAIN
  protected function GetInitialContentMain() {
    switch ($this->pgtype) {
      case PAGETYPE_GUESTBOOK:
        $ret = ''; //$guestbook->WriteInitialMessage();
        break;
      default:
        $ret = $this->page->GetFieldValue('initialcontent');
        break;
    }
    return $ret;
  }


  // SIDEBAR CONTENT

  // ARTICLES - SIDEBAR
  // if page type is ARTICLES then show catgories
  // else show titles of all active articles
  protected function GetArticleSidebar() {
    $articles = articleitem::GetAllCurrentArticles(self::$account->ID());
    if ($this->pgtype == PAGETYPE_ARTICLE) {
      // show just a list of categories with an action link
      $lastcategory = false;
      $list = array();
      foreach($articles as $articleid => $article) {
        $currentcategory = $article['category'];
        if ($currentcategory != $lastcategory) {
          $lastcategory = $currentcategory;
          $list[$currentcategory] = array('value' => $currentcategory, 'title' => 'click to see all articles for ' . $currentcategory);
        }
      }
      $ret = $this->MakeSidebarList('Article Categories', $list, self::AQ_ARTCAT);
    } else {
      $list = array();
      foreach($articles as $articleid => $article) {
        $list[$articleid] = array('value' => $article['heading'], 'title' => 'click to see ' . $article['category']);
      }
      $ret = $this->MakeSidebarList('Articles', $list, self::AQ_RID);
    }
    return $ret;
  }

  // GUESTBOOKS - SIDEBAR
  // only shown if the page type is GUESTBOOK
  // if logged in show guest user name and logout link
  // if not logged in show login form
  protected function GetGuestbookSidebar($guestbookid) {
    if ($this->pgtype == PAGETYPE_GUESTBOOK) {
      $ret = array('<h2>Guestbook</h2>');
      $visitorsession = $this->GetSessionValue(activitymanager::SESS_GUEST); // find session for guest (if exists)
      if ($visitorsession) {
        // the session id is the session key used when then registered
        // lookup the visitor table using the session key found
        $visitor = visitor::FindBySession($visitorsession);
        $loggedin = ($visitor->exists);
      } else {
        $loggedin = false;
      }
      if ($loggedin) {
        // logged in so show visitor name and log out link
        $displayname = $visitor->GetFieldValue('displayname');
        $ret[] = "<h3>Logged In</h3>";
        $ret[] = "<p>{$displayname}</p>";
        $ret[] = $this->MakeActionLink(self::AQ_VISITORLOGOUT, 'v', 'Log Out', 'Click to Log Out');
      } else {
        $ret[] = '<h3>Visitor Login</h3>';
        $ret[] = "<form class='login' name='frmvisitorlogin'>";
        $ret[] = "  <label for='username'>Username";
        $ret[] = "    <input name='username' type='editbox'></label>";
        $ret[] = "  <label for='password'>Password";
        $ret[] = "  <input name='password' type='password'></label>";
        $ret[] = "  <input name='handler' type='hidden' value='" . self::PFH_GUESTBOOKVISITOR . "'>";
        $ret[] = "  <input name='submit' type='submit' value='Login'>";
        $ret[] = '</form>';
      }
    } else {
      $ret = array();
    }
    return $ret;
  }

  // NEWSLETTERS - SIDEBAR
  // show list of 'showable' newsletters (based on the 'showdate' field)
  // note: there is no newsletter page type so just show anyway on all pages
  // TODO: add newsletter page type?
  protected function GetNewsletterSidebar() {
    $ret = array();
    if ($this->page->GetFieldValue('incshownewsletters')) {
      $newsletters = newsletter::FindShowableNewslettersByAccount(self::$account->ID());
      if ($newsletters) {
        $list = array();
        foreach($newsletters as $nlid => $nl) {
          $title = $nl->GetFieldValue('title');
          $display = $title . " - <small>{$nl->showdatedescription}</small>";
          $list[$nlid] = array(
            'value' => $display,
            'title' => 'click to see ' . $title
          );
        }
        $ret = $this->MakeSidebarList('Newsletters', $list, self::AQ_NEWSLETTERID);
      }
    }
    return $ret;
  }

  // SOCIAL NETWORKS - SIDEBAR
  // 
  protected function GetSocialNetworkSidebar() {
    $ret = array(); //array('<p>TODO: Social Networks</p>');
    return $ret;
  }

  // CALENDAR DATES - SIDEBAR
  protected function GetCalendarSidebar() {
    $dates = calendardate::GetList(self::$account->ID());
    if ($dates) {
      $list = array();
      foreach($dates as $calid => $cal) {

        $description = $cal->GetFieldValue('description');
        $entrytypedesc = $cal->GetFieldValue('entrytypedesc');
        $dates = $cal->FormatDisplayTimes();
        $list[$calid] = array(
          'value' => $description,
          'content' => "<p><strong>{$entrytypedesc}</strong></p><p>{$dates}</p>",
          'title' => 'click to view ' . $description
        );
    
      }
      $ret = $this->MakeSidebarList('Important Dates', $list, self::AQ_CALENDARDATEID);
    } else {
      $ret = array();
    }
    return $ret;
  }

  // BOOKINGS SIDEBAR
  protected function GetBookingSidebar() {
    $ret = array(); //array('<p>TODO: Bookings</p>');
    return $ret;
  }

  // PRIVATE AREAS - SIDEBAR
  protected function GetPrivateAreaSidebar() {
    $ret[] = '<h2>Private Area Login</h2>';
    $ret[] = "<form class='login' name='frmprivatelogin'>";
    $ret[] = "  <label for='username'>Username";
    $ret[] = "  <input name='username' type='editbox'></label>";
    $ret[] = "  <label for='password'>Password";
    $ret[] = "  <input name='password' type='password'></label>";
    $ret[] = "  <input name='handler' type='hidden' value='" . self::PFH_PRIVATEAREA . "'>";
    $ret[] = "  <input name='submit' type='submit' value='Login'>";
    $ret[] = '</form>';
    return $ret;
  }

  // DOWNLOADABLE FILES - SIDEBAR
  protected function GetDownloadableFilesSidebar() {
    $ret = array();
    if ($this->page->GetFieldValue('showfiles')) {
      $showimg = true;
      $islistitem = false;
      $linkprefix = '';
      $filelist = new fileitem();
      $list = $filelist->GetCurrentList($showimg, $islistitem, $linkprefix);
      if ($list) {
        $script = $this->sourcepath . DIRECTORY_SEPARATOR . "downloadfile.php?rid=";
        $ret[] = '<h3>Downloable Files</h3>';
        $ret[] = '<ul>';
        foreach ($list as $fileid => $filedetails) {
          $title = $filedetails['TITLE'];
  //        $filename = "<a href='files/" . $filedetails['DESC'] . "' title='click to download {$title}'>{$title}</a>";
          $filename = "<a href='{$script}{$fileid}' title='click to download {$title}'>{$title}</a>";
  //        $icon = $filedetails['FILETYPE'];
          $icon = $filedetails['IMAGE'];
          $filesize = $filedetails['FILESIZE'];
          $ret[] = "<li>{$icon}{$filename} (<em>{$filesize}</em>)</li>";
        }
        $ret[] = '</ul>';
      }
    }
    return $ret;
  }

  // ??
  static public function ShowGallery($galleryid) {
    $gallery = new gallery($galleryid);
    echo $gallery->BuildGallery(self::GetInstance()->pageid);
  }
}

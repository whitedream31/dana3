<?php
// section processor for the page writer for MyLocalSmallBusiness
// written by Ian Stewart (c) 2012 Whitedream Software
// created: 22 nov 2012
// modified: 4 oct 2013

//require_once('library.php');
//require_once('pagewriter.php');
//require_once('galleryclass.php');
//require_once('newsletterclass.php');

// <head>
define('SCT_TITLE', 'title');
define('SCT_KEYWORDS', 'keywords');
define('SCT_METADESCRIPTION', 'description');
//  heading area
define('SCT_NAVIGATION', 'navigation');
define('SCT_LOGO', 'logo');
define('SCT_HEADER', 'header');
define('SCT_TAGLINE', 'tagline');
define('SCT_INITIALCONTENT', 'initialcontent');
define('SCT_MAINCONTENT', 'maincontent');
define('SCT_ARTICLES', 'articles');
define('SCT_GUESTBOOK', 'guestbook');
define('SCT_NEWSLETTERS', 'newsletters');
define('SCT_SOCIALNETWORKS', 'socialnetworks');
define('SCT_TRANSLATION', 'translation');
define('SCT_RSSFEED', 'rssfeed');
define('SCT_CONTACTDETAILS', 'contactdetails');
//define('SCT_DOWNLOADABLEFILES', 'downloadablefiles');
define('SCT_SIDECONTENT', 'sidecontent');
define('SCT_FOOTER', 'footer');
define('SCT_ADDTHIS', 'addthis');
define('SCT_METALINKS', 'metalinks');
define('SCT_SCRIPT', 'script');
define('SCT_ADVERT', 'advert');

// id name of sections
define('HTMLID_LOGO', 'logoimg'); // business logo
define('HTMLID_NAVIGATION', 'menu'); // navigation (menu) of pages
define('HTMLID_INITIALCONTENT', 'inittext');
define('HTMLID_MAINCONTENT', 'maintext');
define('HTMLID_ARTICLESIDEBAR', 'artsidebar');
define('HTMLID_GUESTBOOKSIDEBAR', 'guestbooksidebar');
define('HTMLID_NEWSLETTERSIDEBAR', 'newslettersidebar');
define('HTMLID_SOCIALNETWORKSIDEBAR', 'socialnetworksidebar');
define('HTMLID_TRANSLATION', 'translation');
define('HTMLID_RSSFEED', 'rssfeed');
define('HTMLID_CONTACTDETAILS', 'contactdetails');
define('HTMLID_DOWNLOADABLEFILES', 'downloadablefiles');
define('HTMLID_SIDECONTENT', 'sidetext');
define('HTMLID_PAGEFOOTER', 'pagefooter');
define('HTMLID_ADDTHIS', 'addthis');

abstract class sectionprocessor {
  protected $pagewriter;
  protected $mode;
  protected $rootpath;
  protected $sourcepath;
  protected $page;
  protected $account;
  protected $accountid;
  protected $contact;
  protected $pagelist;
  protected $pagecount;
  
  protected $lastgalleryid;
  protected $lastgallery;
  protected $newsletterlist;
  private $sectiontag;
  private $sectionclass;

  // caches
  protected static $cache_logo;
  protected static $cache_newsletter;
//  private $downloadablefiles;
//  private $addthis;
  protected static $cache_socialnetwork;
  protected static $cache_calendarsidebar;
  protected static $cache_bookingsidebar;
  protected static $cache_privateareasidebar;
  protected static $cache_downloadablefiles;
  protected static $cache_addthis;  

  function __construct($pagewriter) {
    $this->pagewriter = $pagewriter;
    $this->mode = $pagewriter->mode;
    $this->rootpath = $pagewriter->rootpath; // root destination path
    $this->sourcepath = $pagewriter->sourcepath; // scripts path
    $this->page = $pagewriter->currentpage;
    $this->pagelist = $pagewriter->pagelist->pages;
    $this->pagecount = count($this->pagelist);
    $this->account = $this->pagewriter->account;
    $this->contact = $pagewriter->contact;
  }

  abstract protected function FetchPreparationCode();
  abstract protected function FetchMetaLinks();
  abstract protected function FetchScript();

  protected function FetchMainContent() {
    return $this->page->GetFieldValue('maincontent');
  }

  protected function FetchInitialContent() {
    return $this->page->GetFieldValue('initialcontent');
  }

  private function MakeTag($content, $tag = '', $idname = '', $class = '') {
    if (IsBlank($content)) {
      $ret = false;
    } else {
      if (!$tag) {
        $tag = $this->sectiontag;
      }
      if ($tag) {
        $ret = '<' . $tag;
        if (!$class) {
          $class = $this->sectionclass;
        }
        if ($idname) {
          $ret .= " id='{$idname}'";
        }
        if ($class) {
          $ret .= " class='{$class}'";
        }
        $ret .= ">{$content}</{$tag}>";
      } else {
        $ret = $content;
      }
    }
    return $ret;
  }

  private function IncludePublishStateCode() {
    //global $rootpath;
    $path = $this->sourcepath;
    $ret = array();
//    $ret[] = '/* check for publishing state */';
    $ret[] = "require_once '" . $path . "class.websitemanager.php';";
    $ret[] = 'websitemanager::SetAccount(' . $this->account->ID() . ');';
    return $ret;
  }

  private function IncludeStatsCode() {
//    $path = $this->sourcepath;
    $pageid = $this->page->ID();
    $pagemgrid = $this->account->GetFieldValue('pagemgrid');
    $ret = array();
    $ret[] = '/* collect statistics */';
//    $ret[] = "require_once '{$path}scripts" . DIRECTORY_SEPARATOR . "statistics.php';";
    $ret[] = "websitemanager::ProcessPageStats({$pagemgrid}, {$pageid});" . CRNL . CRNL;
    return $ret;
  }

  private function IncludeAdvertPrepareCode() {
    $ret = array();
    // only show adverts when live
    if (($this->mode == PWMODE_LIVE) && $this->account->GetFieldValue('showadverts')) {
      $path = PDIR;
//      $ret[] = '/* advert */';
      $ret[] = "websitemanager::ShowAdvert()";
/*      $ret[] = "require_once('{$path}scripts/advertclass.php');";
      $ret[] = 'function AddAdvertInPage() {';
      $ret[] = '  $advertitem = new advertitem();';
      $ret[] = '  $advert = $advertitem->GetRandomAdvert();';
      $ret[] = '  if ($advert) {';
      $ret[] = '    echo $advert->ShowContent();';
      $ret[] = '  }';
      $ret[] = '}'; */
    }
    return $ret;
  }

  // retrieve php code added at the start of a page (preprocessing)
  public function RetrievePreparationCode() {
    // write the public state code
    $ret = $this->IncludePublishStateCode();
    if ($this->mode != PWOPT_PROFILE) {
      // write the statistics code
      $ret[] = $this->IncludeStatsCode();
      // add advert code
      $ret = array_merge($ret, $this->IncludeAdvertPrepareCode());
    }
    $ret[] = $this->FetchPreparationCode();
    return $ret;
  }

  // main method to retrieve section
  public function RetrieveSection($sectioname, $tag = '', $class = '') {
    $this->sectiontag = $tag;
    $this->sectionclass = $class;
    switch ($sectioname) {
      case SCT_TITLE:
        $ret = $this->GetSectionPageTitle();
        break;
      case SCT_KEYWORDS:
        $ret = $this->GetSectionKeywords();
        break;
      case SCT_METADESCRIPTION:
        $ret = $this->GetSectionMetaDescription();
        break;
      case SCT_NAVIGATION:
        $ret = $this->GetSectionNavigation();
        break;
      case SCT_LOGO:
        $ret = $this->GetSectionLogo();
        break;
      case SCT_HEADER:
        $ret = $this->GetSectionHeader();
        break;
      case SCT_TAGLINE:
        $ret = $this->GetSectionTagline();
        break;
      case SCT_INITIALCONTENT:
        $ret = $this->GetSectionInitialcontent();
        break;
      case SCT_MAINCONTENT:
        $ret = $this->GetSectionMainContent();
        break;
//      case SCT_ARTICLES:
//        $ret = $this->GetSectionArticles();
//        break;
//      case SCT_GUESTBOOK:
//        $ret = $this->GetSectionGuestbook();
//        break;
//      case SCT_NEWSLETTERS:
//        $ret = $this->GetSectionNewsletters();
//        break;
//      case SCT_SOCIALNETWORKS:
//        $ret = $this->GetSectionSocialNetworks();
//        break;
      case SCT_TRANSLATION:
        $ret = $this->GetSectionTranslation();
        break;
      case SCT_RSSFEED:
        $ret = $this->GetSectionRSSFeed();
        break;
      case SCT_CONTACTDETAILS:
        $ret = $this->GetSectionContactDetails();
        break;
//      case SCT_DOWNLOADABLEFILES:
//        $ret = $this->GetSectionDownloadableFiles();
//        break;
      case SCT_SIDECONTENT:
        $ret = $this->GetSectionSideContent();
        break;
      case SCT_FOOTER:
        $ret = $this->GetSectionFooter();
        break;
      case SCT_ADDTHIS:
        $ret = $this->GetSectionAddthis();
        break;
      case SCT_METALINKS:
        $ret = $this->GetSectionMetalinks();
        break;
      case SCT_SCRIPT:
        $ret = $this->GetSectionScript();
        break;
      case SCT_ADVERT:
        $ret = $this->GetSectionAdvert();
        break;
      default:
        $ret = false;
    }
    return $ret;
  }

  // page title
  private function GetSectionPageTitle() {
    return $this->account->GetFieldValue('businessname') . ' - ' . $this->page->GetFieldValue('description');
  }

  // meta keywords
  private function GetSectionKeywords() {
    return $this->account->GetFieldValue('metakeywords');
  }

  // meta description
  private function GetSectionMetaDescription() {
    return $this->account->GetFieldValue('metadescription');
  }

  // navigation - set of available pages
  private function GetSectionNavigation() {
    return $this->DoNavigation();
  }

  private function GetSectionLogo() {
    return $this->DoLogo();
  }

  private function GetSectionHeader() {
    return $this->MakeTag($this->page->GetFieldValue('header'));
  }

  private function GetSectionTagline() {
    return $this->MakeTag($this->account->GetFieldValue('tagline'));
  }

  private function GetSectionInitialcontent() {
    return $this->DoInitialContent();
  }

  private function GetSectionMainContent() {
    return $this->ProcessMainContent();
  }

//  private function GetSectionArticles() {
//    return $this->DoArticlesSidebar();
//  }

//  private function GetSectionGuestBook() {
//    return $this->DoGuestBookSidebar();
//  }

//  private function GetSectionNewsletters() {
//    return $this->DoNewslettersSidebar();
//  }

  private function GetBlogSideContent() {
    return "<?php websitemanager::ShowBlogSideContent(); ?>";
  }

//  private function GetSectionSocialNetworks() {
//    $ret = '';
//    if ($this->page->incsocialnetwork) {
//      $ret = $this->DoSocialNetworkSidebar();
//    }
//    return $ret;
//  }

  private function GetSectionTranslation() {
    $ret = '';
    if ($this->page->GetFieldValue('includetranslation')) {
      $ret = $this->DoGoogleTranslation();
    }
    return $ret;
  }

  private function GetSectionRSSFeed() {
    return '';
/*    $ret = '';
    if ($this->page->includerss) {
      $ret = $this->DoRSSPage();
    }
    return $ret; */
  }

  private function GetSectionContactDetails() {
    return $this->MakeTag(
      $this->page->GetContactInfo(), '', HTMLID_CONTACTDETAILS);
  }

//  private function GetSectionDownloadableFiles() {
//    $ret = '';
//    if ($this->page->showfiles) {
//      $ret = $this->BuildDownloadableFiles();
//    }
//    return $ret;
//  }

  private function GetSectionSideContent() {
//    $list = array();
    $list = StringToArray($this->MakeTag($this->page->GetSidebarContent(), '', HTMLID_SIDECONTENT));
    $list[] = $this->DoArticlesSidebar();
    $list[] = $this->DoGuestBookSidebar();
    $list[] = $this->DoNewslettersSidebar();
    if ($this->page->GetFieldValue('incsocialnetwork')) {
      $list[] = $this->DoSocialNetworkSidebar();
    }
    $list[] = $this->DoCalendarSidebar();
    $list[] = $this->DoBookingSidebar();
    $list[] = $this->DoPrivateAreaSidebar();
    $list[] = $this->GetBlogSideContent(); // $this->DoSurveySidebar()
    if ($this->page->GetFieldValue('showfiles')) {
      $list[] = $this->GetDownloadableFiles();
    }
    return ArrayToString($list);
  }

  private function GetSectionFooter() {
    return $this->MakeTag(
      $this->page->GetFieldValue('footer') . "\n" .
      "<p class='designname'>designed by <a title='Free CSS Templates' href='http://www.freecsstemplates.org'>Free CSS Templates</a>. " .
      "<small>Modified by <a title='whitedream software' href='http://whitedreamsoftware.co.uk'>Whitedream Software</a>.</small> " .
      "&mdash; <a title='click to sign up' href='http://mylocalsmallbusiness.com'><strong>FREE Mini Websites</strong></a></p><br>\n",
      '', HTMLID_PAGEFOOTER);
  }

  private function GetSectionAddthis() {
    return $this->DoAddThis();
  }

  private function GetSectionMetalinks() {
    return $this->ProcessMetaLinks();
  }

  private function GetSectionScript() {
    return $this->ProcessScript();
  }

  private function GetSectionAdvert() {
    return $this->AddAdvert();
  }

  private function FindNewsletterList() {
    $ret = newsletter::FindNewslettersByAccount($this->account->ID());
    return $ret;
  }

//  private function FindDownloadableFiles() {
//    if (!$this->downloadablefiles) {
//      $this->downloadablefiles = $this->BuildDownloadableFiles();
//    }
//    return $this->downloadablefiles;
//  }
  
  private function GetDownloadableFiles() {
    if (!self::$cache_downloadablefiles) {
      $accountid = $this->account->ID();
      $query =
        "SELECT `id` FROM `fileitem` " .
        "WHERE `accountid` = {$accountid} ORDER BY `stampupdated`";
      $result = database::Query($query);
//      $path = PDIR;
      $iconpath = CDN_PATH . 'images' . DIRECTORY_SEPARATOR;
//      $iconpath = str_replace ('\\', '/', $iconpath);
      $list = array();
      while ($line = $result->fetch_assoc()) {
        $id = $line['id'];
        $fileitem = new fileitem($id);
        $title = $fileitem->GetFieldValue('title');
        $description = $fileitem->GetFieldValue('description');
        $icon = $fileitem->GetFileIconURL($iconpath);
        $filename = $fileitem->GetFieldValue('filename');
        $url = 'media' . DIRECTORY_SEPARATOR . $filename;
        $ln = "<a href='{$url}' target='_self' title='click to download {$description}'><img src='{$icon}' alt='' />&nbsp;{$title}</a>";
        $list[] = "    <li class='fileitem'>{$ln}</li>";
      }
      $result->close();
      if (count($list)) {
        $ret = array();
        $ret[] = "  <h2>Downloads</h2>";
        $ret[] = "  <ul>";
        $ret = array_merge($ret, $list);
        $ret[] = "  </ul>";
        $ret = $this->MakeTag(ArrayToString($ret), 'div', HTMLID_DOWNLOADABLEFILES);
      } else {
        $ret = false;
      }
      self::$cache_downloadablefiles = $ret;
    }
    return self::$cache_downloadablefiles;
  }

  protected function AddScriptForGallery() { //$value) {
    return "<script type='text/javascript' src='" . CDN_PATH . "rotator.js'></script>\n";
  }

  private function AddScriptForGoogleAnalytics() {
    return
      "<script type='text/javascript'>\n" .
      "  var _gaq = _gaq || [];\n" .
      "  _gaq.push(['_setAccount', 'UA-20300263-6']);\n" .
      "  _gaq.push(['_trackPageview']);\n" .
      "  (function() {\n" .
      "    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;\n" .
      "    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';\n" .
      "    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);\n" .
      "  })();\n" .
      "</script>\n";
  }

  private function AddAdvert() {
    $ret = '';
    if ($this->account->GetFieldValue('showadverts') && ($this->mode == PWMODE_LIVE)) {
      $ret = $this->MakeTag(
        "<?php\n  AddAdvertInPage();\n?>\n", 'div', '', 'advert');
    }
    return $ret;
  }

  protected function ProcessMainContent() {
    $ret = $this->FetchMainContent();
    return $this->MakeTag($ret, '', HTMLID_MAINCONTENT);
  }

  // other css and js files
  private function ProcessMetaLinks() {
    return "<link href='" . CDN_PATH . "css/shared.css' rel='stylesheet' type='text/css' media='all' />\n" .
      $this->FetchMetaLinks();
  }

  private function ProcessScript() {
    $ret = $this->FetchScript();
    if ($this->mode != PWOPT_PROFILE) {
      $ret .= $this->AddScriptForGoogleAnalytics();
    }
    return $ret;
  }

  private function DoNavigation() {
    if ($this->pagecount > 1) {
      $nav = "<ul>\n";
      $cnt = 0;
      foreach($this->pagelist as $page) {
        $url = $page->GetFieldValue('name') . '.php';
        $class = '';
        $cnt++;
        // work out any required class names
        if ($cnt == 1) {
          $class = 'first';
        }
        if ($cnt == $this->pagecount) {
          $class .= ' last';
        }
        $active = trim($class . ' ' . ($page->ID() == $this->page->ID()) ? 'active' : '');
        if ($active) {
          $active = " class='{$active}'";
        }
        // add the entire page link as a list item
        $nav .= "  <li{$active}><a href='{$url}'>{$page->GetFieldValue('description')}</a></li>\n";
      }
      $nav .= "</ul>";
      $ret = $this->MakeTag($nav, 'nav', HTMLID_NAVIGATION, $this->sectionclass);
    } else {
      $ret = '';
    }
    return $ret;
  }

  private function DoLogo() {
    if (!self::$cache_logo) {
      self::$cache_logo = account::GetImageFilename($this->account->GetFieldValue('logomediaid'), false);
      if (self::$cache_logo) {
        $businessname = $this->account->GetFieldValue('businessname');
        $img = "<img alt='{$businessname}' src='media/" . self::$cache_logo . "' />";
        $website = strtolower($this->account->GetFieldValue('website'));
        if ($website) {
          if (substr($website, 0, 4) != 'http') {
            $website = 'http://' . $website;
          }
          $url = "<a href='{$website}' target='_blank' title='visit our main website'>{$img}</a>";
        } else {
          $url = $img;
        }
        self::$cache_logo = $this->MakeTag($url, 'div', HTMLID_LOGO, $this->sectionclass);
      }
    }
    return self::$cache_logo;
  }

  private function DoInitialContent() {
    $ret = $this->FetchInitialContent();
    return $this->MakeTag($ret, '', HTMLID_INITIALCONTENT);
  }

//  private function DoProductList() {
//    return "<?php ShowProducts({$this->account->ID()}, {$this->page->ID()});
//  }

  private function DoArticlesSidebar() {
    $ret = '(todo: list of recent articles)'; // TODO: list of recent articles
    return $this->MakeTag($ret, '', HTMLID_ARTICLESIDEBAR);
  }

  private function DoGuestBookSidebar() {
    $guestbook = ($this->page->pgtype == PAGETYPE_GUESTBOOK) ? "<?php ShowGuestBookSideContent(); ?>\n" : false;
    $ret = ($guestbook) ? $this->MakeTag($guestbook, '', HTMLID_GUESTBOOKSIDEBAR) : false;
    return $ret;
  }

  private function DoNewslettersSidebar() {
    if (!self::$cache_newsletter) {
      $ret = false;
      $this->newsletterlist = $this->FindNewsletterList();
      if (count($this->newsletterlist)) {
        $list = array();
        $list[] = "  <h2>Newsletters</h2>";
        $list[] = "  <ul>";
        foreach($this->newsletterlist as $nlid) {
          $nl = new newsletter($nlid);
          $url = $this->sourcepath . "viewnewsletter.php?rid=" . $nlid;
          $href = "href='{$url}'";
          $title = $nl->GetFieldValue('title');
          $imgurl = CDN_PATH . "images" . DIRECTORY_SEPARATOR . "newslettersm.png";
          $imgsrc = "src='{$imgurl}'";
          $img = "<img {$imgsrc} alt='newsletter'>";
          $link = $href . " title='read newsletter'";
          
          $desc = "<span>{$title} <small>({$nl->showdatedescription})</small></span>";
          $list[] = "    <li><a {$link} target='_blank'>{$img}{$desc}</a></li>";
        }
        $list[] = "  </ul>";
        $ret = $this->MakeTag(ArrayToString($list), 'div', HTMLID_NEWSLETTERSIDEBAR);
        self::$cache_newsletter = $ret;
      } else {
        $ret = '<p>TODO: link to subscribe to newsletters';
      }
    } else {
      $ret = self::$cache_newsletter;
    }
    return $ret;
  }

  private function DoSocialNetworkSidebar() {
    if (!self::$cache_socialnetwork) {
      $snlist = socialnetworkcontact::FindSocialNetworksForAccount($this->account->ID());
      if ($snlist) {
        $list = array();
        $list[] = "  <h2>Social Networks</h2>";
        $list[] = "  <ul>";
        foreach($snlist as $snid => $sndetails) {
          $sn = new socialnetworkcontact($snid);
          $sndesc = $sndetails['desc'];
          $url = $sn->GetFieldValue('url');
          if (!(strpos('http', $url) === false)) {
            $url = 'http://' . $url;
          }
          $href = "href='{$url}'";
          $title = " title='visit us on {$sndesc}'";
          $icon = $sndetails['icon']; //$sn->GetFieldValue('icon');
          $imgsrc = "src='" . CDN_PATH . "images/social/16x16/{$icon}'";
          $img = "<img {$imgsrc} alt='{$sndesc}' />";
          $list[] =
            "    <li>" .
            "<a {$href}{$title} target='_blank'>{$img}" .
            "<span>Visit us on {$sndesc}</span></a>\n" .
            "</li>";
        }
        $list[] = "  </ul>\n";
        $ret = $this->MakeTag(ArrayToString($list), 'div', HTMLID_SOCIALNETWORKSIDEBAR);
      } else {
        $ret = false;
      }
      self::$cache_socialnetwork = $ret;
    }
    return self::$cache_socialnetwork;
  }

  private function DoCalendarSidebar() {
    if (!self::$cache_calendarsidebar) {
      $ret = '<p>TODO: Calendar Sidebar</p>'; // TODO: add php code to list calendar items (sidebar)
      self::$cache_calendarsidebar = $ret;
    }
    return self::$cache_calendarsidebar;
  }

  private function DoBookingSidebar() {
    if (!self::$cache_bookingsidebar) {
      $ret = '<p>TODO: Booking Sidebar</p>'; // TODO: add php code to offer a booking link
      self::$cache_bookingsidebar = $ret;
    }
    return self::$cache_bookingsidebar;
  }

  private function DoPrivateAreaSidebar() {
    if (!self::$cache_privateareasidebar) {
      $ret = '<p>TODO: Private Area Sidebar</p>'; // TODO: add php code to either show login to private area or (if logged in) show list of private area pages (menu)
      self::$cache_privateareasidebar = $ret;
    }
    return self::$cache_privateareasidebar;
  }

  private function DoGoogleTranslation() {
    $ret =
      "\n<h2>Translate</h2>\n" .
      "<div id=\"google_translate_element\"></div>\n" .
      "<script>\n" .
      "  function googleTranslateElementInit() {\n" .
      "    new google.translate.TranslateElement({\n" .
      "      pageLanguage: 'en',\n" .
      "      autoDisplay: false,\n" .
      "      gaTrack: true,\n" .
      "      gaId: 'UA-20300263-6'\n" .
      "    }, 'google_translate_element');\n" .
      "  }\n" .
      "</script><script src=\"//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit\"></script>\n";
    return $this->MakeTag($ret, 'div', HTMLID_TRANSLATION);
  }

  private function DoRSSPage() {
    $exists = file_exists($this->pagewriter->rssfeedfilename);
    if (($this->pagecount > 2) && $exists) {
      $rss =
        "\n  <h2>RSS Feeds</h2>\n" .
        "  <a href=\"{$this->pagewriter->rssfeedfilename}\" target=\"_self\" title=\"page rss feed\">\n" .
        "    <img src=\"{$this->rootpath}images/rss.png\" alt=\"Page RSS Feed\" /><span>Page Feed</span>\n" .
        "  </a>\n";
      $ret = $this->MakeTag($rss, 'div', HTMLID_RSSFEED);
    } else {
      $ret = '';
    }
    return $ret;
  }

  private function DoAddThis() {
    if (!self::$cache_addthis) {
      $socialnetworkid = (int) $this->page->GetFieldValue('socialnetwork');
      $query = 'SELECT `content` FROM `socialnetwork` WHERE `id` = ' . $socialnetworkid;
      $result = database::Query($query);
      $line = $result->fetch_assoc();
      $result->close();
      if ($line) {
        $ret = $this->MakeTag(
          "  <h2>Share This Page</h2>\n" . $line['content'] . "\n", 'div', HTMLID_ADDTHIS, 'addthis');
      } else {
        $ret = false;
      }
      self::$cache_addthis = $ret;
    }
    return self::$cache_addthis;
  }

  private function DoSocialNetworkActivityContent() {
    $ret = ''; // TODO: add social network activity in main area
    return $ret;
  }

//  private function IncludeAboutUsCheckCode() {
//  }

  private function IncludeProductPrepareCode() {
    switch ($this->mode) {
      case PWOPT_PROFILE:
        $ret = "<?php require('{$this->rootpath}scripts" . DIRECTORY_SEPARATOR . "client.profile.product.php');\n";
        break;
      case PWOPT_LIVE:
        $ret = "<?php require('{$this->rootpath}scripts" . DIRECTORY_SEPARATOR . "client.live.product.php');\n";
        break;
    }
//    $pageid = $this->page->ID();
//    $accid = $this->accountid;
    return $ret;
  }

}

// GENERAL PAGE processor
class sectionprocessorgeneral extends sectionprocessor {

  private function DoGallerySlideShow() {
    if ($this->lastgalleryid == $this->page->GetFieldValue('gengalleryid')) {
      $ret = $this->lastgallery;
    } else {
      $gallery = new gallery($this->page->GetFieldValue('gengalleryid'));
//      $galleryheight = $gallery->GetGalleryHeight();
      $ret = $gallery->BuildSlideShowList(); //$galleryheight);
      $this->lastgalleryid = $this->page->GetFieldValue('gengalleryid');
      $this->lastgallery = $ret;
    }
    return ArrayToString($ret);
  }

/*  private function AddScriptForImageRotator() {
    return
      "<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js'></script>\n" .
      "  <!-- By Dylan Wagstaff, http://www.alohatechsupport.net -->\n" .
      "  <script type='text/javascript'>\n" .
      "  function theRotator() {\n" .
      //"    //Set the opacity of all images to 0\n" .
      "    \$('div.rotator ul li').css({opacity: 0.0});\n" .
      //"    //Get the first image and display it (gets set to full opacity)\n" .
      "    \$('div.rotator ul li:first').css({opacity: 1.0});\n" .
      //"    //Call the rotator function to run the slideshow, 6000 = change to next image after 6 seconds\n" .
      "    setInterval('rotate()',6000);\n" .
      "  }\n" .
      "  function rotate() {\n" .
      //"    //Get the first image\n" .
      "    var current = (\$('div.rotator ul li.show')?  \$('div.rotator ul li.show') : \$('div.rotator ul li:first'));\n" .
      "      if ( current.length == 0 ) current = \$('div.rotator ul li:first');\n" .
      //"    //Get next image, when it reaches the end, rotate it back to the first image\n" .
      "    var next = ((current.next().length) ? ((current.next().hasClass('show')) ? \$('div.rotator ul li:first') :current.next()) : \$('div.rotator ul li:first'));\n" .
      //"    //Un-comment the 3 lines below to get the images in random order\n" .
      //"    //var sibs = current.siblings();\n" .
      //"      //var rndNum = Math.floor(Math.random() * sibs.length );\n" .
      //"      //var next = $( sibs[ rndNum ] );\n" .
      //"    //Set the fade in effect for the next image, the show class has higher z-index\n" .
      "    next.css({opacity: 0.0})\n" .
      "    .addClass('show')\n" .
      "    .animate({opacity: 1.0}, 1500);\n" .
      //"    //Hide the current image\n" .
      "    current.animate({opacity: 0.0}, 1500)\n" .
      "    .removeClass('show');\n" .
      "  };\n" .
      "  \$(document).ready(function() {\n" .
      //"    //Load the slideshow\n" .
      "    theRotator();\n" .
      "    \$('div.rotator').fadeIn(1500);\n" .
      "      \$('div.rotator ul li').fadeIn(1500); // tweek for IE\n".
      "  });\n" .
      "</script>\n";
  } */

  protected function FetchPreparationCode() {
    return '';
  }

  protected function FetchMainContent() {
    $gallery = ($this->page->GetFieldValue('gengalleryid') > 0) ? $this->DoGallerySlideShow() : '';
    $content = $this->page->GetFieldValue('maincontent');
    return $gallery . $content;
  }

  protected function FetchMetaLinks() {
    return ($this->page->GetFieldValue('gengalleryid') > 0)
      ?
        "  <link href='" . CDN_PATH . "css/lightbox.css' rel='stylesheet' type='text/css' media='screen' />\n" .
        "  <script src='" . CDN_PATH . "jquery.js' type='text/javascript'></script>\n" .
        "  <script src='" . CDN_PATH . "lightbox.js' type='text/javascript'></script>\n"
      : '';
  }

  protected function FetchScript() {
    return ($this->page->GetFieldValue('gengalleryid') > 0)
      ? $this->AddScriptForGallery() //'div.rotator ul li.show')
//      ? $this->AddScriptForImageRotator() . $this->AddScriptForGallery() //'div.rotator ul li.show')
      : '';
  }

}

// CONTACT PAGE processor
class sectionprocessorcontact extends sectionprocessor {

  // preparation for contact page
  private function IncludeContactCheckCode() {
    switch ($this->mode) {
      case PWOPT_PROFILE:
        $ret = "<?php require '{$this->sourcepath}client.profile.contact.php';\n";
        break;
      case PWOPT_LIVE:
        $ret = "<?php require '{$this->sourcepath}client.live.contact.php';\n";
        break;
    }
    return $ret;
  }

  private function DoContactForm() {
    $pagename = $this->page->GetFieldValue('pagename');
    $contactname = $this->page->GetFieldValue('contactname');
    $contactemail = $this->page->GetFieldValue('contactemail');
    $contactsubject = $this->page->GetFieldValue('contactsubject');
    $contactmessage = $this->page->GetFieldValue('contactmessage');
    return
      "<?php echo \$response; ?>\n" .
      "<form name='formcontact' method='post' enctype='application/x-www-form-urlencoded' action='{$pagename}.php'>\n" .
      "  <fieldset class='contact'>\n" .
      "    <div>\n" .
      "      <label for='contactname'>{$contactname}</label>\n" .
      "      <br><input class='contactinput' name='contactname' type='text' value='<?php echo \$contactname; ?>' maxlength='100' />\n" .
      "    </div>\n" .
      "    <br>\n" .
      "    <div>\n" .
      "      <label for='contactemail'>{$contactemail}</label>\n" .
      "      <br><input class='contactinput' name='contactemail' type='text' value='<?php echo \$contactemail; ?>' maxlength='100' />\n" .
      "    </div>\n" .
      "    <br>\n" .
      "    <div>\n" .
      "      <label for='contactsubject'>{$contactsubject}</label>\n" .
      "      <br><input class='contactinput' name='contactsubject' type='text' value='<?php echo \$contactsubject; ?>' maxlength='100' />\n" .
      "    </div>\n" .
      "    <br>\n" .
      "    <div>\n" .
      "      <label for='contactmessage'>{$contactmessage}</label>\n" .
      "      <br><textarea class='contactinput' name='contactmessage' rows='20' cols='80'><?php echo \$contactmessage; ?></textarea>\n" .
      "    </div>\n" .
      "    <br>\n" .
      "    <div>\n" .
      "      <?php \$question = new questionmanager(); echo \$question->ShowQuestion('{$this->rootpath}questions'); ?>\n" .
      "    </div>\n" .
      "    <br>\n" .
      "    <div class='button'>\n" .
      "      <input type='reset' name='btnclear' title='clear fields' value='Clear' />\n" .
      "      <input type='submit' name='btnsubmit' title='send message' value='Send' />\n" .
      "    </div>\n" .
      "  </fieldset>\n" .
      "</form>\n";
  }

  private function DoMap() {
    $addr = $this->page->GetFieldValue('mapaddress');
    if (!$addr) {
      $addr = $this->contact->FullAddress('', ' ');
    }
    $addrarray = explode('+', urlencode(str_replace('  ', ' ', $addr)));
    $q = implode(',+', $addrarray);
    $ret = "<iframe width='400' height='400' frameborder='1' scrolling='no' marginheight='0' marginwidth='0' " .
      "src='http://maps.google.co.uk/maps?q={$q},+uk&amp;ie=UTF8&amp;hq=&amp;hnear={$q},+uk&amp;t=m&amp;z=16&amp;vpsrc=0&amp;output=embed'></iframe><br>\n";
    return $ret;
  }

  protected function FetchPreparationCode() {
    return $this->IncludeContactCheckCode();
  }

  protected function FetchMainContent() {
    return $this->DoContactForm() . $this->DoMap();
  }

  protected function FetchMetaLinks() {
    return '';
  }

  protected function FetchScript() {
    return '';
  }
}

// GALLERY PAGE processor
class sectionprocessorgallery extends sectionprocessor {

  private function WriteGalleryPrepareCode() {
/*    $pageid = $this->page->ID();
    $rootdirectory = $this->pagewriter->rootdirectory;
    return ArrayToString(array(
      "require_once('{$this->sourcepath}class.table.gallery.php');",
      "",
//      $this->AddGetVar('pg', 'pagenumbergallery', '1') .
      "\$mode = {$this->mode};",
      "\$rootdirectory = '{$rootdirectory}';",
      "",
      "function ShowGallery(\$galleryid) {",
      "  \$gallery = new gallery(\$galleryid);",
      "  echo \$gallery->BuildGallery({$pageid});",
      "}"
    )); */
  }

  private function DoGallery($galleryid) {
    return "<?php websitemanager::ShowGallery({$galleryid}); ?>\n";
  }

  protected function FetchPreparationCode() {
    return $this->WriteGalleryPrepareCode();
  }

  protected function FetchMainContent() {
    return $this->DoGallery($this->page->GetFieldValue('groupid'));
  }

  protected function FetchMetaLinks() {
    return
      "  <link href='" . CDN_PATH . "lightbox.css' rel='stylesheet' type='text/css' media='screen'/>\n" .
      "  <script src='" . CDN_PATH . "jquery.js'></script>\n" .
      "  <script src='" . CDN_PATH . "lightbox.js'></script>\n";
  }

  protected function FetchScript() {
    return $this->AddScriptForGallery(); //'#gallery a');
  }

}


// ARTICLES PAGE processor
class sectionprocessorarticles extends sectionprocessor {

  private function WriteBlogPrepareCode() {
    $pageid = $this->page->ID();
    $accountid = $this->account->ID();
    return ArrayToString(array(
//      "/* blog prepare */\n" .
      "require_once '{$this->sourcepath}class.table.article.php';",
      "",
      "\$blog = new blog({$accountid}, {$pageid});",
      "",
      "function ShowBlog() {",
      "  global \$blog;",
      "  echo \$blog->BuildPage();",
      "}",
      "",
      "function ShowBlogSideContent() {",
      "  global \$blog;",
      "  echo \$blog->BuildSideContent();",
      "}"
    ));
  }

  private function DoArticleContent() {
    return "<?php ShowBlog(); ?>\n";
  }

  protected function FetchPreparationCode() {
    return $this->WriteBlogPrepareCode();
  }

  protected function FetchMainContent() {
    return $this->DoArticleContent();
  }

  protected function FetchMetaLinks() {
    return '';
  }

  protected function FetchScript() {
    return '';
  }

}


// GUESTBOOK PAGE processor
class sectionprocessorguestbook extends sectionprocessor {

  private function WriteGuestBookPrepareCode() {
    $pageid = $this->page->ID();
    $accountid = $this->account->ID();
    return ArrayToString(array(
//      "/* guest book prepare */",
      "require_once '{$this->sourcepath}class.table.guestbook.php';",
      "require_once '{$this->sourcepath}class.table.visitor.php';",
      "require_once '{$this->sourcepath}class.table.question.php';",
      "",
      "\$guestbook = new guestbookprocess({$accountid}, {$pageid});",
      "",
      "function ShowGuestBook() {",
      "  global \$guestbook;",
      "  echo \$guestbook->BuildPage('{$this->rootpath}');",
      "}",
      "",
      "function ShowGuestBookSideContent() {",
      "  global \$guestbook;",
      "  echo \$guestbook->BuildSideContent();",
      "}"
    ));
  }

  private function DoGuestBookContent() {
    return "<?php ShowGuestBook(); ?>\n";
  }

  protected function FetchPreparationCode() {
    return $this->WriteGuestBookPrepareCode();
  }

  protected function FetchInitialContent() {
    return "<?php \$guestbook->WriteInitialMessage(); ?>";
  }

  protected function FetchMainContent() {
    return $this->DoGuestBookContent();
  }

  protected function FetchMetaLinks() {
    return '';
  }

  protected function FetchScript() {
    return '';
  }

}

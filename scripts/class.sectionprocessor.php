<?php
// section processor for the page writer for MyLocalSmallBusiness
// written by Ian Stewart (c) 2012 Whitedream Software
// created: 22 nov 2012
// modified: 4 sep 2014

//require_once('library.php');
//require_once('pagewriter.php');
//require_once('galleryclass.php');
//require_once('newsletterclass.php');

//// <head>
//define('SCT_TITLE', 'title');
//define('SCT_KEYWORDS', 'keywords');
//define('SCT_METADESCRIPTION', 'description');
////  heading area
//define('SCT_NAVIGATION', 'navigation');
//define('SCT_LOGO', 'logo');
//define('SCT_HEADER', 'header');
//define('SCT_TAGLINE', 'tagline');
//define('SCT_INITIALCONTENT', 'initialcontent');
//define('SCT_MAINCONTENT', 'maincontent');
//define('SCT_ARTICLES', 'articles');
//define('SCT_GUESTBOOK', 'guestbook');
//define('SCT_NEWSLETTERS', 'newsletters');
//define('SCT_SOCIALNETWORKS', 'socialnetworks');
//define('SCT_TRANSLATION', 'translation');
//define('SCT_RSSFEED', 'rssfeed');
//define('SCT_CONTACTDETAILS', 'contactdetails');
////define('SCT_DOWNLOADABLEFILES', 'downloadablefiles');
//define('SCT_SIDECONTENT', 'sidecontent');
//define('SCT_FOOTER', 'footer');
//define('SCT_ADDTHIS', 'addthis');
//define('SCT_METALINKS', 'metalinks');
//define('SCT_SCRIPT', 'script');
//define('SCT_CALLJS', 'calljs');
//define('SCT_ADVERT', 'advert');

// id name of sections
//define('HTMLID_LOGO', 'logoimg'); // business logo
//define('HTMLID_NAVIGATION', 'menu'); // navigation (menu) of pages
//define('HTMLID_INITIALCONTENT', 'inittext');
//define('HTMLID_MAINCONTENT', 'maintext');
//define('HTMLID_ARTICLE', 'art');
//define('HTMLID_GUESTBOOK', 'guestbook');
//define('HTMLID_NEWSLETTER', 'newsletter');
//define('HTMLID_SOCIALNETWORK', 'socialnetwork');
//define('HTMLID_CALENDAR', 'calendar');
//define('HTMLID_BOOKING', 'booking');
//define('HTMLID_PRIVATEAREA', 'privatearea');
//define('HTMLID_TRANSLATION', 'translation');
//define('HTMLID_RSSFEED', 'rssfeed');
//define('HTMLID_CONTACTDETAILS', 'contactdetails');
//define('HTMLID_DOWNLOADABLEFILES', 'downloadablefiles');
//define('HTMLID_SIDECONTENT', 'sidetext');
//define('HTMLID_PAGEFOOTER', 'pagefooter');
//define('HTMLID_ADDTHIS', 'addthis');

//define('CONTENTAREATYPE_SIDEBAR', 's');
//define('CONTENTAREATYPE_MAIN', 'm');

// base class for the section processor
abstract class sectionprocessor {
  // <head>
  const SCT_TITLE = 'title';
  const SCT_KEYWORDS = 'keywords';
  const SCT_METADESCRIPTION = 'description';
  //  heading area
  const SCT_NAVIGATION = 'navigation';
  const SCT_LOGO = 'logo';
  const SCT_HEADER = 'header';
  const SCT_TAGLINE = 'tagline';
  const SCT_INITIALCONTENT = 'initialcontent';
  const SCT_MAINCONTENT = 'maincontent';
  const SCT_ARTICLES = 'articles';
  const SCT_GUESTBOOK = 'guestbook';
  const SCT_NEWSLETTERS = 'newsletters';
  const SCT_SOCIALNETWORKS = 'socialnetworks';
  const SCT_TRANSLATION = 'translation';
  const SCT_RSSFEED = 'rssfeed';
  const SCT_CONTACTDETAILS = 'contactdetails';
  //const SCT_DOWNLOADABLEFILES = 'downloadablefiles';
  const SCT_SIDECONTENT = 'sidecontent';
  const SCT_FOOTER = 'footer';
  const SCT_ADDTHIS = 'addthis';
  const SCT_METALINKS = 'metalinks';
  const SCT_SCRIPT = 'script';
  const SCT_CALLJS = 'calljs';
  const SCT_ADVERT = 'advert';
  // id name of sections
  const HTMLID_LOGO = 'logoimg'; // business logo
  const HTMLID_NAVIGATION = 'menu'; // navigation (menu) of pages
  const HTMLID_INITIALCONTENT = 'inittext';
  const HTMLID_MAINCONTENT = 'maintext';
  const HTMLID_ARTICLE = 'art';
  const HTMLID_GUESTBOOK = 'guestbook';
  const HTMLID_NEWSLETTER = 'newsletter';
  const HTMLID_SOCIALNETWORK = 'socialnetwork';
  const HTMLID_CALENDAR = 'calendar';
  const HTMLID_BOOKING = 'booking';
  const HTMLID_PRIVATEAREA = 'privatearea';
  const HTMLID_TRANSLATION = 'translation';
  const HTMLID_RSSFEED = 'rssfeed';
  const HTMLID_CONTACTDETAILS = 'contactdetails';
  const HTMLID_DOWNLOADABLEFILES = 'downloadablefiles';
  const HTMLID_SIDECONTENT = 'sidetext';
  const HTMLID_PAGEFOOTER = 'pagefooter';
  const HTMLID_ADDTHIS = 'addthis';

  const CONTENTAREATYPE_SIDEBAR = 's';
  const CONTENTAREATYPE_MAIN = 'm';

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

  protected $pgtype;  // page type
  protected $groupid; // bespoke resource id (eg. gallery id)

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
    $this->pgtype = $this->page->pgtype;
    $this->groupid = (int) $this->page->GetFieldValue('groupid', 0);
    $this->pagelist = $pagewriter->pagelist->pages;
    $this->pagecount = count($this->pagelist);
    $this->account = $this->pagewriter->account;
    $this->contact = $pagewriter->contact;
  }

  // returns false or an array
  abstract protected function FetchPreparationCode();
  abstract protected function FetchMetaLinks();
  abstract protected function FetchScript();
  abstract protected function FetchCallJS();

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
        if (is_array($content)) {
          $strcontent = ArrayToString($content);
          $ret .= ">{$strcontent}</{$tag}>";
        } else {
          $ret .= ">{$content}</{$tag}>";
        }
      } else {
        $ret = $content;
      }
    }
    return $ret;
  }

  private function IncludeWebsiteManager() {
    $accid = $this->account->ID();
    $pageid = $this->page->ID();
    $rootpath = rtrim($this->rootpath, "\\");  // remove trailing path
    $sourcepath = rtrim($this->sourcepath, "\\");
//    $path = $this->sourcepath;
    return array(
      "require_once '{$this->sourcepath}class.websitemanager.php';",
      "websitemanager::Initialise({$accid}, '{$this->pgtype}', {$pageid}, " .
        "{$this->groupid}, '{$this->mode}', '{$rootpath}', '{$sourcepath}');"
    );
  }

  private function IncludeStatsCode() {
//    $path = $this->sourcepath;
// TODO: params already known
//    $pageid = $this->page->ID();
//    $pagemgrid = $this->account->GetFieldValue('pagemgrid');
    $ret = array();
    $ret[] = '/* collect statistics */';
//    $ret[] = "require_once '{$path}scripts" . DIRECTORY_SEPARATOR . "statistics.php';";
    $ret[] = "websitemanager::ProcessPageStats({$pagemgrid}, {$pageid});" . CRNL . CRNL;
    return $ret;
  }

  private function IncludeAdvertPrepareCode() {
    $ret = array();
    // only show adverts when live
    if (($this->mode == PWOPT_LIVE) && $this->account->GetFieldValue('showadverts')) {
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
  // this starts and initialises the websitemanager class
  public function RetrievePreparationCode() {
    // write the public state code
    $website = $this->IncludeWebsiteManager();
    if ($this->mode != PWOPT_PROFILE) {
      $stats = $this->IncludeStatsCode(); // write the statistics code
      $advert = $this->IncludeAdvertPrepareCode(); // add advert code
    } else {
      $stats = array();
      $advert = array();
    }
    // get any php code for running before the main page starts
    $prep = $this->FetchPreparationCode();
    $ret = array_merge(array('<?php '), $website, $stats, $advert, $prep, array('?>'));
    return RemoveEmptyElements($ret);
  }

  // main method to retrieve section
  public function RetrieveSection($sectioname, $tag = '', $class = '') {
    $this->sectiontag = $tag;
    $this->sectionclass = $class;
    switch ($sectioname) {
      case self::SCT_TITLE:
        $ret = $this->GetSectionPageTitle();
        break;
      case self::SCT_KEYWORDS:
        $ret = $this->GetSectionKeywords();
        break;
      case self::SCT_METADESCRIPTION:
        $ret = $this->GetSectionMetaDescription();
        break;
      case self::SCT_NAVIGATION:
        $ret = $this->GetSectionNavigation();
        break;
      case self::SCT_LOGO:
        $ret = $this->GetSectionLogo();
        break;
      case self::SCT_HEADER:
        $ret = $this->GetSectionHeader(); // DONE
        break;
      case self::SCT_TAGLINE:
        $ret = $this->GetSectionTagline();
        break;
      case self::SCT_INITIALCONTENT:
        $ret = $this->GetSectionInitialcontent(); // DONE
        break;
      case self::SCT_MAINCONTENT:
        $ret = $this->GetSectionMainContent();
        break;
      case self::SCT_TRANSLATION:
        $ret = $this->GetSectionTranslation();
        break;
      case self::SCT_RSSFEED:
        $ret = $this->GetSectionRSSFeed();
        break;
      case self::SCT_CONTACTDETAILS:
        $ret = $this->GetSectionContactDetails();
        break;
      case self::SCT_SIDECONTENT:
        $ret = $this->GetSectionSideContent(); // DONE
        break;
      case self::SCT_FOOTER:
        $ret = $this->GetSectionFooter(); // DONE
        break;
      case self::SCT_ADDTHIS:
        $ret = $this->GetSectionAddthis();
        break;
      case self::SCT_METALINKS:
        $ret = $this->GetSectionMetalinks();
        break;
      case self::SCT_SCRIPT:
        $ret = $this->GetSectionScript();
        break;
      case self::SCT_ADVERT:
        $ret = $this->GetSectionAdvert();
        break;
      case self::SCT_CALLJS:
        $ret = $this->GetCallJS();
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
    $value = $this->DoGetWebsiteContent(websitemanager::CTWM_INITIALCONTENT, self::CONTENTAREATYPE_MAIN);
    return $this->MakeTag($value, '', self::HTMLID_INITIALCONTENT);
  }

  private function GetSectionMainContent() {
/*    $list = array(
      $this->MakeTag($this->page->GetFieldValue('maincontent'), '', HTMLID_MAINCONTENT),
      $this->MakeTag($this->DoGetWebsiteContent(CT_ARTICLES, CONTENTAREATYPE_MAIN), '', HTMLID_ARTICLE),
      $this->MakeTag($this->DoGetWebsiteContent(CT_GUESTBOOK, CONTENTAREATYPE_MAIN), '', HTMLID_GUESTBOOK),
      $this->MakeTag($this->DoGetWebsiteContent(CT_NEWSLETTERS, CONTENTAREATYPE_MAIN), '', HTMLID_NEWSLETTER),
      $this->MakeTag($this->DoGetWebsiteContent(CT_SOCIALNETWORK, CONTENTAREATYPE_MAIN), '', HTMLID_SOCIALNETWORK),
      $this->MakeTag($this->DoGetWebsiteContent(CT_CALENDAR, CONTENTAREATYPE_MAIN), '', HTMLID_CALENDAR),
      $this->MakeTag($this->DoGetWebsiteContent(CT_BOOKING, CONTENTAREATYPE_MAIN), '', HTMLID_BOOKING),
      $this->MakeTag($this->DoGetWebsiteContent(CT_PRIVATEAREA, CONTENTAREATYPE_MAIN), '', HTMLID_PRIVATEAREA),
      $this->MakeTag($this->DoGetWebsiteContent(CT_DOWNLOADABLEFILES, CONTENTAREATYPE_MAIN), '', HTMLID_DOWNLOADABLEFILES)
    ); */
    return $this->MakeTag($this->DoGetWebsiteContent(
      websitemanager::CTWM_MAINCONTENT, self::CONTENTAREATYPE_MAIN), '', self::HTMLID_MAINCONTENT);
//    return $this->MakeTag($this->page->GetFieldValue('maincontent'), '', HTMLID_MAINCONTENT);
//    return ArrayToString($list);
  }

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
      $this->page->GetContactInfo(), '', self::HTMLID_CONTACTDETAILS);
  }

  // get content from database via the website manager
  // it is only fetched at runtime, when the visitor requests the page
  protected function DoGetWebsiteContent($ct, $areatype) {
    switch ($areatype) {
      case self::CONTENTAREATYPE_SIDEBAR:
        $ret = "<?php websitemanager::ShowSideContent('{$ct}', {$this->groupid}); ?>";
        break;
      case self::CONTENTAREATYPE_MAIN:
        $ret = "<?php websitemanager::ShowMainContent('{$ct}', {$this->groupid}); ?>";
        break;
      default:
        $ret = '';
    }
    return $ret;
  }

  // fetch all side content
  private function GetSectionSideContent() {
    $list = array(
      $this->MakeTag(
        $this->page->GetSidebarContent(), '', self::HTMLID_SIDECONTENT),
      $this->MakeTag(
        $this->DoGetWebsiteContent(
          websitemanager::CTWM_ARTICLES, self::CONTENTAREATYPE_SIDEBAR), '', self::HTMLID_ARTICLE),
      $this->MakeTag(
        $this->DoGetWebsiteContent(
          websitemanager::CTWM_GUESTBOOK, self::CONTENTAREATYPE_SIDEBAR), '', self::HTMLID_GUESTBOOK),
      $this->MakeTag(
        $this->DoGetWebsiteContent(
          websitemanager::CTWM_NEWSLETTERS, self::CONTENTAREATYPE_SIDEBAR), '', self::HTMLID_NEWSLETTER),
      $this->MakeTag(
        $this->DoGetWebsiteContent(
          websitemanager::CTWM_SOCIALNETWORK, self::CONTENTAREATYPE_SIDEBAR), '', self::HTMLID_SOCIALNETWORK),
      $this->MakeTag(
        $this->DoGetWebsiteContent(
          websitemanager::CTWM_CALENDAR, self::CONTENTAREATYPE_SIDEBAR), '', self::HTMLID_CALENDAR),
      $this->MakeTag(
        $this->DoGetWebsiteContent(
          websitemanager::CTWM_BOOKING, self::CONTENTAREATYPE_SIDEBAR), '', self::HTMLID_BOOKING),
      $this->MakeTag(
        $this->DoGetWebsiteContent(
          websitemanager::CTWM_PRIVATEAREA, self::CONTENTAREATYPE_SIDEBAR), '', self::HTMLID_PRIVATEAREA),
      $this->MakeTag(
        $this->DoGetWebsiteContent(
          websitemanager::CTWM_DOWNLOADABLEFILES, self::CONTENTAREATYPE_SIDEBAR), '', self::HTMLID_DOWNLOADABLEFILES)
    );
    return ArrayToString($list);
  }

  // fetch footer for each page
  private function GetSectionFooter() {
    return $this->MakeTag(
      //$this->page->GetFieldValue('footer') . "\n" .
      array(
        "<p class='designname'>Originally designed by <a title='Free CSS Templates' href='http://www.freecsstemplates.org'>Free CSS Templates</a>.",
        "<small>Modified by <a title='whitedream software' href='http://whitedreamsoftware.co.uk'>Whitedream Software</a>.</small> ",
        "&mdash; <a title='click to sign up' href='http://mylocalsmallbusiness.com'><strong>FREE Mini Websites</strong></a></p>"
      ), '', self::HTMLID_PAGEFOOTER);
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

  private function GetCallJS() {
    return $this->DoCallJS();
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
        $ret1 = array("  <h2>Downloads</h2>", "  <ul>");
        $ret2 = array_merge($ret1, $list);
        $ret2[] = "  </ul>";
        $ret = $this->MakeTag(ArrayToString($ret2), 'div', self::HTMLID_DOWNLOADABLEFILES);
      } else {
        $ret = false;
      }
      self::$cache_downloadablefiles = $ret;
    }
    return self::$cache_downloadablefiles;
  }

  protected function AddScriptForGallery() { //$value) {
    return array(
      "  <link href='" . CDN_PATH . "css/lightbox.css' rel='stylesheet' type='text/css' media='screen' />",
      "  <script src='" . CDN_PATH . "js/jquery.js' type='text/javascript'></script>",
      "  <script src='" . CDN_PATH . "js/lightbox.js' type='text/javascript'></script>",
      "  <script src='" . CDN_PATH . "js/rotator.js' type='text/javascript'></script>"
    );
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
    if ($this->account->GetFieldValue('showadverts') && ($this->mode == PWOPT_LIVE)) {
      $ret = $this->MakeTag("<?php AddAdvertInPage();?>", 'div', '', 'advert');
    }
    return $ret;
  }

  private function DoCallJS() {
    return $this->FetchCallJS();
  }

  // other css and js files
  private function ProcessMetaLinks() {
    //return "<link href='" . CDN_PATH . "css/shared.css' rel='stylesheet' type='text/css' media='all' />\n" .
    $ret = array("<link href='../../css/shared.css' rel='stylesheet' type='text/css' media='all' />");
    $links = MakeArray($ret, $this->FetchMetaLinks());
    return $links;
  }

  private function ProcessScript() {
    $ret = $this->FetchScript();
    if ($this->mode != PWOPT_PROFILE) {
      $ret[] = $this->AddScriptForGoogleAnalytics();
    }
    return $ret;
  }

  private function DoNavigation() {
    if ($this->pagecount > 1) {
      $activepageid = $this->page->ID();
      $nav = array("<ul>");
      $cnt = 0;
      foreach($this->pagelist as $page) {
        if ($page->GetFieldValue('visible')) {
          if ($page->GetFieldValue('ishomepage')) {
            $url = 'index.php';
          } else {
            $url = $page->GetFieldValue('name') . '.php';
          }
          $class = '';
          $cnt++;
          // work out any required class names
          if ($cnt == 1) {
            $class = 'first';
          }
          if ($cnt == $this->pagecount) {
            $class .= ' last';
          }
          $pageid = $page->ID();
          $active = trim($class . ' ' . (($pageid == $activepageid) ? 'active' : ''));
          if ($active) {
            $active = " class='{$active}'";
          }
          // add the entire page link as a list item
          $nav[] = "  <li{$active}><a href='{$url}'>{$page->GetFieldValue('description')}</a></li>";
        }
      }
      $nav[] = "</ul>";
      $ret = $this->MakeTag($nav, 'nav', self::HTMLID_NAVIGATION, $this->sectionclass);
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
        self::$cache_logo = $this->MakeTag($url, 'div', self::HTMLID_LOGO, $this->sectionclass);
      }
    }
    return self::$cache_logo;
  }

//  private function DoProductList() {
//    return "<?php ShowProducts({$this->account->ID()}, {$this->page->ID()});
//  }

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
        $ret = $this->MakeTag(ArrayToString($list), 'div', self::HTMLID_NEWSLETTER);
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
        $ret = $this->MakeTag(ArrayToString($list), 'div', self::HTMLID_SOCIALNETWORK);
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
    $ret = array(
      "<h2>Translate</h2>",
      "<div id='google_translate_element'></div>",
      "<script>",
      "  function googleTranslateElementInit() {",
      "    new google.translate.TranslateElement({",
      "      pageLanguage: 'en',",
      "      autoDisplay: false,",
      "      gaTrack: true,",
      "      gaId: 'UA-20300263-6'",
      "    }, 'google_translate_element');",
      "  }",
      "</script>",
      "<script src='//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit\"></script>"
    );
    return $this->MakeTag($ret, 'div', self::HTMLID_TRANSLATION);
  }

  private function DoRSSPage() {
    $exists = file_exists($this->pagewriter->rssfeedfilename);
    if (($this->pagecount > 2) && $exists) {
      $rss =
        "\n  <h2>RSS Feeds</h2>\n" .
        "  <a href=\"{$this->pagewriter->rssfeedfilename}\" target=\"_self\" title=\"page rss feed\">\n" .
        "    <img src=\"{$this->rootpath}images/rss.png\" alt=\"Page RSS Feed\" /><span>Page Feed</span>\n" .
        "  </a>\n";
      $ret = $this->MakeTag($rss, 'div', self::HTMLID_RSSFEED);
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
          "  <h2>Share This Page</h2>\n" . $line['content'] . "\n", 'div', self::HTMLID_ADDTHIS, 'addthis');
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

  protected function FetchPreparationCode() {
    return array();
  }

  protected function FetchMetaLinks() {
    return ($this->page->GetFieldValue('gengalleryid') > 0)
      ? array(
          "  <link href='" . CDN_PATH . "css/lightbox.css' rel='stylesheet' type='text/css' media='screen' />",
          "  <script src='" . CDN_PATH . "jquery.js' type='text/javascript'></script>",
          "  <script src='" . CDN_PATH . "lightbox.js' type='text/javascript'></script>"
        )
      : false;
  }

  protected function FetchScript() {
    return ($this->page->GetFieldValue('gengalleryid') > 0)
      ? $this->AddScriptForGallery() //'div.rotator ul li.show')
//      ? $this->AddScriptForImageRotator() . $this->AddScriptForGallery() //'div.rotator ul li.show')
      : array();
  }

  protected function FetchCallJS() {
//    $cdn = CDN_PATH;
    return array(
      "<script type='text/javascript'>",
      "</script>"
    );
  }
}


/**
  * GALLERY PAGE processor
*/
class sectionprocessorgallery extends sectionprocessor {

  private function DoGallery($galleryid) {
    return "<?php websitemanager::ShowGallery({$galleryid}); ?>\n";
  }

  protected function FetchPreparationCode() {
    return array();
  }

  protected function FetchMetaLinks() {
    return array(
      "  <link href='" . CDN_PATH . "lightbox/css/lightbox.css' rel='stylesheet' type='text/css' media='screen'/>",
      "  <link href='" . CDN_PATH . "css/simplepagination.css' rel='stylesheet' type='text/css' media='screen'/>"
    );
  }

  protected function FetchScript() {
    return array();
/*
      "  <link href='" . CDN_PATH . "css/lightbox.css' rel='stylesheet' type='text/css' media='screen' />",
      "  <link href='" . CDN_PATH . "lightbox/css/lightbox.css' rel='stylesheet' type='text/css' media='screen' />",
      "  <script src='" . CDN_PATH . "js/jquery.js' type='text/javascript'></script>",
//      "  <script src='" . CDN_PATH . "js/lightbox.js' type='text/javascript'></script>",
      "  <script src='" . CDN_PATH . "js/pagination.js' type='text/javascript'></script>"
*/
  }

  protected function FetchCallJS() {
    $cdn = CDN_PATH;
    return array(
      "<script src='" . CDN_PATH . "js/jquery.js' type='text/javascript'></script>",
      "<script src='" . CDN_PATH . "js/pagination.js' type='text/javascript'></script>",
      "<script src='" . CDN_PATH . "lightbox/js/lightbox.js' type='text/javascript'></script>",
      "<script>",
//      "(function(\$){",
//      "  \$('#gallery').simplePagination();",
      "\$('.galleryviewer').simplePagination({",
      "  items_per_page: 3,", // THREE rows
      "  number_of_visible_page_numbers: 5,",
      "  use_page_count: true,",
      "  first_content: '&lt;&lt;',",
      "  previous_content: '&lt;',",
      "  next_content: '&gt;',",
      "  last_content: '&gt;&gt;'",
      "});",
//      "})(jQuery);",
      "</script>"
    );
  }

}

/**
  * CONTACT PAGE processor
*/
class sectionprocessorcontact extends sectionprocessor {

  protected function FetchPreparationCode() {
    return array();
  }

  protected function FetchMetaLinks() {
    return array();
  }

  protected function FetchScript() {
    return array();
  }

  protected function FetchCallJS() {
    return array();
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
    return array(); //$this->WriteBlogPrepareCode();
  }

  protected function FetchMetaLinks() {
    return false;
  }

  protected function FetchScript() {
    return '';
  }

  protected function FetchCallJS() {
    return array();
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
    return array(); //$this->WriteGuestBookPrepareCode();
  }

  protected function FetchMetaLinks() {
    return '';
  }

  protected function FetchScript() {
    return '';
  }

  protected function FetchCallJS() {
    return array();
  }

}

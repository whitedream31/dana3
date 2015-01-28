<?php
// page container class for MyLocalSmallBusiness
// written by Ian Stewart (c) 2012 Whitedream Software
// created: 8 dec 2012 (originally 7 apr 2010)
// modified: 18 feb 2013

//[ALL]
//id  bigint
//pagetypeid  bigint
//pagemgrid  bigint
//name  varchar(30)
//description  varchar(100)
//visible  tinyint
//ishomepage  tinyint
//header  text
//initialcontent  text
//sidecontent  text

//showhours tinyint
//incrss  tinyint
//incshownewsletters  tinyint
//incsocialnetwork  tinyint
//inctranslation  tinyint
//showfiles  tinyint

//privateareaid  bigint ('was `ispublic`)
//footer  text
//category  varchar(50)
//dateadded  timestamp
//dateupdated  datetime
//pageorder  bigint
//status  varchar(3)

//[BK]  Booking
//groupid (bookingsettingid)  tinyint
//[CAL]  Calendar

//[CON]  Contact
//contactname  varchar(100)
//contactemail  varchar(100)
//contactsubject  varchar(100)
//contactmessage  varchar(100)
//inccontactinsidearea  tinyint
//inccontactname  tinyint
//incaddress  tinyint
//inctelephone  tinyint
//incemail  tinyint
//showmap  tinyint
//mapaddress  text

//[GAL]  Gallery
//imagesperpage  int
//hascomments  tinyint
//groupid (galleryid)  tinyint
//gengallerystyleid  bigint

//[GBK]  Guest Book
//cancomment  tinyint
//registervisitors  tinyint

//[BLG]  Blog / Articles
//cancomment  tinyint
//registervisitors  tinyint

//[PRD]  Product
//incspecialoffer  tinyint
//incprices  tinyint
//incimage  tinyint
//incdescription  tinyint
//incdelivery  tinyint
//productsperpage  int

//[SNW]  Social Network
//socialnetwork  bigint

//[unused]
//guestbookid  bigint
//hascomments  tinyint
//default  tinyint

require_once 'class.database.php';
require_once 'class.basetable.php';
require_once 'class.table.account.php';
//require_once('class.formbuilder.php');

// page class - was abstract but requied during pagewriter class
class page extends idtable { //implements pagetype
  const PAGETYPE_GENERAL = 'gen';
  const PAGETYPE_CONTACT = 'con';
//define('PAGETYPE_ABOUTUS', 'abt');
//  const PAGETYPE_PRODUCT = 'prd';
  const PAGETYPE_GALLERY = 'gal';
  const PAGETYPE_ARTICLE = 'art';
  const PAGETYPE_GUESTBOOK = 'gbk';
  const PAGETYPE_SOCIALNETWORK = 'soc';
  const PAGETYPE_BOOKING = 'bk';
  const PAGETYPE_CALENDAR = 'cal';
  const PAGETYPE_NEWSLETTER = 'nl';
//  const PAGETYPE_PRIVATEAREA = 'pvt'; // is a 'resource' rather than a page
//define('PAGETYPE_SURVEY', 'svy');
//define('PAGETYPE_MEDIA', 'md');
  protected $fldpagedescription;
  protected $fldheader;
  protected $fldinitialcontent;
  protected $fldsidecontent;
  protected $fldshowfiles;
  protected $fldincshownewsletters;
  protected $fldincsocialnetwork;
  protected $fldinctranslation;
  protected $fldvisible;
  protected $fldshowhours;
  protected $fldinccontactname;
  protected $fldinctelephone;
  protected $fldincaddress;
//  protected $fldincrss;
  protected $pagetypedetails = array(); // array of fields from pagetype table
  public $pgtype; // ref of page (gen, con etc)
  public $pagetypedescription;
  public $account;

  function __construct($id = 0) {
    $this->account = account::StartInstance();
    $this->AssignPageType();
    $this->FindPageType($this->pgtype);
    parent::__construct('page', $id);
//    if (!$this->exists) {}
    $this->pagetypedescription = $this->GetPageTypeDescription();
  }

  protected function AfterPopulateFields() {
  }

  protected function AssignFields() {
    parent::AssignFields();
    // [ALL] shared by all pages
    $this->AddField('pagetypeid', self::DT_FK);
    $this->AddField('pagemgrid', self::DT_FK);
    $this->AddField('name', self::DT_STRING);
    $this->AddField('description', self::DT_STRING);
    $this->AddField('visible', self::DT_BOOLEAN, true);
    $this->AddField('ishomepage', self::DT_BOOLEAN);
    $this->AddField('header', self::DT_STRING);
    $this->AddField('initialcontent', self::DT_TEXT, '', self::FLDTYPE_TEXTAREA);
    $this->AddField('sidecontent', self::DT_TEXT, '', self::FLDTYPE_TEXTAREA);
//    $this->AddField('maincontent', DT_TEXT); //, '', FLDTYPE_TEXTAREA);
//    $this->AddField('incrss', DT_BOOLEAN, true);
    $this->AddField('incshownewsletters', self::DT_BOOLEAN, true);
    $this->AddField('incsocialnetwork', self::DT_BOOLEAN, true);
    $this->AddField('inctranslation', self::DT_BOOLEAN, true);
    $this->AddField('privateareaid', self::DT_FK);
    $this->AddField('showhours', self::DT_BOOLEAN, true);
    $this->AddField('showfiles', self::DT_BOOLEAN, true);
    $this->AddField('footer', self::DT_STRING);
//    $this->AddField('category', DT_STRING);
    $this->AddField('dateadded', self::DT_DATETIME);
    $this->AddField('dateupdated', self::DT_DATETIME);
    $this->AddField('pageorder', self::DT_INTEGER);
    $this->AddField(basetable::FN_STATUS, self::DT_STATUS);
//    $this->AddField('inccontactinsidearea', DT_BOOLEAN, true);
    $this->AddField('inccontactname', self::DT_BOOLEAN, true);
    $this->AddField('incaddress', self::DT_BOOLEAN, true);
    $this->AddField('inctelephone', self::DT_BOOLEAN, true);
//    $this->AddField('incemail', DT_BOOLEAN, false);
//    $this->AddField('groupid', DT_FK);
//    $this->AddField('guestbookid', DT_FK);
    // $this->AddField(new integerfield('socialnetwork', 7, FIELDFLAG_UNSIGNED));
    $this->AssignPageTypeFields(); // custom fields for specific page type
  }

/*  public function ShowEditor() {
    $fb = new formbuilder(FRM_PAGEEDITOR, FS_PAGEMAIN, 'Main Content');
    $this->AssignFormFields($fb, $idref);
  } */

  protected function InitFieldsForHeading($worker) {
    $this->fldpagedescription = $worker->AddField(
      'description', new formbuildereditbox('description', '', 'Page Description'), $this);
    $this->fldheader = $worker->AddField(
      'header', new formbuildereditbox('header', '', 'Page Heading'), $this);
  }

  protected function InitFieldsForMainContent($worker) {
    $this->fldinitialcontent = $worker->AddField(
      'initialcontent', new formbuildertextarea('initialcontent', '', 'Initial Content'), $this);
  }

  protected function InitFieldsForSideContent($worker) {
    $this->fldsidecontent = $worker->AddField(
      'sidecontent', new formbuildertextarea('sidecontent', '', 'Side Content'), $this);
    $this->fldsidecontent->enableeditor = true;
  }

  protected function InitFieldForContactSidebar($worker) {
    $this->fldinccontactname = $worker->AddField(
      'inccontactname', new formbuildercheckbox('inccontactname', '', 'Include Contact Name'), $this);
    $this->fldincaddress = $worker->AddField(
      'incaddress', new formbuildercheckbox('incaddress', '', 'Include Business Address'), $this);
    $this->fldinctelephone = $worker->AddField(
      'inctelephone', new formbuildercheckbox('inctelephone', '', 'Include Contact Telephone Numbers'), $this);
  }

  protected function InitFieldsForOptions($worker) {
    $this->fldshowhours = $worker->AddField(
      'showfiles', new formbuildercheckbox('showfiles', '', 'Show Downloadable Files?'), $this);
//    $this->fldincrss = $worker->AddField(
//      'showrss', new formbuildercheckbox('showrss', '', 'Show RSS Feed?'), $this);
    $this->fldshowfiles = $worker->AddField(
      'showfiles', new formbuildercheckbox('showfiles', '', 'Show Downloadable Files?'), $this);
    $this->fldincshownewsletters = $worker->AddField(
      'incshownewsletters', new formbuildercheckbox('incshownewsletters', '', 'Show Newsletters (if any)?'), $this);
    $this->fldincsocialnetwork = $worker->AddField(
      'incsocialnetwork', new formbuildercheckbox('incsocialnetwork', '', 'Show Links to Social Networks (if any)?'), $this);
    $this->fldinctranslation = $worker->AddField(
      'inctranslation', new formbuildercheckbox('inctranslation', '', 'Show the Google Translation Service'), $this);
    $this->fldvisible = $worker->AddField(
      'visible', new formbuildercheckbox('visible', '', 'Show in website?'), $this);
  }

  public function InitForm($worker, $action) {
    $this->InitFieldsForHeading($worker);
    $this->InitFieldsForMainContent($worker);
    $this->InitFieldsForSideContent($worker);
//    $this->AssignPageFieldsForFooter($worker);
    $this->InitFieldsForOptions($worker);
  }

  public function AssignFieldProperties($worker, $isnew) {
    $worker->NewSection(
      'sectheadings', 'Headings', 'Please specify the main page details.');
    $worker->NewSection(
      'sectmaincontent', 'Main Contents', 'Please specify the main page content.');
    $worker->NewSection(
      'sectsidebar', 'Sidebar Content', 'Please specify any side bar content.');
    $worker->NewSection(
      'sectoptions', 'Page Options', 'Choose your options to apply to the page.');
    // description field
    $this->fldpagedescription->description = 'This is the title of the page that is shown in the menu area your website. Please keep it short and simple.';
    $this->fldpagedescription->size = 30;
    $worker->AssignFieldToSection('sectheadings', 'description');
    // header field
    $this->fldheader->description = 'This is the Heading of the page that is shown at the top of the page.';
    $this->fldheader->size = 100;
    $worker->AssignFieldToSection('sectheadings', 'header');
    // MAIN CONTENT
    // initial content field
    $this->fldinitialcontent->description = 'This is the text shown at the top of your page. Try explaining the purpose of your page.';
    $this->fldinitialcontent->rows = 10;
    $worker->AssignFieldToSection('sectmaincontent', 'initialcontent');
    // SIDEBAR
    // sidecontent field
    $this->fldsidecontent->description = 'Type in any text to be shown at the side bar of your page. Try to keep this short.';
    $worker->AssignFieldToSection('sectsidebar', 'sidecontent');

    if ($this->fldinccontactname) {
      $this->fldinccontactname->description =
        'Please check the box if you want your name to be shown on this page.';
      $worker->AssignFieldToSection('sectsidebar', 'inccontactname');
    }
    if ($this->fldincaddress) {
      $this->fldincaddress->description =
        'Please check the box if you want the address of your busines to be shown on this page.';
      $worker->AssignFieldToSection('sectsidebar', 'incaddress');
    }
    if ($this->fldinctelephone) {
      $this->fldinctelephone->description =
        'Please check the box if you want the contact telephone numbers to be shown on this page.';
      $worker->AssignFieldToSection('sectsidebar', 'inctelephone');
    }

    // OPTIONS
    // showfiles field
    $this->fldshowfiles->description = 'Please tick the box if you want any <strong>Downloadable Files</strong> to be available on this page, if any.';
    $worker->AssignFieldToSection('sectoptions', 'showfiles');
    // show newsletter field
    $this->fldincshownewsletters->description =
      'Please tick the box if you want the page to show any newsletters you may have created. If so, they will be available to view ' .
      'online as a webpage. There will also be an option for the visitor to subscribe to any future newsletters via email.';
    $worker->AssignFieldToSection('sectoptions', 'incshownewsletters');
    // inc socialnetwork
    $this->fldincsocialnetwork->description =
      'Please tick the box if you want the page to show links to your social networks you may have.';
    $worker->AssignFieldToSection('sectoptions', 'incsocialnetwork');
    // inc rss
//    $this->fldincrss->description = 'Please tick the box if you want to allow visitors to subscribe an RSS feed to your account.';
//    $worker->AssignFieldToSection('sectoptions', 'showfiles');
    // inc translation
    $this->fldinctranslation->description =
      'Please tick the box if you want the the Google Translation Service to appear. This will allow people who may wish to see your text ' .
      'in a different language.';
    $worker->AssignFieldToSection('sectoptions', 'inctranslation');
    // visible field
    $this->fldvisible->description =
      'Please tick the box if you want the page to be shown on your site or untick it to be excluded. Normally, this ' .
      'should be ticked. Your home page should be shown and cannot be hidden.';
    $worker->AssignFieldToSection('sectoptions', 'visible');
  }

/*  public function ValidateFormFields($formeditor, $idref) {
    $this->ValidateFields();
  } */

  protected function AssignPageType() {}

  protected function SaveFormField($fld) {
    return ($fld) ? $fld->Save() : true;
  }

  protected function SaveFormFields() {
    return
      $this->SaveFormField($this->fldpagedescription) + $this->SaveFormField($this->fldheader) +
      $this->SaveFormField($this->fldinitialcontent) + $this->SaveFormField($this->fldsidecontent) +
      $this->SaveFormField($this->fldshowfiles) + $this->SaveFormField($this->fldincshownewsletters) +
      $this->SaveFormField($this->fldincsocialnetwork) + $this->SaveFormField($this->fldinctranslation) +
      $this->SaveFormField($this->fldvisible) + $this->SaveFormField($this->fldshowhours) +
      $this->SaveFormField($this->fldinccontactname) + $this->SaveFormField($this->fldinctelephone) +
      $this->SaveFormField($this->fldincaddress);
  }

  public function ValidateFields() {}

  protected function AssignDefaultFieldValues() {
    $defheader = $this->pagetypedetails['defaultheader'];
    $definitial = $this->pagetypedetails['defaultinitialsection'];
    $this->SetFieldValue('description', $defheader);
    $this->SetFieldValue('header', 'New ' . $defheader . ' Page');
    $this->SetFieldValue('initialcontent', $definitial);
//$this->AssignFieldDefaultValue('header', 'test');
//$this->AssignFieldDefaultValue('description', $defheader, true);
//$this->AssignFieldDefaultValue('initialcontent', $definitial, true);
  }

  protected function FindPageType($pgtype) {
    $query = "SELECT * FROM `pagetype` " .
      "WHERE `pgtype` = '{$pgtype}' AND " .
      "`countryid` = " . (int) account::$instance->Contact()->GetFieldValue('countryid');
    $resource = database::Query($query);
    $line = $resource->fetch_assoc();
    $resource->close();
    $this->pagetypedetails = $line;
    return $line;
  }

  protected function AssignPageTypeFields() {}

  public function StoreChanges() {
    $this->SaveFormFields();
    if (!$this->exists) {
      $name = $this->pagetypedetails['name'];
      $pagemgrid = account::$instance->GetFieldValue('pagemgrid');
      $this->SetFieldValue('pagetypeid', $this->pagetypedetails['id']);
      $this->SetFieldValue('pagemgrid', $pagemgrid);
      $this->SetFieldValue('name', $name);
      $pageorder = database::CountRows('page', '`pagemgrid` = ' . $pagemgrid);
      $this->SetFieldValue('pageorder', $pageorder);
      $this->SetFieldValue(basetable::FN_STATUS, self::STATUS_ACTIVE);
    }
    parent::StoreChanges();
    if (!$this->exists) {
      $typeexists = isset(account::$instance->GetPageList()->pagetypecount[$this->pgtype]);
      $name = $this->pagetypedetails['name'] . ($typeexists ? $this->ID() : '');
      $this->SetFieldValue('name', $name);
      parent::StoreChanges();
    }
  }

  public function ToggleVisibility() {}

  public function MoveOrder($direction) {
    account::$instance->GetPageList()->MovePageOrder($this->ID(), $direction);
//    $pageorder = (int) $this->GetFieldValue('pageorder') + $direction;
//    $query = 'UPDATE `page` SET `pageorder` = ' . $pageorder . ' WHERE `id` = ' . $this->ID();
//    database::Query($query);
//    account::$instance->GetPageList()->ReorderPages();
  }

  protected function GetPageTypeDescription() {
    if (isset($this->pagetypedetails['description'])) {
      $ret = $this->pagetypedetails['description'];
    } else {
      $ret = '(not supported)';
    }
    return $ret;
  }

  public function GetContactInfo() {
    $contact = $this->account->Contact();
    $ret = array();
    $ret[] = "<div>"; // id='contactdetails'>";
    $ret[] = "<h2>Contact Details</h2>";
    $ret[] = "<ul class='contactaddress'>";
    if ($this->GetFieldValue('inccontactname')) {
      $lastname = trim($contact->GetFieldValue('lastname'));
      if (IsBlank($lastname)) {
        $ret[] = '  <li>' . $this->account->GetFieldValue('businessname') . '</li>';
      } else {
        $ret[] = '  <li>' . $contact->FullContactName() . '</li>';
      }
    }
    $addr = $contact->FullAddress('  <li>', "</li>\n", $this->GetFieldValue('incaddress'));
    $ret = array_merge($ret, explode("\n", $addr));
    if ($this->GetFieldValue('inctelephone')) {
      $tellist = $contact->TelephoneNumbersAsArray(false, false);
      foreach($tellist as $itm) {
        if (trim($itm)) {
          $ret[] = "<li>{$itm}</li>";
        }
      }
        //$ret = array_merge($ret, explode("\n", $tellist));
    }
    $email = $contact->EmailAsString(true, true, false);
    $website = $this->account->MainWebsiteURL(true);
    if ($email || $website) {
      if ($email) {
        $ret[] = $email;
      }
      $ret[] = $contact->AddSpecialLinkItem($website, '', 'website', true, '', false, false);
    }
    $ret[] = '</ul>';
    $ret[] = '</div>';
    return ArrayToString($ret);
  }

  public function GetSidebarContent() {
    return $this->GetFieldValue('sidecontent');
  }

  protected function GetGalleryList($includepicturecount = false) {
    if ($includepicturecount) {
      $query = 'SELECT DISTINCT g.`id`, g.`title`, COUNT(gi.`id`) as cnt ' .
      'FROM `gallery` g ' .
      'LEFT OUTER JOIN `galleryitem` gi ON g.`id` = gi.`galleryid` ' .
      'WHERE g.`accountid` = ' . $this->account->ID() . 
      ' GROUP BY g.`id`';
    } else {
      $query = 'SELECT `id`, `title` FROM `gallery` ' .
        'WHERE `accountid` = ' . $this->account->ID() . ' ORDER BY `title`';
    }
    $result = database::Query($query);
    $list = array();
    while ($line = $result->fetch_assoc()) {
      $id = $line['id'];
      $title = $line['title'];
      if ($includepicturecount) {
        $cnt = $line['cnt'];
        if ($cnt == 0) {
          $cntmsg = 'no pictures';
        } else if ($cnt == 1) {
          $cntmsg = '1 picture';
        } else {
          $cntmsg = $cnt . ' pictures';
        }
        $title .= ' <small>(' . $cntmsg . ')</small>';
      }
      $list[$id] = $title;
    }
    $result->free();
    return $list;
  }
}

// page list class
class pagelist extends idtable {
  protected $account; // account object
  public $pages;
  public $pagecount;
  public $pagesavailable;
  public $pagesleft;
  public $pagetypecount = array();

  function __construct($id = 0) {
    parent::__construct('page', $id);
  }

  protected function AssignFields() {
    parent::AssignFields();
    // [ALL] shared by all pages
    $this->AddField('pagetypeid', self::DT_FK);
    $this->AddField('pagemgrid', self::DT_FK);
    $this->AddField('name', self::DT_STRING);
    $this->AddField('description', self::DT_STRING);
    $this->AddField('visible', self::DT_BOOLEAN, true);
    $this->AddField('ishomepage', self::DT_BOOLEAN);
    $this->AddField('header', self::DT_STRING);
//    $this->AddField('initialcontent', DT_TEXT, '', FLDTYPE_TEXTAREA);
//    $this->AddField('sidecontent', DT_TEXT, '', FLDTYPE_TEXTAREA);
//    $this->AddField('incrss', DT_BOOLEAN, true);
//    $this->AddField('incshownewsletters', DT_BOOLEAN, true);
//    $this->AddField('incsocialnetwork', DT_BOOLEAN, true);
//    $this->AddField('inctranslation', DT_BOOLEAN, true);
    $this->AddField('privateareaid', self::DT_FK);
    $this->AddField('showfiles', self::DT_BOOLEAN, true);
//    $this->AddField('footer', DT_STRING);
//    $this->AddField('category', DT_STRING);
    $this->AddField('dateadded', self::DT_DATETIME);
    $this->AddField('dateupdated', self::DT_DATETIME);
    $this->AddField('pageorder', self::DT_INTEGER);
    $this->AddField(basetable::FN_STATUS, self::DT_STATUS);
  }

  public function SetAccount($account) {
    $this->account = $account;
    $this->PopulateList($this->account->GetFieldValue('pagemgrid'));
    $this->GetCounters();
  }

  public function AssignFormFields($formeditor, $idref) {}

  public function GetCounters() {
    $this->pagesavailable = 
      database::$instance->SelectFromTableByField(
        'pagemgr', basetable::FN_ID, $this->account->GetFieldvalue('pagemgrid'), 'pagesavailable');
    $this->pagecount = count($this->pages);
    $this->pagesleft = $this->pagesavailable - $this->pagecount;
  }

  protected function GetPageObject($pgtype, $pageid = 0) {
    switch ($pgtype) {
      case page::PAGETYPE_GENERAL:
        require_once 'class.table.pagegeneral.php';
        $page = new pagegeneral($pageid);
        break;
      case page::PAGETYPE_CONTACT:
        require_once 'class.table.pagecontact.php';
        $page = new pagecontact($pageid);
        break;
//      case page::PAGETYPE_ABOUTUS:
//        require_once('class.table.pageaboutus.php');
//          $this->pages[] = new pageaboutus($pageid);
//          break;
//      case page::PAGETYPE_PRODUCT:
//        require_once 'class.table.pageproduct.php';
//        $page = new pageproduct($pageid);
//        break;
      case page::PAGETYPE_GALLERY:
        require_once 'class.table.pagegallery.php';
        $page = new pagegallery($pageid);
        break;
      case page::PAGETYPE_ARTICLE:
        require_once 'class.table.pagearticle.php';
        $page = new pagearticle($pageid);
        break;
      case page::PAGETYPE_GUESTBOOK:
        require_once 'class.table.pageguestbook.php';
        $page = new pageguestbook($pageid);
        break;
      case page::PAGETYPE_SOCIALNETWORK:
        require_once 'class.table.pagesocialnetwork.php';
        $page = new pagesocialnetwork($pageid);
        break;
      case page::PAGETYPE_BOOKING:
        require_once 'class.table.pagebooking.php';
        $page = new pagebooking($pageid);
        break;
      case page::PAGETYPE_CALENDAR:
        require_once 'class.table.pagecalendar.php';
        $page = new pagecalendar($pageid);
        break;
//        case PAGETYPE_SURVEY:
//          break;
      default:
      $page = false;
    }
    return $page;
  }

  protected function GetPrivatePages() {
    $status = self::STATUS_ACTIVE;
    $accid = $this->account->ID();
    $query = 'SELECT pp.* FROM `privatepage` pp ' .
      'INNER JOIN `privatearea` pa ON pp.`privateareaid` = pa.`id` ' .
      'INNER JOIN `page` p ON p.`id` = pp.`pageid` ' .
      "WHERE pa.`accountid` = {$accid} AND pa.`status` = '{$status}' " .
      "AND p.`status` = '{$status}' AND p.`visible` > 0 " .
      'ORDER BY p.`pageorder`';
    $res = database::Query($query);
    $list = array();
    while ($line = $res->fetch_assoc()) {
      $pageid = $line['pageid'];
      $privateareaid = $line['privateareaid'];
      $list[$pageid] = $privateareaid;
    }
    $res->close();
    return $list;
  }

  protected function PopulateList($pgmgrid) {
    $privatepages = $this->GetPrivatePages();
    $status = self::STATUS_ACTIVE;
    $pid = (int) $pgmgrid;
    $query = 'SELECT p.`id`, pt.`pgtype` FROM `page` p ' .
      'INNER JOIN `pagetype` pt ON p.`pagetypeid` = pt.`id` ' .
      "WHERE (p.`pagemgrid` = {$pid}) AND (p.`status` = '{$status}') " . //AND (p.`visible` = 1)
      'ORDER BY p.`ishomepage` DESC, p.`pageorder`';
    $res = database::Query($query);
    $list = array();
    while ($line = $res->fetch_assoc()) {
      $pageid = $line['id'];
      if (!array_key_exists($pageid, $privatepages)) {
        $pagetype = $line['pgtype'];
        $list[$pageid] = $pagetype;
      }
    }
    $this->pages = array();
    $res->close();
    // populate pages array into objects
//    $this->pages = array();
    foreach($list as $pageid => $pagetype) {
      $page = $this->GetPageObject($pagetype, $pageid);
      if ($page) {
        $this->pages[$pageid] = $page;
        $page->account = $this->account;
        if (isset($this->pagetypecount[$pagetype])) {
          $this->pagetypecount[$pagetype]++;
        } else {
          $this->pagetypecount[$pagetype] = 1;
        }
      }
    }    
  }

  public function FindPageByID($pageid) {
    $ret = false;
    if ($pageid > 0) {
      foreach ($this->pages as $page) {
        if ($page->ID() == $pageid) {
          $ret = $page;
          break;
        }
      }
    }
    return $ret;
  }

  // create a new page based on its type
  static public function NewPage($pgtype = self::PAGETYPE_GENERAL, $pageid = 0) {
    $ret = null;
    switch ($pgtype) {
      case self::PAGETYPE_GENERAL: //gen
        require_once 'class.table.pagegeneral.php';
        $ret = new pagegeneral($pageid);
        break;
      case self::PAGETYPE_CONTACT: //con
        require_once 'class.table.pagecontact.php';
        $ret = new pagecontact($pageid);
        break;
//      case PAGETYPE_ABOUTUS: //abt
//        require_once('class.table.pageaboutus.php');
//        $ret = new pageaboutus($pageid);
//        break;
      case self::PAGETYPE_PRODUCT: //prd
        require_once 'class.table.pageproduct.php';
        $ret = new pageproduct($pageid);
        break;
      case self::PAGETYPE_GALLERY: //gal
        require_once 'class.table.pagegallery.php';
        $ret = new pagegallery($pageid);
        break;
      case self::PAGETYPE_ARTICLE: //art
        require_once 'class.table.pagearticle.php';
        $ret = new pagearticle($pageid);
        break;
      case self::PAGETYPE_GUESTBOOK: //gbk
        require_once 'class.table.pageguestbook.php';
        $ret = new pageguestbook($pageid);
        break;
      case self::PAGETYPE_SOCIALNETWORK: //soc
        require_once 'class.table.pagesocialnetwork.php';
        $ret = new pagesocialnetwork($pageid);
        break;
      case self::PAGETYPE_BOOKING: //bk
        require_once 'class.table.pagebooking.php';
        $ret = new pagebooking($pageid);
        break;
      case self::PAGETYPE_CALENDAR: //cal
        require_once 'class.table.pagecalendar.php';
        $ret = new pagecalendar($pageid);
        break;
/*      case PAGETYPE_SURVEY: //svy
        require_once('class.table.pagesurvey.php');
        $ret = new pagesurvey($pageid);
        break; */        
    }
    return $ret;
  }

  public function MovePageOrder($pageid, $direction) {
    $currentpage = $this->FindPageByID($pageid);
    if ($currentpage) {
      $page = reset($this->pages);
      $fnd = false;
      while ((!$fnd) && $page) {
        $pid = $page->ID();
        if ($pid == $pageid) {
          $fnd = true;
        } else {
          $page = next($this->pages);
        }
      }
      if ($direction > 0) {
        $swappage = next($this->pages);
      } else if ($direction < 0) {
        $swappage = prev($this->pages);
      } else {
        $swappage = false;
      }
      if ($swappage) {
        account::$instance->GetPageList()->ReorderPages(); // ensure order is correct
        $query = 'UPDATE `page` SET `pageorder` = ' . $swappage->GetFieldValue('pageorder') . ' WHERE `id` = ' . $currentpage->ID();
        database::Query($query);
        $query = 'UPDATE `page` SET `pageorder` = ' . $currentpage->GetFieldValue('pageorder') . ' WHERE `id` = ' . $swappage->ID();
        database::Query($query);
      }
    }
  }
  
  public function ReorderPages() {
    database::RefreshTables();
    $pages = array();
    //$homepageid = 0;
    $pagemgrid = account::$instance->GetFieldValue('pagemgrid');
    //$query = 'SELECT `id`, `pageorder`, `ishomepage` FROM `page` WHERE `pagemgrid` = ' . $pagemgrid;
    $query = 'SELECT `id`, `pageorder` FROM `page` ' .
      'WHERE `ishomepage` = 0 AND `pagemgrid` = ' . $pagemgrid .
      ' ORDER BY `pageorder`';
    $result = database::Query($query);
    while ($line = $result->fetch_assoc()) {
      $pageid = $line['id'];
      $pageorder = $line['pageorder'];
      $pages[$pageid] = $pageorder;
    }
    $result->free();
    $currentpageorder = 1;
    foreach ($pages as $pageid => $pageorder) {
      if ($currentpageorder != $pageorder) {
        $query = 'UPDATE `page` SET `pageorder` = ' . $currentpageorder . ' WHERE `id` = ' . $pageid;
        database::Query($query);       
      }
      $currentpageorder++;
    }
    account::$instance->pagelist = array();
  }
/*
  public function ShowPageTypes() {
    $query = 'SELECT * FROM `pagetype` ' .
      'WHERE `countryid` = 2 AND `status` = "A" ORDER BY `pgtypeorder` DESC';
    $result = database::Query($query);
    while ($line = $result->fetch_assoc()) {
      //$ref = $line['ref'];
      $pgtype = $line['pgtype'];
      $icon = 'images/page' . $pgtype . '.png';
      $desc = $line['description'];
      $help = $line['help'];
      $hint = $line['homehelp'];
      $url = $_SERVER['PHP_SELF'] . "?{RUNACTION}=editpg&amp;{RUNMODE}={ACT_NEW}&amp;{RUNTYPE}={$pgtype}";
      $img = (file_exists($icon)) ? "<img src='{$icon}' width='32' height='32' alt='{$hint}'>" : '';
      echo "      <div class='pagenewsection'>\n" .
           "        <div class='pagenewsectiontitle'>\n" .
           "          <a href='{$url}' title='{$hint}'>{$img}{$desc}</a>\n" .
           "        </div>\n" .
           "        <div class='pagenewsectiontext'>{$help}</div>\n" .
           "      </div>\n";
    }
    $result->free();
  }
*/
  public function AssignDataGridColumns($datagrid) {
    $datagrid->AddColumn('DESC', 'Description', true);
    $datagrid->AddColumn('PGTYPE', 'Page Type');
  }

  public function AssignDataGridRows($datagrid) {
    $account = account::$instance;
    $pgmgrid = $account->GetFieldValue('pagemgrid');
    $statusactive = self::STATUS_ACTIVE;
    $query =
      'SELECT p.`id`, p.`description`, pt.`description` AS pgtype, `ishomepage`, `visible` ' .
      'FROM `page` p ' .
      "INNER JOIN `pagetype` pt ON p.`pagetypeid` = pt.`id` " .
      "WHERE (p.`pagemgrid` = '{$pgmgrid}') AND (p.`status` = '{$statusactive}') " . //AND (p.`visible` = 1) ' .
      "ORDER BY p.`ishomepage` DESC, p.`pageorder`";
    $list = array();
    $result = database::Query($query);
    while ($line = $result->fetch_assoc()) {
      $id = $line['id'];
      $pgtype = ($line['ishomepage']) ? '<strong>HOMEPAGE</strong>' : $line['pgtype'];
      $actions = array(
        formbuilderdatagrid::TBLOPT_TOGGLEVISIBLE, formbuilderdatagrid::TBLOPT_DELETABLE, 
        formbuilderdatagrid::TBLOPT_IGNOREFIRSTROW, formbuilderdatagrid::TBLOPT_MOVEUP, 
        formbuilderdatagrid::TBLOPT_MOVEDOWN
      );
      $coldata = array(
        'DESC' => $line['description'],
        'PGTYPE' => $pgtype
      );
      $datagrid->AddRow($id, $coldata, $line['visible'], $actions);
    }
    $result->free();
    return $list;
  }

  public function AssignDataListRows($datalist) {
    $statusactive = self::STATUS_ACTIVE;
    $query =
      'SELECT `id`, `pgtype`, `description`, `help`, `homehelp` ' .
      'FROM `pagetype` ' .
      "WHERE  (`status` = '{$statusactive}') AND `countryid` = 2 " .
      'ORDER BY `pgtypeorder` DESC';
    $result = database::Query($query);
    while ($line = $result->fetch_assoc()) {
      $id = $line['id'];
      $pgtype = $line['pgtype'];
      $img = 'images/page' . $pgtype . '.png';
      $name = $line['description'];
      $desc = $line['help'];
      $hint = $line['homehelp'];
      $icon = (file_exists($img)) ? "<img src='{$img}' width='32' height='32' alt='{$hint}'>" : '';
      $datalist->AddRow($id, array(
        'icon' => $icon, 'edit' => $name, 'desc' => $desc, 'hint' => $hint, 'action' => workerbase::ACT_NEW
      ));
    }
    $result->free();
  }

  protected function GetPrettyValue($value) {
    if ($value == 0) {
      $ret = 'NONE';
    } elseif ($value == 1) {
      $ret = 'just one';
    } else {
      $ret = $value;
    }
    return $ret;
  }

  public function GetPrettyPageStats() {
    return array(
      'available' => $this->GetPrettyValue($this->pagesavailable),
      'count' => $this->GetPrettyValue($this->pagecount),
      'left' => $this->GetPrettyValue($this->pagesleft)
    );
  }

}

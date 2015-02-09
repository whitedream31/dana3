<?php

// account table

require_once 'class.database.php';
require_once 'class.basetable.php';
require_once 'class.table.page.php';
//require_once 'class.table.hours.php';
//require_once('scripts/class.image.php');

//define('SEARCH_MAX', 500); // max number of results found

//define('SEARCH_WHAT', 16);
//define('SEARCH_WHERE', 8);
//define('SEARCH_TAG', 2);
//define('SEARCH_SPONSOR', 128);

// account table

class account extends idtable {
  const ACCSTATUS_UNCONFIRMED = 'uncon';
  const ACCSTATUS_OFFLINE = 'off';
  const ACCSTATUS_UNKNOWN = 'unknown';
  const ACCSTATUS_NOTEXISTS = 'noacc';
  const ACCSTATUS_EXPIRED = 'exp';
  const ACCSTATUS_PUBLISHED = 'pub';
  const ACCSTATUS_PENDING = 'pen';
  const ACCSTATUS_MODIFIED = 'mod';
  const ACCSTATUS_DELETED = 'del';

  const SEARCH_WHAT = 16;
  const SEARCH_WHERE = 8;
  const SEARCH_TAG = 2;
  const SEARCH_SPONSOR = 128;

  static public $instance;
  public $contact = null; // contact object
  public $theme = null; // theme object

  public $media;
  public $hoursid; // pk for hours table
  public $hours;
  public $nextimgnumber; // imgid in media table assigned by GetMediaFilename
  public $showenddate;
  public $expired; // after showenddate?
  public $pagelist; // page list object
  // galleries
  public $gallerygrouplist; // gallery group list object
  // articles
  public $articlelist = array();
  // downloadable files
  public $fileslist = array();
  // guest-books
  public $guestbooklist = array();
  // booking
  public $bookinglist = array();
  public $bookingsettingslist = array();
  // areas covered
  public $areacoveredlist = array();
  // calendar dates
  public $calendarlist = array();
  // private-areas
  public $privateareagrouplist = array();
//  public $privateareamemberlist = array();
  // newsletters
  public $newsletterlist = array();
  public $newslettersubscriberlist = array();

  private $ratingstatistics = false;
  private $businesscategorydescription = '';
  private $rootpath;
  public $logo;

  static public $errors; // normal string - TODO: change to array for creating URL?
  static public $resultmessages; //

  function __construct($id = 0) {
    if ($id == 0) {
      $id = (isset($_POST['accid'])) ? $_POST['accid'] : 2; // 2 - for testing TODO: change default account id
    }
    $this->hoursid = 0;
    $this->hours = false;
    parent::__construct('account', $id);
    $this->ratingstatistics = false;
    $this->pagelist = false;
    self::$errors = array();
    self::$resultmessages = array();
  }

  static function StartInstance($id = 0) {
    if (!isset(self::$instance)) {
      self::$instance = new account($id);
    }
    return self::$instance;
  }

  static function AddResultMessage($key, $msg) {
    self::$resultmessages[$key] = $msg;
  }

  static function ShowUserMessages() {
    return self::ShowErrors() . self::ShowResultMessages();
  }

  private function AssignResultItem($mode, $line, $list, $id, $cnt) {
    if (isset($list[$id])) {
      $itm = $list[$id];
    } else {
      $itm = array();
      $itm['count'] = 0;
      $itm['tag'] = 0;
      $itm['rating'] = 0;
//      $itm['bt'] = 'na';
      $itm['sponsor'] = 0;
//      $itm['location'] = 'na';
      $itm['tag'] = ':';
    }
    switch ($mode) {
      case 'where':
//        $itm['location'] = trim($line['town'] . ' ' . trim($line['countyname'] . ' ' . $line['postcode']));
        break;
      case 'what':
//        $itm['bt1'] = $line['bt1'];
//        $itm['bt2'] = $line['bt2'];
//        $itm['bt3'] = $line['bt3'];
        $itm['rating'] += (int) $line['valueoverall'];
//        $itm['count'] += 1;
        break;
      case 'sponsor':
        $itm['sponsor']++;
        break;
      case 'tag':
        $itm['tag'] .= ' [' . $line['tag'] . ']';
        $cnt++; //$itm['tag']++;
    }
    $itm['count'] += $cnt;
    return $itm;
  }

  static public function FindNewMembers() {
    $query =
      'SELECT `id` FROM `account` ' .
      'WHERE (`authorised` > 0) AND (`published` > 0) AND (`deleted` = 0) ' .
      'AND (`datestarted` >= date_sub(curdate(), interval 1 month)) ' .
      'ORDER BY `datestarted` desc, `dateupdated` desc';
    $list = array();
    $result = database::Query($query);
    while ($line = $result->fetch_assoc()) {
      $list[] = $line['id'];
    }
    $result->free();
    return $list;
  }

  private function PerformWhatSearch($termwhat, &$list) {
    if ($termwhat) {
      $query =
        'SELECT DISTINCT a.`id`, bc1.`description` as bt1, bc2.`description` as bt2, ' .
        'bc3.`description` as bt3, r.`valueoverall` ' .
        'FROM `account` a ' .
        'INNER JOIN `businesscategory` bc1 ON bc1.`id` = a.`businesscategoryid` ' .
        'LEFT OUTER JOIN `businesscategory` bc2 ON bc2.`id` = a.`businesscategory2id` ' .
        'LEFT OUTER JOIN `businesscategory` bc3 ON bc3.`id` = a.`businesscategory3id` ' .
        'LEFT OUTER JOIN `rating` r ON r.`accountid` = a.`id` ' .
        'LEFT OUTER JOIN `tagaccount` t ON t.`accountid` = a.`id` ' .
        'WHERE (a.`authorised` > 0) AND (a.`published` > 0) AND (a.`deleted` = 0) ' .
        "AND ((a.`businessname` LIKE ('%{$termwhat}%')) OR " .
        "(bc1.`description` LIKE ('%{$termwhat}%')) OR " .
        "(bc2.`description` LIKE ('%{$termwhat}%')) OR " .
        "(bc3.`description` LIKE ('%{$termwhat}%')) OR " .
        "(a.`businesscategorylist` LIKE ('%{$termwhat}%')) OR " .
        "(t.`tag` LIKE ('%{$termwhat}%')))" .
        "ORDER BY a.`dateupdated` DESC";
      $result = database::$instance->Query($query);
      while (($line = $result->fetch_assoc())) {
        $id = $line['id'];
        $list[$id] = $this->AssignResultItem('what', $line, $list, $id, self::SEARCH_WHAT);
      }
      $result->free();
    }
  }

  private function PerformWhereSearch($termwhere, &$list) {
    if ($termwhere) {
      $query =
        'SELECT DISTINCT a.`id`, c.`town`, c.`postcode`, l.`description` as countyname ' .
        'FROM `account` a ' .
        'INNER JOIN `contact` c ON c.`id` = a.`contactid` ' .
        'INNER JOIN `county` l ON l.`id` = c.`countyid` ' .
        "WHERE " . //((a.`showaddress` = 0) AND   " . // TODO: add an 'online only' column instead?
        "(a.`authorised` > 0) AND (a.`published` > 0) AND (a.`deleted` = 0) ";
      if ($termwhere) {
        $query .=
          " AND (l.`description` LIKE ('%{$termwhere}%')) OR " .
          "(c.`town` LIKE ('%{$termwhere}%')) OR " .
          "(c.`postcode` LIKE ('{$termwhere}%'))";
      }
      $query .= ' ORDER BY a.`dateupdated` DESC';
      $result = database::$instance->Query($query);
      while (($line = $result->fetch_assoc())) { // && ($cnt < SEARCH_MAX)) {
        $id = $line['id'];
        $list[$id] = $this->AssignResultItem('where', $line, $list, $id, self::SEARCH_WHERE);
      }
      $result->free();
    }
  }

  protected function PerformTagSearch($tag, &$list) {
    if ($tag) {
      $query =
        "SELECT a.`id`, t.`tag` FROM `account` a " . 
        "INNER JOIN `accounttag` t ON t.`accountid` = a.`id` " .
        "WHERE (t.`tag` LIKE ('%{$tag}%')) " .
        "AND (a.`authorised` > 0) AND (a.`published` > 0) AND (a.`deleted` = 0) " .
        "ORDER BY a.`id`";
      $result = database::$instance->Query($query);
      while ($line = $result->fetch_assoc()) {
        $id = $line['id'];
        $tag = $line['tag'];
        $list[$id] = $this->AssignResultItem('tag', $line, $list, $id, self::SEARCH_TAG);
      }
      $result->free();
    }
  }

  private function PerformSponsorSearch(&$list) {
    if (count($list) > 0) {
      $idlist = implode(',', array_keys($list));
      $query =
        "SELECT s.`accountid` FROM `sponsor` s " .
        "INNER JOIN `account` a ON a.`id` = s.`accountid` " .
        "WHERE (s.`accountid` IN ({$idlist})) AND (s.`status` = 'A') AND " .
        "(CURRENT_DATE BETWEEN s.`startdate` AND s.`enddate`) " .
        "AND (a.`authorised` > 0) AND (a.`published` > 0) AND (a.`deleted` = 0) " .
        "ORDER BY s.`startdate`, s.`enddate`";
      $result = database::$instance->Query($query);
      while ($line = $result->fetch_assoc()) {
        $id = $line['accountid'];
        $list[$id] = $this->AssignResultItem('sponsor', $line, $list, $id, self::SEARCH_SPONSOR);
      }
      $result->free();
    }
  }

  protected function RemoveUnwantedFromSearchList(&$list, $mincount) {
    foreach($list as $id => $itm) {
      $cnt = $itm['count'];
      if ($itm['sponsor']) {
        $cnt -= self::SEARCH_SPONSOR;
      }
      if ($cnt < $mincount) { // || (!isset($itm['where']))) {
        unset($list[$id]);
      }
    }
  }

  public function PerformSearch($termwhat, $termwhere) {
    $ret = array();
    $mincount = 0;
    if ($termwhat) {
      $this->PerformWhatSearch($termwhat, $ret);
      $this->PerformTagSearch($termwhat, $ret);
      $mincount += self::SEARCH_WHAT;
    }
    if ($termwhere) {
      $this->PerformWhereSearch($termwhere, $ret);
      $this->PerformTagSearch($termwhere, $ret);
      $mincount += self::SEARCH_WHERE;
    }
    $this->PerformSponsorSearch($ret);
    $this->RemoveUnwantedFromSearchList($ret, $mincount);
    return $ret;
  }
  
  static function ShowResultMessages() {
    self::StartInstance()->CheckErrorCodes();
    $ret = '';
    $header = '';
    $msgs = self::$resultmessages;
    $cnt = count($msgs);
    if ($cnt > 0) {
      if ($cnt == 1) {
        $header = 'Message';
        $ret = '<p>' . array_shift($msgs) . '</p>' . CRNL;
      } else {
        $header = 'Messages';
        $ret = '<ul>' . CRNL;
        foreach ($msgs as $key => $msg) {
          $ret .= '<li>' . $msg . '</li>' . CRNL;
        }
        $ret .= '</ul>' . CRNL;
      }
      $ret =
        '<div class="usermessage">' . CRNL .
          '<h2>' . $header . '</h2>' . CRNL .
          $ret .
        '</div>' . CRNL;
    }
    return $ret;
  }

  static function ShowErrors() {
    $ret = '';
    $header = '';
    $errors = self::$errors;
    $cnt = count($errors);
    if ($cnt > 0) {
      if ($cnt == 1) {
        $header = 'There was an error';
        $ret = '<p>' . array_shift($errors) . '</p>' . CRNL;
      } else {
        $header = 'There were ' . $cnt . ' errors';
        $ret = '<ul>' . CRNL;
        foreach ($errors as $key => $msg) {
          $ret .= '<li>' . $msg . '</li>' . CRNL;
        }
        $ret .= '</ul>' . CRNL;
      }
      $ret =
        '<div class="errors">' . CRNL .
          '<h2>' . $header . '</h2>' . CRNL .
          $ret .
        '</div>' . CRNL;
    }
    return $ret;
  }

  static protected function CheckErrorCodes() {
    $e = (isset($_GET[ERRORCODE]) ? $_GET[ERRORCODE] : (isset($_POST[ERRORCODE]) ? $_POST[ERRORCODE] : 0));
    if ($e) {
      $countryid = self::StartInstance()->Contact()->GetFieldValue('countryid');
      require_once('class.table.errorcode.php');
      $errorcode = new errorcode($countryid, $e);
      if ($errorcode->exists) {
        $msg = $errorcode->GetFieldValue('message');
        self::AddResultMessage($e, $msg);
      }
    }
  }

  protected function AssignFields() {
    parent::AssignFields();
    $this->AddField('contactid', self::DT_FK);
    $this->AddField('pagemgrid', self::DT_FK);
    $this->AddField('themeid', self::DT_FK);
    $this->AddField('hoursid', self::DT_FK);
    $this->AddField('businessname', self::DT_STRING);
    $this->AddField('tagline', self::DT_STRING);
    $this->AddField('logomediaid', self::DT_FK);
    $this->AddField('session', self::DT_STRING, self::FLDTYPE_HIDDEN);
    $this->AddField('hoursid', self::DT_FK);
    $this->AddField('sidecontent', self::DT_STRING);
    $this->AddField('businessinfo', self::DT_STRING);
    $this->AddField('businesscategoryid', self::DT_FK);
    $this->AddField('businesscategory2id', self::DT_FK);
    $this->AddField('businesscategory3id', self::DT_FK);
    $this->AddField('businesscategorylist', self::DT_STRING);
    $this->AddField('website', self::DT_STRING);
    $this->AddField('nickname', self::DT_STRING);
    $this->AddField('showaddress', self::DT_BOOLEAN);
    $this->AddField('showindirectory', self::DT_BOOLEAN);
    $this->AddField('amountcharged', self::DT_FLOAT);
    $this->AddField('amountpaid', self::DT_FLOAT);
    $this->AddField('datestarted', self::DT_DATETIME);
    $this->AddField('expirydate', self::DT_DATE);
    $this->AddField('dateupdated', self::DT_DATETIME);
    $this->AddField('newsletter', self::DT_BOOLEAN);
    $this->AddField('hasrating', self::DT_BOOLEAN);
    $this->AddField('showadverts', self::DT_BOOLEAN);
    $this->AddField('published', self::DT_BOOLEAN);
    $this->AddField('authorised', self::DT_BOOLEAN);
    $this->AddField('modified', self::DT_BOOLEAN);
    $this->AddField('confirmed', self::DT_BOOLEAN);
    $this->AddField('deleted', self::DT_BOOLEAN);
    $this->AddField('metakeywords', self::DT_STRING);
    $this->AddField('metadescription', self::DT_STRING);
  }

  public function GetRelativePath($leafname = '') {
    return $this->RootPath() . $leafname . '/'; //DIRECTORY_SEPARATOR;
  }

  public function WebsiteURL($live = true, $showbusinessname = false) {
    $nickname = $this->GetFieldValue('nickname');
    $bn = ($showbusinessname) ? $this->GetFieldValue('businessname') : false;
    if ($live) {
      $url = 'mlsb.org/' . $nickname;
      $value = ($showbusinessname) ? $bn : $url;
      $title = 'link to your LIVE website';
    } else {
      $url = 'mylocalsmallbusiness.com/profiles/' . $nickname;
      $value = ($showbusinessname) ? $bn : '.../' . $nickname;
      $title = 'link to your PREVIEW website';
    }
    return '<a href="http://' . $url . '" target="_blank" title="' . $title . '">' . $value . '</a>';
  }

  public function MainWebsiteURL($makelink = false) {
    $website = $this->GetFieldValue('website');
    $ret = $website;
    if ($ret && $makelink) {
      $pos = strpos($website, '//');
      if ($pos) {
        $website = substr($website, $pos+2);
      }
      $ret = "<a href='{$ret}' target='_blank' title='visit our website'>{$website}</a>";
    }
    return $ret;
  }

  public function GetMediaDetails($mediaid) {
    $return = false;
    if ($mediaid > 0) {
      require_once('class.table.media.php');
      $query = 'SELECT * FROM `media` ' .
        'WHERE `id` = ' . (int) $mediaid;
      $result = database::$instance->Query($query);
      $line = $result->fetch_assoc();
      if ($line) {
        $return = array();
        foreach($line as $ln => $value) {
          $return[$ln] = $value;
        }
        $result->free();
      }
    }
    return $return;
  }

  public function AssignFormFields($formeditor, $idref) {
    $formeditor->usetabs = true;
    $formeditor->useeditor = false;
    //$businessname = $formeditor->AddDataField($this, 'businessname', 'Business Name', FLDTYPE_EDITBOX, 80, true);
    //$tagline = $formeditor->AddDataField($this, 'tagline', 'Tag Line', FLDTYPE_EDITBOX, 80);
    // business category section
    $formeditor->AssignActiveFieldSet(FS_BUSINESSMAINCATEGORY, 'Business Category');
    // - build business categories
    $businesscategory = $formeditor->AddDataField(
      $this, 'businesscategoryid', 'Main Business Category', self::FLDTYPE_SELECT, true);
    $categorygrouplist = database::RetrieveLookupList(
      'businesscategorygroup', basetable::FN_DESCRIPTION, basetable::FN_REF, basetable::FN_ID, '');
    foreach($categorygrouplist as $groupid =>$groupname) {
      $categorylist = database::RetrieveLookupList(
        'businesscategory', basetable::FN_DESCRIPTION, basetable::FN_REF, basetable::FN_ID,
        '`businesscategorygroupid` = ' . $groupid);
      foreach ($categorylist as $catid => $catdescription) {
        $businesscategory->AddToGroup($groupname, $catid, $catdescription);
      }
    }
//    $bespokelist = $formeditor->AddDataField($this, 'businesscategorylist', 'Bespoke list of categories', FLDTYPE_EDITBOX, 80);
    // custom section
    $formeditor->AssignActiveFieldSet(FS_BUSINESSCUSTOM, 'Description and Logo');
    $businessinfo = $formeditor->AddDataField(
      $this, 'businessinfo', 'Brief Business Description', self::FLDTYPE_TEXTAREA, true);
    $businessinfo->cols= 80;
    $businessinfo->rows= 5;
    // set up logo field
    $this->logo = $formeditor->AddDataField(
      $this, 'logomediaid', 'Business Logo', self::FLDTYPE_FILEWEBIMAGES);
    $this->logo->mediaid = $this->GetFieldValue('logomediaid');
    $media = $this->GetMediaDetails($this->logo->mediaid); // get the fk for media id
    if ($media) {
      $this->logo->previewthumbnail = $media['thumbnail'];
    } else {
      $this->logo->previewthumbnail = 'none';
    }
    $this->logo->targetfilename = $this->GetMediaFilename($this->logo->mediaid);
    $this->logo->targetpath = $this->GetRelativePath('media');

//    $website = $formeditor->AddDataField($this, 'website', 'Main Website', FLDTYPE_EDITBOX, 80);
    if ($this->hours) {
      $this->hours->AssignFormFields($formeditor, $idref);
    }
    $formeditor->class = 'controlactivitybox wide';
    $formeditor->PostFields();
  }

  public function UpdateLogoMedia($mediaid, $postnow = true) {
    if (isset($this->media)) {
      $this->media->AssignFromWebImage($mediaid);
      $this->media->SetFieldValue('imgid', $this->nextimgnumber);
      $this->media->StoreChanges();
      $this->SetFieldValue('logomediaid', $this->media->ID());
      if ($postnow) {
        $this->StoreChanges();
      }
    }
  }

  public function GetMediaFilename($mediaid) {
    require_once 'class.table.media.php';
    $this->media = new media($mediaid);
    if ($this->media->exists) {
      $this->nextimgnumber = $this->media->GetFieldValue('imgid');
      $ret = $this->media->GetFieldValue('imgname');
    } else {
      $this->nextimgnumber = $this->media->FindNextImgID($this->ID());
      $ret = 'img' . $this->nextimgnumber;
    }
    return $ret;
  }

  public function StoreChanges() {
    if (isset($this->media)) {
      $this->media->AssignFromWebImage($this->logo);
      $this->media->SetFieldValue('imgid', $this->nextimgnumber);
      $msg = ($this->media->exists) ? 'updated' : 'added';
      if ($this->media->StoreChanges()) {
        account::AddResultMessage('logo', 'Organisation details ' . $msg);
      }
      $this->SetFieldValue('logomediaid', $this->media->ID());
    }
    if ($this->hours) {
      $update = $this->hours->StoreChanges();
      if ($update) {
        $this->SetFieldValue('hoursid', $this->hours->ID());
      }
    }
    return parent::StoreChanges();
  }

  protected function AfterPopulateFields() {
    $this->areacoveredlist = array();
    $this->articlelist = array();
    $this->bookinglist = array();
    $this->bookingsettingslist = array();
    $this->calendarlist = array();
    $this->fileslist = array();
    $this->guestbooklist = array();
    $this->gallerygrouplist = array();
    $this->newsletterlist = array();
    $this->newslettersubscriberlist = array();
    $this->privateareagrouplist = array();
//    $this->privateareamemberlist = array();

    $this->businesscategorydescription = false;
    $this->rootpath = false;
    $enddate = $this->showenddate[basetable::FA_VALUE];
    if ($enddate != '') {
      $this->expired = date('Y-m-d') > $enddate;
    } else {
      $this->expired = false;
    }
    $this->GetHours();
  }

  public function GetHours($newhourid = 0) {
    $this->hoursid = ($newhourid) ? $newhourid : (int) $this->GetFieldValue('hoursid');
    $this->SetFieldValue('hoursid', $this->hoursid); // update, if neccessary
    $this->hours = new hours($this->hoursid);
    return $this->hours;
  }

  protected function LoadGalleryGroups() {
    require_once 'class.table.gallery.php';
    $this->gallerygrouplist = gallery::GetGroupList($this->ID());
//    require_once 'class.table.gallerygroup.php';
//    $this->gallerygrouplist = gallerygroup::GetList($this->ID());
  }

  protected function LoadNewsletters() {
    require_once 'class.table.newsletter.php';
    $this->newsletterlist = newsletter::FindShowableNewslettersByAccount($this->ID());
  }

  protected function LoadNewsletterSubscribers() {
    require_once 'class.table.newslettersubscriber.php';
    $this->newslettersubscriberlist = $this->NewsletterSubscriberList();
  }

  protected function LoadArticles() {
    require_once 'class.table.articleitem.php';
    $this->articlelist = articleitem::GetList($this->ID());
  }

  protected function LoadFiles() {
    require_once 'class.table.fileitem.php';
    $this->fileslist = fileitem::GetList($this->ID());
  }

  protected function LoadGuestBooks() {
    require_once 'class.table.guestbook.php';
    $this->guestbooklist = guestbook::GetList($this->ID());
  }

  protected function LoadAreasCovered() {
    require_once 'class.table.areacovered.php';
    $this->areacoveredlist = areacovered::GetList($this->ID());
  }

  protected function LoadBookings() {
    require_once 'class.table.booking.php';
    $this->bookinglist = booking::GetList($this->ID());
  }

  protected function LoadBookingSettings() {
    require_once 'class.table.bookingsetting.php';
    $this->bookingsettingslist = bookingsetting::GetList($this->ID());
  }

  protected function LoadCalendarDates() {
    require_once 'class.table.calendaritem.php';
    $this->calendarlist = calendaritem::GetList($this->ID());
  }

  protected function LoadPrivateGroupAreas() {
    require_once 'class.table.privatearea.php';
    $this->privateareagrouplist = privatearea::GetList($this->ID());
  }

/*  protected function LoadPrivateAreaMembers() {
    require_once('class.table.visitor.php');
    $this->privateareamemberlist = array();
    $query = 'SELECT v.`id` FROM `visitor` v ' .
      'INNER JOIN `privatemember` pm ON v.`id` = pm.`visitorid` ' .
      'INNER JOIN `privatearea` pa ON pm.`privateareaid` = pa.`id` ' .
      'WHERE pa.`accountid` = ' . $this->ID() .
      ' ORDER BY v.`displayname`';
    $result = database::$instance->Query($query);
    while ($line = $result->fetch_assoc()) {
      $id = $line['id'];
      $itm = new visitor($id);
      if ($itm->exists) {
        $this->privateareamemberlist[$id] = $itm;
      }
    }
    $result->free();
  } */

  public function Show() {
    if ($this->exists) {
      $ret = '';
    } else {
      $ret = '<p>Account not found</p>';
    }
    return $ret;
  }

  // retrieve an existing page from its id
  public function FindPage($id) {
    $ret = null;
    $pgtype = database::SelectFromTableByField('pagetype', basetable::FN_ID, $id, 'pgtype');
    switch ($pgtype) {
      case PAGECREATION_GENERAL: //gen
        require_once 'class.table.pagegeneral.php';
        $ret = new pagegeneral($id);
        break;
      case PAGECREATION_CONTACT: //con
        require_once('class.table.pagecontact.php');
        $ret = new pagecontact($id);
        break;
//      case PAGECREATION_ABOUTUS: //abt
//        require_once('class.table.pageaboutus.php');
//        $ret = new pageaboutus($id);
//        break;
      case PAGECREATION_PRODUCT: //prd
        require_once('class.table.pageproduct.php');
        $ret = new pageproduct($id);
        break;
      case PAGECREATION_GALLERY: //gal
        require_once('class.table.pagegallery.php');
        $ret = new pagegallery($id);
        break;
      case PAGECREATION_ARTICLE: //art
        require_once('class.table.pagearticle.php');
        $ret = new pagearticle($id);
        break;
      case PAGECREATION_GUESTBOOK: //gbk
        require_once('class.table.pageguestbook.php');
        $ret = new pageguestbook($id);
        break;
      case PAGECREATION_SOCIALNETWORK: //soc
        require_once('class.table.pagesocialnetwork.php');
        $ret = new pagesocialnetwork($id);
        break;
      case PAGECREATION_BOOKING: //bk
        require_once('class.table.pagebooking.php');
        $ret = new pagebooking($id);
        break;
      case PAGECREATION_CALENDAR: //cal
        require_once('class.table.pagecalendar.php');
        $ret = new pagecalendar($id);
        break;
//      case PAGECREATION_SURVEY: //svy
//        require_once('class.table.pagesurvey.php');
//        $ret = new pagesurvey($id);
//        break;
    }
    return $ret;
  }

  public function GetPageList($refresh = false) {
    if (!$this->pagelist || $refresh) {
      $this->pagelist = new pagelist();
      $this->pagelist->SetAccount($this);
    }
    return $this->pagelist;
  }

  public function GetCurrentStatus() {
    $authorised = $this->GetFieldValue('authorised');
    $published = $this->GetFieldValue('published');
    $modified = $this->GetFieldValue('modified');
    $confirmed = $this->GetFieldValue('confirmed');
    $deleted = $this->GetFieldValue('deleted');
    return self::GetStatus(
      $authorised, $published, $modified, $confirmed, $deleted
    );
  }

  static public function GetStatus(
    $authorised, $published, $modified, $confirmed, $deleted) {
    if ($deleted) {
      $ret = self::ACCSTATUS_DELETED;
    } else if ($authorised) {
      if ($published) {
        if ($confirmed) {
          if ($modified) {
            $ret = self::ACCSTATUS_MODIFIED;
          } else {
            $ret = self::ACCSTATUS_PUBLISHED;
          }
        } else {
          $ret = self::ACCSTATUS_UNCONFIRMED;
        }
      } else {
        $ret = self::ACCSTATUS_OFFLINE;
      }
    } else {
        $ret = self::ACCSTATUS_PENDING;
    }
    return $ret;
  }

  public function StatusAsString($status = false) {
    $ret = '';
    $status = ($status) ? $status : $this->GetCurrentStatus();
//    $status = $this->GetCurrentStatus(); //$this->GetFieldValue(FN_STATUS);
    if ($status) {
      switch ($status) {
        case self::ACCSTATUS_MODIFIED:
          $ret = 'Modified';
          break;
        case self::ACCSTATUS_UNCONFIRMED:
          $ret = 'Un-Confirmed';
          break;
        case self::ACCSTATUS_NOTEXISTS:
          $ret = 'Offline';
          break;
        case self::ACCSTATUS_EXPIRED:
          $ret = 'Expired';
          break;
        case self::ACCSTATUS_PUBLISHED:
          $ret = 'Published';
          break;
/*        case ACCSTATUS_PENDING:
          $ret = 'Pending';
          break;
        case ACCSTATUS_DELETED:
          $ret = 'Deleted';
          break; */
        case self::ACCSTATUS_OFFLINE:
          $ret = 'Offline';
          break;
        default: // ACCSTATUS_UNKNOWN:
          $ret = 'Unknown';
          break;
      }
    } else {
      $status = parent::StatusAsString($status);
    }
    return $ret;
  }

  // find account row by nickname
  public function FindByNickName($nickname) {
    return $this->FindByField('nickname', $nickname);
  }
  
  // get the singleton instance for current CONTACT
  public function Contact($reload = false) {
    require_once('class.table.contact.php');
    if (!$this->contact) {
      $id = $this->GetFieldValue('contactid');
      $this->contact = new contact($id);
    } elseif ($reload) {
      $id = $this->GetFieldValue('contactid');
      $this->contact->FindByKey($id);
    }
    return $this->contact;
  }

  // get the singleton instance for the current THEME
  public function Theme() {
    require_once('class.table.theme.php');
    if (!$this->theme) {
      $this->theme = new theme($this->GetFieldValue('themeid'));
    }
    return $this->theme;
  }

  public function GetThemeDescription() {
    $theme = $this->Theme();
    if ($theme) {
      $ret = $theme->GetFieldValue('description');
    } else {
      $ret = '(unknown)';
    }
    return $ret;
  }

  public function GalleryGroupList($refresh = false) {
    if ((!$this->gallerygrouplist || $refresh) && ($this->ID() > 0)) {
      $this->LoadGalleryGroups();
    }
    return $this->gallerygrouplist;
  }

  public function NewsletterList($refresh = false) {
    if ((!$this->newsletterlist || $refresh) && ($this->ID() > 0)) {
      $this->LoadNewsletters();
    }
    return $this->newsletterlist;
  }

  public function ArticleList($refresh = false) {
    if ((!$this->articlelist || $refresh) && ($this->ID() > 0)) {
      $this->LoadArticles();
    }
    return $this->articlelist;
  }

  public function FileList($refresh = false) {
    if ((!$this->fileslist || $refresh) && ($this->ID() > 0)) {
      $this->LoadFiles();
    }
    return $this->fileslist;
  }

  public function AreaCoveredList($refresh = false) {
    if ((!$this->areacoveredlist || $refresh) && ($this->ID() > 0)) {
      $this->LoadAreasCovered();
    }
    return $this->areacoveredlist;
  }

  public function GuestBookList($refresh = false) {
    if ((!$this->guestbooklist || $refresh) && ($this->ID() > 0)) {
      $this->LoadGuestBooks();
    }
    return $this->guestbooklist;
  }

  public function BookingList($refresh = false) {
    if ((!$this->bookinglist || $refresh) && ($this->ID() > 0)) {
      $this->LoadBookings();
    }
    return $this->bookinglist;
  }

  public function BookingSettingsList($refresh = false) {
    if ((!$this->bookingsettingslist || $refresh) && ($this->ID() > 0)) {
      $this->LoadBookingSettings();
    }
    return $this->bookingsettingslist;
  }

  public function CalendarList($refresh = false) {
    if ((!$this->calendarlist || $refresh) && ($this->ID() > 0)) {
      $this->LoadCalendarDates();
    }
    return $this->calendarlist;
  }

  public function PrivateAreaGroupList($refresh = false) {
    if ((!$this->privateareagrouplist || $refresh) && ($this->ID() > 0)) {
      $this->LoadPrivateGroupAreas();
    }
    return $this->privateareagrouplist;
  }

/*  public function PrivateAreaMemberList($refresh = false) {
    if ((!$this->privateareamemberlist || $refresh) && ($this->ID() > 0)) {
      $this->LoadPrivateAreaMembers();
    }
    return $this->privateareamemberlist;
  } */

  public function CountGuestBookComments($newonly = false) {
    $query = 'SELECT COUNT(*) AS cnt ' .
      'FROM `guestbookentry` e ' .
      'INNER JOIN `guestbook` g ON g.`id` = e.`guestbookid` ' .
      'WHERE g.`accountid` = ' . $this->ID() . ' AND e.`status` = \'N\' AND g.`status` = \'A\'';
    $result = database::Query($query);
    $line = $result->fetch_assoc();
    $result->free();
    return $line['cnt'];
  }

  public function CountBookings($newonly = false) {
    $query = 'SELECT COUNT(*) AS cnt ' .
      'FROM `booking` ' .
      'WHERE `accountid` = ' . $this->ID() . " AND `status` = 'A'";
    $result = database::Query($query);
    $line = $result->fetch_assoc();
    $result->free();
    return $line['cnt'];
  }

  public function CountNewsletterSubscribers() {
    $query = 'SELECT COUNT(*) AS cnt ' .
      'FROM `newslettersubscriber` ' .
      'WHERE `accountid` = ' . $this->ID() . " AND `status` = 'A'";
    $result = database::Query($query);
    $line = $result->fetch_assoc();
    $result->free();
    return $line['cnt'];
  }

  public function CountPrivateAreaGroups() {
    $query = 'SELECT COUNT(*) AS cnt FROM `privatearea` ' .
      'WHERE `accountid` = ' . $this->ID();
    $result = database::Query($query);
    $line = $result->fetch_assoc();
    $result->free();
    return $line['cnt'];
  }

  public function CountPrivateAreaMembers($newonly = false) {
/*    $query = 'SELECT COUNT(*) AS cnt FROM `visitor` v ' .
      'INNER JOIN `privatemember` pm ON v.`id` = pm.`visitorid` ' .
      'INNER JOIN `privatearea` pa ON pm.`privateareaid` = pa.`id` ' .
      'WHERE pa.`accountid` = ' . $this->ID(); */
    $query = 'SELECT COUNT(*) AS cnt FROM `privateareamember` ' .
      'WHERE `accountid` = ' . $this->ID();
    $result = database::Query($query);
    $line = $result->fetch_assoc();
    $result->free();
    return $line['cnt'];
  }

  public function RootPath() {
    // TODO: add mode (profile / live)
    if (!$this->rootpath) {
      $this->rootpath = '../profiles/' . $this->GetFieldValue('nickname') . '/';
    }
    return $this->rootpath;
  }

  public function BusinessCategoryDescription() {
    if (!$this->businesscategorydescription) {
      $bcid = $this->GetFieldValue('businesscategoryid');
      $this->businesscategorydescription =
        database::SelectFromTableByField(
          'businesscategory', basetable::FN_ID, $bcid, 'description');
    }
    return $this->businesscategorydescription;
  }

  static public function GetImageFilename($mediaid, $thumbnail = true) {
    //$mediaid = $this->GetFieldValue('logomediaid');
    if ($mediaid > 0) {
      $field = ($thumbnail) ? 'thumbnail' : 'imgname';
      $filename = database::SelectFromTableByField('media', basetable::FN_ID, $mediaid, $field);
    } else {
      $filename = false;
    }
    return $filename;
  }

  public function LogoImage($userootpath = true, $class = '', $thumbnail = true) {
    $path = ($userootpath)
      ? $this->GetRelativePath('media')
      : 'media/'; // '../' . $this->GetFieldValue('nickname') . '/media/';
    $filename = self::GetImageFilename($this->GetFieldValue('logomediaid'), $thumbnail);
    if ($filename === false) {
      $exists = false;
    } else {
      $filename = $path . $filename;
      $exists = file_exists($filename);
      $classname = ($class) ? " class='{$class}'" : '';
    }
    $ret = ($exists)
      ? "<img src='{$filename}'{$classname} alt=''>"
      : '<em>none</em>';
    return $ret;
  }

/*  public function NewsletterList() {
    require_once('class.table.newsletter.php');
    $query = 'SELECT * FROM `newsletter` ' .
      'WHERE `accountid` = ' . $this->ID() . ' AND (NOT `status` = \'D\') ' .
      'ORDER BY `showdate` DESC';
    $list = array();
    $result = database::$instance->Query($query);
    while ($line = $result->fetch_assoc()) {
      $id = $line['id'];
      $itm = new newsletter($id);
      if ($itm->exists) {
        $list[$id] = $itm;
      }
    }
    $result->free();
    return $list;
  } */

  public function NewsletterSubscriberList($status = false) {
    require_once('class.table.newslettersubscriber.php');
    $accountid = $this->ID();
    $statusdeleted = basetable::STATUS_DELETED;
    $statuscancelled = basetable::STATUS_CANCELLED;
    $sql = array(
      "SELECT `id` FROM `newslettersubscriber` ",
      "WHERE `accountid` = {$accountid} ");
    if ($status) {
      $sql[] = "AND `status` = '{$status}' ";
    } else {
      $sql[] = "AND NOT (`status` IN ('{$statusdeleted}', '{$statuscancelled}')) ";
    }
    $sql[] = 'ORDER BY `status`, `datestarted` DESC';
    $query = ArrayToString($sql);
    $result = database::Query($query);
    $list = array();
    while ($line = $result->fetch_assoc()) {
      $subid = $line['id'];
      $list[$subid] = new newslettersubscriber($subid);
    }
    $result->close();
    return $list;
  }

  public function FindNewsletterTypes() {
    $list = array();
    $query = 'SELECT `id` FROM `newsletteritemtype` ' .
      "WHERE `status` = 'A' ORDER BY `ref`";
    $result = database::Query($query);
    while ($line = $result->fetch_assoc()) {
      $id = $line['id'];
      $list[] = $id;
    }
    $result->free();
    return $list;
  }

  public function FindPagesByField($fieldname, $id) {
    $list = array();
    $pagelist = $this->GetPageList();
    foreach($pagelist->pages as $page) {
      $pgid = $page->GetFieldValue($fieldname);
      if ($pgid == $id) {
        $list[] = $page; //->ID();
      }
    }
    $ret = (count($list) > 0) ? $list : false;
    return $ret;
  }

  public function GetRating($refresh = false) {
    if ((!$this->ratingstatistics) || $refresh) {
      include_once('class.table.rating.php');
      $this->ratingstatistics = rating::GetStatistics($this->ID());
    }
    return $this->ratingstatistics;
  }

  public function GetRatingStars($ratingtype, $title = '', $showratenow = false, $forcerefresh = false) {
    if ($this->GetFieldValue('hasrating')) {
      $this->GetRating($forcerefresh);
      $ret = rating::GetRatingStars($ratingtype, $title, $showratenow);
    } else {
      $ret = '';
    }
    return $ret;
  }

  static public function GetSponsorRows() {
    $ret = array();
    $query =
      "SELECT * FROM `sponsor` " . 
      "WHERE `status` = 'A' AND " .
      "CURRENT_DATE BETWEEN `startdate` AND `enddate` " .
      "ORDER BY `startdate`, `enddate`";
    $result = database::$instance->Query($query);
    while ($line = $result->fetch_assoc()) {
      $ret[] = $line;
    }
    $result->free();
    return $ret;
  }

  static public function GetAllCurrentArticles() {
    include_once 'class.table.article.php';
    return articleitem::GetAllCurrentArticles();
  }
}

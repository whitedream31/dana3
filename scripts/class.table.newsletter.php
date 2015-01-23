<?php
require_once 'class.database.php';
require_once 'class.basetable.php';

// newsletter item type (types of newsletters)
class newsletteritemtype extends lookuptable {
  public $help;

  function __construct($id = 0) {
    parent::__construct('newsletteritemtype', $id);
  }

  protected function AssignFields() {
    parent::AssignFields();
    $this->AddField('help', self::DT_STRING);
  }

  protected function AfterPopulateFields() {
    parent::AfterPopulateFields();
    $this->help = $this->GetFieldValue('help');
  }
}

// newsletter
class newsletter extends idtable {
  public $showdate;
  public $showdatedescription;
  public $help;

  const STATUS_CANCELLED = 'C'; // invited but not accepted after 30 days

  function __construct($id = 0) {
    parent::__construct('newsletter', $id);
  }

  protected function AfterPopulateFields() {
    $this->showdate = $this->GetFieldValue('showdate');
//    $date = strtotime($this->showdate);
    $this->showdatedescription = basetable::FormatDateTime(self::DF_MEDIUMDATE, $this->showdate);
  }

  protected function AssignFields() {
    parent::AssignFields();
    $this->AddField(self::FN_ACCOUNTID, self::DT_FK);
    $this->AddField('title', self::DT_STRING);
    $this->AddField('showdate', self::DT_DATE);
    $this->AddField('datelastsent', self::DT_DATE);
    $this->AddField('showaddress', self::DT_BOOLEAN);
    $this->AddField('showtelephone', self::DT_BOOLEAN);
    $this->AddField('showwebsite', self::DT_BOOLEAN);
    $this->AddField('footertext', self::DT_STRING);
    $this->AddField('style', self::DT_STRING);
    $this->AddField('newsletterformatid', self::DT_FK);
    $this->AddField(basetable::FN_STATUS, self::DT_STATUS);
  }

  static public function FindNewslettersByAccount($accountid) {
    $status = self::STATUS_ACTIVE;
    $query = 
      "SELECT `id` FROM `newsletter` " .
      "WHERE `accountid` = {$accountid} AND `status` = '{$status}' " .
      'ORDER BY `showdate` DESC';
    $result = database::Query($query);
    $list = array();
    while ($line = $result->fetch_assoc()) {
      $list[] = $line['id'];
    }
    $result->close();
    return $list;
  }

  static public function FindShowableNewslettersByAccount($accountid) {
    $status = self::STATUS_ACTIVE;
    $query = 
      "SELECT `id` FROM `newsletter` " .
      "WHERE `accountid` = {$accountid} AND `status` = '{$status}' " .
      "AND `showdate` < NOW() " .
      'ORDER BY `showdate` DESC';
    $result = database::Query($query);
    $list = array();
    while ($line = $result->fetch_assoc()) {
      $id = $line['id'];
      $list[$id] = new newsletter($id);
    }
    $result->close();
    return $list;
  }

  public function FindNewsletterItems() {
    $id = $this->ID();
//    $status = self::STATUS_ACTIVE;
    $query = 
      "SELECT `id` FROM `newsletteritem` " .
      "WHERE `newsletterid` = {$id} " .
      'ORDER BY `itemorder`';
    $result = database::Query($query);
    $list = array();
    while ($line = $result->fetch_assoc()) {
      $itemid = $line['id'];
      $list[$itemid] = new newsletteritem($itemid);
    }
    $result->close();
    return $list;
  }

  public function FindSubscribers() {
    $id = account::$instance->ID();
    $statusdeleted = self::STATUS_DELETED;
    $statuscancelled = self::STATUS_CANCELLED;
    $query = 
      "SELECT `id` FROM `newslettersubscriber` " .
      "WHERE `accountid` = {$id} " .
      "AND NOT (`status` IN ('{$statusdeleted}', '{$statuscancelled}')) " .
      'ORDER BY `status`, `datestarted` DESC';
    $result = database::Query($query);
    $list = array();
    while ($line = $result->fetch_assoc()) {
      $subid = $line['id'];
      $list[$subid] = new newslettersubscriber($subid);
    }
    $result->close();
    return $list;
  }

  public function AssignDataGridColumns($datagrid) {
    $datagrid->showactions = true;
    $datagrid->AddColumn('DESC', 'Title', true);
    $datagrid->AddColumn('SHOWDATE', 'Date');
  }

  public function AssignDataGridRows($datagrid) {
    $accountid = account::$instance->ID();
    $query =
      'SELECT * FROM `newsletter` ' .
      "WHERE `accountid` = {$accountid} " .
      "ORDER BY `showdate` DESC";
    $actions = array(formbuilderdatagrid::TBLOPT_DELETABLE);
    $list = array();
    $result = database::Query($query);
    while ($line = $result->fetch_assoc()) {
      $id = $line['id'];
      $coldata = array(
        'DESC' => $line['title'],
        'SHOWDATE' => $this->FormatDateTime(self::DF_MEDIUMDATE, $line['showdate'], '<em>none</em>')
      );
      $datagrid->AddRow($id, $coldata, true, $actions);
    }
    $result->free();
    return $list;
  }
}

<?php
namespace dana\table;

use dana\core;

require_once 'class.basetable.php';

/**
  * newsletter item type (types of newsletters)
  * @version dana framework v.3
*/

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

/**
  * newsletter table
  * @version dana framework v.3
*/

// newsletter
class newsletter extends idtable {
  public $showdate;
  public $showdatedescription;
  public $help;

  function __construct($id = 0) {
    parent::__construct('newsletter', $id);
  }

  protected function AfterPopulateFields() {
    $this->showdate = $this->GetFieldValue('showdate');
//    $date = strtotime($this->showdate);
    $this->showdatedescription = \dana\table\basetable::FormatDateTime(self::DF_MEDIUMDATE, $this->showdate);
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
    $this->AddField(\dana\table\basetable::FN_STATUS, self::DT_STATUS);
  }

  static public function FindNewslettersByAccount($accountid) {
    $status = self::STATUS_ACTIVE;
    $query =
      "SELECT `id` FROM `newsletter` " .
      "WHERE `accountid` = {$accountid} AND `status` = '{$status}' " .
      'ORDER BY `showdate` DESC';
    $result = \dana\core\database::Query($query);
    $list = array();
    while ($line = $result->fetch_assoc()) {
      $list[] = $line['id'];
    }
    $result->close();
    return $list;
  }

  static public function FindShowableNewslettersByAccount(
    $accountid, $status = self::STATUS_ACTIVE, $showdate = 'NOW()') {
    $sql = array('SELECT `id` FROM `newsletter` ');
    $sql[] = "WHERE `accountid` = {$accountid} ";
    if ($status) {
      $sql[] = "AND `status` = '{$status}' ";
    }
    if ($showdate) {
      $sql[] = "AND `showdate` > $showdate"; //(NOW() - INTERVAL 1 MONTH) " .
    }
    $sql[] = 'ORDER BY `showdate` DESC';
    $query = ArrayToString($sql);
//echo "<p>QUERY='{$query}'</p>";
    $result = \dana\core\database::Query($query);
    $list = array();
    while ($line = $result->fetch_assoc()) {
      $id = $line['id'];
      $list[$id] = new newsletter($id);
//echo "<p>ID={$id}</p>\n";
    }
    $result->close();
//exit;
    return $list;
  }

  public function FindNewsletterItems() {
    $id = $this->ID();
//    $status = self::STATUS_ACTIVE;
    $query =
      "SELECT `id` FROM `newsletteritem` " .
      "WHERE `newsletterid` = {$id} " .
      'ORDER BY `itemorder`';
    $result = \dana\core\database::Query($query);
    $list = array();
    while ($line = $result->fetch_assoc()) {
      $itemid = $line['id'];
      $list[$itemid] = new newsletteritem($itemid);
    }
    $result->close();
    return $list;
  }

//  public function FindSubscribers($accountid = false) {
//    if (!$accountid) {
//      $accountid = account::$instance->ID();
//    }
//    $statusdeleted = self::STATUS_DELETED;
//    $statuscancelled = self::STATUS_CANCELLED;
//    $query = 
//      "SELECT `id` FROM `newslettersubscriber` " .
//      "WHERE `accountid` = {$accountid} " .
//      "AND NOT (`status` IN ('{$statusdeleted}', '{$statuscancelled}')) " .
//      'ORDER BY `status`, `datestarted` DESC';
//    $result = \dana\core\database::Query($query);
//    $list = array();
//    while ($line = $result->fetch_assoc()) {
//      $subid = $line['id'];
//      $list[$subid] = new newslettersubscriber($subid);
//    }
//    $result->close();
//    return $list;
//  }

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
    $actions = array(\dana\formbuilder\formbuilderdatagrid::TBLOPT_DELETABLE);
    $list = array();
    $result = \dana\core\database::Query($query);
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

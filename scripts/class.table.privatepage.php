<?php
namespace dana\table;

//use dana\core;

require_once 'class.basetable.php';

/**
  * private area page table
  * @version dana framework v.3
*/

class privatepage extends linktable {

  public $page;

  function __construct($id = 0) {
    parent::__construct('privatepage', $id);
  }

  protected function AssignFields() {
//    parent::AssignFields();
    $this->AddField('privateareaid', self::DT_FK);
    $this->AddField('pageid', self::DT_FK);
  }

  protected function AfterPopulateFields() {
    $pageid = $this->GetFieldValue('pageid');
    $this->page = new page($pageid);
  }

  static public function GetList($groupid) {
    $accountid = account::$instance->ID();
    $status = self::STATUS_ACTIVE;
    $ret = array();
    $query =
      'SELECT pam.`id` FROM `privateareamember` pam ' .
      'INNER JOIN `privatemember` pm ON pm.`privateareamemberid` = pam.`id` ' .
      "WHERE pm.`privateareaid` = {$groupid} AND pam.`status` = '{$status}' AND pm.`status` = '{$status}' ORDER BY pam.`username`";
    $result = \dana\core\database::Query($query);
    while ($line = $result->fetch_assoc()) {
      $id = $line['id'];
      $itm = new privateareamember($id);
      if ($itm->exists) {
        $ret[$id] = $itm;
      }
    }
    $result->close();
    return $ret;
  }
}

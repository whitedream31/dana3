<?php
require_once 'class.database.php';
require_once 'class.table.page.php';

// private area member class
class privateareamember extends idtable {

  public $startdatedescription;
  public $lastlogindescription;

  function __construct($id = 0) {
    parent::__construct('privateareamember', $id);
  }

  protected function AssignFields() {
    parent::AssignFields();
    $this->AddField('accountid', DT_FK);
    $this->AddField('username', DT_STRING);
    $this->AddField('password', DT_STRING);
    $this->AddField('displayname', DT_DESCRIPTION);
    $this->AddField('email', DT_STRING);
    $this->AddField('startdate', DT_DATETIME);
    $this->AddField('lastlogin', DT_DATETIME);
    $this->AddField('status', DT_STRING);
  }

  protected function AfterPopulateFields() {
    $this->startdatedescription = $this->FormatDateTime(DF_LONGDATETIME, $this->GetFieldValue('startdate'));
    $this->lastlogindescription = $this->FormatDateTime(DF_LONGDATETIME, $this->GetFieldValue('lastlogin'), 'never');
  }

  public function GetDisplayDescription() {
    $displayname = $this->GetFieldValue('displayname');
    return ($displayname) ? $displayname : '<em>anonymous</em>';
  }

  static public function GetList($groupid) {
    $accountid = account::$instance->ID();
    $status = STATUS_ACTIVE;
    $ret = array();
    $query =
      'SELECT pam.`id` FROM `privateareamember` pam ' .
      'INNER JOIN `privatemember` pm ON pm.`privateareamemberid` = pam.`id` ' .
      "WHERE pm.`privateareaid` = {$groupid} AND pam.`status` = '{$status}' AND pm.`status` = '{$status}' ORDER BY pam.`username`";
    $result = database::Query($query);
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

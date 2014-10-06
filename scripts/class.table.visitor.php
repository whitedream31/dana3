<?php
require_once 'class.database.php';
require_once 'class.table.page.php';

// private area visitor class
class visitor extends idtable {

  public $startdatedescription;
  public $lastlogindescription;

  function __construct($id = 0) {
    parent::__construct('visitor', $id);
  }

  protected function AssignFields() {
    parent::AssignFields();
    $this->AddField('username', DT_STRING);
    $this->AddField('password', DT_STRING);
    $this->AddField('displayname', DT_DESCRIPTION);
    $this->AddField('session', DT_STRING);
    $this->AddField('email', DT_STRING);
    $this->AddField('startdate', DT_DATETIME);
    $this->AddField('lastlogin', DT_DATETIME);
    $this->AddField('status', DT_STRING);
  }

  public function AssignFormFields($formeditor, $idref) {
/*    $formeditor->AddField('title', DT_STRING, 'title');
*/
    return true;
  }

  protected function AfterPopulateFields() {
    $this->startdatedescription = $this->FormatDate(DF_LONGDATETIME, $this->GetFieldValue('startdate'));
    $this->lastlogindescription = $this->FormatDate(DF_LONGDATETIME, $this->GetFieldValue('lastlogin'), 'never');
  }

  static public function GetList($groupid) {
    $ret = array();
    $query = 'SELECT `visitorid` FROM `privatemember` ' .
      "WHERE `privateareaid` = {$groupid} ORDER BY `visitorid`";
    $result = database::$instance->Query($query);
    while ($line = $result->fetch_assoc()) {
      $id = $line['visitorid'];
      $itm = new visitor($id);
      if ($itm->exists) {
        $ret[$id] = $itm;
      }
    }
    $result->free();
    return $ret;
  }

  static public function FindBySession($session) {
    $query = 'SELECT `id` FROM `visitor` ' .
      "WHERE `session` = '{$session}'";
    $result = database::$instance->Query($query);
    $line = $result->fetch_assoc();
    $result->close();
    $id = ($line) ? $line['id'] : 0;
    return new visitor($id);
  }
}

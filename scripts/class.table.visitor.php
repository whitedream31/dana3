<?php
namespace dana\table;

//use dana\core;

require_once 'class.basetable.php';

/**
  * private area visitor table
  * created: 3 dec 2012
  * modified: 10 feb 2015
  * @version dana framework v.3
*/

class visitor extends idtable {

  public $startdatedescription;
  public $lastlogindescription;

  function __construct($id = 0) {
    parent::__construct('visitor', $id);
  }

  protected function AssignFields() {
    parent::AssignFields();
    $this->AddField('username', self::DT_STRING);
    $this->AddField('password', self::DT_STRING);
    $this->AddField('displayname', self::DT_DESCRIPTION);
    $this->AddField('session', self::DT_STRING);
    $this->AddField('email', self::DT_STRING);
    $this->AddField('startdate', self::DT_DATETIME);
    $this->AddField('lastlogin', self::DT_DATETIME);
    $this->AddField(self::FN_STATUS, self::DT_STATUS);
  }

  public function AssignFormFields($formeditor, $idref) {
/*    $formeditor->AddField('title', DT_STRING, 'title');
*/
    return true;
  }

  protected function AfterPopulateFields() {
    $this->startdatedescription =
      $this->FormatDate(self::DF_LONGDATETIME, $this->GetFieldValue('startdate'));
    $this->lastlogindescription =
      $this->FormatDate(self::DF_LONGDATETIME, $this->GetFieldValue('lastlogin'), 'never');
  }

  static public function GetList($groupid) {
    $ret = array();
    $query = 'SELECT `visitorid` FROM `privatemember` ' .
      "WHERE `privateareaid` = {$groupid} ORDER BY `visitorid`";
    $result = \dana\core\database::$instance->Query($query);
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
    $result = \dana\core\database::$instance->Query($query);
    $line = $result->fetch_assoc();
    $result->close();
    $id = ($line) ? $line['id'] : 0;
    return new visitor($id);
  }
}

<?php
require_once 'class.database.php';
require_once 'class.basetable.php';

define('DEFAULT_STATE_REF', 'PROVISIONAL');

// booking class
class booking extends idtable {

  public $bookingdatedescription;

  function __construct($id = 0) {
    parent::__construct('booking', $id);
  }

  protected function AssignFields() {
    parent::AssignFields();
    $this->AddField('accountid', DT_FK);
    $this->AddField('bookingsettingsid', DT_FK);
    $this->AddField('clientname', DT_STRING);
    // clientid
    //$this->AddField('addressid', DT_FK);
    $this->AddField('address', DT_STRING);
    $this->AddField('telephone', DT_STRING);
    $this->AddField('email', DT_STRING);
    $this->AddField('title', DT_STRING);
    $this->AddField('datestamp', DT_DATETIME);
    $this->AddField('startdate', DT_DATE);
    $this->AddField('timetext', DT_STRING);
    // bookingdurationid
//    $this->AddField('duration', DT_INTEGER); // ????
//    $this->AddField('content', DT_TEXT); // ???
    // bookingdurationid
    $this->AddField('notes', DT_TEXT);
    $this->AddField('confirmedbycontact', DT_BOOLEAN);
    $this->AddField('confirmedbyclient', DT_BOOLEAN);
    $this->AddField('bookingstateid', DT_FK);
    $this->AddField(FN_STATUS, DT_STATUS);
    $this->bookingdatedescription = '';
  }

  protected function AfterPopulateFields() {
    $this->bookingdatedescription =
      $this->FormatDateTime(DF_LONGDATETIME, $this->GetFieldValue('startdate'));
  }

  protected function AssignDefaultFieldValues() {
    $staterow = database::SelectFromTableByRef('bookingstate', DEFAULT_STATE_REF);
    $stateid = (int) $staterow['id'];
    $this->SetFieldValue('bookingstateid', $stateid);
  }
  
  public function GetActiveSettingsList() {
    $ret = array();
    $accountid = account::$instance->ID();
    $status = STATUS_ACTIVE;
    $query =
      'SELECT bs.`id`, bs.`description`, bt.`ref` AS bookingtyperef, bt.`description` AS bookingtype ' .
      'FROM `bookingsetting` bs ' .
      'INNER JOIN `bookingtype` bt ON bs.`bookingtypeid` = bt.`id` ' .
      "WHERE bs.`accountid` = {$accountid} AND bs.`STATUS` = '{$status}' " .
      'ORDER BY bs.`description`';
    $result = database::Query($query);
    while ($line = $result->fetch_assoc()) {
      $id = $line['id'];
      $ret[$id] = array(
        'description' => $line['description'],
        'bookingtype' => $line['bookingtype'],
        'bookingtyperef' => $line['bookingtyperef']
      );
    }
    $result->free();
    return $ret;
  }
/*
  public function StatusAsString($status = false) {
    $ret = '';
    $status = ($status) ? $status : $this->GetFieldValue(FN_STATUS);
    if ($status) {
      switch ($status) {
        case STATUS_ACTIVE:
          $ret = 'Active';
          break;
        case STATUS_DELETED:
          $ret = 'Deleted';
          break;
        case STATUS_PENDING:
          $ret = 'Pending';
          break;
        case STATUS_UNCONFIRMED:
          $ret = 'Unconfirmed';
          break;
        case STATUS_COMPLETED:
          $ret = 'Completed';
          break;
        case STATUS_INACTIVE:
          $ret = 'Inactive';
          break;
      }
    }
    return $ret;
  }
*/
  public function FindBookingEntries($settingid, $confirmed) {
    $ret = array();
    if ($confirmed) {
      $search = '(b.`confirmedbyclient` > 0) AND (b.`confirmedbycontact` > 0)';
    } else {
      $search = '(b.`confirmedbyclient` = 0) OR (b.`confirmedbycontact` = 0)';
    }
    /*
     * note:
     * provisional : confirmedbycontact = 1, confirmedbyclient = 0
     * confirmed:    confirmedbycontact = 1, confirmedbyclient = 1
     * cancelled:    confirmedbycontact = 0, confirmedbyclient = 0
     */
    $query =
      'SELECT b.`id` AS bookingid, b.`clientname`, b.`title`, b.`startdate`, b.`timetext`, bs.* ' .
      'FROM `booking` b ' .
      'INNER JOIN `bookingstate` bs ON b.`bookingstateid` = bs.`id` ' .
      "WHERE b.`bookingsettingsid` = {$settingid} AND {$search} ORDER BY b.`startdate` DESC, b.`timetext`";
    $result = database::Query($query);
    while ($line = $result->fetch_assoc()) {
      $id = $line['bookingid'];
      $ret[$id] = array(
        'clientname' => $line['clientname'],
        'title' => $line['title'],
        'startdate' => $line['startdate'],
        'timetext' => $line['timetext'],
        'statedesc' => $line['description'],
        'stateref' => $line['ref'],
        'statecolour' => $line['colour']
      );
    }
    $result->free();
    return $ret;
  }

  static public function GetList($accountid) {
    $ret = array();
    $status = STATUS_ACTIVE;
    $query = 'SELECT `id` FROM `booking` ' .
      "WHERE `accountid` = {$accountid} AND `status` = '{$status}'" .
      ' ORDER BY `datestamp` DESC';
    $result = database::Query($query);
    while ($line = $result->fetch_assoc()) {
      $id = $line['id'];
      $itm = new booking($id);
      if ($itm->exists) {
        $ret[$id] = $itm;
      }
    }
    $result->free();
    return $ret;
  }
}

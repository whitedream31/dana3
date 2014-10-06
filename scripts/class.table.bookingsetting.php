<?php
require_once 'class.database.php';
require_once 'class.basetable.php';

// booking settings class
class bookingsetting extends idtable {

  public $bookingdatedescription;

  function __construct($id = 0) {
    parent::__construct('bookingsetting', $id);
  }

  protected function AssignFields() {
    parent::AssignFields();
    $this->AddField('accountid', DT_FK);
    $this->AddField(FN_DESCRIPTION, DT_DESCRIPTION);
    // bookingtypeid
//    $this->AddField('unitname', DT_STRING);
//    $this->AddField('unitsatatime', DT_INTEGER);
    $this->AddField('workmondaystart', DT_STRING);
    $this->AddField('workmondayend', DT_STRING);
//    $this->AddField('workmondayinterval', DT_INTEGER);
    $this->AddField('worktuesdaystart', DT_STRING);
    $this->AddField('worktuesdayend', DT_STRING);
//    $this->AddField('worktuesdayinterval', DT_INTEGER);
    $this->AddField('workwednesdaystart', DT_STRING);
    $this->AddField('workwednesdayend', DT_STRING);
//    $this->AddField('workwednesdayinterval', DT_INTEGER);
    $this->AddField('workthursdaystart', DT_STRING);
    $this->AddField('workthursdayend', DT_STRING);
//    $this->AddField('workthursdayinterval', DT_INTEGER);
    $this->AddField('workfridaystart', DT_STRING);
    $this->AddField('workfridayend', DT_STRING);
//    $this->AddField('workfridayinterval', DT_INTEGER);
    $this->AddField('worksaturdaystart', DT_STRING);
    $this->AddField('worksaturdayend', DT_STRING);
//    $this->AddField('worksaturdayinterval', DT_INTEGER);
    $this->AddField('worksundaystart', DT_STRING);
    $this->AddField('worksundayend', DT_STRING);
//    $this->AddField('worksundayinterval', DT_INTEGER);
    $this->AddField('provisionalmessage', DT_STRING);
    $this->AddField('confirmedmessage', DT_STRING);
    $this->AddField('cancelledmessage', DT_STRING); // ?????
    // addressrequired
    // typicaldurationid
    $this->AddField(FN_STATUS, DT_STATUS);
  }

  protected function AfterPopulateFields() {
  }

  static public function GetList($accountid) {
    $ret = array();
    $query = 'SELECT `id` FROM `bookingsetting` ' .
      'WHERE `accountid` = {$accountid} ORDER BY `description`';
    $result = database::Query($query);
    while ($line = $result->fetch_assoc()) {
      $id = $line['id'];
      $itm = new bookingsetting($id);
      if ($itm->exists) {
        $ret[$id] = $itm;
      }
    }
    $result->free();
    return $ret;
  }
}

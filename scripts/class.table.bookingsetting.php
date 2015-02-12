<?php
namespace dana\table;

use dana\core;

require_once 'class.basetable.php';

/**
  * booking settings table
  * @version dana framework v.3
*/

class bookingsetting extends idtable {

  public $bookingdatedescription;

  function __construct($id = 0) {
    parent::__construct('bookingsetting', $id);
  }

  protected function AssignFields() {
    parent::AssignFields();
    $this->AddField('accountid', self::DT_FK);
    $this->AddField(self::FN_DESCRIPTION, self::DT_DESCRIPTION);
    // bookingtypeid
    $this->AddField('bookingtypeid', self::DT_FK);
//    $this->AddField('unitname', DT_STRING);
//    $this->AddField('unitsatatime', DT_INTEGER);
    $this->AddField('workmondaystart', self::DT_STRING);
    $this->AddField('workmondayend', self::DT_STRING);
//    $this->AddField('workmondayinterval', DT_INTEGER);
    $this->AddField('worktuesdaystart', self::DT_STRING);
    $this->AddField('worktuesdayend', self::DT_STRING);
//    $this->AddField('worktuesdayinterval', DT_INTEGER);
    $this->AddField('workwednesdaystart', self::DT_STRING);
    $this->AddField('workwednesdayend', self::DT_STRING);
//    $this->AddField('workwednesdayinterval', DT_INTEGER);
    $this->AddField('workthursdaystart', self::DT_STRING);
    $this->AddField('workthursdayend', self::DT_STRING);
//    $this->AddField('workthursdayinterval', DT_INTEGER);
    $this->AddField('workfridaystart', self::DT_STRING);
    $this->AddField('workfridayend', self::DT_STRING);
//    $this->AddField('workfridayinterval', DT_INTEGER);
    $this->AddField('worksaturdaystart', self::DT_STRING);
    $this->AddField('worksaturdayend', self::DT_STRING);
//    $this->AddField('worksaturdayinterval', DT_INTEGER);
    $this->AddField('worksundaystart', self::DT_STRING);
    $this->AddField('worksundayend', self::DT_STRING);
//    $this->AddField('worksundayinterval', DT_INTEGER);
    $this->AddField('provisionalmessage', self::DT_STRING);
    $this->AddField('confirmedmessage', self::DT_STRING);
    $this->AddField('cancelledmessage', self::DT_STRING); // ?????
    // addressrequired
    // typicaldurationid
    $this->AddField(\dana\table\basetable::FN_STATUS, self::DT_STATUS);
  }

  protected function AfterPopulateFields() {
  }

  static public function GetList($accountid) {
    $ret = array();
    $query = 'SELECT `id` FROM `bookingsetting` ' .
      'WHERE `accountid` = {$accountid} ORDER BY `description`';
    $result = \dana\core\database::Query($query);
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

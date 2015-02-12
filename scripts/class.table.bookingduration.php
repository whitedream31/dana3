<?php
namespace dana\table;

use dana\core;

require_once 'class.basetable.php';

/**
  * booking duration table
  * @version dana framework v.3
*/

class bookingduration extends lookuptable {

  function __construct($id = 0) {
    parent::__construct('bookingduration', $id);
  }

  protected function AssignFields() {
    parent::AssignFields();
    $this->AddField('minutes', self::DT_INTEGER);
    $this->AddField('hours', self::DT_INTEGER);
    $this->AddField('days', self::DT_INTEGER);
  }

  protected function AfterPopulateFields() {
  }
}

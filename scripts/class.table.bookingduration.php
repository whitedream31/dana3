<?php
require_once 'class.database.php';
require_once 'class.basetable.php';

// booking duration class
class bookingduration extends lookuptable {

  function __construct($id = 0) {
    parent::__construct('bookingduration', $id);
  }

  protected function AssignFields() {
    parent::AssignFields();
    $this->AddField('minutes', DT_INTEGER);
    $this->AddField('hours', DT_INTEGER);
    $this->AddField('days', DT_INTEGER);
  }

  protected function AfterPopulateFields() {
  }
}

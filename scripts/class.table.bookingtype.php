<?php
require_once 'class.database.php';
require_once 'class.basetable.php';

// booking duration class
class bookingtype extends lookuptable {

  function __construct($id = 0) {
    parent::__construct('bookingtype', $id);
  }

  protected function AssignFields() {
    parent::AssignFields();
  }

  protected function AfterPopulateFields() {
  }
}

<?php
namespace dana\table;

use dana\core;

require_once 'class.basetable.php';

/**
  * booking type table
  * @version dana framework v.3
*/

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

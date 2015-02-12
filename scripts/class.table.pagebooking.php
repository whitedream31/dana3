<?php
namespace dana\table;

use dana\core;

require_once 'class.table.page.php';

/**
  * page booking class - BOOKING
  * written by Ian Stewart (c) 2012 Whitedream Software
  * created: 8 dec 2012
  * modified: 10 feb 2015
  * @version dana framework v.3
*/

class pagebooking extends page {

  protected function AssignPageType() {
    $this->pgtype = self::PAGETYPE_BOOKING;
  }
}

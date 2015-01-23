<?php
// BOOKING page container class for MyLocalSmallBusiness
// written by Ian Stewart (c) 2012 Whitedream Software
// created: 8 dec 2012
// modified: 13 jan 2015

require_once 'class.table.page.php';

// booking page class
class pagebooking extends page {

  protected function AssignPageType() {
    $this->pgtype = self::PAGETYPE_BOOKING;
  }
}

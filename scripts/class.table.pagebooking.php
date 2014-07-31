<?php
// BOOKING page container class for MyLocalSmallBusiness
// written by Ian Stewart (c) 2012 Whitedream Software
// created: 8 dec 2012
// modified: 27 jul 2014

require_once 'class.table.page.php';

// booking page class
class pagebooking extends page {

  protected function AssignPageType() {
    $this->pgtype = PAGETYPE_BOOKING;
  }
}

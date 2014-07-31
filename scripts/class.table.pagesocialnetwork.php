<?php
// SOCIAL NETWORK page container class for MyLocalSmallBusiness
// written by Ian Stewart (c) 2012 Whitedream Software
// created: 8 dec 2012
// modified: 18 feb 2013

require_once 'class.table.page.php';

// social network page class
class pagesocialnetwork extends page {

  protected function AssignPageType() {
    $this->pgtype = PAGECREATION_SOCIALNETWORK;
  }

  // assign table columns just used by this type of page
  protected function AssignPageTypeFields() {}

  public function AssignFormFields($formeditor, $idref) {
  }

  public function ValidateFields() {
  }
}

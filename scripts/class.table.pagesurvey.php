<?php
// SURVEY page container class for MyLocalSmallBusiness
// written by Ian Stewart (c) 2012 Whitedream Software
// created: 8 dec 2012
// modified: 18 feb 2013

require_once 'class.table.page.php';

// survey page class
class pagesurvey extends page {

  protected function AssignPageType() {
    $this->pgtype = PAGECREATION_SURVEY;
  }

  // assign table columns just used by this type of page
  protected function AssignPageTypeFields() {}

  public function AssignFormFields($formeditor, $idref) {
  }

  public function ValidateFields() {
  }
}

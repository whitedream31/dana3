<?php
// ABOUT US page container class for MyLocalSmallBusiness
// written by Ian Stewart (c) 2012 Whitedream Software
// created: 8 dec 2012 (originally 7 apr 2010)
// modified: 18 feb 2013

require_once 'class.table.page.php';

// about us page class
class pageaboutus extends page {

  protected function AssignPageType() {
    $this->pgtype = PAGECREATION_ABOUTUS;
  }

  // assign table columns just used by this type of page
  protected function AssignPageTypeFields() {
    $this->AddField('showmap', DT_BOOLEAN, true);
    $this->AddField('mapaddress', DT_STRING);
  }

  public function AssignFormFields($formeditor, $idref) {
  }

  public function ValidateFields() {
    
  }
}

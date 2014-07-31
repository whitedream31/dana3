<?php
// PRODUCT page container class for MyLocalSmallBusiness
// written by Ian Stewart (c) 2012 Whitedream Software
// created: 8 dec 2012 (originally 7 apr 2010)
// modified: 18 feb 2013

require_once 'class.table.page.php';

// product page class
class pageproduct extends page {

  protected function AssignPageType() {
    $this->pgtype = PAGECREATION_PRODUCT;
  }

  // assign table columns just used by this type of page
  protected function AssignPageTypeFields() {
    $this->AddField('includespecialoffers', DT_BOOLEAN, true);
    $this->AddField('includeprices', DT_BOOLEAN, true);
    $this->AddField('includeimage', DT_BOOLEAN, true);
    $this->AddField('includedelivery', DT_BOOLEAN, true);
    $this->AddField('productsperpage', DT_INTEGER, 12);
  }

  public function AssignFormFields($formeditor, $idref) {
  }

  public function ValidateFields() {
    
  }
}

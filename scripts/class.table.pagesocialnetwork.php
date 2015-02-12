<?php
namespace dana\table;

use dana\core;

require_once 'class.table.page.php';

/**
  * page socialnetwork class - SOCIALNETWORK
  * written by Ian Stewart (c) 2012 Whitedream Software
  * created: 8 dec 2012
  * modified: 10 feb 2015
  * @version dana framework v.3
*/

class pagesocialnetwork extends page {

  protected function AssignPageType() {
    $this->pgtype = page::PAGETYPE_SOCIALNETWORK;
  }

  // assign table columns just used by this type of page
  protected function AssignPageTypeFields() {}

  public function AssignFormFields($formeditor, $idref) {
  }

  public function ValidateFields() {
  }
}

<?php
require_once 'class.formbuilderbase.php';

/**
 * hidden field - FLDTYPE_HIDDEN
 * part of the formbuilder class set
 */

class formbuilderhidden extends formbuilderbase {

  function __construct($name, $value) {
    parent::__construct($name, $value, FLDTYPE_HIDDEN, '');
  }

  public function GetControl() {
    return array(
      "<input type='hidden'" . $this->IncludeAllAttributes() . "value='{$this->GetValue()}'>"
    );
  }
}

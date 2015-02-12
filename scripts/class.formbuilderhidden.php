<?php
namespace dana\formbuilder;

require_once 'class.formbuilderbase.php';

/**
  * hidden field - FLDTYPE_HIDDEN
  * @version dana framework v.3
*/

class formbuilderhidden extends formbuilderbase {

  function __construct($name, $value) {
    parent::__construct($name, $value, \dana\table\basetable::FLDTYPE_HIDDEN, '');
  }

  public function GetControl() {
    return array(
      "<input type='hidden'" . $this->IncludeAllAttributes() . " value='{$this->GetValue()}'>"
    );
  }
}

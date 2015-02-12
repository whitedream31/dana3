<?php
namespace dana\formbuilder;

use dana\table;

require_once 'class.formbuildereditbox.php';

/**
  * time field - FLDTYPE_TIME - detrieved from edit box
  * @version dana framework v.3
*/

class formbuildertime extends formbuildereditbox {

  function __construct($name, $value, $label = '') {
    parent::__construct($name, $value, $label);
    $this->fieldtype = \dana\table\basetable::FLDTYPE_TIME;
    $this->size = 5;
    $this->maxlength = 5;
  }

  public function GetControl() {
    $ret = array(
      "<input type='time' name='{$this->name}' id='{$this->id}' value='{$this->GetValue()}'" .
      $this->IncludeAllAttributes() .
      $this->AddDisabled() . $this->AddReadOnly() . $this->AddRequired() . " >"
    );
    return $ret;
  }
}

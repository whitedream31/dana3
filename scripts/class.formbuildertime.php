<?php
require_once 'class.formbuildereditbox.php';

// date field - FLDTYPE_TIME - detrieved from edit box
class formbuildertime extends formbuildereditbox {

  function __construct($name, $value, $label = '') {
    parent::__construct($name, $value, $label);
    $this->fieldtype = FLDTYPE_TIME;
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

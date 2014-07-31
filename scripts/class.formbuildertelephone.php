<?php
require_once 'class.formbuildereditbox.php';

// telephone field - FLDTYPE_TELEPHONE - derived from edit box
class formbuildertelephone extends formbuildereditbox {

  function __construct($name, $value, $label = '') {
    parent::__construct($name, $value, $label);
    $this->fieldtype = FLDTYPE_TELEPHONE;
    $this->size = 30;
    $this->maxlength = 30;
    $this->inputtype = 'tel';
  }

}

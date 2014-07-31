<?php
require_once 'class.formbuildereditbox.php';

// email field - FLDTYPE_EMAIL - detrieved from edit box
class formbuilderemail extends formbuildereditbox {

  function __construct($name, $value, $label = '') {
    parent::__construct($name, $value, $label);
    $this->fieldtype = FLDTYPE_EMAIL;
    $this->size = 50;
    $this->maxlength = 100;
    $this->inputtype = 'email';
  }
}

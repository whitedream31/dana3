<?php
require_once 'class.formbuildereditbox.php';

// url field - FLDTYPE_URL - detrieved from edit box
class formbuilderurl extends formbuildereditbox {

  function __construct($name, $value, $label = '', $size = 80) {
    parent::__construct($name, $value, $label);
    $this->fieldtype = FLDTYPE_URL;
    $this->size = $size;
    $this->maxlength = 200;
    $this->pattern = "https?://.+";
    $this->inputtype = 'url';
  }
}

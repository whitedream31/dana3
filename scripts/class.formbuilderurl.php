<?php
namespace dana\formbuilder;

use dana\table;

require_once 'class.formbuildereditbox.php';

/**
  * url field - FLDTYPE_URL - detrieved from edit box
  * @version dana framework v.3
*/

class formbuilderurl extends formbuildereditbox {

  function __construct($name, $value, $label = '', $size = 80) {
    parent::__construct($name, $value, $label);
    $this->fieldtype = \dana\table\basetable::FLDTYPE_URL;
    $this->size = $size;
    $this->maxlength = 200;
    $this->pattern = "https?://.+";
    $this->inputtype = 'url';
  }
}

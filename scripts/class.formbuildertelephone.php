<?php
namespace dana\formbuilder;

require_once 'class.formbuildereditbox.php';

/**
  * telephone field - FLDTYPE_TELEPHONE - detrieved from edit box
  * @version dana framework v.3
*/

// telephone field - FLDTYPE_TELEPHONE - derived from edit box
class formbuildertelephone extends formbuildereditbox {

  function __construct($name, $value, $label = '') {
    parent::__construct($name, $value, $label);
    $this->fieldtype = \dana\table\basetable::FLDTYPE_TELEPHONE;
    $this->size = 30;
    $this->maxlength = 30;
    $this->inputtype = 'tel';
  }

}

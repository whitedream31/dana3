<?php
namespace dana\formbuilder;

use dana\table;

require_once 'class.formbuildereditbox.php';

/**
  * email field - FLDTYPE_EMAIL - detrieved from edit box
  * @version dana framework v.3
*/

class formbuilderemail extends formbuildereditbox {

  function __construct($name, $value, $label = '') {
    parent::__construct($name, $value, $label);
    $this->fieldtype = \dana\table\basetable::FLDTYPE_EMAIL;
    $this->size = 50;
    $this->maxlength = 100;
    $this->inputtype = 'email';
  }
}

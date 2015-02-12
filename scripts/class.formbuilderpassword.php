<?php
namespace dana\formbuilder;

require_once 'class.formbuildereditbox.php';

/**
  * password field - FLDTYPE_PASSWORD
  * @version dana framework v.3
*/

class formbuilderpassword extends formbuildereditbox {

  function __construct($name, $value, $label = '') {
    parent::__construct($name, $value, $label);
    $this->fieldtype = \dana\table\basetable::FLDTYPE_PASSWORD;
    $this->required = true;
    $this->inputtype = 'password';
  }
}

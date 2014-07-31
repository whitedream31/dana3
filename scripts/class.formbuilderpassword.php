<?php
  require_once 'class.formbuildereditbox.php';

  // password field - FLDTYPE_PASSWORD
  class formbuilderpassword extends formbuildereditbox {

    function __construct($name, $value, $label = '') {
      parent::__construct($name, $value, $label);
      $this->fieldtype = FLDTYPE_PASSWORD;
      $this->required = true;
      $this->inputtype = 'password';
    }
  }
?>
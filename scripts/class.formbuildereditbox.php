<?php
namespace dana\formbuilder;

use dana\table;

require_once 'class.formbuilderbase.php';

/**
  * edit box field - FLDTYPE_EDITBOX
  * part of the formbuilder class set
  * @version dana framework v.3
*/

class formbuildereditbox extends formbuilderbase {
  public $size = false;
  public $maxlength = false;
  public $placeholder = false;
  public $pattern = false;
  public $inputtype = 'text';

  function __construct($name, $value, $label = '') {
    parent::__construct($name, $value, \dana\table\basetable::FLDTYPE_EDITBOX, $label);
  }

  protected function AddAttributesAndValues() {
    parent::AddAttributesAndValues();
    $this->AddAttribute('size', $this->size);
    $this->AddAttribute('maxlength', $this->maxlength);
    $this->AddAttribute('placeholder', $this->placeholder);
    $this->AddAttribute('pattern', $this->pattern);
  }

  public function GetControl() {
    return array(
      "<input type='{$this->inputtype}' value='{$this->GetValue()}'" .
      $this->IncludeAllAttributes() .
      $this->AddDisabled() . $this->AddReadOnly() . $this->AddRequired() . " >");
  }
}

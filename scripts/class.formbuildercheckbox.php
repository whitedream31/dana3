<?php
require_once 'class.formbuilderbase.php';

// checkbox field - FLDTYPE_CHECKBOX
class formbuildercheckbox extends formbuilderbase {
  public $checked;
  public $tickedvalue = 'yes';

  function __construct($name, $value, $label = '') {
    parent::__construct($name, $value, FLDTYPE_CHECKBOX, $label);
  }

  protected function AddChecked() {
    return $this->AddOption('checked', $this->checked);
  }

  protected function GetValue() {
    $this->value = (int) $this->value;
  }

  protected function ConvertValue() {
    return in_array(strtolower($this->value), array('t', 'y', 'true', 'yes', '1', 'on', $this->tickedvalue));
  }

  protected function ValidateValue() {
    $this->checked = $this->ConvertValue();
    return $this->checked;
  }

  protected function GetPostValue() {
    $ret = (isset($_POST[$this->name]));
    $this->value = (int) $ret;
  }

  protected function CheckPostExists() {
    return true;
/*    $ret = (isset($_POST[$this->name]));
    $this->value = (int) $ret;
    return $ret; */
  }

  protected function AddAttributesAndValues() {
    parent::AddAttributesAndValues();
//    $this->AddAttribute('size', $this->size);
//    $this->AddAttribute('maxlength', $this->maxlength);
  }

  public function GetControl() {
    $this->checked = $this->ConvertValue();
    return array(
      "<input type='checkbox' " .
      "value='{$this->tickedvalue}' " . $this->AddChecked() . $this->AddRequired() .
      $this->IncludeAllAttributes() . " >"
    );
  }
}
?>
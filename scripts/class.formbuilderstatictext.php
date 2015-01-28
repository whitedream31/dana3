<?php
require_once 'class.formbuilderbase.php';

class formbuilderstatictext extends formbuilderbase {
  public $emptyvalue = '';

  function __construct($name, $value, $label = '') {
    parent::__construct($name, $value, basetable::FLDTYPE_STATIC, $label);
  }

  public function GetControl() {
    $value = $this->GetValue();
    if (IsBlank($value)) {
      $value = '[' . $this->emptyvalue . ']';
    }
    return array(
      "<p name='{$this->name}' id='{$this->id}'>{$value}</p>"
    );
  }
}

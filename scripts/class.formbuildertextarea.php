<?php
require_once('class.formbuilderbase.php');

// text area field - FLDTYPE_TEXTAREA
class formbuildertextarea extends formbuilderbase {
  public $rows;
  public $cols;
  public $placeholder;

  function __construct($name, $value, $label = '') {
    parent::__construct($name, $value, FLDTYPE_TEXTAREA, $label);
    $this->rows = 20;
    $this->cols = 60;
  }

  protected function AddAttributesAndValues() {
    parent::AddAttributesAndValues();
    $this->AddAttribute('rows', $this->rows);
    $this->AddAttribute('cols', $this->cols);
    $this->AddAttribute('placeholder', $this->placeholder);
  }

  public function GetControl() {
    return array(
      "<textarea name='{$this->name}' id='{$this->id}'" .
        $this->IncludeAllAttributes() .
        $this->AddDisabled() . $this->AddReadOnly() . $this->AddRequired() . ">{$this->GetValue()}</textarea>"
    );
  }
}

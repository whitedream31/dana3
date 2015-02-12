<?php
namespace dana\formbuilder;

require_once 'class.formbuilderbase.php';

/**
  * text area field - FLDTYPE_TEXTAREA
  * @version dana framework v.3
*/

class formbuildertextarea extends formbuilderbase {
  public $rows;
  public $cols;
  public $placeholder;
  public $enableeditor = true; //false;

  function __construct($name, $value, $label = '') {
    parent::__construct($name, $value, \dana\table\basetable::FLDTYPE_TEXTAREA, $label);
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
//    if ($this->enableeditor && strpos($this->classname, ' editable') !== false) {
    if ($this->enableeditor) {
      $this->classname .= ' editable';
    }
    return array(
      "<textarea" .
        $this->IncludeAllAttributes() .
        $this->AddDisabled() . $this->AddReadOnly() . $this->AddRequired() . ">{$this->GetValue()}</textarea>"
    );
  }
}

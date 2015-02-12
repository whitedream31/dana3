<?php
namespace dana\formbuilder;

require_once 'class.formbuilderbase.php';

/**
  * static (non edit) field - FLDTYPE_STATIC
  * @version dana framework v.3
*/

class formbuilderstatictext extends formbuilderbase {
  public $emptyvalue = '';

  function __construct($name, $value, $label = '') {
    parent::__construct($name, $value, \dana\table\basetable::FLDTYPE_STATIC, $label);
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

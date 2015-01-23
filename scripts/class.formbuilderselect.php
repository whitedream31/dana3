<?php
require_once 'class.formbuildermultivalue.php';

/**
 * select field - FLDTYPE_SELECT
 * part of the formbuilder class set
 */
class formbuilderselect extends formbuildermultivalue {
  public $size;
  public $includenone = false;
  public $nonecaption = 'None';

  function __construct($name, $value, $label = '') {
    parent::__construct($name, $value, basetable::FLDTYPE_SELECT, $label);
  }

  public function SetNoneCaption($value) {
    $this->includenone = $value != '';
    if ($this->includenone) {
      $this->nonecaption = $value;
    }
  }

  protected function AddChecked($item) {
    return $this->AddOption('selected', $this->selected == $item);
  }

  protected function AddAttributesAndValues() {
    parent::AddAttributesAndValues();
    $this->AddAttribute('size', $this->size);
  }

  protected function GetValue() {
//      $this->value = $this->selected;
    return $this->value;
  }

  public function GetControl() {
    if (IsBlank($this->selected)) {
      $this->selected = $this->value;
    }
    $ret = array();
    $ret[] = "<select " . //name='{$this->name}'" .
      $this->IncludeAllAttributes() .
      $this->AddOption('multiple', $this->ismultiple) . ">";
    if ($this->includenone && $this->nonecaption) { //(!$this->value)) {
      $ret[] = "<option value='0' selected>{$this->nonecaption}</option>";
    }
    if (count($this->group) > 0) {
      foreach($this->group as $keygroup => $grouplist) {
        $ret[] = "<optgroup label='{$keygroup}'>";
        foreach($grouplist as $key => $value) {
          $ret[] = "<option value='{$key}'{$this->AddChecked($key)}>{$value}</option>";
        }
        $ret[] = '</optgroup>';
      }
    } else {
      foreach($this->list as $key => $value) {
        if (!$value) {
          $value = $key;
        }
        $ret[] = "<option value='{$key}'{$this->AddChecked($key)}>{$value}</option>";
      }
    }
    $ret[] = '</select>';
    return $ret;
  }

}

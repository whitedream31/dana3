<?php
require_once 'class.formbuilder.multivalue.php';

// radio field - FLDTYPE_RADIO
class radiofield extends multivaluefield {
  public $selected;

  function __construct($name, $value, $label = '') {
    parent::__construct($name, $value, basetable::FLDTYPE_RADIO, $label);
  }

  public function GetControl() {
    $ret = array();
    if ($this->value) {
      $this->selected = $this->value;
    }
    foreach($this->list as $key => $value) {
      if (!$value) {
        $value = $key;
      }
      $ret[] =
        "<div class='fieldselection'>" .
        "  <input type='radio' name='{$this->name}' " . //id=\"{$this->id}\" " .
        "value='{$key}'" . $this->AddChecked($key) .
        $this->IncludeAllAttributes() . ">{$value}" .
        '</div>';
    }
    return $ret;
  }
}

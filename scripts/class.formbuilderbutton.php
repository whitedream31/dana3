<?php
require_once 'class.formbuildereditbox.php';

// button field - FLDTYPE_BUTTON
class formbuilderbutton extends formbuildereditbox {
  public $caption;
  public $url;
  public $icon;

  function __construct($name, $value, $label = '') {
    parent::__construct($name, $value, $label);
    $this->fieldtype = FLDTYPE_BUTTON;
    $this->inputtype = 'button';
    $this->size = 50;
    $this->classname = 'fieldbutton';
    $this->url = '';
  }

  protected function AddAttributesAndValues() {
    parent::AddAttributesAndValues();
//echo "<p>URL = '{$url}'</p>\n";
    if ($this->url) {
      $event = "javascript:window.open('{$this->url}', '_self');";
      $this->AddAttribute('onclick', $event);
    }
  }

/*  public function GetControl() {
//  $this->GetValue()
    return array(
      "<input type='button' name='{$this->name}' id='{$this->id}'" .
        $this->IncludeAllAttributes() .
        $this->AddDisabled() . "/>"
    );
  } */
}

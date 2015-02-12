<?php
namespace dana\worker;

require_once 'class.formbuilderbase.php';

/**
  * worker form field control class
  * container class for all worker form control fields
  * @version dana framework v.3
*/

class workerformfieldcontrol extends workerbase {
  protected $control = false;

  protected function DoPrepare() {}

  public function SetControl($control) {
    if ($control instanceof \dana\formbuilder\formbuilderbase) {
      $this->control = $control;
    }
  }

  public function GetControl() {
    return $this->control;
  }

  public function BindControl($table) {
    if ($this->control) {
      $this->control->BindToTable($table);
    }
  }

  public function Execute() {}

  public function AsArray() {
    return ($this->control) ? $this->control->GetFieldAsArray() : false;
  }
}

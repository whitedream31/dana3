<?php
require_once 'class.formbuildereditbox.php';

define('DATEFMT_DMY', 'dmy');
define('DATEFMT_MDY', 'mdy');
define('DATEFMT_YMD', 'ymd');

// date field - FLDTYPE_DATE - detrieved from edit box
class formbuilderdate extends formbuildereditbox {
  public $inputformat = DATEFMT_DMY;
  public $outputformat = DATEFMT_YMD;
  public $separator = '-';

  function __construct($name, $value, $label = '') {
    parent::__construct($name, $value, $label);
    $this->fieldtype = FLDTYPE_DATE;
    $this->size = 10;
    $this->maxlength = 10;
  }

  protected function ValidateValue() {
    $ret = false;
    $date = $this->value;
    if ($date) {
      list($yy, $mm, $dd) = explode('-', $date); /// $this->separator instead of '-' ?
      if (is_numeric($yy) && is_numeric($mm) && is_numeric($dd)) {
        $ret = checkdate($mm, $dd, $yy);
      }
    }
    return $ret;
  }

  // convert the value to array of parts, then back again for errors
  // then convert to a datetime value, finally returning the date in output format
  protected function GetValue() {
    try {
      $parts = explode($this->separator, $this->value);
      $datecheck = implode($this->separator, $parts);
      if (!$date = strtotime($datecheck)) {
        $date = time();
      }
    }
    catch (Exception $e) {
      $date = time();
    }
    return date($this->DateTypeToDateFormat($this->outputformat), $date);
  }

  protected function DateTypeToDateFormat($dt) {
    switch ($dt) {
      case DATEFMT_DMY: // DMY
        $ret = 'd' . $this->separator . 'm' . $this->separator . 'Y';
        break;
      case DATEFMT_MDY: // MDY
        $ret = 'm' . $this->separator . 'd' . $this->separator . 'Y';
        break;
      default: // DATEFMT_YMD: // YMD
        $ret = 'Y' . $this->separator . 'm' . $this->separator . 'd';
        break;
    }
    return $ret;
  }

  public function GetControl() {
    $ret = array();
    if ($this->usehtml5) {
      $ret[] =
        "<input type='date' name='{$this->name}' id='{$this->id}' value='{$this->GetValue()}'" .
        $this->IncludeAllAttributes() .
        $this->AddDisabled() . $this->AddReadOnly() . $this->AddRequired() . " >";
    } else {
      $event = "displayDatePicker('{$this->name}', '{$this->inputformat}', '{$this->separator}');";
      $dispfmt = $this->DateTypeToDateFormat($this->inputformat);
      $ret[] = '<small>(use ' . strtoupper($dispfmt) . ' format)</small>&nbsp;' .
        "<input name='{$this->name}' type='text' value='{$this->value}' onclick='{$event}' " .
        $this->IncludeAllAttributes() .
        $this->AddDisabled() . $this->AddReadOnly() . $this->AddRequired() . " >" .
        "<input type='button' class='dateselection' value='select' onclick='{$event}'>";
    }
    return $ret;
  }
}

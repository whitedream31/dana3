<?php
namespace dana\formbuilder;

use dana\table;

require_once('class.formbuilderbase.php');

/**
  * abstract class for multi-value field
  * @version dana framework v.3
*/

abstract class formbuildermultivalue extends formbuilderbase {
  public $list;
  public $ismultiple;
  public $selected;
  public $group;

  function __construct($name, $value, $fieldtype, $label = '') {
    parent::__construct($name, $value, $fieldtype, $label);
    $this->list = array();
    $this->group = array();
  }

  function __toString() {
    if ($this->ismultiple) {
      $ret = ''; // TODO
    } else {
      $ret = (string)$this->value;
    }
    return $ret;
  }

  protected function AddChecked($item) {
    return $this->AddOption('checked', $this->selected == $item);
  }
/*
  protected function ValidateValue() {
    return $this->selected; // value ?
  }
*/
  public function AddToGroup($groupname, $key, $description) {
    if (!isset($this->group[$groupname])) {
      $this->group[$groupname] = array();
    }
    $this->group[$groupname][$key] = $description;
  }

  public function AddValue($key, $description, $selected = false) {
    $this->list[$key] = $description;
    if ($selected) {
      $this->selected = $key;
    }
  }
}

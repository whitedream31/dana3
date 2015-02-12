<?php
namespace dana\formbuilder;

//use dana\table;

require_once 'class.formbuilderbase.php';

/**
  * summary box field - FLDTYPE_SUMMARYBOX
  * @version dana framework v.3
*/

class formbuildersummarybox extends formbuilderbase {
  public $list = array();
  public $changeidname = false;
  public $changecaption = 'Change';
  public $worker = false;

  function __construct($name, $value, $label = '') {
    parent::__construct($name, $value, \dana\table\basetable::FLDTYPE_SUMMARYBOX, $label);
  }

  public function AddItem($key, $caption, $value, $default = '') {
    if (!IsBlank($value)) {
      $this->list[$key] = array(
        'caption' => $caption,
        'value' => $value,
        'default' => $default
      );
    }
  }

  public function AddItemWithField($key, $caption, $fieldname, $default = '') {
    if ($this->table && $this->table instanceof \dana\table\basetable) {
      $this->AddItem($key, $caption, $this->table->GetFieldValue($fieldname), $default);
    }
  }

  public function AddItemLookup(
    $key, $caption, $lookuptablename, $lookupid,
    $lookupdescfieldname = \dana\table\basetable::FN_DESCRIPTION, $default = '') {
    $lookupidvalue = (is_string($lookupid))
      ? $this->table->GetFieldValue($lookupid)
      : $lookupid;
    $value =
      \dana\core\database::$instance->SelectDescriptionFromLookup($lookuptablename, $lookupidvalue);
    $this->AddItem($key, $caption, $value, $default);
  }

  private function BuildList() {
    $ret = array(
      '<div>',
      "  <table class='summarybox' " . $this->IncludeAllAttributes() //name='{$this->name}' id='{$this->id}'>"
    );
    foreach ($this->list as $key => $item) {
      $caption = $item['caption'];
      $value = $item['value'];
      if (IsBlank($value)) {
        $value = $item['default'];
      }
      $ret[] = '  <tr>';
      if ($caption) {
        $ret[] = "    <td class='summaryboxcaption'>{$caption}</td>";
      } else {
        $ret[] = "    <td></td>";
      }
      $ret[] = "    <td class='summaryboxvalue'>{$value}</td>";
      $ret[] = '  </tr>';
    }
    $ret[] = '  </table>';
    if ($this->worker && $this->worker instanceof \dana\worker\workerbase && $this->changeidname && $this->changecaption) {
      $ret[] = $this->worker->GetControlButton($this->changeidname, $this->changecaption);
    }
    $ret[] = '</div>';
    return $ret;
  }

  public function GetControl() {
    return $this->BuildList();
  }
}

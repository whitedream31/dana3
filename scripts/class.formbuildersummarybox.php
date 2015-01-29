<?php
require_once 'class.formbuilderbase.php';

class formbuildersummarybox extends formbuilderbase {
  public $list = array();
  public $changeidname = false;
  public $changecaption = 'Change';
  public $worker = false;

  function __construct($name, $value, $label = '') {
    parent::__construct($name, $value, basetable::FLDTYPE_SUMMARYBOX, $label);
  }

  public function AddItem($key, $caption, $value, $default = '') {
    $this->list[$key] = array(
      'caption' => $caption,
      'value' => $value,
      'default' => $default
    );
  }

  public function AddItemWithField($key, $caption, $fieldname, $default = '') {
    $this->AddItem($key, $caption, $this->table->GetFieldValue($fieldname), $default);
  }

  public function AddItemLookup(
    $key, $caption, $lookuptablename, $lookupid, $lookupdescfieldname = basetable::FN_DESCRIPTION, $default = '') {
    $lookupidvalue = (is_string($lookupid))
      ? $this->table->GetFieldValue($lookupid)
      : $lookupid;
    $value =
      database::$instance->SelectDescriptionFromLookup($lookuptablename, $lookupidvalue);
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
    if ($this->worker && $this->worker instanceof workerbase && $this->changeidname && $this->changecaption) {
      $ret[] = $this->worker->GetControlButton($this->changeidname, $this->changecaption);
    }
    $ret[] = '</div>';
    return $ret;
  }

  public function GetControl() {
    return $this->BuildList();
  }
}

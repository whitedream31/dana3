<?php

class formbuilderstatusgrid extends formbuilderbase {
  protected $rows = array();
  protected $rowcount;
  public $width = '400px';

  function __construct($name, $value, $label = '') {
    parent::__construct($name, $value, basetable::FLDTYPE_STATUSGRID, $label);
  }

  private function AddCells($list, $classname = '') {
    $ret = array();
    $class = ($classname) ? " class='{$classname}'" : '';
    $ret[] = "<tr{$class}>";
    foreach ($list as $cell) {
      $ret[] = $cell;
    }
    $ret[] = '</tr>';
    return implode("\n", $ret);
  }

  public function AddRow($rowitem) {
    $this->rows[] = $rowitem;
  }

  private function ShowRow($rowitem) {
    $list = array();
    $rowlabel = isset($rowitem['label']) ? $rowitem['label'] : '';
    $rowvalue = isset($rowitem['value']) ? $rowitem['value'] : '';
    $align = isset($rowitem['align']) ? " {$rowitem['align']}" : '';
    $labelwidth = isset($rowitem['labelwidth']) ? " style='width:{$rowitem['labelwidth']}'" : '';
    $valuewidth = isset($rowitem['valuewidth']) ? " style='width:{$rowitem['valuewidth']}'" : '';
    $list[] = "<td class='statuslabel'{$labelwidth}>{$rowlabel}</td>";
    $list[] = "<td class='statusvalue{$align}'{$valuewidth}>{$rowvalue}</td>";
    return $this->AddCells($list, 'statuslistrow');
  }

  private function ShowRows() {
    $ret = array();
    if ($this->rowcount > 0) {
      foreach ($this->rows as $rowitem) {
        $ret[] = $this->ShowRow($rowitem);
      }
    } else {
      $ret[] = $this->AddCells(array("<td>none found</td>"), 'tablenodata');
    }
    return $ret;
  }

  private function PopulateRows() {
//    if ($this->table) {
//      $this->table->AssignDataListRows($this);
//    }
  }

  public function GetControl() {
    $this->PopulateRows();
    $this->rowcount = count($this->rows);
    $ret = array();
    $width = ($this->width) ? " style='width:{$this->width}'" : '';
    $ret[] = "<div name='{$this->name}' id='{$this->id}' class='controlactivitystatusgrid'{$width}>";
    $ret[] = "  <table>";
    $ret = array_merge($ret, $this->ShowRows());
    $ret[] = "  </table>";
    $ret[] = "</div>";
    return $ret;
  }
}

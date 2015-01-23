<?php

class formbuilderdatalist extends formbuilderbase {
  protected $idname;
  protected $script;
//  protected $table; // idtable

  protected $rows = array(); // Array of controlactivitysectionitemtablerows
  protected $rowcount;

  function __construct($name, $value, $label = '') {
    parent::__construct($name, $value, basetable::FLDTYPE_DATALIST, $label);
  }

  public function SetIDName($idname) {
    $this->idname = $idname;
  }

  public function SetScript($script) {
    $this->script = $script;
  }
/*
  protected function GetActionButton($action, $state, $rowid, $text) {
    if ($icon) {
      $link = "{$this->script}?in={$this->idname}&rid={$rowid}&act={$action}";
      $img = "<img class='actionimg' src='images//{$icon}' alt=''>";
      $ret = "<a class='action' href='{$link}' title='{$title}'>{$img}{$text}</a>";
    } else {
      $icon = 'act_blank.png';
      $ret = "<img class='actionimg' src='images//{$icon}' alt=''>";
    }
    return $ret;
  }
*/
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

  public function AddRow($id, $rowitem) {
    $this->rows[$id] = $rowitem;
  }

  private function ShowRow($id, $rowitem) {
    $list = array();
    $action = (isset($rowitem['action'])) ? $rowitem['action'] : workerbase::ACT_EDIT;
    $url = "{$this->script}?in={$this->idname}&rid={$id}&act={$action}";
    $hint = $rowitem['hint'];
    $link = "<a href='{$url}' title='{$hint}'>";
    $list[] = '<td>' . $link . $rowitem['icon'] . "</a></td>";
    $list[] = "<td class='fieldlabel'>" . $link . $rowitem['edit'] . "</a></td>";
    $list[] = "<td class='fielddescription'>" . $rowitem['desc'] . '</td>';
    return $this->AddCells($list, 'tablelistrow');
  }

  private function ShowRows() {
    $ret = array();
    if ($this->rowcount > 0) {
      foreach ($this->rows as $rowid => $rowitem) {
        $ret[] = $this->ShowRow($rowid, $rowitem);
      }
    } else {
      $ret[] = $this->AddCells(array("<td>none found</td>"), 'tablenodata');
    }
    return $ret;
  }

  private function PopulateRows() {
    if ($this->table) {
      $this->table->AssignDataListRows($this);
    }
  }

  public function GetControl() {
    $this->PopulateRows();
    $this->rowcount = count($this->rows);
    $ret = array();
    $ret[] = "<div name='{$this->name}' id='{$this->id}' class='controlactivitylist'>";
    $ret[] = "  <table>";
    $ret = array_merge($ret, $this->ShowRows());
    $ret[] = "  </table>";
    $ret[] = "</div>";
//    $ret[] = "<script src='../jscripts/activitytableaction.js'></script>";
    return $ret;
  }
}

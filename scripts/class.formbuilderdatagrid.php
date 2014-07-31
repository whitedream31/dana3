<?php

define('TBLOPT_IGNOREFIRSTROW', 'if');
define('TBLOPT_TOGGLEVISIBLE', 'tv');
define('TBLOPT_EDITABLE', ACT_EDIT);
define('TBLOPT_DELETABLE', ACT_REMOVE);
define('TBLOPT_MOVEDOWN', 'md');
define('TBLOPT_MOVEUP', 'mu');
define('TBLOPT_NEWITEM', ACT_NEW);
define('TBLOPT_SENDNL', 'sn');

define('ROWSTATE_ISTOP', 1);
define('ROWSTATE_ISBOTTOM', 2);
define('ROWSTATE_IGNORE', 4);
define('ROWSTATE_VISHIDE', 8);
define('ROWSTATE_VISSHOW', 16);

class formbuilderdatagrid extends formbuilderbase {
  protected $idname;
  protected $script;
//  protected $table; // idtable

  protected $columns; // array - List of column header titles
  public $showactions = true;
  protected $ignorefirstrow = false;
  protected $rows = array(); // Array of controlactivitysectionitemtablerows
  protected $rowcount;

  function __construct($name, $value, $label = '') {
    parent::__construct($name, $value, FLDTYPE_DATAGRID, $label);
  }

  public function SetIDName($idname) {
    $this->idname = $idname;
  }

  public function SetScript($script) {
    $this->script = $script;
  }

  public function AddColumn($key, $title, $editable = false, $class = '') {
    $this->columns[$key] = array(
      'title' => $title, 'editable' => $editable, 'class' => $class
    );
  }

  private function CheckColumns() {
    if (!isset($this->columns['DESC'])) {
      $this->AddColumn('DESC', 'Description', true);
    }
    if ($this->showactions && (!isset($this->columns['ACT']))) {
      $this->AddColumn('ACT', 'Actions'); //, array(TBLOPT_TOGGLEVISIBLE, TBLOPT_DELETABLE, TBLOPT_MOVEDOWN, TBLOPT_MOVEUP), 'narrow');
    }
  }

  private function ShowHeader() {
    $ret = array('<tr>');
    foreach ($this->columns as $key => $column) {
      $classname = $column['class'];
      if (!$classname) {
        $classname = ($key == 'ACT') ? 'datagridaction' : '';
      }
      $class = ($classname) ? " class='{$classname}'" : '';
      $colname = $column['title'];
      $ret[] = "<th{$class}>{$colname}</th>";
    }
    $ret[] = "</tr>";
    return $ret;
  }

  protected function GetActionButton($action, $state, $rowid, $text) {
    $icon = false;
    $title = '';
    $ret = '';
    if (!($state & ROWSTATE_IGNORE)) {
      switch ($action) {
        case TBLOPT_DELETABLE:
          $icon = 'act_remove.png';
          $title = 'click to remove';
          break;
        case TBLOPT_TOGGLEVISIBLE:
          if ($state & ROWSTATE_VISHIDE) {
            $icon = 'act_hide.png';
            $title = 'click to EXCLUDE from the website';
          } else if ($state & ROWSTATE_VISSHOW) {
            $icon = 'act_show.png';
            $title = 'click to INCLUDE in the website';
          }
          break;
        case TBLOPT_MOVEDOWN:
          if (!($state & ROWSTATE_ISBOTTOM)) {
            $icon = 'act_movedn.png';
            $title = 'click to move down the list';
            $action = ACT_MOVEDOWN;
          }
          break;
        case TBLOPT_MOVEUP:
          if (!($state & ROWSTATE_ISTOP)) {
            $icon = 'act_moveup.png';
            $title = 'click to move up the list';
            $action = ACT_MOVEUP;
          }
          break;
      }
    }
    switch ($action) {
      case TBLOPT_EDITABLE:
        $icon = 'act_edit.png';
        $title = 'click to edit';
        $action = ACT_EDIT;
        break;
      case TBLOPT_NEWITEM:
        $icon = 'act_additem.png';
        $title = 'click to add';
        $action = ACT_NEW;
        break;
      case TBLOPT_DELETABLE:
        $action = ACT_REMOVE;
        break;
      case TBLOPT_SENDNL:
        $icon = 'act_nlsend.png';
        $title = 'click to send the newsletter to subscribers';
        $action = ACT_NLSEND;
        break;
    }
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

  protected function GetActionButtons($actions, $state, $rowid, $text = '') {
    $ret = array();
    if (is_array($actions)) {
      foreach($actions as $action) {
        $ret[] = $this->GetActionButton($action, $state, $rowid, $text);
      }
      $ret = implode(' ', $ret);
    } else {
      $ret = $this->GetActionButton($actions, $state, $rowid, $text);
    }
    return $ret;
  }

  private function AddCells($list) {
    $ret = array();
    $ret[] = '<tr>';
    foreach ($list as $cell) {
      $ret[] = $cell;
    }
    $ret[] = '</tr>';
    return implode("\n", $ret);
  }

  public function AddRow($id, $columns, $isvisible, $actions) {
    $this->rows[$id] = array(
      'columns' => $columns, 'visible' => $isvisible, 'actions' => $actions
    );
  }

  private function ShowRow($id, $idx, $columns, $actions, $visible) {
    $this->ignorefirstrow = (in_array(TBLOPT_IGNOREFIRSTROW, $actions));
    $showvisible = (in_array(TBLOPT_TOGGLEVISIBLE, $actions));
    $isfirst = ($idx == 1);
    $issecond = ($idx == 2);
    $islast = ($idx == $this->rowcount);
//    $editrow = reset($columns);
    $list = array();
    foreach ($this->columns as $key => $column) {
      $col = ((isset($columns[$key]))) ? $columns[$key] : $key;
      if ($column['editable']) {
        $link = $this->GetActionButtons(TBLOPT_EDITABLE, 0, $id, $col);
        $list[] = "<td class='editable'>{$link}</td>";
      } elseif ($key == 'ACT') {
        $state = 0;
        if ($this->ignorefirstrow) {
          if ($isfirst) {
            $state += ROWSTATE_IGNORE;
          } else if ($issecond) {
            $state += ROWSTATE_ISTOP;
          }
        } else {
          if ($isfirst) {
            $state += ROWSTATE_ISTOP;
          }
        }
        if ($showvisible) {
          if (!($state & ROWSTATE_IGNORE)) {
            if ($visible) {
              $state += ROWSTATE_VISHIDE;
            } else {
              $state += ROWSTATE_VISSHOW;
            }
          }
        }
        if ($islast) {
          $state += ROWSTATE_ISBOTTOM;
        }
        $hasaction = isset($column['actions']) && $column['actions'];
        $colactions = ($hasaction) ? $column['actions'] : $actions;
        $actions = $this->GetActionButtons($colactions, $state, $id);
        $list[] = "<td class='actions'>{$actions}</td>";
      } else {
        $class = ($column['class']) ? ' ' . $column['class'] : '';
        $list[] = "<td class='centre{$class}'>{$col}</td>";
      }
    }    
    return $this->AddCells($list);
  }

  private function ShowRows() {
    $ret = array();
    if ($this->rowcount > 0) {
      $idx = 1;
      foreach ($this->rows as $rowid => $row) {
        $id = $rowid; //(isset($row['id'])) ? $row['id'] : $idx;
        $columns = $row['columns'];
        if (count($columns)) {
          $ret[] = $this->ShowRow($id, $idx, $columns, $row['actions'], $row['visible']);
        }
        $idx++;
      }
    } else {
      $colcount = count($this->columns) + 1;
      $ret[] = $this->AddCells(array("<td class='tablenodata' colspan='{$colcount}'>none found</td>"));
    }
    return $ret;
  }

  private function PopulateRows() {
    if ($this->table) {
      $this->table->AssignDataGridColumns($this);
      $this->table->AssignDataGridRows($this);
    }
  }

  public function GetControl() {
    $this->PopulateRows();
    $this->CheckColumns();
    $this->rowcount = count($this->rows);
    $ret = array();
    $ret[] = "<div name='{$this->name}' id='{$this->id}' class='controlactivitylist'>";
    $ret[] = "  <table>";
    $ret = array_merge(
      $ret, $this->ShowHeader(), $this->ShowRows()
    );
    $ret[] = "  </table>";
    $ret[] = "</div>";
//    $ret[] = "<script src='../jscripts/activitytableaction.js'></script>";
    return $ret;
  }
}

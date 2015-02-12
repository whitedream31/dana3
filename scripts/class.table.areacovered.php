<?php
namespace dana\table;

use dana\core;

require_once 'class.basetable.php';

/**
  * areas covered table
  * @version dana framework v.3
*/

class areacovered extends idtable {
  public $accountid;
  public $description;
  public $postalarea;
  public $countyid;

  function __construct($id = 0) {
    parent::__construct('areacovered', $id);
  }

  protected function AssignFields() {
    parent::AssignFields();
    $this->accountid = $this->AddField('accountid', self::DT_FK);
    $this->description = $this->AddField('description', self::DT_STRING);
    $this->postalarea = $this->AddField('postalarea', self::DT_STRING);
    $this->countyid = $this->AddField('countyid', self::DT_FK);
  }

  protected function AssignDefaultFieldValue($name, $fld) {
    if ($name == 'description') {
      $postalarea = $this->GetFieldValue('postalarea');
      $countyid = $this->GetFieldValue('countyid');
      $desc = ($postalarea) ? $postalarea . ' Area' : \dana\core\database::SelectFromTableByField('county', 'id', $countyid, 'description');
      $this->SetFieldValue($name, $desc);
    } else {
      parent::AssignDefaultFieldValue($name, $fld);
    }
  }

  static public function GetList($accountid) {
    $ret = array();
    $status = self::STATUS_ACTIVE;
    $query =
      'SELECT `id`, `description` FROM `areacovered` ' .
      "WHERE (`accountid` = {$accountid}) AND " .
      "(`status` = '{$status}') " .
      'ORDER BY `description`';
    $result = \dana\core\database::$instance->Query($query);
    while ($line = $result->fetch_assoc()) {
      $id = $line['id'];
      $itm = new areacovered($id);
      if ($itm->exists) {
        $ret[$id] = $itm;
      }
    }
    $result->free();
    return $ret;
  }

  public function AssignDataGridColumns($datagrid) {
    $datagrid->AddColumn('DESC', 'Description', true);
  }

  public function AssignDataGridRows($datagrid) {
    $accountid = account::$instance->ID();
    $list = self::GetList($accountid);
    foreach($list as $id => $item) {
      $coldata = array(
        'DESC' => $item->GetFieldValue('description')
      );
      $datagrid->AddRow($id, $coldata, true, array(\dana\formbuilder\formbuilderdatagrid::TBLOPT_DELETABLE));
    }
    return $list;
  }

  public function Show() {
    $ret = '(no areas covered available)';
    if ($this->exists) {
      $ret = '(areas covered)';
    }
    return $ret;
  }

}

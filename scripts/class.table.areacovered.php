<?php
require_once 'class.basetable.php';

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
    $this->accountid = $this->AddField('accountid', DT_FK);
    $this->description = $this->AddField('description', DT_STRING);
    $this->postalarea = $this->AddField('postalarea', DT_STRING);
    $this->countyid = $this->AddField('countyid', DT_FK);
  }

  protected function AssignDefaultFieldValue($name, $fld) {
    if ($name == 'description') {
      $postalarea = $this->GetFieldValue('postalarea');
      $countyid = $this->GetFieldValue('countyid');
      $desc = ($postalarea) ? $postalarea . ' Area' : database::SelectFromTableByField('county', 'id', $countyid, 'description');
      $this->SetFieldValue($name, $desc);
    } else {
      parent::AssignDefaultFieldValue($name, $fld);
    }
  }

  public function AssignDataGridColumns($datagrid) {
    $datagrid->AddColumn('DESC', 'Description', true);
  }

  public function AssignDataGridRows($datagrid) {
    $accountid = account::$instance->ID();
    $status = STATUS_ACTIVE;
    $query =
      'SELECT `id`, `description` FROM `areacovered` ' .
      "WHERE (`accountid` = {$accountid}) AND " .
      "(`status` = '{$status}') " .
      'ORDER BY `description`';
    $list = array();
    $result = database::Query($query);
    while ($line = $result->fetch_assoc()) {
      $id = $line['id'];
      $coldata = array(
        'DESC' => $line['description']
      );
      $datagrid->AddRow($id, $coldata, true, array(TBLOPT_DELETABLE));
    }
    $result->free();
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

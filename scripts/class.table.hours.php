<?php
require_once 'class.basetable.php';

class hours extends idtable {
  public $accountid;
  public $is24hrs;
  public $monday;
  public $tuesday;
  public $wednesday;
  public $thursday;
  public $friday;
  public $saturday;
  public $sunday;
  public $comments;
  public $description;
  public $active;

  function __construct($id = 0) {
    parent::__construct('hours', $id);
  }

  protected function AssignFields() {
    parent::AssignFields();
    $this->accountid = $this->AddField(FN_ACCOUNTID, DT_FK);
    $this->is24hrs = $this->AddField('is24hrs', DT_BOOLEAN);
    $this->monday = $this->AddField('monday', DT_STRING);
    $this->tuesday = $this->AddField('tuesday', DT_STRING);
    $this->wednesday = $this->AddField('wednesday', DT_STRING);
    $this->thursday = $this->AddField('thursday', DT_STRING);
    $this->friday = $this->AddField('friday', DT_STRING);
    $this->saturday = $this->AddField('saturday', DT_STRING);
    $this->sunday = $this->AddField('sunday', DT_STRING);
    $this->description = $this->AddField('description', DT_STRING);
    $this->comments = $this->AddField('comments', DT_TEXT);
    $this->active = $this->AddField('active', DT_BOOLEAN);
  }

  public function AssignFormFields($formeditor, $idref) {
    // business category section
    $formeditor->AssignActiveFieldSet(FS_OPENHOURS, 'Opening Hours');
    // - opening hours
    $monday = $formeditor->AddDataField($this, 'monday', 'Monday', FLDTYPE_EDITBOX, 50);
    $monday->description = 'If you are open, please type in your opening hours (eg. 9am to 5pm). <strong>Leave blank if you are closed.</strong>';
    $tuesday = $formeditor->AddDataField($this, 'tuesday', 'Tuesday', FLDTYPE_EDITBOX, 50);
    $tuesday->description = 'If you are open, please type in your opening hours (eg. 9am to 5pm). <strong>Leave blank if you are closed.</strong>';
    $wednesday = $formeditor->AddDataField($this, 'wednesday', 'Wednesday', FLDTYPE_EDITBOX, 50);
    $wednesday->description = 'If you are open, please type in your opening hours (eg. 9am to 5pm). <strong>Leave blank if you are closed.</strong>';
    $thursday = $formeditor->AddDataField($this, 'thursday', 'Thursday', FLDTYPE_EDITBOX, 50);
    $thursday->description = 'If you are open, please type in your opening hours (eg. 9am to 5pm). <strong>Leave blank if you are closed.</strong>';
    $friday = $formeditor->AddDataField($this, 'friday', 'Friday', FLDTYPE_EDITBOX, 50);
    $friday->description = 'If you are open, please type in your opening hours (eg. 9am to 5pm). <strong>Leave blank if you are closed.</strong>';
    $saturday = $formeditor->AddDataField($this, 'saturday', 'Saturday', FLDTYPE_EDITBOX, 50);
    $saturday->description = 'If you are open, please type in your opening hours (eg. 9am to 5pm). <strong>Leave blank if you are closed.</strong>';
    $sunday = $formeditor->AddDataField($this, 'sunday', 'Sunday', FLDTYPE_EDITBOX, 50);
    $sunday->description = 'If you are open, please type in your opening hours (eg. 9am to 5pm). <strong>Leave blank if you are closed.</strong>';
  }

  protected function AssignDefaultFieldValues() {
    $this->AssignFieldDefaultValue('monday', '9am to 5pm', true);
    $this->AssignFieldDefaultValue('tuesday', '9am to 5pm', true);   
    $this->AssignFieldDefaultValue('wednesday', '9am to 5pm', true);
    $this->AssignFieldDefaultValue('thursday', '9am to 5pm', true);   
    $this->AssignFieldDefaultValue('friday', '9am to 5pm', true);
    $this->AssignFieldDefaultValue('saturday', '', true);   
    $this->AssignFieldDefaultValue('sunday', '', true);   
  }

  protected function AssignDefaultFieldValue($name, $fld) {
    if ($name == 'description') {
      $desc = ($this->GetFieldValue('is24hrs')) ? 'Open 24 Hours' : 'New Opening Hours';
      $this->SetFieldValue($name, $desc);
    } else {
      parent::AssignDefaultFieldValue($name, $fld);
    }
  }

  public function AssignDataGridColumns($datagrid) {
    $datagrid->AddColumn('DESC', 'Description', true);
    $datagrid->AddColumn('ACTIVE', 'active');
  }

  public function AssignDataGridRows($datagrid) {
    $accountid = account::$instance->ID();
//    $status = STATUS_ACTIVE;
    $query =
      'SELECT `id`, `description`, `active` FROM `hours` ' .
      "WHERE (`accountid` = {$accountid}) " .
      'ORDER BY `active` DESC, `description`';
    $list = array();
    $result = database::Query($query);
    while ($line = $result->fetch_assoc()) {
      $id = $line['id'];
      $isactive = $line['active'];
      $actions = ($isactive) ? array() : array(TBLOPT_DELETABLE);
      $coldata = array(
        'DESC' => $line['description'],
        'ACTIVE' => ($isactive) ? 'YES' : 'no'
      );
      $datagrid->AddRow($id, $coldata, true, $actions);
    }
    $result->free();
    return $list;
  }

  public function Show() {
    $ret = '(no hours available)';
    if ($this->exists) {
      $ret = '(hours open)';
    }
    return $ret;
  }

}

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
    $this->accountid = $this->AddField(basetable::FN_ACCOUNTID, self::DT_FK);
    $this->is24hrs = $this->AddField('is24hrs', self::DT_BOOLEAN);
    $this->monday = $this->AddField('monday', self::DT_STRING);
    $this->tuesday = $this->AddField('tuesday', self::DT_STRING);
    $this->wednesday = $this->AddField('wednesday', self::DT_STRING);
    $this->thursday = $this->AddField('thursday', self::DT_STRING);
    $this->friday = $this->AddField('friday', self::DT_STRING);
    $this->saturday = $this->AddField('saturday', self::DT_STRING);
    $this->sunday = $this->AddField('sunday', self::DT_STRING);
    $this->description = $this->AddField('description', self::DT_STRING);
    $this->comments = $this->AddField('comments', self::DT_TEXT);
    $this->active = $this->AddField('active', self::DT_BOOLEAN);
  }

  public function AssignFormFields($formeditor, $idref) {
    // business category section
    $formeditor->AssignActiveFieldSet(FS_OPENHOURS, 'Opening Hours');
    // - opening hours
    $monday = $formeditor->AddDataField($this, 'monday', 'Monday', self::FLDTYPE_EDITBOX, 50);
    $monday->description = 'If you are open, please type in your opening hours (eg. 9am to 5pm). <strong>Leave blank if you are closed.</strong>';
    $tuesday = $formeditor->AddDataField($this, 'tuesday', 'Tuesday', self::FLDTYPE_EDITBOX, 50);
    $tuesday->description = 'If you are open, please type in your opening hours (eg. 9am to 5pm). <strong>Leave blank if you are closed.</strong>';
    $wednesday = $formeditor->AddDataField($this, 'wednesday', 'Wednesday', self::FLDTYPE_EDITBOX, 50);
    $wednesday->description = 'If you are open, please type in your opening hours (eg. 9am to 5pm). <strong>Leave blank if you are closed.</strong>';
    $thursday = $formeditor->AddDataField($this, 'thursday', 'Thursday', self::FLDTYPE_EDITBOX, 50);
    $thursday->description = 'If you are open, please type in your opening hours (eg. 9am to 5pm). <strong>Leave blank if you are closed.</strong>';
    $friday = $formeditor->AddDataField($this, 'friday', 'Friday', self::FLDTYPE_EDITBOX, 50);
    $friday->description = 'If you are open, please type in your opening hours (eg. 9am to 5pm). <strong>Leave blank if you are closed.</strong>';
    $saturday = $formeditor->AddDataField($this, 'saturday', 'Saturday', self::FLDTYPE_EDITBOX, 50);
    $saturday->description = 'If you are open, please type in your opening hours (eg. 9am to 5pm). <strong>Leave blank if you are closed.</strong>';
    $sunday = $formeditor->AddDataField($this, 'sunday', 'Sunday', self::FLDTYPE_EDITBOX, 50);
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
      $actions = ($isactive) ? array() : array(formbuilderdatagrid::TBLOPT_DELETABLE);
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

  public function GetActiveRow() {
    return $this->CheckActiveRow();
  }

  public function MakeDefaultRow() {
    $hour = new hours();
    $hour->SetFieldValue(basetable::FN_ACCOUNTID, account::$instance->ID());
    $hour->SetFieldValue(basetable::FN_DESCRIPTION, 'New Opening Hours');
    $hour->SetFieldValue('active', 1);
    $hour->StoreChanges();
    return $hour->ID();
  }

  public function CheckActiveRow() {
    $activeid = false;
    $accountid = account::$instance->ID();
    // find rows that are active
    $query = "SELECT `id`, `active` FROM `hours` WHERE `accountid` = {$accountid} ORDER BY `id`";
    $found = false;
    $list = array();
    $result = database::Query($query);
    while ($line = $result->fetch_assoc()) {
      $id = $line['id'];
      $isactive = $line['active'];
      if ($isactive & !$found) {
        $found = true;
        $activeid = $id;
      } elseif ($isactive && $found) { // is currently active but already found, change to 'off'
        $list[$id] = 0;
      }
    }
    $result->free();
    if (count($list)) {
      foreach($list as $id => $value) {
        $query = "UPDATE `hours` SET `active` = {$value} WHERE `id` = {$id}";
        database::Query($query);
      }
    }
    // if no active row found then make one
    if (!$found) {
      $activeid = $this->MakeDefaultRow();
    }
    return $activeid;
  }
}

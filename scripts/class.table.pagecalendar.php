<?php
namespace dana\table;

use dana\core;

require_once 'class.table.page.php';

/**
  * page calendar class - CALENDAR
  * written by Ian Stewart (c) 2012 Whitedream Software
  * created: 8 dec 2012
  * modified: 10 feb 2015
  * @version dana framework v.3
*/

class pagecalendar extends page {
  protected $fldgroupid;

  protected function AssignPageType() {
    $this->pgtype = self::PAGETYPE_CALENDAR;
  }

  // assign table columns just used by this type of page
  protected function AssignPageTypeFields() {
    $this->AddField('groupid', self::DT_FK);
  }

  private function GetDisplayTypeList() {
    $statusactive = self::STATUS_ACTIVE;
    $query =
      'SELECT * FROM `calendardisplaytype` ' .
      "WHERE  (`status` = '{$statusactive}') " .
      'ORDER BY `ref`';
    $list = array();
    $result = \dana\core\database::Query($query);
    while ($line = $result->fetch_assoc()) {
      $id = $line['id'];
      $list[$id] = $line['description'];
    }
    $result->free();
    return $list;
  }

  protected function InitFieldsForMainContent($worker) {
    parent::InitFieldsForMainContent($worker);
    $displaytypelist = $this->GetDisplayTypeList();
    $this->fldgroupid = $worker->AddField(
      'groupid', new \dana\formbuilder\formbuilderselect('groupid', '', 'Calendar Display Type'), $this);
    $groupid = $this->GetFieldValue('groupid');
//    $this->fldgroupid->AddValue(0, '(no gallery)', $groupid == 0);
    foreach ($displaytypelist as $id => $title) {
      $this->fldgroupid->AddValue($id, $title, $id == $groupid);
    }
  }

  public function AssignFieldProperties($worker, $isnew) {
    parent::AssignFieldProperties($worker, $isnew);
    $this->fldgroupid->description =
      'Choose the way the calendar items are shown.';
    $this->fldgroupid->size = 3;
    $worker->AssignFieldToSection('sectmaincontent', 'groupid');
  }

  protected function SaveFormFields() {
    return parent::SaveFormFields() +
      $this->SaveFormField($this->fldgroupid);
  }

  public function ValidateFields() {
  }
}

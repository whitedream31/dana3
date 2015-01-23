<?php
require_once 'class.workerform.php';
require_once 'class.workerbase.php';
require_once 'class.formbuildereditbox.php';
require_once 'class.formbuilderfilewebimage.php';
require_once 'class.formbuilderbutton.php';

/**
  * activity worker for managing private area pages
  * dana framework v.3
*/

// resource manage private area pages

class workerresmanprivateareapages extends workerform {
//  protected $datagrid;
//  protected $table;
  protected $pageid;
  protected $fldpageid;

  protected function InitForm() {
    $this->table = new privateareapage($this->itemid);
    $this->icon = 'images/sect_resource.png';
    $this->activitydescription = 'some text here';
    $this->contextdescription = 'Manage private area pages';
    switch ($this->action) {
      case workerbase::ACT_NEW:
      case workerbase::ACT_EDIT:
        $this->title = 'Link pages with the private area';
        $this->returnidname = activitymanager::IDNAME_MANAGEPRIVATEAREAS;
        $this->showroot = false; 
        break;
      case workerbase::ACT_REMOVE:
        break;
      default:
        break;
    }
  }

  protected function PostFields() {
    switch ($this->action) {
      case workerbase::ACT_NEW:
      case workerbase::ACT_EDIT:
//        $ret = $this->fldfirstname->Save() + $this->fldlastname->Save() +
//          $this->fldemail->Save();
        break;
      default:
        $ret = true;
    }
    return $ret;
  }

  protected function SaveToTable() {
    $ret = (int) $this->table->StoreChanges();
    return $ret;
  }

  protected function AddErrorList() {}

  protected function AssignFieldDisplayProperties() {
  }

  private function GetPageList() {
    $ret = array();
    $account = account::$instance;
    $pagemgrid = $account->GetFieldValue('pagemgrid');
    $status = basetable::STATUS_ACTIVE;
    $query =
      'SELECT p.`id`, pt.`pgtype` FROM `page` p ' .
      'INNER JOIN `pagetype` pt ON pt.`id` = p.`pagetypeid` ' .
      "WHERE p.`pagemgrid` = {$pagemgrid} AND (p.`ishomepage` = 0) AND p.`status` = '{$status}' " .
      'ORDER BY p.`pageorder`';
    $result = database::Query($query);
    while ($line = $result->fetch_assoc()) {
      $pageid = $line['id'];
      $pgtype = $line['pgtype'];
      $page = pagelist::NewPage($pgtype, $pageid);
      if ($page->exists) {
        $ret[$pageid] = $page;
      }
    }
    $result->close();
    return $ret;
  }

  protected function AssignItemEditor($isnew) {
    $this->NewSection(
      'page', 'Private area page',
      'Please select a page that will be linked to .');
    // page id
    $this->fldpageid = $this->AddField(
      'pageid', new formbuilderselect('pageid', '', 'Page to assign to private area'), $this->pageid);
    $this->fldpageid->size = 
    $pagelist = $this->GetPageList();
    $this->fldpageid->size = count($pagelist);
    foreach($pagelist as $pageid => $page) {
      $desc = $page->GetFieldValue('description') . " ({$page->pagetypedescription} page)";
      $this->fldpageid->AddValue($pageid, $desc);
    }
    $this->AssignFieldToSection('page', 'pageid');
  }

  protected function AssignItemRemove($confirmed) {
  }
}

$worker = new workerresmanprivateareapages();

<?php
namespace dana\worker;

require_once 'class.workerform.php';
require_once 'class.workerbase.php';

/**
  * worker resource manage private area pages
  * @version dana framework v.3
*/

class workerresmanprivateareapages extends workerform {
//  protected $datagrid;
//  protected $table;
  protected $pageid;
  protected $privateareaid;
  protected $fldpageid;

  protected function InitForm() {
    $this->table = new \dana\table\privatepage();
    $this->icon = 'images/sect_resource.png';
    $this->activitydescription = 'some text here';
    $this->contextdescription = 'Link page to private area';
    $this->privateareaid = $this->groupid;
    switch ($this->action) {
      case workerbase::ACT_NEW:
      case workerbase::ACT_EDIT:
        $this->title = 'Link page with the private area';
        $this->returnidname = 'IDNAME_RESOURCES_PRIVATEAREAS';
        $this->returnaction = self::ACT_LIST;
        $this->showroot = false;
        // page id
        $this->fldpageid = $this->AddField(
          'pageid',
          new \dana\formbuilder\formbuilderselect('pageid', '', 'Page to assign'),
          $this->table);
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
        $this->table->SetFieldValue('privateareaid', $this->privateareaid);
        $ret = $this->fldpageid->Save();
        break;
      default:
        $ret = true;
    }
    return $ret;
  }

  protected function SaveToTable() {
//    $pageid = $this->table->GetFieldValue('pageid');
//    $pagelist = $this->account->pagelist->pages;

//    $privateareaid = $this->table->GetFieldValue('privateareaid');
    $ret = (int) $this->table->StoreChanges();
    return $ret;
  }

  protected function AddErrorList() {}

  protected function AssignFieldDisplayProperties() {
  }

  private function GetPageList() {
    $ret = array();
    $pgmgrid = $this->account->GetFieldValue('pgmgrid');
    $privatearea = $this->groupid;
    $pagelist = $this->account->GetPageList()->pages;
    foreach($pagelist as $pageid => $page) {
      if ($page->exists && !$page->GetFieldValue('ishomepage')) {
        $ret[$pageid] = $page;
      }
    }
    return $ret;
  }

  protected function AssignItemEditor($isnew) {
    $this->NewSection(
      'page', 'Private Area Page',
      'Please select a page that the private area will be linked to. ' .
      '<strong>Please note any page linked to a private page will no longer ' .
      'be accessible unless a member is logged in.</strong>');
    $pagelist = $this->GetPageList();
    $this->fldpageid->size = count($pagelist);
    foreach($pagelist as $pageid => $page) {
      $desc = $page->GetFieldValue('description') . " ({$page->pagetypedescription} page)";
      $this->fldpageid->AddValue($pageid, $desc);
    }
    if ($this->action == workerbase::ACT_NEW) {
      if (count($pagelist)) {
        reset($pagelist);
        $page = current($pagelist);
        $this->fldpageid->value = $page->ID();
      }
    } else {
      $this->fldpageid->value = $this->itemid;
    }

    $this->AssignFieldToSection('page', 'pageid');
  }

  protected function AssignItemRemove($confirmed) {
  }
}

$worker = new workerresmanprivateareapages();

<?php
namespace dana\worker;

require_once 'class.workerform.php';
require_once 'class.workerbase.php';

/**
  * worker resource manage private area members
  * @version dana framework v.3
*/

class workerresmanprivateareamembers extends workerform {
  protected $memberid;
  protected $privateareaid;
  protected $fldmemberid;
/*
    $this->AddField('username', self::DT_STRING);
    $this->AddField('password', self::DT_STRING);
    $this->AddField('displayname', self::DT_DESCRIPTION);
    $this->AddField('email', self::DT_STRING);
    $this->AddField('startdate', self::DT_DATETIME);
    $this->AddField('lastlogin', self::DT_DATETIME);
    $this->AddField(self::FN_STATUS, self::DT_STATUS);

 */
  protected function InitForm() {
    $this->table = new \dana\table\privateareamember();
    $this->icon = 'images/sect_resource.png';
    $this->activitydescription = 'some text here';
    $this->contextdescription = 'Link member to private area';
    $this->privateareaid = $this->groupid;
    switch ($this->action) {
      case workerbase::ACT_NEW:
      case workerbase::ACT_EDIT:
        $this->title = 'Link member with the private area';
        $this->returnidname = 'IDNAME_RESOURCES_PRIVATEAREAPAGES';
        $this->showroot = false;
        // member id
        $this->fldmemberid = $this->AddField(
          'memberid',
          new \dana\formbuilder\formbuilderselect('memberid', '', 'Member to assign'),
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
        $ret = $this->fldmemberid->Save();
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
/*
  private function GetMemberList() {
    $ret = array();
    $pgmgrid = $this->account->GetFieldValue('pgmgrid');
    $privatearea = $this->groupid;
    $memberlist = $this->account->GetPageList()->pages;
    foreach($pagelist as $pageid => $page) {
      if ($page->exists && !$page->GetFieldValue('ishomepage')) {
        $ret[$pageid] = $page;
      }
    }
    return $ret;
  }
*/
  protected function AssignItemEditor($isnew) {
    $this->NewSection(
      'member', 'Private Area Member',
      'Please select a member that the private area will be linked to. ' .
      '<strong>Please note any member linked to a private page will no longer ' .
      'be accessible unless a member is logged in.</strong>');
/*    $pagelist = $this->GetPageList();
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
*/
    $this->AssignFieldToSection('member', 'memberid');
  }

  protected function AssignItemRemove($confirmed) {
  }
}

$worker = new workerresmanprivateareamembers();

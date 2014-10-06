<?php
require_once 'class.workerform.php';
require_once 'class.workerbase.php';
require_once 'class.formbuildereditbox.php';
//require_once 'class.formbuilderfilewebimage.php';
require_once 'class.formbuilderbutton.php';

/**
  * activity worker for managing guest book entries
  * dana framework v.3
*/

// resource manage guest book entries

class workerresmanguestbookentry extends workerform {
//  protected $datagrid;
  protected $table;
  protected $fldheading;
  protected $fldshowonpage;

  protected function InitForm() {
    $this->table = new guestbookentry($this->itemid);
    $this->icon = 'images/sect_resource.png';
    $this->activitydescription = 'some text here';
    $this->contextdescription = 'Guest-Book Entry';
    switch ($this->action) {
      case ACT_NEW:
      case ACT_EDIT:
        $this->title = 'Moderate Guest-Book Entry';
        $this->fldentry = $this->AddField(
          'entry', new formbuilderstatictext('entry', '', 'Entry Details'));
        $this->fldshowonpage = $this->AddField(
          'showonpage', new formbuildercheckbox('showonpage', '', 'Show On Guest-Book Page?'));
        $this->fldshowonpage->value = ($this->table->GetFieldValue(FN_STATUS) == STATUS_ACTIVE);
        $this->returnidname = IDNAME_MANAGEGUESTBOOKS;
        $this->showroot = false; 
        break;
      case ACT_REMOVE:
        break;
      default:
        break;
    }
  }

  protected function PostFields() {
    switch ($this->action) {
      case ACT_NEW:
      case ACT_EDIT:
        $ret = $this->fldshowonpage->Save();
        break;
      default:
        $ret = true;
    }
    return $ret;
  }

  protected function SaveToTable() {
    return (int) $this->table->StoreChanges();
  }

  protected function AddErrorList() {}

  protected function AssignFieldDisplayProperties() {}

  protected function AssignItemEditor($isnew) {
    $this->NewSection(
      'entry', 'Moderate Entry',
      'Here, you can specify a header (title) of the item and the content (text) of the item.');
    $this->fldentry->description = 'Specify a item heading. Please keep it short and simple.';
    $this->fldentry->value = $this->table->GetEntryDetails();
    $this->AssignFieldToSection('entry', 'entry');
    $this->fldshowonpage->description = 'Check the box to make the comment visible in your guest-book page.';
    $this->AssignFieldToSection('entry', 'showonpage');
  }

  protected function AssignItemRemove($confirmed) {
  }
}

$worker = new workerresmanguestbookentry();

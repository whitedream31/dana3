<?php
require_once 'class.workerform.php';
require_once 'class.workerbase.php';
require_once 'class.formbuilderdatagrid.php';

/**
  * base activity worker
  * dana framework v.3
*/

// manage working managing (downloadable files)

class workerresmanfiles extends workerform {
  protected $datagrid;
  protected $table;
  protected $filelist;
  protected $fldtitle;
  protected $fldfilename;
  protected $flddescription;
  protected $fldaddfile;

  protected function InitForm() {
    $this->table = new fileitem($this->itemid);
    $this->icon = 'images/sect_resources.png';
    $this->activitydescription = 'some text here';
    $this->contextdescription = 'managing downloadable files';
    $this->datagrid = new formbuilderdatagrid('files', '', 'Files Available');
    switch ($this->action) {
      case ACT_EDIT:
      case ACT_NEW:
        $this->title = (($this->action == ACT_EDIT) ? 'Modify' : 'New') . ' File';
        $this->fldtitle = $this->AddField(
          'title', new formbuildereditbox('title', '', 'Title'), $this->table);
        $this->fldfilename = $this->AddField(
          'filename', new filewebsitefield('filename', '', 'Filename'), $this->table);
        $this->flddescription = $this->AddField(
          'description', new formbuildereditbox('description', '', 'Description of File'), $this->table);
        $this->returnidname = $this->idname;
        $this->showroot = false;
        break;
      case ACT_REMOVE:
        $this->buttonmode = array(BTN_CONFIRM, BTN_CANCEL);
        $this->title = 'Remove File';
        $this->fldtitle = $this->AddField(
          'title', new formbuilderstatictext('description', '', 'File to be removed'));
        $this->action = ACT_CONFIRM;
        $this->returnidname = $this->idname;
        $this->showroot = false;
        break;
      default:
        $this->fldaddfile = $this->AddField(
          'addfile', new formbuilderbutton('addfile', 'Upload New File'));
        $url = $_SERVER['PHP_SELF'] . "?in={$this->idname}&act=" . ACT_NEW;
        $this->fldaddfile->url = $url;

        $this->buttonmode = array(BTN_BACK);
        $this->title = 'Manage Downloadable Files'; 
        $this->filelist = $this->AddField('filelist', $this->datagrid, $this->table);
        break;
    }
  }

  protected function DeleteItem($itemid) {
    try {
      $status = STATUS_DELETED;
      $query = 'DELETE `fileitem` WHERE `id` = ' . $itemid;
      database::Query($query);
      $ret= true;
    } catch (Exception $e) {
      $this->AddMessage('Cannot remove file');
      $ret = false;
    }
    return $ret;
  }

  protected function PostFields() {
    switch ($this->action) {
      case ACT_EDIT:
      case ACT_NEW:
        $ret = $this->fldtitle->Save() + $this->fldfilename->Save() +
          $this->flddescription->Save();
        break;
      case ACT_CONFIRM:
        $caption = $this->table->GetFieldValue('title');
        if ($this->DeleteItem($this->itemid)) {
          $this->AddMessage("File '{$caption}' removed");
        }
        $ret = false;
        break;
      default:
        $ret = true;
    }
    return $ret;
  }

  protected function SaveToTable() {
    return (int) $this->table->StoreChanges();
  }

  protected function AddErrorList() {
  }

  protected function AssignFieldDisplayProperties() {
    $this->datagrid->SetIDName($this->idname);
    $this->NewSection(
      'files', 'Files Available For Your Visitors to Download',
      'Please specify the filename to upload and optionally a friendly title and description.');
    $this->filelist->description = 'Files Currently Available';
    $this->AssignFieldToSection('files', 'filelist');
    if ($this->fldaddfile) {
      $this->fldaddfile->description = 'Click this button to upload a new file to your website';
      $this->AssignFieldToSection('files', 'addfile');
    }
  }

  protected function AssignItemEditor($isnew) {
    $title = ($isnew) ? 'Upload a New File' : 'Change File Details';
    $this->NewSection(
      'files', $title,
      'Please specify the file details to make available to your visitors.');
    // title field
    $this->fldtitle->description = 'A friendly title for your file.';
    $this->fldtitle->placeholder = 'eg. Price List ' . date('Y');
    $this->fldtitle->size = 50;
    $this->AssignFieldToSection('files', 'title');
    // filename field
    $this->fldfilename->description = 'Please select the file you wish to upload.';
    $this->AssignFieldToSection('files', 'filename');
    // description field
    $this->flddescription->description = 'A short description of the file.';
    $this->flddescription->placeholder = 'eg. Our price list for ' . date('Y');
    $this->flddescription->size = 100;
    $this->AssignFieldToSection('files', 'description');
  }

  protected function AssignItemRemove($confirmed) {
    $caption = $this->table->GetFieldValue('title');
    $this->NewSection(
      'confirmation', "Remove '{$caption}'",
      'This cannot be undone! Please click on the Confirm button to remove this file.');
    $desc = $this->AddField(
      'title', new formbuilderstatictext('title', '', 'Name of the file'), $this->table);
    $this->AssignFieldToSection('confirmation', 'title');
  }
}

$worker = new workerresmanfiles();

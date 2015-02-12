<?php
namespace dana\worker;

require_once 'class.workerform.php';
require_once 'class.workerbase.php';
require_once 'class.formbuilderdatagrid.php';

/**
  * worker resource manage files (downloadable files)
  * @version dana framework v.3
*/

class workerresmanfiles extends workerform {
  protected $datagrid;
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
      case workerbase::ACT_EDIT:
      case workerbase::ACT_NEW:
        $this->title = (($this->action == workerbase::ACT_EDIT) ? 'Modify' : 'New') . ' File';
        $this->fldtitle = $this->AddField(
          'title', new formbuildereditbox('title', '', 'Title'), $this->table);
        $this->fldfilename = $this->AddField(
          'filename', new formbuilderfilewebsite('filename', '', 'Filename'), $this->table);
        $this->fldfilename->targetpath = account::$instance->GetRelativePath('files'); // get the filename based on the media/account tables
        $fname = $this->table->GetFieldValue('filename', null);
        $this->fldfilename->targetfilename = ($this->action == workerbase::ACT_EDIT)
          ? $fname
          : null;

$this->AssignFileDetails();

        $this->fldfilename->mediaid = $this->table->ID(); // are we modifying item (as apposed to new item)
        $this->flddescription = $this->AddField(
          'description', new formbuildereditbox('description', '', 'Description of File'), $this->table);
        $this->returnidname = $this->idname;
        $this->showroot = false;
        break;
      case workerbase::ACT_REMOVE:
        $this->buttonmode = array(workerform::BTN_CONFIRM, workerform::BTN_CANCEL);
        $this->title = 'Remove File';
        $this->fldtitle = $this->AddField(
          'title', new formbuilderstatictext('description', '', 'File to be removed'));
        $this->action = workerbase::ACT_CONFIRM;
        $this->returnidname = $this->idname;
        $this->showroot = false;
        break;
      default:
        $this->fldaddfile = $this->AddField(
          'addfile', new formbuilderbutton('addfile', 'Upload New File'));
        $url = $_SERVER['PHP_SELF'] . "?in={$this->idname}&act=" . workerbase::ACT_NEW;
        $this->fldaddfile->url = $url;

        $this->buttonmode = array(workerform::BTN_BACK);
        $this->title = 'Manage Downloadable Files'; 
        $this->filelist = $this->AddField('filelist', $this->datagrid, $this->table);
        break;
    }
  }

  private function AssignFileDetails() {
    $file = (isset($_FILES[$this->fldfilename->name]))
      ? $file = $_FILES[$this->fldfilename->name]
      : false;
    $this->fldfilename->file = $file;
    if ((!$file) || (isset($file['error']) && $file['error'] == UPLOAD_ERR_NO_FILE)) {
      if ($this->itemid) {
        $file = \dana\core\database::SelectFromTableByField(
          'fileitem', \dana\table\basetable::FN_ID, $this->itemid
        );
        $this->fldfilename->file = array(
          'name' => $file['filename'], // $this->table->GetFieldValue('filename'),
          'size' => $file['filesize'], // $this->table->GetFieldValue('filesize'),
          'type' => $file['filetypeid'] //false
        );
      }
    }
  }

  protected function DeleteItem($itemid) {
    try {
      // TODO: mark as deleted in status
      //$status = STATUS_DELETED;
      //$query = 'DELETE `fileitem` WHERE `id` = ' . $itemid;
      //\dana\core\database::Query($query);
      $ret= true;
    } catch (\Exception $e) {
      $this->AddMessage('Cannot remove file');
      $ret = false;
    }
    return $ret;
  }

  protected function PostFields() {
    switch ($this->action) {
      case workerbase::ACT_EDIT:
      case workerbase::ACT_NEW:
        $ret = $this->fldtitle->Save() + $this->fldfilename->Save() +
          $this->flddescription->Save();
        break;
      case workerbase::ACT_CONFIRM:
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

  protected function GetTitleFromFilename($value) {
    return str_replace('_', ' ', pathinfo($value, PATHINFO_FILENAME));
  }

  protected function SaveToTable() {
    $this->AssignFileDetails();
    $file = $this->fldfilename->file;

    if ($this->table && $file && $file['name']) {
      if (isset($file['type']) && $file['type']) {
        $ftype = $file['type'];
        if (is_numeric($ftype)) {
          $filetypeid = $ftype;
        } else {
          $ln = \dana\core\database::SelectFromTableByField('filetype', 'filetype', $ftype, 'id');
          $filetypeid = ($ln) ? $ln : 0;
        }
        $this->table->SetFieldValue('filetypeid', $filetypeid);
      }
      $size = $file['size'];
      if ($size > 0) {
        $this->table->SetFieldValue('filesize', $size);
      }
      $title = $this->fldtitle->value;
      if (!$title) {
        $name = $this->GetTitleFromFilename($file['name']);
        $this->table->SetFieldValue('title', $name);
      }
      if (!$this->table->GetFieldValue(\dana\table\basetable::FN_STATUS)) {
        $this->table->SetFieldValue(\dana\table\basetable::FN_STATUS, \dana\table\basetable::STATUS_ACTIVE);
      }
    }
    return (int) $this->table->StoreChanges();
  }

  protected function AddErrorList() {
    $this->AddErrors($this->fldfilename->errors);
    $this->AddErrors($this->flddescription->errors);
    $this->AddErrors($this->fldtitle->errors);
  }

  protected function AssignFieldDisplayProperties() {
    $this->datagrid->SetIDName($this->idname);
    $this->NewSection(
      'files', 'Files Available For Your Visitors to Download',
      'Please specify the filename to upload and optionally a friendly title and description.');
    if ($this->filelist) {
      $this->filelist->description = 'Files Currently Available';
      $this->AssignFieldToSection('files', 'filelist');
    }
    if ($this->fldaddfile) {
      if ($this->fldaddfile) {
        $this->fldaddfile->description = 'Click this button to upload a new file to your website. ' .
        'The types of files to upload are office files (Word, Excel, Powerpoint or equivalents, ' .
        'JPEG, GIF, PNG images and PDF files). The maximum file size is 2MB (megabytes).';
        $this->AssignFieldToSection('files', 'addfile');
      }
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
    $this->fldfilename->description = 'Please select the file you wish to upload. ' .
      'The types of files to upload are <strong>office files</strong> ' .
      '(Word, Excel, Powerpoint or equivalents), ' .
      '<strong>Picture files</strong> (JPEG, GIF, PNG images) and ' .
      '<strong>Adobe Acrobat</strong> (PDF files).<br><strong>The maximum ' .
      'file size is 2MB (megabytes).</strong>';
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

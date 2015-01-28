<?php
require_once 'class.workerform.php';
require_once 'class.workerbase.php';
require_once 'class.formbuildereditbox.php';
require_once 'class.formbuilderfilewebimage.php';
require_once 'class.formbuilderbutton.php';

/**
  * activity worker for managing newsletter items
  * dana framework v.3
*/

// resource manage newsletter items

class workerresmannewsletteritems extends workerform {
//  protected $datagrid;
//  protected $table;
  protected $fldheading;
  protected $fldcontent;
  protected $fldnewsletterid;

  protected function InitForm() {
    $this->table = new newsletteritem($this->itemid);
    $this->icon = 'images/sect_resource.png';
    $this->activitydescription = 'some text here';
    $this->contextdescription = 'Newsletter items';
    $this->fldnewsletterid = new formbuilderhidden('newsletterid', $this->groupid);
    switch ($this->action) {
      case workerbase::ACT_NEW:
      case workerbase::ACT_EDIT:
        $this->title = 'Modify Newsletter Item';
        $this->fldheading = $this->AddField(
          'heading', new formbuildereditbox('heading', '', 'Item Heading'), $this->table);
        $this->fldcontent = $this->AddField(
          'content', new formbuildertextarea('content', '', 'Content'), $this->table);
        $this->returnidname = 'IDNAME_MANAGENEWSLETTERS';
        $this->showroot = false; 
        break;
      case workerbase::ACT_REMOVE:
        break;
      default:
/*        $this->buttonmode = array(BTN_BACK);
        $this->title = 'Manage Galleries'; 
        $this->fldgalleryimages = $this->AddField('gallery', $this->datagrid, $this->table);
        $this->fldaddimage = $this->AddField(
          'addgallery', new formbuilderbutton('addgallery', 'Add New Gallery'));
        $url = $_SERVER['PHP_SELF'] . "?in={$this->idname}&act=" . ACT_NEW;
        $this->fldaddimage->url = $url; */
        break;
    }
  }

  protected function PostFields() {
    switch ($this->action) {
      case workerbase::ACT_NEW:
      case workerbase::ACT_EDIT:
        $ret = $this->fldheading->Save() + $this->fldcontent->Save();
        break;
      default:
        $ret = true;
    }
    return $ret;
  }

  protected function SaveToTable() {
    $this->table->SetFieldValue('newslettertypeid', 1); // TODO: TEXT ONLY FOR NOW - add new types
    $this->table->SetFieldValue('newsletterid', $this->groupid);
    if (!trim($this->fldheading->value)) {
      $this->table->SetFieldValue('heading', 'New Heading');
    }
    // back to parent worker
    $ret = $this->SaveAndReset($this->table, 'IDNAME_MANAGENEWSLETTERS');
    $_GET['rid'] = $this->groupid;
    $_GET['action'] = workerbase::ACT_EDIT;
    return $ret;
  }

  protected function AddErrorList() {}

  protected function AssignFieldDisplayProperties() {
//    $this->datagrid->SetIDName($this->idname);
/*    $this->NewSection(
      'galleryimage', 'Gallery Picture',
      'You can upload a picture file to the gallery, type in a title (leave blank to use the filename of the image) and give a general description, which can be shown in Gallery pages.');
    $this->fldcontent->description = 'Gallery Picture';
    $this->AssignFieldToSection('galleryimage', 'image');
    if ($this->fldaddimage) {
      $this->fldaddimage->description = "Click this button to add a new image to the gallery";
      $this->AssignFieldToSection('galleryimage', 'addimage');
    } */
  }

  protected function AssignItemEditor($isnew) {
    $title = (($isnew) ? 'Adding a new' : 'Modify a ') . 'Newsletter Item';
    $this->NewSection(
      'item', 'Newsletter Item',
      'Here, you can specify a header (title) of the item and the content (text) of the item.');
    $this->fldheading->description = 'Specify a item heading. Please keep it short and simple.';
    $this->fldheading->size = 50;
    $this->fldheading->placeholder = 'eg. our latest product release';
    $this->AssignFieldToSection('item', 'heading');
    $this->fldcontent->description = 'This is the content of the item. This can be as long as you like but keep it interesting!';
    $this->AssignFieldToSection('item', 'content');
  }

  protected function AssignItemRemove($confirmed) {
  }
}

$worker = new workerresmannewsletteritems();

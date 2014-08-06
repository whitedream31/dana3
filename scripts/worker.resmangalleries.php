<?php
require_once 'class.workerform.php';
require_once 'class.workerbase.php';
require_once 'class.formbuilderdatagrid.php';
require_once 'class.formbuilderbutton.php';

/**
  * activity worker for managing galleries
  * dana framework v.3
*/

// resource manage galleries

class workerresmangalleries extends workerform {
  protected $datagrid;
  protected $table;
  protected $tableitems;
  protected $areadescription;
  protected $fldgalleries;
  protected $fldaddgallery;
  
  protected $fldtitle;
  protected $imagegrid;

  protected function InitForm() {
    $this->table = new gallery($this->itemid);
    $this->icon = 'images/sect_resource.png';
    $this->activitydescription = 'some text here' . ' - ' . $this->idname;
    $this->contextdescription = 'gallery management';
    $this->datagrid = new formbuilderdatagrid('gallery', '', 'Galleries');
    switch ($this->action) {
      case ACT_NEW:
      case ACT_EDIT:
        //$this->tableitems = new galleryitems
        $this->title = 'Modify Gallery';
        $this->fldtitle = $this->AddField(
          'title', new formbuildereditbox('title', '', 'Gallery Title'), $this->table);
        $this->imagegrid = $this->AddField(
          'imagegrid', new formbuilderdatagrid('imagegrid', '', 'Gallery Images'));
        $this->returnidname = $this->idname;
        $this->showroot = false;
        break;
      case ACT_REMOVE:
        break;
      default:
        $this->buttonmode = array(BTN_BACK);
        $this->title = 'Manage Galleries'; 
        $this->fldgalleries = $this->AddField('gallery', $this->datagrid, $this->table);
        $this->fldaddgallery = $this->AddField(
          'addgallery', new formbuilderbutton('addgallery', 'Add New Gallery'));
        $url = $_SERVER['PHP_SELF'] . "?in={$this->idname}&act=" . ACT_NEW;
        $this->fldaddgallery->url = $url;
        break;
    }
  }

  protected function PostFields() {
    switch ($this->action) {
      case ACT_EDIT:
        $ret = $this->fldtitle->Save();
        break;
      default:
        $ret = true;
    }
    return $ret;
  }

  protected function SaveToTable() {
    return (int) parent::StoreChanges(); //$this->table->StoreChanges();
  }

  protected function AddErrorList() {}

  protected function AssignFieldDisplayProperties() {
    $this->datagrid->SetIDName($this->idname);
    $this->NewSection(
      'gallery', 'Gallery',
      'Below are your galleries. Each gallery can contain as many images as you wish. Please note that the more images you add the slower the page will load and your viewers may be put off from viewing them. Try for a maximum of 40-50 images per gallery.');
    $this->fldgalleries->description = 'Image Galleries';
    $this->AssignFieldToSection('gallery', 'gallery');
    if ($this->fldaddgallery) {
      $this->fldaddgallery->description = "Click this button to add a new set of images - known as a 'gallery'";
      $this->AssignFieldToSection('gallery', 'addgallery');
    }
  }

  private function PopulateImageGrid() {
    $this->imagegrid->showactions = true;
    $this->imagegrid->AddColumn('IMG', 'Image', true);
    $this->imagegrid->AddColumn('DESC', 'Title', false);
    $this->imagegrid->AddColumn('TEXT', 'Description', false);
    $this->table->PopulateItems();
    $list = $this->table->visibleitems;
    if ($list) {
      $actions = array(TBLOPT_DELETABLE);
      $path = '../profiles/' . $this->account->GetFieldValue('nickname') . '/media/';
      foreach($list as $imageitem) {
        $thumbnail = $path . account::GetImageFilename($imageitem->GetFieldValue('largemediaid'), true);
        $coldata = array(
          'IMG' => "<img src='{$thumbnail}' alt=''>",
          'DESC' => $imageitem->GetFieldValue('title', '<em>(untitled)</em>'),
          'TEXT' => $imageitem->GetFieldValue('description')
        );
        $this->imagegrid->AddRow($imageitem->ID(), $coldata, true, $actions);
      }

    }
  }

  protected function AssignItemEditor($isnew) {
    $title = (($isnew) ? 'Creating a new' : 'Modify a ') . 'Gallery1';
    $this->NewSection(
      'gallery', $title,
      "Please describe the gallery with a simple name or phrase, such as 'Products', 'Portfolio', or 'Before and After' etc.");
    $this->NewSection(
      'imagegrid', 'Managing the Images in the Gallery',
      "Below are the images currently in the gallery. You can edit the details about each image, delete it or add a new image.");
    // title field
    $this->fldtitle->description = 'This is the name of the gallery.';
    $this->fldtitle->size = 50;
    $this->AssignFieldToSection('gallery', 'title');
    // imagegrid
    $this->imagegrid->SetIDName(IDNAME_MANAGEGALLERYIMAGES);
    $this->PopulateImageGrid();
    $this->imagegrid->description = 'Type in your reply to the comment. Please read the notice above.';
    $this->AssignFieldToSection('imagegrid', 'imagegrid');
  }

  protected function AssignItemRemove($confirmed) {
  }
}

$worker = new workerresmangalleries();
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
//  protected $table;
  protected $tableitems;
  protected $areadescription;
  protected $fldgalleries;
  protected $fldaddgallery;
  protected $fldaddimage;
  protected $fldtitle;
  protected $imagegrid;

  protected function InitForm() {
    $this->table = new gallery($this->itemid);
    $this->icon = 'images/sect_resource.png';
    $this->activitydescription = 'some text here' . ' - ' . $this->idname;
    $this->contextdescription = 'gallery management';
    $this->datagrid = new formbuilderdatagrid('gallery', '', 'Galleries');
    switch ($this->action) {
      case workerbase::ACT_NEW:
      case workerbase::ACT_EDIT:
        //$this->tableitems = new galleryitems
        $this->title = ($this->action == workerbase::ACT_NEW) ? 'New Gallery' : 'Modify Gallery';
        $this->fldtitle = $this->AddField(
          'title', new formbuildereditbox('title', '', 'Gallery Title'), $this->table);
        if ($this->action == workerbase::ACT_EDIT) {
          $this->imagegrid = $this->AddField(
            'imagegrid', new formbuilderdatagrid('imagegrid', '', 'Gallery Images'));
        }
        $this->returnidname = $this->idname;
        $this->showroot = false;
        break;
      case workerbase::ACT_REMOVE:
        break;
      default:
        $this->buttonmode = array(workerform::BTN_BACK);
        $this->title = 'Manage Galleries'; 
        $this->fldgalleries = $this->AddField('gallery', $this->datagrid, $this->table);
        $this->fldaddgallery = $this->AddField(
          'addgallery', new formbuilderbutton('addgallery', 'Add New Gallery'));
        $url = $_SERVER['PHP_SELF'] . "?in={$this->idname}&amp;act=" . workerbase::ACT_NEW;
        $this->fldaddgallery->url = $url;
        break;
    }
  }

  protected function PostFields() {
    switch ($this->action) {
      case workerbase::ACT_NEW:
      case workerbase::ACT_EDIT:
        $ret = $this->fldtitle->Save();
        break;
      default:
        $ret = true;
    }
    return $ret;
  }

  protected function SaveToTable() {
    return (int) $this->table->StoreChanges(); //parent::StoreChanges(); //$this->table->StoreChanges();
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
      $actions = array(formbuilderdatagrid::TBLOPT_DELETABLE);
      $options = array('parentid' => $this->itemid); // parent of the images is the gallery (item id)
      $path = '../profiles/' . $this->account->GetFieldValue('nickname') . '/media/';
      foreach($list as $imageitem) {
        $thumbnail = $path . account::GetImageFilename($imageitem->GetFieldValue('largemediaid'), true);
        $coldata = array(
          'IMG' => "<img src='{$thumbnail}' alt=''>",
          'DESC' => $imageitem->GetFieldValue('title', '<em>(untitled)</em>'),
          'TEXT' => $imageitem->GetFieldValue('description'),
        );
        $this->imagegrid->AddRow($imageitem->ID(), $coldata, true, $actions, $options);
      }

    }
  }

  protected function AssignItemEditor($isnew) {
    $title = (($isnew) ? 'Creating a new ' : 'Modify a ') . 'Gallery';
    $this->NewSection(
      'gallery', $title,
      "Please describe the gallery with a simple name or phrase, such as 'Products', 'Portfolio', or 'Before and After' etc.");
    $desc = ($isnew) 
      ? 'To add images to the gallery first save the gallery then click on the gallery name to edit it.'
      : "Below are the images currently in the gallery. You can edit the details about each image, delete it or add a new image.";
    $this->NewSection(
      'imagegrid', 'Managing the Images in the Gallery', $desc);
    // title field
    $this->fldtitle->description = 'This is the name of the gallery.';
    $this->fldtitle->size = 50;
    $this->AssignFieldToSection('gallery', 'title');
    // imagegrid
    if ($this->imagegrid) {
      $this->imagegrid->SetIDName('IDNAME_RESOURCES_GALLERYIMAGES');
      $this->PopulateImageGrid();
      $this->imagegrid->description = 'Type in your reply to the comment. Please read the notice above.';
      $this->AssignFieldToSection('imagegrid', 'imagegrid');
      // add image button
      $this->fldaddimage = $this->AddField(
        'addimage', new formbuilderbutton('addimage', 'Add Picture'));
      $url = $_SERVER['PHP_SELF'] . "?in=IDNAME_RESOURCES_GALLERYIMAGES" .
        "&amp;pid={$this->itemid}&amp;act=" . workerbase::ACT_NEW;
      $this->fldaddimage->url = $url;
      $this->AssignFieldToSection('imagegrid', 'addimage');
    }
  }

  protected function AssignItemRemove($confirmed) {
  }
}

$worker = new workerresmangalleries();

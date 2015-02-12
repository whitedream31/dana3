<?php
namespace dana\worker;

require_once 'class.workerform.php';
require_once 'class.workerbase.php';

/**
  * worker resource manage gallery images
  * @version dana framework v.3
*/

class workerresmangalleryimages extends workerform {
  protected $fldtitle;
  protected $fldimage;
  protected $flddescription;
  protected $fldgalleryid;

  protected function InitForm() {
    $this->table = new \dana\table\galleryitem($this->itemid);
    $this->icon = 'images/sect_resource.png';
    $this->activitydescription = 'some text here';
    $this->contextdescription = 'gallery picture management';
//    $this->datagrid = new formbuilderdatagrid('galleryimage', '', 'Gallery Picture');
    $this->fldgalleryid = new \dana\formbuilder\formbuilderhidden('galleryid', $this->groupid); // //$table->GetFieldValue('galleryid'));
    switch ($this->action) {
      case workerbase::ACT_NEW:
      case workerbase::ACT_EDIT:
        $this->title = 'Gallery Picture';
        $this->fldtitle = $this->AddField(
          'title', new \dana\formbuilder\formbuildereditbox('title', '', 'Picture Title'), $this->table);
        $this->fldimage = $this->AddField(
          'image', new \dana\formbuilder\formbuilderfilewebimage('largemediaid', '', 'Picture'), $this->table);

        $this->fldimage->mediaid = $this->table->GetFieldValue('largemediaid');

        $media = $this->GetTargetNameFromMedia($this->fldimage->mediaid); // get the fk for media id
        if ($this->posting) {
          $this->fldimage->targetfilename = \dana\table\account::$instance->GetMediaFilename($this->fldimage->mediaid); // get the filename based on the media/account tables
        } else {
          if ($media) {
            $this->fldimage->previewthumbnail = $media['thumbnail'];
          } else {
            $this->fldimage->previewthumbnail = 'none';
          }
        }
        $this->fldimage->AssignThumbnail(
          '../profiles/' . $this->account->GetFieldValue('nickname') . '/media/',
          ($media) ? $media['thumbnail'] : 'none',
          ($media) ? $media['filename'] : false
        );

        $this->flddescription = $this->AddField(
          'description', new \dana\formbuilder\formbuildertextarea('description', '', 'Description of Picture'), $this->table);
        $this->returnidname = 'IDNAME_RESOURCES_GALLERIES';
        $this->showroot = false;

        break;
      case workerbase::ACT_REMOVE:
        break;
      default:
/*        $this->buttonmode = array(workerform::BTN_BACK);
        $this->title = 'Manage Galleries'; 
        $this->fldgalleryimages = $this->AddField('gallery', $this->datagrid, $this->table);
        $this->fldaddimage = $this->AddField(
          'addgallery', new formbuilderbutton('addgallery', 'Add New Gallery'));
        $url = $_SERVER['PHP_SELF'] . "?in={$this->idname}&act=" . ACT_NEW;
        $this->fldaddimage->url = $url; */
        break;
    }
  }

  protected function Notify($postsucceeded) {
    if ($postsucceeded) {
      echo "<p>SUCCEEDED</p>\n";
//      $this->redirect = activitymanager::IDNAME_MANAGEGALLERIES;
    } else {
      echo "<p>FAILED</p>\n";
    }
  }

  protected function PostFields() {
    switch ($this->action) {
      case workerbase::ACT_NEW:
      case workerbase::ACT_EDIT:
        $ret = $this->fldtitle->Save() + $this->fldimage->Save() + $this->flddescription->Save();
        break;
      default:
        $ret = true;
    }
    return $ret;
  }

  protected function SaveToTable() {
    $ret = -1;
    if ($this->fldimage->usecurrentfile) {
      $ret = (int) $this->table->StoreChanges(); //$ret = $this->table->ID();
    } else {
      $media = $this->account->media;
      if ($media) {
        $media->AssignFromWebImage($this->fldimage);
        $nextimgnumber = \dana\table\account::$instance->nextimgnumber;
        $media->SetFieldValue('imgid', $nextimgnumber);

        if ($media->StoreChanges()) {
          $mediaid = $media->ID();
          $this->table->SetFieldValue('largemediaid', $mediaid);
          $this->table->SetFieldValue('galleryid', $this->groupid);
          $ret = (int) $this->table->StoreChanges();
        }
      }
    }
    // back to gallery worker
    $this->SaveAndReset(false, 'IDNAME_MANAGEGALLERIES');
    return $ret;
  }

  protected function AddErrorList() {
    $this->AddErrors($this->fldtitle->errors);
    $this->AddErrors($this->fldimage->errors);
    $this->AddErrors($this->flddescription->errors);
  }

  protected function AssignFieldDisplayProperties() {
//    $this->datagridsettings->SetIDName(activitymanager::IDNAME_MANAGEGALLERIES);
//    $this->datagrid->SetIDName($this->idname);
/*    $this->NewSection(
      'galleryimage', 'Gallery Picture',
      'You can upload a picture file to the gallery, type in a title (leave blank to use the filename of the image) and give a general description, which can be shown in Gallery pages.');
    $this->fldimage->description = 'Gallery Picture';
    $this->AssignFieldToSection('galleryimage', 'image');
    if ($this->fldaddimage) {
      $this->fldaddimage->description = "Click this button to add a new image to the gallery";
      $this->AssignFieldToSection('galleryimage', 'addimage');
    } */
  }

  protected function AssignItemEditor($isnew) {
    $title = (($isnew) ? 'Adding a new' : 'Modify a ') . 'Gallery Picture';
    $this->NewSection(
      'galleryimage', $title,
      'You can upload a picture file to the gallery, type in a title (leave blank to use the filename of the image) and give a general description, which can be shown in Gallery pages.');
    $this->fldtitle->description = 'Specify a title of the image (leave blank to use the filename of the image).';
    $this->fldtitle->size = 50;
    $this->fldtitle->placeholder = 'eg. first picture';
    $this->AssignFieldToSection('galleryimage', 'title');
/*
    $this->fldimage->mediaid = $this->table->GetFieldValue('largemediaid');
    $media = $this->GetTargetNameFromMedia($this->fldimage->mediaid);
    $this->fldimage->AssignThumbnail(
      '../profiles/' . $this->account->GetFieldValue('nickname') . '/media/',
      ($media) ? $media['thumbnail'] : 'none',
      ($media) ? $media['filename'] : false
    );
*/
    $this->fldimage->description = 'Gallery Picture';
    $this->AssignFieldToSection('galleryimage', 'image');
    $this->flddescription->description = 'Picture Description';
    $this->flddescription->rows = 10;
    $this->AssignFieldToSection('galleryimage', 'description');
  }

  protected function AssignItemRemove($confirmed) {
  }
}

$worker = new workerresmangalleryimages();

<?php
// GALLERY page container class for MyLocalSmallBusiness
// written by Ian Stewart (c) 2012 Whitedream Software
// created: 8 dec 2012
// modified: 24 jul 2014

require_once 'class.table.page.php';

// gallery page class
class pagegallery extends page {
  protected $galleries;
  protected $fldgallerygroup;
  protected $fldincdescription;
  protected $fldimagesperpage;

  protected function AssignPageType() {
    $this->pgtype = self::PAGETYPE_GALLERY;
  }

  // assign table columns just used by this type of page
  protected function AssignPageTypeFields() {
    //hascomments  tinyint
    $this->AddField('groupid', self::DT_FK);
    $this->AddField('incdescription', self::DT_BOOLEAN, true);
    $this->AddField('imagesperpage', self::DT_INTEGER, 12);
  }

  protected function InitFieldsForMainContent($worker) {
    parent::InitFieldsForMainContent($worker);
    // gallery
    $this->galleries = $this->GetGalleryList(true);
    $groupid = $this->GetFieldValue('groupid');
    $this->fldgallerygroup = $worker->AddField(
      'groupid', new formbuilderselect('groupid', '', 'Choose the gallery to show'), $this);
    if ($this->galleries) {
      foreach ($this->galleries as $id => $title) {
        $this->fldgallerygroup->AddValue($id, $title, $id == $groupid);
      }
    } else {
      $this->fldgallerygroup->AddValue(0, '(no galleries found)', true);
      $this->fldgallerygroup->isdisabled = true;
    }
    // images per page
    $ippvalues = array(
      6 => '6 (2 cols x 3 rows)',
      12 => '12 (3 cols x 4 rows)',
      24 => '24 (4 cols x 6 rows)',
      48 => '48 (4 cols x 12 rows)'
    );
    $ipp = $this->GetFieldValue('imagesperpage');
    $this->fldimagesperpage = $worker->AddField(
      'imagesperpage', new formbuilderselect('imagesperpage', '', 'How many pictures per page?'), $this);
    foreach ($ippvalues as $id => $title) {
      $this->fldimagesperpage->AddValue($id, $title, $id == $ipp);
    }
  }

  public function AssignFieldProperties($worker, $isnew) {
    parent::AssignFieldProperties($worker, $isnew);
    $count = count($this->galleries);
    if ($count) {
      $this->fldgallerygroup->size = ($count < 5) ? $count : 5;
      $this->fldgallerygroup->description =
        'Pick the gallery to be shown in this page. Each picture will be shown in a grid on this page.';
    } else {
      $this->fldgallerygroup->size = 2;
      $this->fldgallerygroup->description =
        'No galleries found - please go to the Resources section and add a new gallery.';
    }
    $worker->AssignFieldToSection('sectmaincontent', 'groupid');
    $this->fldimagesperpage->description =
      'Choose the number of pictures you would like to be shown at a time (if there are more pictures they will be paginated).';
    $this->fldimagesperpage->size = 4;
    $worker->AssignFieldToSection('sectmaincontent', 'imagesperpage');
  }

  protected function SaveFormFields() {
    return parent::SaveFormFields() +
      $this->SaveFormField($this->fldgallerygroup) + $this->SaveFormField($this->fldimagesperpage);
  }

  public function ValidateFields() {
    require_once 'class.table.media.php';
    $groupid = $this->gallerygroup->value;
    if ($groupid > 0) {
      $galleryheight = media::GetHighestImageValue($groupid);
      $this->SetFieldValue('galleryheight', $galleryheight);
    }
  }
}

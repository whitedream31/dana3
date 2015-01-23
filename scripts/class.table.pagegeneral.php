<?php
// GENERAL page container class for MyLocalSmallBusiness
// written by Ian Stewart (c) 2012 Whitedream Software
// created: 8 dec 2012 (originally 7 apr 2010)
// modified: 23 jul 2014

require_once 'class.table.page.php';

// general page class
class pagegeneral extends page {
  protected $fldgallerygroup;
  protected $fldmaincontent;

  protected function AssignPageType() {
    $this->pgtype = self::PAGETYPE_GENERAL;
  }

  // assign table columns just used by this type of page
  protected function AssignPageTypeFields() {
    $this->AddField('maincontent', self::DT_TEXT); //, '', FLDTYPE_TEXTAREA);
    $this->AddField('gengalleryid', self::DT_FK);
    $this->AddField('galleryheight', self::DT_FK);
    //$this->AddField('gengallerystyleid', DT_FK);
  }

  protected function InitFieldsForMainContent($worker) {
    parent::InitFieldsForMainContent($worker);
    $this->fldmaincontent = $worker->AddField(
      'maincontent', new formbuildertextarea('maincontent', '', 'Main Content'), $this);
    // gallery
    $gallerylist = $this->GetGalleryList(true);
    $this->fldgallerygroup = $worker->AddField(
      'gengalleryid', new formbuilderselect('gengalleryid', '', 'Include a gallery?'), $this);
    $groupid = $this->GetFieldValue('gengalleryid');
    $this->fldgallerygroup->AddValue(0, '(no gallery)', $groupid == 0);
    foreach ($gallerylist as $id => $title) {
      $this->fldgallerygroup->AddValue($id, $title, $id == $groupid);
    }
  }

  protected function InitFieldsForSideContent($worker) {
    parent::InitFieldsForSideContent($worker);
    $this->InitFieldForContactSidebar($worker);
  }

  public function AssignFieldProperties($worker, $isnew) {
    parent::AssignFieldProperties($worker, $isnew);
    // main content field
    $this->fldmaincontent->description =
      'This is the main text of your page. This can be as long as you like. <strong>Please check your spelling and ' .
      'grammar carefully.</strong>';
    $this->fldmaincontent->size = 100;
    $worker->AssignFieldToSection('sectmaincontent', 'maincontent');
    // gengalleryid field
    $this->fldgallerygroup->description =
      'If you have one or more galleries, you can specify it here and it will be shown on this page (one item at a time).';
    $worker->AssignFieldToSection('sectmaincontent', 'gengalleryid');
  }

  protected function SaveFormFields() {
    return parent::SaveFormFields() +
      $this->SaveFormField($this->fldgallerygroup) + $this->SaveFormField($this->fldmaincontent);
  }

  public function ValidateFields() {
    require_once('class.table.media.php');
    $groupid = $this->GetFieldValue('gengalleryid');
    if ($groupid > 0) {
      $galleryheight = media::GetHighestImageValue($groupid);
      $this->SetFieldValue('galleryheight', $galleryheight);
    }
  }

}

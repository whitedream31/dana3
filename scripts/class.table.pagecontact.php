<?php
// CONTACT page container class for MyLocalSmallBusiness
// written by Ian Stewart (c) 2012 Whitedream Software
// created: 8 dec 2012 (originally 7 apr 2010)
// modified: 23 jul 2014

require_once 'class.table.page.php';

// contact page class
class pagecontact extends page {
  protected $fldshowmap;
  protected $fldmapaddress;

  protected function AssignPageType() {
    $this->pgtype = PAGETYPE_CONTACT;
  }

  protected function AfterPopulateFields() {
    parent::AfterPopulateFields();
  }

  // assign table columns just used by this type of page
  protected function AssignPageTypeFields() {
//incemail  tinyint
    $this->AddField('contactname', DT_STRING, 'Name');
    $this->AddField('contactemail', DT_STRING, 'E-mail Address');
    $this->AddField('contactsubject', DT_STRING, 'Subject');
    $this->AddField('contactmessage', DT_STRING, 'Message');
//    $this->AddField('inccontactinsidearea', DT_BOOLEAN);
    $this->AddField('inccontactname', DT_BOOLEAN);
    $this->AddField('incaddress', DT_BOOLEAN);
    $this->AddField('inctelephone', DT_BOOLEAN);
    $this->AddField('showmap', DT_BOOLEAN);
    $this->AddField('mapaddress', DT_STRING);
  }

  protected function InitFieldsForMainContent($worker) {
    parent::InitFieldsForMainContent($worker);
    $this->fldgallerygroup = $worker->AddField(
      'gengalleryid', new formbuilderselect('gengalleryid', '', 'Include a gallery?'), $this);
    $this->fldshowmap = $worker->AddField(
      'showmap', new formbuildercheckbox('showmap', '', 'Show map'), $this);
    $this->fldmapaddress = $worker->AddField(
      'mapaddress', new formbuildereditbox('mapaddress', '', 'Map Address'), $this);
    $this->fldmapaddress->size = 80;
  }

  protected function InitFieldsForSideContent($worker) {
    parent::InitFieldsForSideContent($worker);
    $this->InitFieldForContactSidebar($worker);
  }

  public function AssignFieldProperties($worker, $isnew) {
    parent::AssignFieldProperties($worker, $isnew);
    if (!$this->GetFieldValue('mapaddress')) {
      $addr = strtolower($this->account->Contact()->FullAddress('', ' '));
      $this->fldmapaddress->value = $addr;
      //$this->SetFieldValue('mapaddress', $addr);
    }
    // show map field
    $this->fldshowmap->description =
      'Please check the box if you want a map showing where your business is situated. If you have a showroom or shop if is highly ' .
      'recommended. If you are based at home or telephone/online based it is not recommended and should be unticked.';
    $worker->AssignFieldToSection('sectmaincontent', 'showmap');
    // map address field
    $this->fldmapaddress->description =
      'Please enter your <strong>full</strong> business address, including your post code. Please check for spelling mistakes. This ' .
      'is used to find you on the map. <strong>If your address is not correct it will not be shown in the map.</strong> It is ' .
      'highly recommended to register free in ' .
      '<a href="http://google.co.uk/local/add" title="register for google places">Google Places</a>.';
    $worker->AssignFieldToSection('sectmaincontent', 'mapaddress');
  }

  protected function SaveFormFields() {
    return parent::SaveFormFields() +
      $this->SaveFormField($this->fldshowmap) + $this->SaveFormField($this->fldmapaddress);
  }

  public function ValidateFields() {
    //TODO: process map address
  }
}

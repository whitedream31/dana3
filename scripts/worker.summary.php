<?php
require_once 'class.workerform.php';
require_once 'class.workerbase.php';
require_once 'class.formbuildersummarybox.php';
//require_once 'class.formbuildereditbox.php';
//require_once 'class.formbuilderselect.php';
//require_once 'class.formbuildertextarea.php';
//require_once 'class.formbuilderurl.php';

/**
  * activity worker for account summary (main home)
  * dana framework v.3
*/

// account change org details

class workeraccsummary extends workerform {
  protected $fldorgdetails;
  protected $fldlogomediaid;

//  protected $fldbusinesscategoryid;
//  protected $fldbusinesscategory2id;
//  protected $fldbusinesscategory3id;

  protected function BlankValue($value, $default) {
    return ($value) ? $value : $default;
  }

  protected function InitForm() {
    $this->title = 'Account Summary';
    $this->icon = 'images/sect_account.png';
    $this->activitydescription = 'some text here';
    $this->contextdescription = 'account summary stuff';

    $this->fldorgdetails = $this->AddField(
      'orgdetails',
      new formbuildersummarybox('orgdetails', '', 'org label'),
      $this->account
    );
    $this->fldorgdetails->AddItemWithField('orgname', 'Organisation Name', 'businessname');
    $this->fldorgdetails->AddItemLookup(
      'orgbusinesscategoryid1', 'Business Type #1', 'businesscategory', 'businesscategoryid', basetable::FN_DESCRIPTION, '<em>(none)</em>');
    $this->fldorgdetails->AddItemLookup(
      'orgbusinesscategoryid2', 'Business Type #2', 'businesscategory', 'businesscategoryid2', basetable::FN_DESCRIPTION, '<em>(none)</em>');
    $this->fldorgdetails->AddItemLookup(
      'orgbusinesscategoryid3', 'Business Type #3', 'businesscategory', 'businesscategoryid3', basetable::FN_DESCRIPTION, '<em>(none)</em>');
    $this->fldorgdetails->AddItemWithField('orgwebsite', 'Website', 'website', '<em>(none)</em>');
    $this->fldorgdetails->AddItemWithField('orginfo', 'Brief Description', 'businessinfo');

    $this->fldorgdetails->AddItem(
      'orgaddress', 'Home Location', account::$instance->contact->FullAddress(), '<em>(unknown</em>');

    // logo
    $this->fldlogomediaid = $this->AddField(
      'logomediaid', new formbuilderfilewebimage('logomediaid', '', 'Business Logo'), $this->account);
    $this->fldlogomediaid->mediaid = $this->account->GetFieldValue('logomediaid');
    $media = $this->GetTargetNameFromMedia($this->fldlogomediaid->mediaid); // get the fk for media id
    if ($media) {
      $this->fldlogomediaid->previewthumbnail = $media['thumbnail'];
    } else {
      $this->fldlogomediaid->previewthumbnail = 'none';
    }
    $this->fldlogomediaid->AssignThumbnail(
      '../profiles/' . $this->account->GetFieldValue('nickname') . '/media/',
      ($media) ? $media['thumbnail'] : 'none',
      ($media) ? $media['filename'] : false
    );

//    $this->fldtagline = $this->AddField(
//      'tagline',
//      new formbuildereditbox('tagline', '', 'Tagline'),
//      $this->account);

    // populate the business types
//    $categorylist = array(); //$this->GetCategoryList();
//    $this->fldbusinesscategoryid->AddToGroup('', 0, 'none');
//    $this->fldbusinesscategory2id->AddToGroup('', 0, 'none');
//    $this->fldbusinesscategory3id->AddToGroup('', 0, 'none');
//    foreach($categorylist as $catgroupname => $catgrouplist) {
//      foreach($catgrouplist as $catid => $catdescription) {
//        $this->fldbusinesscategoryid->AddToGroup($catgroupname, $catid, $catdescription);
//        $this->fldbusinesscategory2id->AddToGroup($catgroupname, $catid, $catdescription);
//        $this->fldbusinesscategory3id->AddToGroup($catgroupname, $catid, $catdescription);
//      }
//    }
  }

  protected function PostFields() {
//    return
//      $this->fldbusinessname->Save() + $this->fldtagline->Save() +
//      $this->fldbusinesscategoryid->Save() + $this->fldbusinesscategory2id->Save() +
//      $this->fldbusinesscategory3id->Save() + $this->fldbusinessinfo->Save() +
//      $this->fldwebsite->Save() + $this->fldlogomediaid->Save();
  }

  protected function SaveToTable() {
//    $this->account->UpdateLogoMedia($this->fldlogomediaid, false);
//    $this->showroot = $this->account->StoreChanges();
//    return (int) $this->showroot;
  }

  protected function AddErrorList() {
//    $this->AddErrors($this->fldbusinessname->errors);
//    $this->AddErrors($this->fldtagline->errors);
//    $this->AddErrors($this->fldbusinesscategoryid->errors);
//    $this->AddErrors($this->fldbusinesscategory2id->errors);
//    $this->AddErrors($this->fldbusinesscategory3id->errors);
//    $this->AddErrors($this->fldbusinessinfo->errors);
//    $this->AddErrors($this->fldwebsite->errors);
//    $this->AddErrors($this->fldlogomediaid->errors);
  }

  protected function AssignFieldDisplayProperties() {
    // add sections
    $this->NewSection(
      'orggroup', 'Organisation Details', 'Your business information.');
//    $this->NewSection(
//      'contgroup', 'Your Details', 'Your contact information.');

    $this->fldorgdetails->description = 'The name of your organisation';
    $this->AssignFieldToSection('orggroup', 'orgdetails');

    // add org fields
    // logo
    $this->fldlogomediaid->description = 'Please select a image file to upload for your business, if you have one.';
    $this->fldlogomediaid->isreadonly = true;
    $this->AssignFieldToSection('orggroup', 'logomediaid');
    // - tagline
//    $this->fldtagline->description = 'Please enter a tagline (i.e. company slogan), if you have one';
//    $this->fldtagline->required = false;
//    $this->fldtagline->size = 80;
//    $this->fldtagline->maxlength = 100;
//    $this->fldtagline->placeholder = 'a short phase here';
//    $this->AssignFieldToSection('orggroup', 'tagline');

    // - business types
//    $this->fldbusinesscategoryid->description = 'Please choose your primary type of business';
//    $this->fldbusinesscategoryid->required = true;
//    $this->fldbusinesscategoryid->pattern = ".{6,50}";
//    $this->AssignFieldToSection('contgroup', 'businesscategoryid');
//    $this->fldbusinesscategory2id->description = 'Please choose your secondary type of business';
//    $this->fldbusinesscategory2id->required = false;
//    $this->fldbusinesscategory2id->pattern = ".{6,50}";
//    $this->AssignFieldToSection('contgroup', 'businesscategory2id');
//    $this->fldbusinesscategory3id->description = 'Please choose your third type of business';
//    $this->fldbusinesscategory3id->required = false;
//    $this->fldbusinesscategory3id->pattern = ".{6,50}";
//    $this->AssignFieldToSection('contgroup', 'businesscategory3id');
  }

}

$worker = new workeraccsummary();

<?php
require_once 'class.workerform.php';
require_once 'class.workerbase.php';
require_once 'class.formbuildereditbox.php';
require_once 'class.formbuilderselect.php';
require_once 'class.formbuildertextarea.php';
require_once 'class.formbuilderurl.php';

/**
  * base activity worker
  * dana framework v.3
*/

// account change org details

class workeraccchgorgdet extends workerform {
  protected $fldbusinessname;
  protected $fldtagline;
  protected $fldbusinessinfo;
  protected $fldwebsite;
  protected $fldbusinesscategoryid;
  protected $fldbusinesscategory2id;
  protected $fldbusinesscategory3id;

  protected function InitForm() {
    $this->title = 'Change Account Details';
    $this->icon = 'images/sect_account.png';
    $this->activitydescription = 'some text here';
    $this->contextdescription = 'account details';

    $this->fldbusinessname = $this->AddField(
      'businessname',
      new formbuildereditbox('businessname', '', 'Organisation Name'),
      $this->account);
    $this->fldtagline = $this->AddField(
      'tagline',
      new formbuildereditbox('tagline', '', 'Tagline'),
      $this->account);
    $this->fldbusinesscategoryid = $this->AddField(
      'businesscategoryid',
      new formbuilderselect('businesscategoryid', '', 'Main type of business'),
      $this->account);
    $this->fldbusinesscategory2id = $this->AddField(
      'businesscategory2id', new formbuilderselect('businesscategory2id', '', 'Secondary type of business'),
      $this->account);
    $this->fldbusinesscategory3id = $this->AddField(
      'businesscategory3id', new formbuilderselect('businesscategory3id', '', 'Other type of business'),
      $this->account);
    // populate the business types
    $categorylist = $this->GetCategoryList();
    $this->fldbusinesscategoryid->AddToGroup('', 0, 'none');
    $this->fldbusinesscategory2id->AddToGroup('', 0, 'none');
    $this->fldbusinesscategory3id->AddToGroup('', 0, 'none');
    foreach($categorylist as $catgroupname => $catgrouplist) {
      foreach($catgrouplist as $catid => $catdescription) {
        $this->fldbusinesscategoryid->AddToGroup($catgroupname, $catid, $catdescription);
        $this->fldbusinesscategory2id->AddToGroup($catgroupname, $catid, $catdescription);
        $this->fldbusinesscategory3id->AddToGroup($catgroupname, $catid, $catdescription);
      }
    }
    $this->fldbusinessinfo = $this->AddField(
      'businessinfo', new formbuildertextarea('businessinfo', '', 'Brief Description'),
      $this->account);
    $this->fldwebsite = $this->AddField(
      'website', new formbuilderurl('website', '', 'Main Web Site (if any)'),
      $this->account);
    // logo
    $this->fldlogomediaid = $this->AddField(
      'logomediaid', new formbuilderfilewebimage('logomediaid', '', 'Business Logo'), $this->account);
    $this->fldlogomediaid->mediaid = $this->GetFieldValue('logomediaid');
    $media = $this->GetMediaThumbnail($this->fldlogomediaid->mediaid); // get the fk for media id
    if ($media) {
      $this->fldlogomediaid->previewthumbnail = $media['thumbnail'];
    } else {
      $this->fldlogomediaid->previewthumbnail = 'none';
    }
    $this->fldlogomediaid->targetfilename = $media['targetfilename']; // $this->GetMediaFilename($this->logo->mediaid);
    $this->fldlogomediaid->targetpath = $this->GetRelativePath('media');
  }

  protected function PostFields() {
    return
      $this->fldbusinessname->Save() + $this->fldtagline->Save() +
      $this->fldbusinesscategoryid->Save() + $this->fldbusinesscategory2id->Save() +
      $this->fldbusinesscategory3id->Save() + $this->fldbusinessinfo->Save() +
      $this->fldwebsite->Save() + $this->fldlogomediaid->Save();
  }

  protected function SaveToTable() {
    $this->account->UpdateLogoMedia($this->fldlogomediaid, false);
    $this->showroot = $this->account->StoreChanges();
    return (int) $this->showroot;
  }

  protected function AddErrorList() {
    $this->AddErrors($this->fldbusinessname->errors);
    $this->AddErrors($this->fldtagline->errors);
    $this->AddErrors($this->fldbusinesscategoryid->errors);
    $this->AddErrors($this->fldbusinesscategory2id->errors);
    $this->AddErrors($this->fldbusinesscategory3id->errors);
    $this->AddErrors($this->fldbusinessinfo->errors);
    $this->AddErrors($this->fldwebsite->errors);
    $this->AddErrors($this->fldlogomediaid->errors);
  }

  protected function GetCategoryList() {
    $ret = array();
    $categorygrouplist = database::RetrieveLookupList('businesscategorygroup', FN_DESCRIPTION, FN_REF, FN_ID, '');
    foreach($categorygrouplist as $groupid =>$groupname) {
      $categorylist = database::RetrieveLookupList('businesscategory', FN_DESCRIPTION, FN_REF, FN_ID, '`businesscategorygroupid` = ' . $groupid);
      foreach ($categorylist as $catid => $catdescription) {
        $ret[$groupname][$catid] = $catdescription;
      }
    }
    return $ret;
  }

  protected function AssignFieldDisplayProperties() {
    // add section
    $this->NewSection(
      'orggroup', 'Organisation Details', 'Your business information.');
    $this->NewSection(
      'btypegroup', 'Business Category', 
      'Please specify the type of business you have. You can have up to three but try putting them in order of importance.');
    $this->NewSection(
      'logo', 'Business Logo', 
      'Please specify a picture that represents your organisation, if you have one. This is shown next to your organisation name on each page of your website.');
    // add org fields
    // - business name
    $this->fldbusinessname->description = 'Please enter the name of your organisation';
    $this->fldbusinessname->required = true;
    $this->fldbusinessname->size = 80;
    $this->fldbusinessname->pattern = ".{3,100}"; // min 3, max 100
    $this->AssignFieldToSection('orggroup', 'businessname');
    // - tagline
    $this->fldtagline->description = 'Please enter a tagline (i.e. company slogan), if you have one';
    $this->fldtagline->required = false;
    $this->fldtagline->size = 80;
    $this->fldtagline->maxlength = 100;
    $this->fldtagline->placeholder = 'a short phase here';
    $this->AssignFieldToSection('orggroup', 'tagline');
    $this->fldwebsite->description = "If you have another (larger) website that you would like to link and help promote, please specify it here. <strong>Please include the prefix: &quot;http://&quot;</strong>";
    $this->AssignFieldToSection('orggroup', 'website');
    // - business types
    $this->fldbusinesscategoryid->description = 'Please choose your primary type of business';
    $this->fldbusinesscategoryid->required = true;
    $this->fldbusinesscategoryid->pattern = ".{6,50}";
    $this->AssignFieldToSection('btypegroup', 'businesscategoryid');
    $this->fldbusinesscategory2id->description = 'Please choose your secondary type of business';
    $this->fldbusinesscategory2id->required = false;
    $this->fldbusinesscategory2id->pattern = ".{6,50}";
    $this->AssignFieldToSection('btypegroup', 'businesscategory2id');
    $this->fldbusinesscategory3id->description = 'Please choose your third type of business';
    $this->fldbusinesscategory3id->required = false;
    $this->fldbusinesscategory3id->pattern = ".{6,50}";
    $this->AssignFieldToSection('btypegroup', 'businesscategory3id');
    $this->fldbusinessinfo->description = 'Provide a short description of your business';
    $this->fldbusinessinfo->required = false;
    $this->fldbusinessinfo->cols = 120;
    $this->AssignFieldToSection('btypegroup', 'businessinfo');
    $this->fldlogomediaid->description = 'Please select a image file to upload for your business, if you have one.';
    $this->AssignFieldToSection('logo', 'logomediaid');
  }

}

$worker = new workeraccchgorgdet();

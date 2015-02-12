<?php
namespace dana\worker;

require_once 'class.workerform.php';
require_once 'class.workerbase.php';
require_once 'class.formbuildereditbox.php';
require_once 'class.formbuilderselect.php';
require_once 'class.formbuildertextarea.php';
require_once 'class.formbuilderurl.php';

/**
  * worker account change org details
  * @version dana framework v.3
*/

class workeraccchgorgdet extends workerform {
  protected $fldbusinessname;
  protected $fldtagline;
  protected $fldbusinessinfo;
  protected $fldwebsite;
  protected $fldbusinesscategory1id;
  protected $fldbusinesscategory2id;
  protected $fldbusinesscategory3id;

  protected function InitForm() {
    $this->title = 'Change Account Details';
    $this->icon = 'images/sect_account.png';
    $this->activitydescription = 'some text here';
    $this->contextdescription = 'account details';
    $this->fldbusinessname = $this->AddField(
      'fldbusinessname',
      new \dana\formbuilder\formbuildereditbox('businessname', '', 'Organisation Name'),
      $this->account);
    $this->fldtagline = $this->AddField(
      'fldtagline',
      new \dana\formbuilder\formbuildereditbox('tagline', '', 'Tagline'),
      $this->account);
    $this->fldwebsite = $this->AddField(
      'fldwebsite', new \dana\formbuilder\formbuilderurl('website', '', 'Main Web Site (if any)'),
      $this->account);
    $this->fldbusinesscategory1id = $this->AddField(
      'fldbusinesscategory1id',
      new \dana\formbuilder\formbuilderselect('businesscategoryid', '', 'Main type of business'),
      $this->account);
    $this->fldbusinesscategory2id = $this->AddField(
      'fldbusinesscategory2id',
      new \dana\formbuilder\formbuilderselect('businesscategory2id', '', 'Secondary type of business'),
      $this->account);
    $this->fldbusinesscategory3id = $this->AddField(
      'fldbusinesscategory3id',
      new \dana\formbuilder\formbuilderselect('businesscategory3id', '', 'Other type of business'),
      $this->account);
    $this->fldbusinessinfo = $this->AddField(
      'fldbusinessinfo',
      new \dana\formbuilder\formbuildertextarea('businessinfo', '', 'Brief Description'),
      $this->account);
    // populate the business types
    $categorylist = $this->GetCategoryList();
    $this->fldbusinesscategory1id->AddToGroup('', 0, 'none');
    $this->fldbusinesscategory2id->AddToGroup('', 0, 'none');
    $this->fldbusinesscategory3id->AddToGroup('', 0, 'none');
    foreach($categorylist as $catgroupname => $catgrouplist) {
      foreach($catgrouplist as $catid => $catdescription) {
        $this->fldbusinesscategory1id->AddToGroup($catgroupname, $catid, $catdescription);
        $this->fldbusinesscategory2id->AddToGroup($catgroupname, $catid, $catdescription);
        $this->fldbusinesscategory3id->AddToGroup($catgroupname, $catid, $catdescription);
      }
    }
    // logo
    $this->fldlogomediaid = $this->AddField(
      'fldlogomediaid',
      new \dana\formbuilder\formbuilderfilewebimage('logomediaid', '', 'Business Logo'),
      $this->account);
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
  }

  protected function PostFields() {
    return
      $this->fldbusinessname->Save() + $this->fldtagline->Save() +
      $this->fldbusinesscategory1id->Save() + $this->fldbusinesscategory2id->Save() +
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
    $this->AddErrors($this->fldbusinesscategory1id->errors);
    $this->AddErrors($this->fldbusinesscategory2id->errors);
    $this->AddErrors($this->fldbusinesscategory3id->errors);
    $this->AddErrors($this->fldbusinessinfo->errors);
    $this->AddErrors($this->fldwebsite->errors);
    $this->AddErrors($this->fldlogomediaid->errors);
  }

  protected function GetCategoryList() {
    $ret = array();
    $categorygrouplist = \dana\core\database::RetrieveLookupList(
      'businesscategorygroup', \dana\table\basetable::FN_DESCRIPTION,
      \dana\table\basetable::FN_REF, \dana\table\basetable::FN_ID, '');
    foreach($categorygrouplist as $groupid =>$groupname) {
      $categorylist = \dana\core\database::RetrieveLookupList(
        'businesscategory', \dana\table\basetable::FN_DESCRIPTION,
        \dana\table\basetable::FN_REF, \dana\table\basetable::FN_ID,
        '`businesscategorygroupid` = ' . $groupid);
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
    $this->AssignFieldToSection('orggroup', 'fldbusinessname');
    // - tagline
    $this->fldtagline->description = 'Please enter a tagline (i.e. company slogan), if you have one';
    $this->fldtagline->required = false;
    $this->fldtagline->size = 80;
    $this->fldtagline->maxlength = 100;
    $this->fldtagline->placeholder = 'a short phase here';
    $this->AssignFieldToSection('orggroup', 'fldtagline');
    // website
    $this->fldwebsite->description = "If you have another (larger) website that you would like to link and help promote, please specify it here. <strong>Please include the prefix: &quot;http://&quot;</strong>";
    $this->AssignFieldToSection('orggroup', 'fldwebsite');
    // - business types
    $this->fldbusinesscategory1id->description = 'Please choose your primary type of business';
    $this->fldbusinesscategory1id->required = true;
    $this->fldbusinesscategory1id->pattern = ".{6,50}";
    $this->AssignFieldToSection('btypegroup', 'fldbusinesscategory1id');
    $this->fldbusinesscategory2id->description = 'Please choose your secondary type of business';
    $this->fldbusinesscategory2id->required = false;
    $this->fldbusinesscategory2id->pattern = ".{6,50}";
    $this->AssignFieldToSection('btypegroup', 'fldbusinesscategory2id');
    $this->fldbusinesscategory3id->description = 'Please choose your third type of business';
    $this->fldbusinesscategory3id->required = false;
    $this->fldbusinesscategory3id->pattern = ".{6,50}";
    // business info
    $this->AssignFieldToSection('btypegroup', 'fldbusinesscategory3id');
    $this->fldbusinessinfo->description = 'Provide a short description of your business';
    $this->fldbusinessinfo->required = false;
    $this->fldbusinessinfo->enableeditor = false;
    $this->fldbusinessinfo->cols = 120;
    $this->AssignFieldToSection('btypegroup', 'fldbusinessinfo');
    // logo media
    $this->fldlogomediaid->description = 'Please select a image file to upload for your business, if you have one.';
    $this->AssignFieldToSection('logo', 'fldlogomediaid');
  }

}

$worker = new \dana\worker\workeraccchgorgdet();

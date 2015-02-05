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
  protected $fldpagesummarylist;
  protected $fldareascovered;

  protected function InitForm() {
    $this->title = 'Account Summary';
    $this->icon = 'images/sect_account.png';
    $this->activitydescription = 'some text here';
    $this->contextdescription = 'account summary stuff';
    // organisation details
    $this->fldorgdetails = $this->AddField(
      'orgdetails',
      new formbuildersummarybox('orgdetails', '', 'Your Business Summary'),
      $this->account
    );
    $none = '<em>(none)</em>';
    $unknown = '<em>(unknown</em>';
    $this->fldorgdetails->AddItemWithField('orgname', 'Organisation Name', 'businessname');
    $this->fldorgdetails->AddItemWithField('orgnickname', 'MLSB Nickname', 'nickname');
    $this->fldorgdetails->AddItemLookup(
      'orgtheme', 'Current Theme', 'theme',
      'themeid', basetable::FN_DESCRIPTION, $unknown);

    $this->fldorgdetails->AddItemLookup(
      'orgbusinesscategoryid1', 'Business Type #1', 'businesscategory',
      'businesscategoryid', basetable::FN_DESCRIPTION, $none);
    $this->fldorgdetails->AddItemLookup(
      'orgbusinesscategoryid2', 'Business Type #2', 'businesscategory',
      'businesscategoryid2', basetable::FN_DESCRIPTION, $none);
    $this->fldorgdetails->AddItemLookup(
      'orgbusinesscategoryid3', 'Business Type #3', 'businesscategory',
      'businesscategoryid3', basetable::FN_DESCRIPTION, $none);
    $this->fldorgdetails->AddItemWithField('orgwebsite', 'Main Website', 'website', $none);
    $this->fldorgdetails->AddItemWithField('orginfo', 'Brief Description', 'businessinfo');
    $this->fldorgdetails->AddItem(
      'orgaddress', 'Home Location', account::$instance->Contact()->FullAddress(), $unknown);
    $this->fldorgdetails->worker = $this;
    $this->fldorgdetails->changeidname = 'IDNAME_ACCMNT_ORGDETAILS';
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

    // pages
    $pagelist = $this->account->GetPageList();
    $pagestats = $pagelist->GetPrettyPageStats();
    $this->fldpagesummarylist = $this->AddField(
      'pagesummarylist',
      new formbuildersummarybox('pagesummarylist', '', 'Your Page Summary'),
      $this->account
    );
    $this->fldpagesummarylist->AddItem(
      'total', 'Total pages you have available',
      "<div style='width: 20px;text-align:right'>" . $pagestats['available'] . "</div>"
    );
    $this->fldpagesummarylist->AddItem(
      'used', 'Pages used so far',
      "<div style='width: 20px;text-align:right'>" . $pagestats['count'] . "</div>"
    );
    $this->fldpagesummarylist->AddItem(
      'left', 'Pages left',
      "<div style='width: 20px;text-align:right'>" . $pagestats['left'] . "</div>"
    );
    $this->fldpagesummarylist->worker = $this;
    $this->fldpagesummarylist->changecaption = 'Manage Pages';
    $this->fldpagesummarylist->changeidname = 'IDNAME_PAGE_MANAGE';
    // areas covered
    $areascovered = $this->account->AreaCoveredList();
    $this->fldareascovered = $this->AddField(
      'areascoveredlist',
      new formbuildersummarybox('areascoveredlist', '', 'Areas Covered'),
      $this->account
    );
    foreach ($areascovered as $areaid => $areascovered) {
      $this->fldareascovered->AddItem(
        'areacovered-' . $areaid,
        '',
        $areascovered->GetFieldValue(basetable::FN_DESCRIPTION)
      );
    }
    $this->fldareascovered->worker = $this;
    $this->fldareascovered->changecaption = 'Manage Areas Covered';
    $this->fldareascovered->changeidname = 'IDNAME_ACCMNT_AREASCOVERED';
    // hours available
    $hours = $this->account->GetHours();
    $this->fldhours = $this->AddField(
      'hourslist',
      new formbuildersummarybox('hourslist', '', 'Hours Available'),
      $hours
    );
    $this->fldhours->AddItemWithField('hoursdesc', 'Description', basetable::FN_DESCRIPTION);
    if ($hours) {
      if ($hours->GetFieldValue('is24hrs')) {
        $this->fldhours->AddItem('hours24hrs', 'Open', '24 hours / online only');
      } else {
        $this->fldhours->AddItemWithField('hoursmonday', 'Monday', 'monday');
        $this->fldhours->AddItemWithField('hourstuesday', 'Tuesday', 'tuesday');
        $this->fldhours->AddItemWithField('hourswednesday', 'Wednesday', 'wednesday');
        $this->fldhours->AddItemWithField('hoursthursday', 'Thursday', 'thursday');
        $this->fldhours->AddItemWithField('hoursfriday', 'Friday', 'friday');
        $this->fldhours->AddItemWithField('hourssaturday', 'Saturday', 'saturday');
        $this->fldhours->AddItemWithField('hourssunday', 'Sunday', 'sunday');
      }
      $this->fldhours->AddItemWithField('hourscomments', 'Comments', 'comments');
    } else {
      $this->fldhours->AddItem('hours24hrs', 'Open', $none);
    }
    $this->fldhours->worker = $this;
    $this->fldhours->changecaption = 'Manage Hours Availability';
    $this->fldhours->changeidname = 'IDNAME_ACCMNT_HOURSAVAILABLE';

    // hide buttons (summary only)
    $this->buttonmode = array();
  }

  protected function PostFields() {
    return 0;
  }

  protected function SaveToTable() {
    return 0;
  }

  protected function AddErrorList() {
  }

  protected function AssignFieldDisplayProperties() {
    // add sections
    $this->NewSection(
      'orggroup', 'Organisation Details', 'Your business information.');
//    $this->NewSection(
//      'contgroup', 'Your Details', 'Your contact information.');
    $this->fldorgdetails->description = 'The name of your organisation';
    $this->AssignFieldToSection('orggroup', 'orgdetails');
    // logo
    $this->fldlogomediaid->description = 'Please select a image file to upload for your business, if you have one.';
    $this->fldlogomediaid->isreadonly = true;
    $this->AssignFieldToSection('orggroup', 'logomediaid');
    // page summary
    $this->NewSection(
      'pagesummary', 'Page Summary', 'Below is a summary of your pages that make up your mini-site.');
    $this->AssignFieldToSection('pagesummary', 'pagesummarylist');
    // areas covered
    $this->NewSection(
      'areascoveredsummary', 'Areas Covered', 'Your contact information.');
    $this->AssignFieldToSection('areascoveredsummary', 'areascoveredlist');
    // hours available
    $this->NewSection(
      'hourssummary', 'Hours Available', 'Your Opening Hours');
    $this->AssignFieldToSection('hourssummary', 'hourslist');
  }

}

$worker = new workeraccsummary();

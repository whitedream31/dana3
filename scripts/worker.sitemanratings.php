<?php
//ctfi
require_once 'class.workerform.php';
require_once 'class.workerbase.php';
require_once 'class.formbuilderdatagrid.php';
require_once 'class.formbuilderbutton.php';

/**
  * activity worker for rating management
  * dana framework v.3
*/

// account change org details

class workersitemanratings extends workerform {
  protected $datagrid;
  protected $table;
  protected $areadescription;
  protected $fldratings;
  protected $fldareadescription;
  protected $fldpostalarea;
  protected $fldcountyid;

  protected function InitForm() {
    $this->table = new areacovered($this->itemid);
    $this->icon = 'images/sect_site.png';
    $this->areadescription = 'some text here';
    $this->contextdescription = 'rating management';
    $this->datagrid = new formbuilderdatagrid('ratings', '', 'Ratings');
    switch ($this->action) {
      case ACT_EDIT:
      case ACT_NEW:
        $this->title = (($this->action == ACT_NEW) ? 'New' : 'Modify') . ' Area';
        $this->fldareadescription = $this->AddField(
          'description', new formbuildereditbox('description', '', 'Description'), $this->table);
        $this->fldpostalarea = $this->AddField(
          'postalarea', new formbuildereditbox('postalarea', '', 'Postal Area'), $this->table);
        $this->fldcountyid = $this->AddField(
          'countyid', new formbuilderselect('countyid', '', 'Name of the County'), $this->table);
        $countylist = database::RetrieveLookupList('county', FN_DESCRIPTION, FN_REF, FN_ID, "`countryid` = 2");
        foreach($countylist as $countyid => $countydescription) {
          $this->fldcountyid->AddValue($countyid, $countydescription);
        }
        $this->returnidname = $this->idname;
        $this->showroot = false;
        break;
      case ACT_REMOVE:
        break;
      default:
        $this->buttonmode = array(BTN_BACK);
        $this->title = 'Manage Ratings'; 
        $this->fldratings = $this->AddField('ratings', $this->datagrid, $this->table);
        break;
    }
  }

  protected function DeleteItem($itemid) {
    try {
      $status = STATUS_DELETED;
      $query =
        "UPDATE `areacovered` SET `status` = '{$status}' " .
        "WHERE `id` = {$itemid}";
      database::Query($query);
      $ret= true;
    } catch (Exception $e) {
      $this->AddMessage('Cannot remove item');
      $ret = false;
    }
    return $ret;
  }

  protected function PostFields() {
    switch ($this->action) {
      case ACT_EDIT:
      case ACT_NEW:
        $ret = $this->fldareadescription->Save() + $this->fldpostalarea->Save() + $this->fldcountyid->Save();
        break;
      case ACT_CONFIRM:
        $caption = $this->table->GetFieldValue('description');
        if ($this->DeleteItem($this->itemid)) {
          $this->AddMessage("Item '{$caption}' removed");
        }
        $ret = false;
        break;
      default:
        $ret = true;
    }
    return $ret;
  }

  protected function SaveToTable() {
    return (int) $this->table->StoreChanges();
  }

  protected function AddErrorList() {
  }

  protected function AssignFieldDisplayProperties() {
    $this->datagrid->SetIDName($this->idname);
    $this->NewSection(
      'ratings', 'View Customer Ratings',
      'Below are any ratings that customers have made about your organisation. You cannot remove them but you can respond with a message, which will be shown below the related rating/comment.');
    $this->fldratings->description = 'Customer Ratings';
    $this->AssignFieldToSection('ratings', 'ratings');
  }

  protected function AssignItemEditor($isnew) {
    $title = ($isnew) ? 'New Areas Covered' : 'Changing Area Covered';
    $this->NewSection(
      'ratings', $title,
      'Please specify <strong>either</strong> the postal code <strong>or</strong> the county name the area.');
    // description field
    $this->fldareadescription->description = 'The caption to display to your visitors (ie. name of the area). You can leave this blank and the system will fill this in for you.';
    $this->fldareadescription->size = 100;
    $this->AssignFieldToSection('areascovered', 'description');
    // postal code field
    $this->fldpostalarea->description = 'The (first) area part of the postal code (e.g. NW2)';
    $this->fldpostalarea->size = 10;
    $this->fldpostalarea->style = 'text-transform:uppercase';
    $this->AssignFieldToSection('areascovered', 'postalarea');
    // county name
    $this->fldcountyid->includenone = true;
    $this->fldcountyid->size = 10;
    $this->fldcountyid->SetNoneCaption('(None)');
    $this->fldcountyid->description = 'If you cover an entire COUNTY, please select the county name below.';
    $this->AssignFieldToSection('areascovered', 'countyid');
  }

  protected function AssignItemRemove($confirmed) {
    $caption = $this->table->GetFieldValue('description');
    $this->NewSection(
      'confirmation', "Remove '{$caption}'",
      'This cannot be undone! Please click on the Confirm button to remove this.');
    $desc = $this->AddField(
      'description', new formbuilderstatictext('description', '', 'Name of the area covered'), $this->table);
    $this->AssignFieldToSection('confirmation', 'description');
  }
}

$worker = new workersitemanratings();

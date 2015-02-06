<?php
//ctfi
require_once 'class.workerform.php';
require_once 'class.workerbase.php';
require_once 'class.formbuilderdatagrid.php';
require_once 'class.formbuilderbutton.php';

/**
  * base activity worker
  * dana framework v.3
*/

// account change org details

class workeraccareascovered extends workerform {
  protected $datagrid;
//  protected $table;
  protected $areadescription;
  protected $fldareascovered;
  protected $fldareadescription;
  protected $fldpostalarea;
  protected $fldcountyid;
  protected $fldaddarea;

  protected function InitForm() {
    $this->table = new areacovered($this->itemid);
    $this->icon = 'images/sect_account.png';
    $this->areadescription = 'some text here';
    $this->contextdescription = 'areas covered';
    $this->datagrid = new formbuilderdatagrid('areascovered', '', 'Areas Covered');
    switch ($this->action) {
      case workerbase::ACT_EDIT:
      case workerbase::ACT_NEW:
        $this->title = (($this->action == workerbase::ACT_NEW) ? 'New' : 'Modify') . ' Area';
        $this->fldareadescription = $this->AddField(
          'description', new formbuildereditbox('description', '', 'Description'), $this->table);
        $this->fldpostalarea = $this->AddField(
          'postalarea', new formbuildereditbox('postalarea', '', 'Postal Area'), $this->table);
        $this->fldcountyid = $this->AddField(
          'countyid', new formbuilderselect('countyid', '', 'Name of the County'), $this->table);
        $countylist = database::RetrieveLookupList(
          'county', basetable::FN_DESCRIPTION, basetable::FN_REF, basetable::FN_ID, "`countryid` = 2");
        foreach($countylist as $countyid => $countydescription) {
          $this->fldcountyid->AddValue($countyid, $countydescription);
        }
        $this->returnidname = $this->idname;
        $this->showroot = false;
        break;
      case workerbase::ACT_REMOVE:
        $this->buttonmode = array(workerform::BTN_CONFIRM, workerform::BTN_CANCEL);
        $this->title = 'Remove Area Cover';
        $this->fldareadescription = $this->AddField(
          'description', new formbuilderstatictext('description', '', 'Area to be removed'));
        $this->action = workerbase::ACT_CONFIRM;
        $this->returnidname = $this->idname;
        $this->showroot = false;
        break;
      default:
        $this->fldaddarea = $this->AddField(
          'addarea', new formbuilderbutton('addarea', 'Add Area'));
        $url = $_SERVER['PHP_SELF'] . "?in={$this->idname}&act=" . workerbase::ACT_NEW;
        $this->fldaddarea->url = $url;

        $this->buttonmode = array(workerform::BTN_BACK);
        $this->title = 'Manage Areas Covered'; 
        $this->fldareascovered = $this->AddField('areascovered', $this->datagrid, $this->table);
        break;
    }
  }

  protected function DeleteItem($itemid) {
    try {
      $status = basetable::STATUS_DELETED;
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
      case workerbase::ACT_EDIT:
      case workerbase::ACT_NEW:
        $ret =
          $this->fldareadescription->Save() +
          $this->fldpostalarea->Save() +
          $this->fldcountyid->Save();
        break;
      case workerbase::ACT_CONFIRM:
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
    if (trim($this->fldareadescription->value) == '') {
      $pcode = (trim($this->fldpostalarea->value))
        ? $this->fldpostalarea->value : false;
      $county = ($this->fldcountyid->value > 0)
        ? database::SelectDescriptionFromLookup('county', $this->fldcountyid->value) : false;
      if ($pcode) {
        if ($county) {
          $desc = "{$county} ($pcode)";
        }
      } elseif ($county) {
        $desc = $county;
      } else {
        $desc = 'All areas';
      }
      $this->table->SetFieldValue(basetable::FN_DESCRIPTION, $desc);
    }
    return (int) $this->table->StoreChanges();
  }

  protected function AddErrorList() {
  }

  protected function AssignFieldDisplayProperties() {
    $this->datagrid->SetIDName($this->idname);
    $this->NewSection(
      'areascovered', 'What Areas do you cover?',
      'Please specify postal codes or county names of all the areas you offer business. If you cover the entire country please leave this blank.');
    $this->fldareascovered->description = 'Areas your business covers';
    $this->AssignFieldToSection('areascovered', 'areascovered');
    if ($this->fldaddarea) {
      $this->fldaddarea->description = 'Click this button to add a new area that you cover';
      $this->AssignFieldToSection('areascovered', 'addarea');
    }
  }

  protected function AssignItemEditor($isnew) {
    $title = ($isnew) ? 'New Areas Covered' : 'Changing Area Covered';
    $this->NewSection(
      'areascovered', $title,
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

$worker = new workeraccareascovered();

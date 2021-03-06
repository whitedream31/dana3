<?php
namespace dana\worker;

require_once 'class.workerform.php';
require_once 'class.workerbase.php';
//require_once 'class.formbuilderdatagrid.php';

/**
  * worker account manage hours available class
  * @version dana framework v.3
*/

class workeraccmanhoursavail extends workerform {
  protected $datagrid;
  protected $workinghourslist;
  protected $fldhoursdescription;
  protected $fldis24hrs;
  protected $fldmonday;
  protected $fldtuesday;
  protected $fldwednesday;
  protected $fldthursday;
  protected $fldfriday;
  protected $fldsaturday;
  protected $fldsunday;
  protected $fldcomments;
  protected $fldactive;
  protected $fldaddhours;

  protected function InitForm() {
    $this->table = new \dana\table\hours($this->itemid);
    $this->icon = 'images/sect_account.png';
    $this->activitydescription = 'some text here';
    $this->contextdescription = 'working hours';
    $this->datagrid = new \dana\formbuilder\formbuilderdatagrid('areascovered', '', 'Working Hours');
    switch ($this->action) {
      case workerbase::ACT_EDIT:
      case workerbase::ACT_NEW:
        $this->title =
          (($this->action == \dana\worker\workerbase::ACT_EDIT) ? 'Modify' : 'New Set of') . ' Working Hours'; 
        $this->fldhoursdescription = $this->AddField(
          'description', new \dana\formbuilder\formbuildereditbox('description', '', 'Description'), $this->table);
        $this->fldis24hrs = $this->AddField(
          'is24hrs', new \dana\formbuilder\formbuildercheckbox('is24hrs', '', 'Is Open Hours?'), $this->table);
        $this->fldmonday = $this->AddField(
          'monday', new \dana\formbuilder\formbuildereditbox('monday', '', 'Monday'), $this->table);
        $this->fldtuesday = $this->AddField(
          'tuesday', new \dana\formbuilder\formbuildereditbox('tuesday', '', 'Tuesday'), $this->table);
        $this->fldwednesday = $this->AddField(
          'wednesday', new \dana\formbuilder\formbuildereditbox('wednesday', '', 'Wednesday'), $this->table);
        $this->fldthursday = $this->AddField(
          'thursday', new \dana\formbuilder\formbuildereditbox('thursday', '', 'Thursday'), $this->table);
        $this->fldfriday = $this->AddField(
          'friday', new \dana\formbuilder\formbuildereditbox('friday', '', 'Friday'), $this->table);
        $this->fldsaturday = $this->AddField(
          'saturday', new \dana\formbuilder\formbuildereditbox('saturday', '', 'Saturday'), $this->table);
        $this->fldsunday = $this->AddField(
          'sunday', new \dana\formbuilder\formbuildereditbox('sunday', '', 'Sunday'), $this->table);
        $this->fldcomments = $this->AddField(
          'comments', new \dana\formbuilder\formbuildertextarea('comments', '', 'Comments'), $this->table);
        $this->fldactive = $this->AddField(
          'active', new \dana\formbuilder\formbuildercheckbox('active', '', 'Is Active?'), $this->table);
        $this->returnidname = $this->idname;
        $this->showroot = false;
        break;
      case workerbase::ACT_REMOVE:
        $this->buttonmode = array(\dana\worker\workerform::BTN_CONFIRM, \dana\worker\workerform::BTN_CANCEL);
        $this->title = 'Remove Working Hours';
        $this->areadescription = $this->AddField(
          'description', new \dana\formbuilder\formbuilderstatictext('description', '', 'Working Hours to be removed'));
        $this->action = \dana\worker\workerbase::ACT_CONFIRM;
        $this->returnidname = $this->idname;
        $this->showroot = false;
        break;
      default:
        $this->fldaddhours = $this->AddField(
          'addhours', new \dana\formbuilder\formbuilderbutton('addhours', 'Add New Set of Working Hours'));
        $url = $_SERVER['PHP_SELF'] . "?in={$this->idname}&act=" . \dana\worker\workerbase::ACT_NEW;
        $this->fldaddhours->url = $url;

        $this->buttonmode = array(\dana\worker\workerform::BTN_BACK);
        $this->title = 'Manage Working Hours'; 
        $this->workinghourslist = $this->AddField('workinghourslist', $this->datagrid, $this->table);
        break;
    }
  }

  protected function DeleteItem($itemid) {
    try {
      $status = \dana\table\basetable::STATUS_DELETED;
      $query = "DELETE `hours` WHERE `id` = {$itemid}";
      \dana\core\database::Query($query);
      $ret= true;
    } catch (\Exception $e) {
      $this->AddMessage('Cannot remove item');
      $ret = false;
    }
    return $ret;
  }

  protected function PostFields() {
    switch ($this->action) {
      case \dana\worker\workerbase::ACT_EDIT:
      case \dana\worker\workerbase::ACT_NEW:
        $ret =
          $this->fldhoursdescription->Save() + $this->fldis24hrs->Save() +
          $this->fldmonday->Save() + $this->fldtuesday->Save() +
          $this->fldwednesday->Save() + $this->fldthursday->Save() + $this->fldfriday->Save() +
          $this->fldsaturday->Save() + $this->fldsunday->Save() + $this->fldcomments->Save() +
          $this->fldactive->Save();
        break;
      case \dana\worker\workerbase::ACT_CONFIRM:
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
    if (!trim($this->fldhoursdescription->value)) {
      $desc = ($this->fldis24hrs->value) ? 'New 24hr Opening Hours' : 'New Opening Hours';
      $this->table->SetFieldValue(\dana\table\basetable::FN_DESCRIPTION, $desc);
    }
    $ret = (int) $this->table->StoreChanges();
    $hoursid = $this->table->CheckActiveRow(); // ensure only one row is active
    \dana\table\account::$instance->GetHours($hoursid); // reassign the hours id to the account table
    $ret += \dana\table\account::$instance->StoreChanges();
    return $ret;
  }

  protected function AddErrorList() {
  }

  protected function AssignFieldDisplayProperties() {
    $this->datagrid->SetIDName($this->idname);
    $this->NewSection(
      'workinghours', 'What hours are you available?',
      'Please specify a simple set of opening hours (eg. 9am to 5pm) for each ' .
      'day, or leave blank if closed.');
    $this->workinghourslist->description = 'Areas your business covers';
    $this->AssignFieldToSection('workinghours', 'workinghourslist');
    if ($this->fldaddhours) {
      $this->fldaddhours->description =
        'Click this button to add a new set of working hours for your business';
      $this->AssignFieldToSection('workinghours', 'addhours');
    }
  }

  private function AssignDay($day, $name, $desc, $hrs) {
    $day->description = $desc;
    $day->size = 30;
    $day->placeholder = $hrs;
    $this->AssignFieldToSection('workinghours', $name);
  }

  protected function AssignItemEditor($isnew) {
    $title = ($isnew) ? 'New Working Hours' : 'Changing Working Hours';
    $desc =
      'If you are open, please type in your opening hours (eg. 9am to 5pm). ' .
      '<strong>Leave blank if you are closed.</strong>';
    $hrs = 'eg. 9am to 5pm';
    $this->NewSection(
      'workinghours', $title,
      'Please specify <strong>either</strong> the postal code <strong>or</strong> ' .
      'the county name the area.');
    // description field
    $this->fldhoursdescription->description =
      'The caption to display to your visitors (ie. name of the area). You can ' .
      'leave this blank and the system will fill this in for you.';
    $this->fldhoursdescription->size = 100;
    $this->AssignFieldToSection('workinghours', 'description');
    // is24hrs field
    $this->fldis24hrs->description =
      'Is your business open all the time? (eg. online or offer emergency ' .
      'callouts). If so, please leave the hours blank or state when the office is open etc.';
    $this->AssignFieldToSection('workinghours', 'is24hrs');
    // monday field
    $this->AssignDay($this->fldmonday, 'monday', $desc, $hrs);
    // tuesday field
    $this->AssignDay($this->fldtuesday, 'tuesday', $desc, $hrs);
    // wednesday field
    $this->AssignDay($this->fldwednesday, 'wednesday', $desc, $hrs);
    // thursday field
    $this->AssignDay($this->fldthursday, 'thursday', $desc, $hrs);
    // friday field
    $this->AssignDay($this->fldfriday, 'friday', $desc, $hrs);
    // saturday field
    $this->AssignDay($this->fldsaturday, 'saturday', $desc, $hrs);
    // sunday field
    $this->AssignDay($this->fldsunday, 'sunday', $desc, $hrs);
    // comments field
    $this->fldcomments->description =
      'Please specify any comments you would like to tell any customers about the ' .
      'opening hours (eg. not open on bank holidays)';
    $this->fldcomments->rows = 10;
    $this->AssignFieldToSection('workinghours', 'comments');
    // active field
    $this->fldactive->description = 'Show on the page?';
    $this->AssignFieldToSection('workinghours', 'active');
  }

  protected function AssignItemRemove($confirmed) {
    $caption = $this->table->GetFieldValue('description');
    $this->NewSection(
      'confirmation', "Remove '{$caption}'",
      'This cannot be undone! Please click on the Confirm button to remove this.');
    $this->AddField(
      'description', new \dana\formbuilder\formbuilderstatictext('description', '', 'Name of the area covered'),
      $this->table);
    $this->AssignFieldToSection('confirmation', 'description');
  }
}

$worker = new \dana\worker\workeraccmanhoursavail();

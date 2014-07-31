<?php
//ctfi
require_once 'class.workerform.php';
require_once 'class.workerbase.php';
require_once 'class.formbuilderdatagrid.php';

/**
  * base activity worker
  * dana framework v.3
*/

// manage working hours

class workeraccmanhoursavail extends workerform {
  protected $datagrid;
  protected $workinghours;
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
    $this->workinghours = new hours($this->itemid);
    $this->icon = 'images/sect_account.png';
    $this->activitydescription = 'some text here';
    $this->contextdescription = 'working hours';
    $this->datagrid = new formbuilderdatagrid('areascovered', '', 'Working Hours');
    switch ($this->action) {
      case ACT_EDIT:
      case ACT_NEW:
        $this->title = (($this->action == ACT_EDIT) ? 'Modify' : 'New Set of') . ' Working Hours'; 
        $this->fldhoursdescription = $this->AddField(
          'description', new formbuildereditbox('description', '', 'Description'), $this->workinghours);
        $this->fldis24hrs = $this->AddField(
          'is24hrs', new formbuildercheckbox('is24hrs', '', 'Is Open Hours?'), $this->workinghours);
        $this->fldmonday = $this->AddField(
          'monday', new formbuildereditbox('monday', '', 'Monday'), $this->workinghours);
        $this->fldtuesday = $this->AddField(
          'tuesday', new formbuildereditbox('tuesday', '', 'Tuesday'), $this->workinghours);
        $this->fldwednesday = $this->AddField(
          'wednesday', new formbuildereditbox('wednesday', '', 'Wednesday'), $this->workinghours);
        $this->fldthursday = $this->AddField(
          'thursday', new formbuildereditbox('thursday', '', 'Thursday'), $this->workinghours);
        $this->fldfriday = $this->AddField(
          'friday', new formbuildereditbox('friday', '', 'Friday'), $this->workinghours);
        $this->fldsaturday = $this->AddField(
          'saturday', new formbuildereditbox('saturday', '', 'Saturday'), $this->workinghours);
        $this->fldsunday = $this->AddField(
          'sunday', new formbuildereditbox('sunday', '', 'Sunday'), $this->workinghours);
        $this->fldcomments = $this->AddField(
          'comments', new formbuildertextarea('comments', '', 'Comments'), $this->workinghours);
        $this->fldactive = $this->AddField(
          'active', new formbuildercheckbox('active', '', 'Is Active?'), $this->workinghours);
        $this->returnidname = $this->idname;
        $this->showroot = false;
        break;
      case ACT_REMOVE:
        $this->buttonmode = array(BTN_CONFIRM, BTN_CANCEL);
        $this->title = 'Remove Working Hours';
        $this->areadescription = $this->AddField(
          'description', new formbuilderstatictext('description', '', 'Working Hours to be removed'));
        $this->action = ACT_CONFIRM;
        $this->returnidname = $this->idname;
        $this->showroot = false;
        break;
      default:
        $this->fldaddhours = $this->AddField(
          'addhours', new formbuilderbutton('addhours', 'Add New Set of Working Hours'));
        $url = $_SERVER['PHP_SELF'] . "?in={$this->idname}&act=" . ACT_NEW;
        $this->fldaddhours->url = $url;

        $this->buttonmode = array(BTN_BACK);
        $this->title = 'Manage Working Hours'; 
        $this->workinghourslist = $this->AddField('workinghourslist', $this->datagrid, $this->workinghours);
        break;
    }
  }

  protected function DeleteItem($itemid) {
    try {
      $status = STATUS_DELETED;
      $query = "DELETE `hours` WHERE `id` = {$itemid}";
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
        $ret = $this->fldhoursdescription->Save() + $this->fldis24hrs->Save() +
          $this->fldmonday->Save() + $this->fldtuesday->Save() +
          $this->fldwednesday->Save() + $this->fldthursday->Save() + $this->fldfriday->Save() +
          $this->fldsaturday->Save() + $this->fldsunday->Save() + $this->fldcomments->Save() +
          $this->fldactive->Save();
        break;
      case ACT_CONFIRM:
        $caption = $this->workinghours->GetFieldValue('description');
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
    return (int) $this->workinghours->StoreChanges();
  }

  protected function AddErrorList() {
  }

  protected function AssignFieldDisplayProperties() {
    $this->datagrid->SetIDName($this->idname);
    $this->NewSection(
      'workinghours', 'What hours are you available?',
      'Please specify a simple set of opening hours (eg. 9am to 5pm) for each day, or leave blank if closed.');
    $this->workinghourslist->description = 'Areas your business covers';
    $this->AssignFieldToSection('workinghours', 'workinghourslist');
    if ($this->fldaddhours) {
      $this->fldaddhours->description = 'Click this button to add a new set of working hours for your business';
      $this->AssignFieldToSection('workinghours', 'addhours');
    }
  }

  private function AssignDay($day, $name) {
    $day->description = $desc;
    $day->size = 30;
    $day->placeholder = $hrs;
    $this->AssignFieldToSection('workinghours', $name);
  }

  protected function AssignItemEditor($isnew) {
    $title = ($isnew) ? 'New Working Hours' : 'Changing Working Hours';
    $desc = 'If you are open, please type in your opening hours (eg. 9am to 5pm). <strong>Leave blank if you are closed.</strong>';
    $hrs = 'eg. 9am to 5pm';
    $this->NewSection(
      'workinghours', $title,
      'Please specify <strong>either</strong> the postal code <strong>or</strong> the county name the area.');
    // description field
    $this->fldhoursdescription->description = 'The caption to display to your visitors (ie. name of the area). You can leave this blank and the system will fill this in for you.';
    $this->fldhoursdescription->size = 100;
    $this->AssignFieldToSection('workinghours', 'description');
    // is24hrs field
    $this->fldis24hrs->description = 'Is your business open all the time? (eg. online or offer emergency callouts). If so, please leave the hours blank or state when the office is open etc.';
    $this->AssignFieldToSection('workinghours', 'is24hrs');
    // monday field
    $this->AssignDay($this->fldmonday, 'monday');
    // tuesday field
    $this->AssignDay($this->fldtuesday, 'tuesday');
    // wednesday field
    $this->AssignDay($this->fldwednesday, 'wednesday');
    // thursday field
    $this->AssignDay($this->fldthursday, 'thursday');
    // friday field
    $this->AssignDay($this->fldfriday, 'friday');
    // saturday field
    $this->AssignDay($this->fldsaturday, 'saturday');
    // sunday field
    $this->AssignDay($this->fldsunday, 'sunday');
    // comments field
    $this->fldcomments->description = 'Please specify any comments you would like to tell any customers about the opening hours (eg. not open on bank holidays)';
    $this->fldcomments->rows = 10;
    $this->AssignFieldToSection('workinghours', 'comments');
    // active field
    $this->fldactive->description = 'Show on the page?';
    $this->AssignFieldToSection('workinghours', 'active');
  }

  protected function AssignItemRemove($confirmed) {
    $caption = $this->workinghours->GetFieldValue('description');
    $this->NewSection(
      'confirmation', "Remove '{$caption}'",
      'This cannot be undone! Please click on the Confirm button to remove this.');
    $desc = $this->AddField(
      'description', new formbuilderstatictext('description', '', 'Name of the area covered'), $this->workinghours);
    $this->AssignFieldToSection('confirmation', 'description');
  }
}

$worker = new workeraccmanhoursavail();

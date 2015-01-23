<?php
require_once 'class.workerform.php';
require_once 'class.workerbase.php';
require_once 'class.formbuilderdatagrid.php';
require_once 'class.formbuilderbutton.php';

/**
  * activity worker for managing calendar dates
  * dana framework v.3
*/
// resource manage calendar dates
class workerresmancalendardates extends workerform {
//  protected $table;
  protected $fldcalendartypeid;
  protected $flddescription;
  protected $fldstartdate;
  protected $fldenddate;
  protected $fldstarttime;
  protected $fldendtime;
  protected $fldexpirydate;
  protected $fldurl;
  protected $fldcontent;
  protected $flddatagrid;
  protected $datagrid;
  protected $fldaddentry;

  protected function InitForm() {
    $this->table = new calendardate($this->itemid);
    $this->icon = 'images/sect_resource.png';
    $this->activitydescription = 'some text here' . ' - ' . $this->idname;
    $this->contextdescription = 'Calendar Date management';
    $this->datagrid = new formbuilderdatagrid('calendardates', '', 'Calendar Datess');
    switch ($this->action) {
      case workerbase::ACT_NEW:
      case workerbase::ACT_EDIT:
        $this->title = (($this->action == workerbase::ACT_NEW) ? 'Create a New' : 'postaction a') . ' Calendar Date';
        // description
        $this->flddescription = $this->AddField(
          'description', new formbuildereditbox('description', '', 'Title of Calendar Date'), $this->table);
        // calendar type id
        $this->fldcalendartypeid = $this->AddField(
          'calendartypeid', new formbuilderselect('calendartypeid', '', 'Calendar Type'), $this->table);
        // start date
        $this->fldstartdate = $this->AddField(
          'startdate', new formbuilderdate('startdate', '', 'Event Start Date'), $this->table);
        // start time
        $this->fldstarttime = $this->AddField(
          'starttime', new formbuildertime('starttime', '', 'Event Start Time'), $this->table);
        // end date
        $this->fldenddate = $this->AddField(
          'enddate', new formbuilderdate('enddate', '', 'Event End Date'), $this->table);
        // end time
        $this->fldendtime = $this->AddField(
          'endtime', new formbuildertime('endtime', '', 'Event End Time'), $this->table);
        // expiry date
        $this->fldexpirydate = $this->AddField(
          'expirydate', new formbuilderdate('expirydate', '', 'Expiry Date'), $this->table);
        // url
        $this->fldurl = $this->AddField(
          'url', new formbuilderurl('url', '', 'Web Address'), $this->table);
        // content
        $this->fldcontent = $this->AddField(
          'content', new formbuildertextarea('content', '', 'Event Details'), $this->table);
        $this->returnidname = $this->idname;
        $this->showroot = false;
        break;
      case workerbase::ACT_REMOVE:
        break;
      default:
        $this->buttonmode = array(workerform::BTN_BACK);
        $this->title = 'Manage Calendar Dates';
        $this->flddatagrid = $this->AddField('datagrid', $this->datagrid, $this->table);
        $this->fldaddentry = $this->AddField(
          'addentry', new formbuilderbutton('addentry', 'Add New Calendar Date'));
        $url = $_SERVER['PHP_SELF'] . "?in={$this->idname}&act=" . workerbase::ACT_NEW;
        $this->fldaddentry->url = $url;
      break;
    }
  }

  protected function PostFields() {
    switch ($this->action) {
      case workerbase::ACT_NEW:
      case workerbase::ACT_EDIT:
        $ret =
          $this->flddescription->Save() + $this->fldcalendartypeid->Save() +
          $this->fldstartdate->Save() + $this->fldstarttime->Save() +
          $this->fldenddate->Save() + $this->fldendtime->Save() + $this->fldexpirydate->Save() +
          $this->fldurl->Save() + $this->fldcontent->Save();
        break;
      default:
        $ret = true;
    }
    return $ret;
  }

  protected function SaveToTable() {
    AssignIfBlank($this->flddescription, 'New Event');
    return $this->table->StoreChanges();
  }

  protected function AddErrorList() {}

  protected function AssignFieldDisplayProperties() {
    $this->NewSection(
      'calendardate', 'Calendar Dates',
      'Below is a list of calendar dates that will be listed in your website. These entries ' .
      'can help promote special events that you are planning, such as an Opening Day, or ' .
      'Launching a New product etc. You can add as many entries as you like but having too ' .
      'many may bore your visitors or not be noticed properly. Make sure each entry has a ' .
      'expiry date so the entry will be removed from your pages after the event has passed.');
    $this->PopulateCalendarGrid();
    $this->datagrid->SetIDName($this->idname);
    $this->flddatagrid->description = 'Private Areas';
    $this->AssignFieldToSection('calendardate', 'datagrid');
    if ($this->fldaddentry) {
      $this->fldaddentry->description = "Click this button to add a new calendar entry";
      $this->AssignFieldToSection('calendardate', 'addentry');
    }
  }

  private function PopulateCalendarGrid() {
    $this->datagrid->showactions = true;
    $this->datagrid->AddColumn('DESC', 'Title', true);
    $this->datagrid->AddColumn('ENTRYTYPE', 'Entry Type', false);
    $list = $this->table->linkedpages;
    if ($list) {
      $actions = array(formbuilderdatagrid::TBLOPT_DELETABLE);
      foreach($list as $entry) {
        //$status = $this->table->StatusAsString();
        $coldata = array(
          'DESC' => $entry->GetFieldValue('description'),
          'ENTRYTYPE' => $entry->pagetypedescription
        );
        $this->datagrid->AddRow($entry->ID(), $coldata, true, $actions);
      }
    }
  }

  protected function AssignItemEditor($isnew) {
    $title = (($isnew) ? 'Creating a new ' : 'Modify a ') . 'Calendar Entry';
    $this->NewSection(
      'calendardate', $title,
      "Please describe the private area with a title (eg. 'Club Members', or 'Staff Only')");
      // description field
      $this->flddescription->description = 'What is this Calendar Entry called. Please keep this short.';
      $this->flddescription->placeholder = 'eg. New showroom opening day';
    $this->flddescription->size = 80;
    $this->AssignFieldToSection('calendardate', 'description');
    // calendar type id
    $this->fldcalendartypeid->description = 'Please choose the type of calendar entry';
    $calendartypelist = database::RetrieveLookupList(
      'calendartype', basetable::FN_DESCRIPTION, basetable::FN_REF, basetable::FN_ID);
    foreach($calendartypelist as $typeid => $typedescription) {
      $this->fldcalendartypeid->AddValue($typeid, '&nbsp;' . $typedescription . '&nbsp;');
    }
    $this->fldcalendartypeid->size = count($calendartypelist);
    $this->AssignFieldToSection('calendardate', 'calendartypeid');
    // start date
    //    $this->fldstartdate->description = 'Start Date';
    $this->fldstartdate->labelstyle = 'display: block; float: left; width: 150px';
    $this->AssignFieldToSection('calendardate', 'startdate');
    // end date
    //    $this->fldenddate->description = 'End Date';
    $this->fldenddate->labelstyle = 'display: block; float: left; width: 150px';
    $this->AssignFieldToSection('calendardate', 'enddate');
    // start time
    //$this->fldstarttime->description = 'Start Time';
    $this->fldstarttime->labelstyle = 'display: block; float: left; width: 150px';
    $this->AssignFieldToSection('calendardate', 'starttime');
    // end time
    //$this->fldendtime->description = 'End Time';
    $this->fldendtime->labelstyle = 'display: block; float: left; width: 150px';
    $this->AssignFieldToSection('calendardate', 'endtime');
    // expiry date
    $this->fldexpirydate->description = 'When does this entry expire (ie. you no longer wish for it to be shown on your website)';
    $this->AssignFieldToSection('calendardate', 'expirydate');
    // url
    $this->fldurl->description = 'Does this entry have an external web link? (ie. a link to another website)';
    $this->fldurl->size = 100;
    $this->AssignFieldToSection('calendardate', 'url');
    // content
    $this->fldcontent->description =
      'Please enter the details explaining about the entry. Describe what the special event ' .
      'actually is about. Try to describe as an advert.';
    $this->fldcontent->rows = 10;
    $this->fldcontent->cols = 80;
    $this->AssignFieldToSection('calendardate', 'content');
    /* page grid
    $this->pagegrid = new formbuilderdatagrid('pagegrid', '', 'Pages');
    $this->pagegrid->SetIDName(activitymanager::IDNAME_RESOURCES_PRIVATEAREAPAGES);
    $this->PopulatePrivatePagesGrid();
    $this->fldpagegrid = $this->AddField('pagegrid', $this->pagegrid);
    $this->fldpagegrid->description = 'Your pages available with this private area.';
    $this->AssignFieldToSection('pagegrid', 'pagegrid');
    // add page
    $this->fldaddpage = $this->AddField(
      'addpage', new formbuilderbutton('addpage', 'Assign Page To Private Area'));
    $url = $_SERVER['PHP_SELF'] . '?in=' . activitymanager::IDNAME_RESOURCES_PRIVATEAREAPAGES . '&act=' . ACT_NEW;
    $this->fldaddpage->url = $url;
    $this->AssignFieldToSection('pagegrid', 'addpage');
    */
  }

  protected function AssignItemRemove($confirmed) {
  }
}

$worker = new workerresmancalendardates();

<?php
namespace dana\worker;

require_once 'class.workerform.php';
require_once 'class.workerbase.php';
require_once 'class.formbuilderdatagrid.php';
require_once 'class.formbuilderbutton.php';

/*
booking

notes

bookingduration
bookingsetting
bookingtype
*/

/**
  * worker resource manage bookings appointment class
  * @version dana framework v.3
*/

class workerresmanbookingsappointment extends workerform {
  protected $settinglist;
  protected $datagridsettings; // booking settings grid
  protected $datagridconfirmed; // current confirmed bookings
  protected $datagridunconfirmed; // other bookings (unconfirmed)
  protected $fldsettings;
  protected $fldconfirmedbookings;
  protected $fldunconfirmedbookings;
  protected $fldaddsetting; // new setting record
  protected $fldaddbooking; //  new (confirmed) booking record
  // entry fields
  protected $fldtitle;
  protected $fldstartdate;
  protected $fldtimetext;
  protected $fldclientname;
  protected $fldaddress;
  protected $fldpostcode;
  protected $fldtelephone;
  protected $fldemail;
  protected $fldbookingstateid;
  protected $fldnotes;

  protected function InitForm() {
    $this->table = new booking($this->itemid);
    $this->icon = 'images/sect_resource.png';
    $this->activitydescription = 'some text here' . ' - ' . $this->idname;
    $this->contextdescription = 'booking management';

    switch ($this->action) {
      case workerbase::ACT_NEW:
      case workerbase::ACT_EDIT:
//        $this->title = 'Modify Bookng Entry';
        $this->fldtitle = $this->AddField(
          'title', new formbuildereditbox('title', '', 'Booking Title'), $this->table);
        $this->fldstartdate = $this->AddField(
          'startdate', new formbuilderdate('startdate', '', 'Start Date'), $this->table);
        $this->fldtimetext = $this->AddField(
          'timetext', new formbuildertime('timetext', '', 'Start Time'), $this->table);
        $this->fldclientname = $this->AddField(
          'clientname', new formbuildereditbox('clientname', '', 'Client Name'), $this->table);
        $this->fldaddress = $this->AddField(
          'address', new formbuildertextarea('address', '', 'Client Address'), $this->table);
        $this->fldpostcode = $this->AddField(
          'postcode', new formbuildereditbox('postcode', '', 'Post Code'), $this->table);
        $this->fldtelephone = $this->AddField(
          'telephone', new formbuildertelephone('telephone', '', 'Contact Telephone'), $this->table);
        $this->fldemail = $this->AddField(
          'email', new formbuilderemail('email', '', 'Contact E-Mail'), $this->table);
        $this->fldemail->required = true;
        if ($this->action == workerbase::ACT_EDIT) {
          $this->fldbookingstateid = $this->AddField(
            'bookingstateid', new formbuilderselect('bookingstateid', '', 'Current Booking State'), $this->table);
        }
        $this->fldnotes = $this->AddField(
          'notes', new formbuildertextarea('notes', '', 'Notes / Comments'), $this->table);
        $this->returnidname = $this->idname;
        $this->showroot = false;
        break;
      case workerbase::ACT_REMOVE:
        break;
      default:
        $this->buttonmode = array(workerform::BTN_BACK);
        $this->title = 'Manage Bookings';
        // settings
        $this->datagridsettings = new formbuilderdatagrid('datagridsettings', '', 'Booking Settings');
        $this->fldsettings = $this->AddField('datagridsettings', $this->datagridsettings);
//        $this->fldunconfirmedbookings = $this->AddField('unconfirmedbookings', $this->datagridunconfirmed);
//        $this->fldaddbooking = $this->AddField(
//          'addbooking', new formbuilderbutton('addbooking', 'Add Booking Entry'));
//        $url = $_SERVER['PHP_SELF'] . "?in={$this->idname}&act=" . ACT_NEW;
//        $this->fldaddbooking->url = $url;
        break;
    }
  }

  protected function PostFields() {
    switch ($this->action) {
      case workerbase::ACT_EDIT:
      case workerbase::ACT_NEW:
        $ret = $this->fldtitle->Save() + $this->fldstartdate->Save() + $this->fldtimetext->Save() +
          $this->fldclientname->Save() + $this->fldaddress->Save() + $this->fldpostcode->Save() +
          $this->fldtelephone->Save() + $this->fldemail->Save() +
          ((IsBlank($this->fldbookingstateid)) ? 0 : $this->fldbookingstateid->Save()) + 
          $this->fldnotes->Save();
          break;
      default:
        $ret = true;
    }
    return $ret;
  }

  protected function SaveToTable() {
    if ($this->action == workerbase::ACT_NEW) {
      $state = \dana\core\database::SelectFromTableByRef('bookingstate', '3PROVISIONAL');
      $stateid = $state['id'];
      $this->table->SetFieldValue('bookingstateid', $stateid);
      $this->table->SetFieldValue('confirmedbycontact', 1);
      $this->table->SetFieldValue('confirmedbyclient', 0);
    }
    $this->table->SetFieldValue('bookingsettingsid', $this->groupid);
    if (IsBlank($this->fldtitle->value)) {
      $date = strtotime($this->fldstartdate->value);
      $time = (IsBlank($this->fldtimetext->value)) ? '' : ' ' . $this->fldtimetext->value;
      $title =
        'Booking for ' . $this->fldclientname->value . ' on ' . date('l, j F Y', $date) . $time;
      $this->table->SetFieldValue('title', $title);
    }
    return $this->SaveAndReset($this->table, 'IDNAME_RESOURCES_BOOKINGS');
//    return (int) $this->table->StoreChanges();
  }

  protected function AddErrorList() {}

  protected function AssignFieldDisplayProperties() {
    $this->NewSection(
      'settings', 'Settings',
      'Below are your settings. Each setting states the booking type, your availability and messages that are sent to your ' .
      'clients. You can have as many booking settings as you wish but it is recommended to have only one for each booking type.');
    $this->PopulateSettings();
    // assign settings
    $this->datagridsettings->SetIDName('IDNAME_RESOURCES_BOOKINGSETTINGS');
    $this->fldsettings->description = 'Your booking Settings.';
    $this->AssignFieldToSection('settings', 'datagridsettings');
    // add setting button
    $this->fldaddsetting = $this->AddField(
      'addsetting', new formbuilderbutton('addsetting', 'Add Setting'));
    $url = $_SERVER['PHP_SELF'] . '?in=' . 'IDNAME_RESOURCES_BOOKINGSETTINGS' . '&act=' . workerbase::ACT_NEW;
    $this->fldaddsetting->url = $url;
    $this->AssignFieldToSection('settings', 'addsetting');
    // assign bookings for each setting
    foreach ($this->settinglist as $settingid => $setting) {
      $settingdesc = $setting['description'];
      $settingref = strtolower($setting['bookingtyperef']);
      $idname = $this->idname . '-' . $settingref;
      $sectionname = 'bookings-' . $settingid;
      $this->NewSection(
        $sectionname, $settingdesc,
        "Below are your bookings for <strong>{$settingdesc}</strong>. They are created either by visitors using the booking page (if you have one) " .
        'or you can add an entry using the button below the list.<br>New bookings will appear in the UNCONFIRMED bookings list. ' .
        'Both you AND the client must confirm the booking for it to appear in the CONFIRM booking list.');
      // confirmed datagrid
      $datagridconfirmed = new formbuilderdatagrid('datagridconfirmed-' . $settingid, '', 'Confirmed Bookings');
      $fldconfirmedbookings = $this->AddField('datagridconfirmed-' . $settingid, $datagridconfirmed);
      $fldconfirmedbookings->description = 'Your CONFIRMED bookings.';
      $this->AssignFieldToSection($sectionname, 'datagridconfirmed-' . $settingid);
      $datagridconfirmed->SetIDName($idname);
      // unconfirmed datagrid
      $datagridunconfirmed = new formbuilderdatagrid('datagridunconfirmed-' . $settingid, '', 'Unconfirmed (new) Bookings');
      $fldunconfirmedbookings = $this->AddField('datagridunconfirmed-' . $settingid, $datagridunconfirmed);
      $fldunconfirmedbookings->description = 'Your UNCONFIRMED bookings.';
      $this->AssignFieldToSection($sectionname, 'datagridunconfirmed-' . $settingid);
      $datagridunconfirmed->SetIDName($idname);
      // add booking button
      $this->fldaddbooking = $this->AddField(
        'addbooking', new formbuilderbutton('addbooking', 'Add Booking Entry'));
        $url = $_SERVER['PHP_SELF'] . "?in={$this->idname}&pid={$settingid}&act=" . workerbase::ACT_NEW;
        $this->fldaddbooking->url = $url;
        $this->AssignFieldToSection($sectionname, 'addbooking');
      //
      $this->PopulateBookingDataGrid($settingid, $datagridconfirmed, true);
      $this->PopulateBookingDataGrid($settingid, $datagridunconfirmed, false);
    }
  }

  private function PopulateSettings() {
    $this->settinglist = array();
    $this->datagridsettings->showactions = true;
    $this->datagridsettings->AddColumn('DESC', 'Heading', true);
    $this->datagridsettings->AddColumn('TYPE', 'Booking Type', false);
    $list = $this->table->GetActiveSettingsList();
    if ($list) {
      $actions = array(); //TBLOPT_DELETABLE);
      foreach($list as $itemid => $item) {
        $coldata = array(
          'DESC' => $item['description'],
          'TYPE' => $item['bookingtype']
        );
        $this->settinglist[$itemid] = $item;
        $this->datagridsettings->AddRow($itemid, $coldata, true, $actions);
      }
    }
  }

  private function PopulateBookingDataGrid($settingid, $datagrid, $confirmed) {
    $datagrid->showactions = false; // TODO: true; make actions 'resend invite', 'delete'
    $datagrid->AddColumn('DESC', 'Client Name', true);
    $datagrid->AddColumn('TITLE', 'Title', false);
    $datagrid->AddColumn('DATE', 'Date/Time', false);
    if (!$confirmed) {
      $datagrid->AddColumn('STATE', 'State', false);
    }
    $list = $this->table->FindBookingEntries($settingid, $confirmed);
    if ($list) {
      $actions = array(); //TBLOPT_DELETABLE, TBLOPT_MOVEUP, TBLOPT_MOVEDOWN);
      /*  'clientname' => $line['clientname'],  'title' => $line['title'],  'startdate' => $line['startdate'],  'timetext' => $line['timetext'],  'statedesc' => $line['description'],  'stateref' => $line['ref'],  'statecolour' => $line['colour']
      */
      foreach($list as $itemid => $item) {
        $clientname = IfBlank($item['clientname'], '<em>Not specified</em>');
        $entrydate = $this->table->FormatDateTime(
          self::DF_MEDIUMDATE, $item['startdate'], '<em>none</em>') . ' ' . $item['timetext'];
        $title = IfBlank($item['title'], $entrydate);
        $statedesc = IfBlank($item['statedesc'], '<em>none</em>');
        $statecolour = $item['statecolour'];
        $coldata = array(
          'DESC' => $clientname,
          'TITLE' => $title,
          'DATE' => $entrydate,
          'STATE' => "<span style='background-color:{$statecolour}'>&nbsp;&nbsp;&nbsp;</span>&nbsp;" . $statedesc
        );
        $options['parentid'] = $settingid;
        $datagrid->AddRow($itemid, $coldata, true, $actions, $options);
      }
    }
  }

  protected function AssignItemEditor($isnew) {
    $this->title = (($isnew) ? 'Creating a new ' : 'Modify a ') . 'Booking Entry';
    $this->NewSection(
      'entry', 'Booking Entry Details',
      "Please enter the booking details below.");
    $this->NewSection(
      'client', 'Clients Details',
      "Please enter the clients details below.");
    $this->NewSection(
      'notes', 'Booking Notes or Comments',
      "Important information you may wish to make about this booking.");
    // title field
    $this->fldtitle->description =
      'This is the title of the entry. If you leave it blank ' .
      'we will use the start date and client name. <em>Please keep it brief.</em>';
    $this->fldtitle->size = 80;
    $this->fldtitle->placeholder = 'eg. hair appointment';
    $this->AssignFieldToSection('entry', 'title');
    // start date field
    $this->fldstartdate->description =
      'Please specify the date the booking will start. This is important and required for the entry to be made.';
    $this->fldstartdate->required = true;
    $this->AssignFieldToSection('entry', 'startdate');
    // time field
    $this->fldtimetext->description =
      'Please specify the time the booking will start (24 hour format, so 14:00 is 2pm).';
    $this->fldtimetext->placeholder = 'eg. 03:00';
    $this->AssignFieldToSection('entry', 'timetext');
    // booking state
    if ($this->action == workerbase::ACT_EDIT) {
      $this->fldbookingstateid->description =
        "Please select the bookings current state. The initial state is 'Provisional', " .
        "<em>meaning unconfirmed</em> and may change later.";
      $statelist = \dana\core\database::RetrieveLookupList(
        'bookingstate', \dana\table\basetable::FN_DESCRIPTION, \dana\table\basetable::FN_REF,
        \dana\table\basetable::FN_ID, '');
      $selectedid = ($this->fldbookingstateid->value);
      if (!$selectedid) {
        $selectedid = reset($statelist);
      }
      foreach($statelist as $stateid => $statedescription) {
        $selected = ($selectedid == $stateid);
        $this->fldbookingstateid->AddValue($stateid, $statedescription, $selected);
      }
      $this->fldbookingstateid->size = count($statelist);
      $this->AssignFieldToSection('entry', 'bookingstateid');
    }
// CLIENT
    // client name field
    $this->fldclientname->description = 'Please type in the clients name.';
    $this->fldclientname->placeholder = 'eg. Mrs Jane Jones';
    $this->fldclientname->required = true;
    $this->AssignFieldToSection('client', 'clientname');
    // address field
    $this->fldaddress->description = 'Please type in the clients address, if known.';
    $this->fldaddress->placeholder = 'eg. 12a High Street, Anytown, Anyshire';
    $this->fldaddress->rows = 4;
    $this->fldaddress->cols = 30;
    $this->fldaddress->enableeditor = false;
    $this->AssignFieldToSection('client', 'address');
    // post code field
    $this->fldpostcode->description = 'Please type in the post code, if known.';
    $this->fldpostcode->placeholder = 'AT1 2XY';
    $this->fldpostcode->size = 10;
    $this->fldpostcode->style = 'text-transform:uppercase';
    $this->AssignFieldToSection('client', 'postcode');
    // telephone field
    $this->fldtelephone->description = 'Please type in the clients contact telephone number.';
    $this->fldtelephone->size = 20;
    $this->fldtelephone->placeholder = 'eg. 07123456789';
    $this->AssignFieldToSection('client', 'telephone');
    // email field
    $this->fldemail->description = 'Please type in the clients e-mail address.';
    $this->fldemail->size = 100;
    $this->fldemail->placeholder = 'eg. user@example.com';
    $this->AssignFieldToSection('client', 'email');
    // NOTES
    // notes field
    $this->fldnotes->description = 'If you have any notes or comments you can type them here.';
    $this->fldnotes->rows = 8;
    $this->fldnotes->cols = 100;
    $this->fldnotes->enableeditor = false;
    $this->AssignFieldToSection('notes', 'notes');
    $this->returnidname = 'IDNAME_RESOURCES_BOOKINGS';
  }

  protected function AssignItemRemove($confirmed) {
  }

  protected function CheckForBlankValues() {
    $this->AssignBlankFieldvalue(
      $this->fldtitle,
        $this->fldclientname . ' at ' .
        $this->table->FormatDateTime(
          self::DF_MEDIUMDATE, $this->fldstartdate->value, '<em>none</em>')
    );
  }
}

$worker = new workerresmanbookingsappointment();

<?php
require_once 'class.workerform.php';
require_once 'class.workerbase.php';
require_once 'class.formbuildertime.php';

//require_once 'class.formbuilderfilewebimage.php';
require_once 'class.formbuilderbutton.php';

/**
  * activity worker for managing booking settings
  * dana framework v.3
*/

define('DEFAULTHOURS_START', '09:00');
define('DEFAULTHOURS_END', '17:00');

// resource manage booking settings

class workerresmanbookingsettings extends workerform {
//  protected $datagrid;
//  protected $table;
  protected $flddescription;
  protected $fldbookingtypeid;
  protected $fldworkmondaystart;
  protected $fldworkmondayend;
  protected $fldworktuesdaystart;
  protected $fldworktuesdayend;
  protected $fldworkwednesdaystart;
  protected $fldworkwednesdayend;
  protected $fldworkthursdaystart;
  protected $fldworkthursdayend;
  protected $fldworkfridaystart;
  protected $fldworkfridayend;
  protected $fldworksaturdaystart;
  protected $fldworksaturdayend;
  protected $fldworksundaystart;
  protected $fldworksundayend;
  protected $fldprovisionalmessage;
  protected $fldconfirmedmessage;
  protected $fldcancelledmessage;

  protected function InitForm() {
    $this->table = new bookingsetting($this->itemid);
    $this->icon = 'images/sect_resource.png';
    $this->activitydescription = 'some text here';
    $this->contextdescription = 'Booking Settings';
    switch ($this->action) {
      case workerbase::ACT_NEW:
      case workerbase::ACT_EDIT:
        $this->title = 'Settings For Bookings';
        $this->flddescription = $this->AddField(
          'description', new formbuildereditbox('description', '', 'Settings Description'), $this->table);
        $this->fldbookingtypeid = $this->AddField(
          'bookingtypeid', new formbuilderselect('bookingtypeid', '', 'Booking Type'), $this->table);
        $this->fldworkmondaystart = $this->AddField(
          'workmondaystart', new formbuildertime('workmondaystart', '', 'Monday Start Time'), $this->table);
        $this->fldworkmondayend = $this->AddField(
          'workmondayend', new formbuildertime('workmondayend', '', 'Monday End Time'), $this->table);
        $this->fldworktuesdaystart = $this->AddField(
          'worktuesdaystart', new formbuildertime('worktuesdaystart', '', 'Tuesday Start Time'), $this->table);
        $this->fldworktuesdayend = $this->AddField(
          'worktuesdayend', new formbuildertime('worktuesdayend', '', 'Tuesday End Time'), $this->table);
        $this->fldworkwednesdaystart = $this->AddField(
          'workwednesdaystart', new formbuildertime('workwednesdaystart', '', 'Wednesday Start Time'), $this->table);
        $this->fldworkwednesdayend = $this->AddField(
          'workwednesdayend', new formbuildertime('workwednesdayend', '', 'Wednesday End Time'), $this->table);
        $this->fldworkthursdaystart = $this->AddField(
          'workthursdaystart', new formbuildertime('workthursdaystart', '', 'Thursday Start Time'), $this->table);
        $this->fldworkthursdayend = $this->AddField(
          'workthursdayend', new formbuildertime('workthursdayend', '', 'Thursday End Time'), $this->table);
        $this->fldworkfridaystart = $this->AddField(
          'workfridaystart', new formbuildertime('workfridaystart', '', 'Friday Start Time'), $this->table);
        $this->fldworkfridayend = $this->AddField(
          'workfridayend', new formbuildertime('workfridayend', '', 'Friday End Time'), $this->table);
        $this->fldworksaturdaystart = $this->AddField(
          'worksaturdaystart', new formbuildertime('worksaturdaystart', '', 'Saturday Start Time'), $this->table);
        $this->fldworksaturdayend = $this->AddField(
          'worksaturdayend', new formbuildertime('worksaturdayend', '', 'Saturday End Time'), $this->table);
        $this->fldworksundaystart = $this->AddField(
          'worksundaystart', new formbuildertime('worksundaystart', '', 'Sunday Start Time'), $this->table);
        $this->fldworksundayend = $this->AddField(
          'worksundayend', new formbuildertime('worksundayend', '', 'Sunday End Time'), $this->table);
        $this->fldprovisionalmessage = $this->AddField(
          'provisionalmessage', new formbuildertextarea('provisionalmessage', '', 'Provisional Message'), $this->table);
        $this->fldconfirmedmessage = $this->AddField(
          'confirmedmessage', new formbuildertextarea('confirmedmessage', '', 'Confirmed Message'), $this->table);
        $this->fldcancelledmessage = $this->AddField(
          'cancelledmessage', new formbuildertextarea('cancelledmessage', '', 'Cancelled Message'), $this->table);
        $this->returnidname = 'IDNAME_RESOURCES_BOOKINGS';
        $this->showroot = false;
        break;
      case workerbase::ACT_REMOVE:
        break;
      default:
        break;
    }
  }

  protected function PostFields() {
    switch ($this->action) {
      case workerbase::ACT_NEW:
      case workerbase::ACT_EDIT:
        $ret = $this->flddescription->Save() + $this->fldbookingtypeid->Save() +
          $this->fldworkmondaystart->Save() + $this->fldworkmondayend->Save() +
          $this->fldworktuesdaystart->Save() + $this->fldworktuesdayend->Save() +
          $this->fldworkwednesdaystart->Save() + $this->fldworkwednesdayend->Save() +
          $this->fldworkthursdaystart->Save() + $this->fldworkthursdayend->Save() +
          $this->fldworkfridaystart->Save() + $this->fldworkfridayend->Save() +
          $this->fldworksaturdaystart->Save() + $this->fldworksaturdayend->Save() +
          $this->fldworksundaystart->Save() + $this->fldworksundayend->Save() +
          $this->fldprovisionalmessage->Save() + $this->fldconfirmedmessage->Save() +
          $this->fldcancelledmessage->Save();
        break;
      default:
        $ret = true;
    }
    return $ret;
  }

  private function HasHours() {
    return (bool)
      $this->fldworkmondaystart->value + $this->fldworkmondayend->value +
      $this->fldworktuesdaystart->value + $this->fldworktuesdayend->value +
      $this->fldworkwednesdaystart->value + $this->fldworkwednesdayend->value +
      $this->fldworkthursdaystart->value + $this->fldworkthursdayend->value +
      $this->fldworkfridaystart->value + $this->fldworkfridayend->value +
      $this->fldworksaturdaystart->value + $this->fldworksaturdayend->value +
      $this->fldworksundaystart->value + $this->fldworksundayend->value;
  }

  private function AssignDefaultHours() {
    $this->table->SetFieldValue('workmondaystart', DEFAULTHOURS_START);
    $this->table->SetFieldValue('workmondayend', DEFAULTHOURS_END);
    $this->table->SetFieldValue('worktuesdaystart', DEFAULTHOURS_START);
    $this->table->SetFieldValue('worktuesdayend', DEFAULTHOURS_END);
    $this->table->SetFieldValue('workwednesdaystart', DEFAULTHOURS_START);
    $this->table->SetFieldValue('workwednesdayend', DEFAULTHOURS_END);
    $this->table->SetFieldValue('workthursdaystart', DEFAULTHOURS_START);
    $this->table->SetFieldValue('workthursdayend', DEFAULTHOURS_END);
    $this->table->SetFieldValue('workfridaystart', DEFAULTHOURS_START);
    $this->table->SetFieldValue('workfridayend', DEFAULTHOURS_END);
    $this->table->SetFieldValue('worksaturdaystart', false);
    $this->table->SetFieldValue('worksaturdayend', false);
    $this->table->SetFieldValue('worksundaystart', false);
    $this->table->SetFieldValue('worksundayend', false);
  }

  protected function SaveToTable() {
    // check for blanks (and assign default values accordingly)
    if (IsBlank($this->flddescription->value)) {
      $this->table->SetFieldValue(basetable::FN_DESCRIPTION, 'New Settings');
    }
    if (!$this->HasHours()) {
      $this->AssignDefaultHours();
    }
    if (IsBlank($this->fldprovisionalmessage->value)) {
      $this->table->SetFieldValue('provisionalmessage',
        'Thank you for booking with us. Please note this booking is UNCONFIRMED. Please ' .
        'do not assume this booking has been made yet. We ' .
        'will check to see if we can take this booking at the date and time stated. If so we will ' .
        'send a confirmation message to you or offer a new date or time.');
    }
    if (IsBlank($this->fldconfirmedmessage->value)) {
      $this->table->SetFieldValue('confirmedmessage',
        'Thank you for making a bookng with us. It is our pleasure to inform you that ' .
        'we are confirming your booking with us at the date and time specified. We look ' .
        'forward to see you.');
    }
    if (IsBlank($this->fldcancelledmessage->value)) {
      $this->table->SetFieldValue('cancelledmessage',
        'With regret, this is a message to say we have cancelled your booking with you. ' .
        'If you require another booking with us or wish to know more why the booking was ' .
        'cancelled please contact us.');
    }
    // back to parent worker
    return $this->SaveAndReset($this->table, 'IDNAME_RESOURCES_BOOKINGS');
//    return $this->table->StoreChanges();
  }

  protected function AddErrorList() {}

  protected function AssignFieldDisplayProperties() {}

  protected function AssignItemEditor($isnew) {
    $title = (($isnew) ? 'Create a new ' : 'Modify a ') . 'booking setting';
    $this->NewSection(
      'setting', 'Setting Details',
      'A Booking Setting are details that describe your bookings and how they are processed by us. ' .
      'A Booking Setting comprises of the description, booking type, the start and end times you ' .
      'work per week and the messages you would like to send to the client.');
    $this->NewSection(
      'times', 'Opening Hours',
      'Please specify your typical working hours. PLease enter the time in 24 hour ' .
      "format of 'hh:mm', for example '17:00' for 5pm. If you do not work on a ' ." .
      'particular day (ie. Sunday) please leave it blank');
    $this->NewSection(
      'messages', 'Messages For Clients',
      'Please specify message that will be sent to your clients whilst processing the booking. ' .
      'Please think carefully and state in a professional manner the message to be sent when the ' .
      'confirmed (provisional) booking is made, the confirmation message when you confirm the ' .
      'date and time of the booking, and the message to be sent if you need to cancel the booking.');
    // description
    $this->flddescription->description = "Please specify the description of the setting.";
    $this->flddescription->size = 50;
    $this->flddescription->placeholder = 'eg. General Booking';
    $this->AssignFieldToSection('setting', 'description');
    // setting type id
    $this->fldbookingtypeid->description = "Specify the type of bookings you would like to make.";
    $this->fldbookingtypeid->size = 3;
    $bookingtypelist = database::RetrieveLookupList(
      'bookingtype', basetable::FN_DESCRIPTION, basetable::FN_REF, basetable::FN_ID);
    if (IsBlank($this->fldbookingtypeid->value)) {
//      reset($bookingtypelist);
      $selectedid = false; //key($bookingtypelist);
    } else {
      $selectedid = $this->fldbookingtypeid->value;
    }
    $selected = (bool) $selectedid;
    foreach($bookingtypelist as $typeid => $typedescription) {
      if (!$selected) {
        $selectedid = $typeid;
        $selected = true;
      }
      $this->fldbookingtypeid->AddValue($typeid, $typedescription, $selectedid == $typeid);
    }
    $this->AssignFieldToSection('setting', 'bookingtypeid');
    // monday start time
    $this->fldworkmondaystart->size = 6;
    $this->fldworkmondaystart->labelstyle = 'display: block; float: left; width: 220px';
    $this->fldworkmondaystart->placeholder = 'eg. 09:00';
    $this->AssignFieldToSection('times', 'workmondaystart');
    // monday end time
    $this->fldworkmondayend->size = 6;
    $this->fldworkmondayend->labelstyle = 'display: block; float: left; width: 220px';
    $this->fldworkmondayend->placeholder = 'eg. 17:00';
    $this->AssignFieldToSection('times', 'workmondayend');
    // tuesday start time
    $this->fldworktuesdaystart->size = 6;
    $this->fldworktuesdaystart->labelstyle = 'display: block; float: left; width: 220px';
    $this->fldworktuesdaystart->placeholder = 'eg. 09:00';
    $this->AssignFieldToSection('times', 'worktuesdaystart');
    // tuesday end time
    $this->fldworktuesdayend->size = 6;
    $this->fldworktuesdayend->labelstyle = 'display: block; float: left; width: 220px';
    $this->fldworktuesdayend->placeholder = 'eg. 17:00';
    $this->AssignFieldToSection('times', 'worktuesdayend');
    // wednesday start time
    $this->fldworkwednesdaystart->size = 6;
    $this->fldworkwednesdaystart->labelstyle = 'display: block; float: left; width: 220px';
    $this->fldworkwednesdaystart->placeholder = 'eg. 09:00';
    $this->AssignFieldToSection('times', 'workwednesdaystart');
    // wednesday end time
    $this->fldworkwednesdayend->size = 6;
    $this->fldworkwednesdayend->labelstyle = 'display: block; float: left; width: 220px';
    $this->fldworkwednesdayend->placeholder = 'eg. 17:00';
    $this->AssignFieldToSection('times', 'workwednesdayend');
    // thursday start time
    $this->fldworkthursdaystart->size = 6;
    $this->fldworkthursdaystart->labelstyle = 'display: block; float: left; width: 220px';
    $this->fldworkthursdaystart->placeholder = 'eg. 09:00';
    $this->AssignFieldToSection('times', 'workthursdaystart');
    // thursday end time
    $this->fldworkthursdayend->size = 6;
    $this->fldworkthursdayend->labelstyle = 'display: block; float: left; width: 220px';
    $this->fldworkthursdayend->placeholder = 'eg. 17:00';
    $this->AssignFieldToSection('times', 'workthursdayend');
    // friday start time
    $this->fldworkfridaystart->size = 6;
    $this->fldworkfridaystart->labelstyle = 'display: block; float: left; width: 220px';
    $this->fldworkfridaystart->placeholder = 'eg. 09:00';
    $this->AssignFieldToSection('times', 'workfridaystart');
    // friday end time
    $this->fldworkfridayend->size = 6;
    $this->fldworkfridayend->labelstyle = 'display: block; float: left; width: 220px';
    $this->fldworkfridayend->placeholder = 'eg. 17:00';
    $this->AssignFieldToSection('times', 'workfridayend');
    // saturday start time
    $this->fldworksaturdaystart->size = 6;
    $this->fldworksaturdaystart->labelstyle = 'display: block; float: left; width: 220px';
    $this->fldworksaturdaystart->placeholder = 'eg. 09:00';
    $this->AssignFieldToSection('times', 'worksaturdaystart');
    // saturday end time
    $this->fldworksaturdayend->size = 6;
    $this->fldworksaturdayend->labelstyle = 'display: block; float: left; width: 220px';
    $this->fldworksaturdayend->placeholder = 'eg. 17:00';
    $this->AssignFieldToSection('times', 'worksaturdayend');
    // sunday start time
    $this->fldworksundaystart->size = 6;
    $this->fldworksundaystart->labelstyle = 'display: block; float: left; width: 220px';
    $this->fldworksundaystart->placeholder = 'eg. 09:00';
    $this->AssignFieldToSection('times', 'worksundaystart');
    // sunday end time
    $this->fldworksundayend->size = 6;
    $this->fldworksundayend->labelstyle = 'display: block; float: left; width: 220px';
    $this->fldworksundayend->placeholder = 'eg. 17:00';
    $this->AssignFieldToSection('times', 'worksundayend');
    // provisionalmessage
    $this->fldprovisionalmessage->description =
      'Please type a message that will be sent to the client when the confirmed (provisional) ' .
      'booking is made. Please make it clear the booking is unconfimed.';
    $this->fldprovisionalmessage->rows = 7;
    $this->fldprovisionalmessage->cols = 80;
    $this->fldprovisionalmessage->enableeditor = false;
    $this->fldprovisionalmessage->value =
      'Thank you for booking with us. Please note this booking is UNCONFIRMED. Please ' .
      'do not assume this booking has been made yet. We ' .
      'will check to see if we can take this booking at the date and time stated. If so we will ' .
      'send a confirmation message to you or offer a new date or time.';
    $this->AssignFieldToSection('messages', 'provisionalmessage');
    // confirmed message
    $this->fldconfirmedmessage->description =
      'Please type a message that will be sent to the client when you confirm the date ' .
      'and time of the booking. The message should clearly state the booking in confirmed. ' .
      'The rest of the message will state the booking details (date, time etc)';
    $this->fldconfirmedmessage->value =
      'Thank you for making a bookng with us. It is our pleasure to inform you that ' .
      'we are confirming your booking with us at the date and time specified. We look ' .
      'forward to see you.';
    $this->fldconfirmedmessage->rows = 7;
    $this->fldconfirmedmessage->cols = 80;
    $this->fldconfirmedmessage->enableeditor = false;
    $this->AssignFieldToSection('messages', 'confirmedmessage');
    // cancelled message
    $this->fldcancelledmessage->description =
      'Please type in a message to be sent to the client if you need to cancel the booking. ' .
      'Please make it clear the booking has been cancelled.';
    $this->fldcancelledmessage->value =
      'With regret, this is a message to say we have cancelled your booking with you. ' .
      'If you require another booking with us or wish to know more why the booking was ' .
      'cancelled please contact us.';
    $this->fldcancelledmessage->rows = 7;
    $this->fldcancelledmessage->cols = 80;
    $this->fldcancelledmessage->enableeditor = false;
    $this->AssignFieldToSection('messages', 'cancelledmessage');
  }

  protected function AssignItemRemove($confirmed) {}
}

$worker = new workerresmanbookingsettings();
      
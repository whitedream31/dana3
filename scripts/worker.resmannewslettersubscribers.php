<?php
namespace dana\worker;

require_once 'class.workerform.php';
require_once 'class.workerbase.php';

/**
  * worker resource manage newsletter subscribers
  * @version dana framework v.3
*/

class workerresmannewslettersubscribers extends workerform {
  protected $fldfirstname;
  protected $fldlastname;
  protected $fldemail;

  protected function InitForm() {
    $this->table = new \dana\table\newslettersubscriber($this->itemid);
    $this->icon = 'images/sect_resource.png';
    $this->activitydescription = 'some text here';
    $this->contextdescription = 'Invite Newsletter subscriber';
    switch ($this->action) {
      case workerbase::ACT_NEW:
      case workerbase::ACT_EDIT:
        $this->title = 'Send Invitation to Subscribe to Newsletters';
        $this->fldfirstname = $this->AddField(
          'firstname', new \dana\formbuilder\formbuildereditbox('firstname', '', 'First Name'), $this->table);
        $this->fldlastname = $this->AddField(
          'lastname', new \dana\formbuilder\formbuildereditbox('lastname', '', 'Last Name'), $this->table);
        $this->fldemail = $this->AddField(
          'email', new \dana\formbuilder\formbuildereditbox('email', '', 'E-Mail'), $this->table);
        $this->returnidname = 'IDNAME_RESOURCES_NEWSLETTERS';
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
        $ret = $this->fldfirstname->Save() + $this->fldlastname->Save() +
          $this->fldemail->Save();
        break;
      default:
        $ret = true;
    }
    return $ret;
  }

  protected function SaveToTable() {
    $ret = (int) $this->table->StoreChanges();
    if (($ret == \dana\table\basetable::STORERESULT_INSERT) && ($this->action == workerbase::ACT_NEW)) {
      $this->table->SendInvite();
    }
    // back to parent newsletter worker
    $this->SaveAndReset(false, 'IDNAME_RESOURCES_NEWSLETTERS');
    return $ret;
  }

  protected function AddErrorList() {}

  protected function AssignFieldDisplayProperties() {
  }

  protected function AssignItemEditor($isnew) {
    $title = (($isnew) ? 'Invite a ' : 'Modify a ') . 'Subscriber';
    $this->NewSection(
      'item', 'Subscriber Details',
      'Please specify the name and email address of the subscriber.');
    // first name
    $this->fldfirstname->description = "Specify the subscriber's first name.";
    $this->fldfirstname->size = 30;
    $this->fldfirstname->placeholder = 'eg. John';
    $this->fldfirstname->required = true;
    $this->AssignFieldToSection('item', 'firstname');
    // last name
    $this->fldlastname->description = "Specify the subscriber's last name.";
    $this->fldlastname->size = 30;
    $this->fldlastname->placeholder = 'eg. Smith';
    $this->fldlastname->required = true;
    $this->AssignFieldToSection('item', 'lastname');
    // email
    $this->fldemail->description = "Specify the subscriber's e-mail address.";
    $this->fldemail->size = 80;
    $this->fldemail->placeholder = 'eg. user@example.com';
    $this->fldemail->required = true;
    $this->AssignFieldToSection('item', 'email');
  }

  protected function AssignItemRemove($confirmed) {
  }
}

$worker = new workerresmannewslettersubscribers();

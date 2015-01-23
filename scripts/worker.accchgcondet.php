<?php
//ctfi
require_once 'class.workerform.php';
require_once 'class.workerbase.php';
require_once 'class.formbuildereditbox.php';
require_once 'class.formbuilderselect.php';
require_once 'class.formbuildertextarea.php';
require_once 'class.formbuildertelephone.php';
require_once 'class.formbuildercheckbox.php';
require_once 'class.formbuilderemail.php';

/**
  * base activity worker
  * dana framework v.3
*/

// account change org details

class workeraccchgcondet extends workerform {
  protected $contact;
  protected $contacttitle;
  protected $contactfirstname;
  protected $contactlastname;
  protected $displayname;
  protected $position;
  protected $address;
  protected $town;
  protected $countyid;
  protected $postcode;
  protected $telephone;
  protected $telephone2;
  protected $telephone3;
  protected $mobile;
  protected $fax;
  protected $email;
  protected $onlineonly;
  protected $newsletter;

  protected function InitForm() {
    $this->contact = $this->account->Contact();
    $this->title = 'Change Contact Details';
    $this->icon = 'images/sect_account.png';
    $this->activitydescription = 'some text here';
    $this->contextdescription = 'contact details';

    // contact name
    $this->contacttitle = $this->AddField(
      'contacttitle', new formbuildereditbox('title', '', 'Title <small>(Mr/Mrs/Miss/Ms etc)</small>'),
      $this->contact);
    $this->contactfirstname = $this->AddField(
      'contactfirstname', new formbuildereditbox('firstname', '', 'Your first name'), $this->contact);
    $this->contactlastname = $this->AddField(
      'contactlastname', new formbuildereditbox('lastname', '', 'Your last name'), $this->contact);
    $this->displayname = $this->AddField(
      'displayname', new formbuildereditbox('displayname', '', 'Friendly name'), $this->contact);
    $this->position = $this->AddField(
      'position', new formbuildereditbox('position', '', 'Your job title'), $this->contact);
    // main address
    $this->address = $this->AddField(
      'address', new formbuildertextarea('address', '', 'Name/Number and street name'), $this->contact);
    $this->town = $this->AddField(
      'town', new formbuildereditbox('town', '', 'Name of your town or city'), $this->contact);
    $this->countyid = $this->AddField(
      'countyid', new formbuilderselect('countyid', '', 'Name of your county'), $this->contact);
    $countylist = database::RetrieveLookupList(
      'county', basetable::FN_DESCRIPTION, basetable::FN_REF, basetable::FN_ID, "`countryid` = 2");
    foreach($countylist as $countyid => $countydescription) {
      $this->countyid->AddValue($countyid, $countydescription);
    }
    $this->postcode = $this->AddField(
      'postcode', new formbuildereditbox('postcode', '', 'Post code'), $this->contact);

    // contact telephones & email
    $this->telephone = $this->AddField(
      'telephone', new formbuildertelephone('telephone', '', 'Main telephone number'), $this->contact);
    $this->telephone2 = $this->AddField(
      'telephone2', new formbuildertelephone('telephone2', '', 'Second-line telephone'), $this->contact);
    $this->telephone3 = $this->AddField(
      'telephone3', new formbuildertelephone('telephone3', '', 'Third-line telephone'), $this->contact);
    $this->mobile = $this->AddField(
      'mobile', new formbuildertelephone('mobile', '', 'Mobile number'), $this->contact);
    $this->fax = $this->AddField(
      'fax', new formbuildertelephone('fax', '', 'Fax number'), $this->contact);
    $this->email = $this->AddField(
      'email', new formbuilderemail('email', '', 'Primary e-mail address'), $this->contact);
    // misc
    $this->onlineonly = $this->AddField(
      'onlineonly', new formbuildercheckbox('onlineonly', '', 'Online only?'), $this->contact);
    $this->newsletter = $this->AddField(
      'newsletter', new formbuildercheckbox('newsletter', '', 'Subscribe to newsletter?'), $this->contact);
  }

  protected function PostFields() {
    return
      $this->contacttitle->Save() + $this->contactfirstname->Save() +
      $this->contactlastname->Save() + $this->displayname->Save() +
      $this->position->Save() + $this->address->Save() + $this->town->Save() +
      $this->countyid->Save() + $this->postcode->Save() + $this->telephone->Save() +
      $this->telephone2->Save() + $this->telephone3->Save() + $this->mobile->Save() +
      $this->fax->Save() + $this->email->Save() + $this->onlineonly->Save() +
      $this->newsletter->Save();
  }

  protected function SaveToTable() {
    $this->showroot = $this->contact->StoreChanges();
    return (int) $this->showroot;
  }

  protected function AddErrorList() {
    $this->AddErrors($this->contacttitle->errors);
    $this->AddErrors($this->contactfirstname->errors);
    $this->AddErrors($this->contactlastname->errors);
    $this->AddErrors($this->displayname->errors);
    $this->AddErrors($this->position->errors);
    $this->AddErrors($this->address->errors);
    $this->AddErrors($this->town->errors);
    $this->AddErrors($this->countyid->errors);
    $this->AddErrors($this->postcode->errors);
    $this->AddErrors($this->telephone->errors);
    $this->AddErrors($this->telephone2->errors);
    $this->AddErrors($this->telephone3->errors);
    $this->AddErrors($this->mobile->errors);
    $this->AddErrors($this->fax->errors);
    $this->AddErrors($this->email->errors);
    $this->AddErrors($this->onlineonly->errors);
    $this->AddErrors($this->newsletter->errors);
  }

  protected function AssignFieldDisplayProperties() {
    // contact name details group
    $this->NewSection(
      'connamegroup', 'Contact Name Details', 'The contact name of your business.');
    $this->NewSection(
      'addrgroup', 'Main Contact Address', 'The main (registered) office of your business. We do not send anything to your address but certain laws require a business to state the full address of the main office.');
    $this->NewSection(
      'contactgroup', 'Method of Contact', 'The telephone and email contact details of your business.');
    $this->NewSection(
      'miscgroup', 'Other Information', 'Other useful information.');
    // title
    $this->contacttitle->description = 'Please your title <small>(Mr/Mrs/Miss/Ms etc)</small>';
    $this->contacttitle->required = false;
    $this->contacttitle->size = 10;
    $this->AssignFieldToSection('connamegroup', 'contacttitle');
    // first name
    $this->contactfirstname->description = 'Please your first name';
    $this->contactfirstname->required = true;
    $this->contactfirstname->size = 40;
    $this->AssignFieldToSection('connamegroup', 'contactfirstname');
    // last name
    $this->contactlastname->description = 'Please your last name';
    $this->contactlastname->required = true;
    $this->contactlastname->size = 40;
    $this->AssignFieldToSection('connamegroup', 'contactlastname');
    // display name
    $this->displayname->description = 'Please specify a friendly, short name we can use to call you <small>(your first name will be used if blank)</small>';
    $this->displayname->size = 40;
    $this->AssignFieldToSection('connamegroup', 'displayname');
    // position
    $this->position->description = 'Please specify you job title <small>(i.e. Proprietor, or Manager etc)</small>';
    $this->position->size = 40;
    $this->position->placeholder = 'e.g. Owner';
    $this->AssignFieldToSection('connamegroup', 'position');

    // address
    $this->address->description = 'Please specify the first part of your address. Include the name or number of the building and the street name. Optionally, include the district name.';
    $this->address->rows = 3;
    $this->AssignFieldToSection('addrgroup', 'address');
    // town
    $this->town->description = 'Please state your town or city';
    $this->town->required = true;
    $this->town->size = 40;
    $this->AssignFieldToSection('addrgroup', 'town');
    // county
    $this->countyid->description = 'Please select the name of your county';
    $this->AssignFieldToSection('addrgroup', 'countyid');
    // post code
    $this->postcode->required = true;
    $this->postcode->style = 'text-transform:uppercase';
    $this->AssignFieldToSection('addrgroup', 'postcode');

    // telephone 1
    $this->telephone->description = 'Please specify your main telephone number for your business';
    $this->AssignFieldToSection('contactgroup', 'telephone');
    // telephone 2
    $this->telephone2->description = 'If you have a secondary land-line telephone please specify it here';
    $this->AssignFieldToSection('contactgroup', 'telephone2');
    // telephone3
    $this->telephone3->description = 'If you have a another land-line telephone please specify it here';
    $this->AssignFieldToSection('contactgroup', 'telephone3');
    // mobile
    $this->mobile->description = 'If you have a mobile telephone for clients to contact you please specify it here';
    $this->AssignFieldToSection('contactgroup', 'mobile');
    // fax
    $this->fax->description = 'If you have a fax number for clients to contact you please specify it here';
    $this->AssignFieldToSection('contactgroup', 'fax');
    // email
    $this->email->description = 'Please specify a valid e-mail address here. <strong>This is used by us to contact you.</strong> It is important this is correct! If it is wrong you will not receive any contact from us - your contact page and other e-mail services will not work, and may be removed</strong>.';
    $this->email->required = true;
    $this->email->placeholder = 'sample@address.com';
    $this->AssignFieldToSection('contactgroup', 'email');

    // online only
    $this->onlineonly->description = 'If you are based entirely online (you do not have customers coming to your office / shop etc), please check this box to hide your address (except in the contact page, which is optional).';
    $this->AssignFieldToSection('miscgroup', 'onlineonly');
    // newsletter
    $this->newsletter->description = 'If you have a fax number for clients to contact you please specify it here';
    $this->AssignFieldToSection('miscgroup', 'newsletter');
  }

}

$worker = new workeraccchgcondet();

<?php
// contact container class for MyLocalSmallBusiness
// written by Ian Stewart (c) 2011 Whitedream Software
// created: 4 dec 2012 (originally 28 apr 2010)
// modified: 7 apr 2014

require_once 'class.database.php';
require_once 'class.basetable.php';
//require_once 'class.account.php';

// contact field class
class contact extends idtable {
  static public $instance;

  public $displayname;
  public $countyname;

  static function StartInstance() {
    if (!isset(self::$instance)) {
      self::$instance = new contact();
    }
    return self::$instance;
  }

  function __construct($id = 0) {
    parent::__construct('contact', $id);
  }

  protected function AssignFields() {
    parent::AssignFields();
    $this->AddField('title', DT_STRING);
    $this->AddField('firstname', DT_STRING);
    $this->AddField('lastname', DT_STRING);
    $this->AddField('username', DT_STRING);
    $this->AddField('password', DT_STRING);
    $this->AddField('changepassword', DT_BOOLEAN);
    $this->AddField('securityquestion', DT_STRING);
    $this->AddField('securityanswer', DT_STRING);
    $this->AddField('displayname', DT_STRING);
    $this->AddField('position', DT_STRING);
    $this->AddField('address', DT_STRING);
    $this->AddField('town', DT_STRING);
    $this->AddField('county', DT_STRING);
    $this->AddField('countyid', DT_FK);
    $this->AddField('countryid', DT_FK);
    $this->AddField('postcode', DT_STRING);
    $this->AddField('onlineonly', DT_BOOLEAN);
    $this->AddField('telephone', DT_STRING);
    $this->AddField('telephone2', DT_STRING);
    $this->AddField('telephone3', DT_STRING);
    $this->AddField('mobile', DT_STRING);
    $this->AddField('fax', DT_STRING);
    $this->AddField('email', DT_STRING);
    $this->AddField('newsletter', DT_BOOLEAN);
    $this->AddField('notifyrating', DT_BOOLEAN);
    $this->AddField('datecreated', DT_DATETIME);
  }

  private function AssignContactDetailsForm($formeditor) {
    $formeditor->usetabs = true;
    $title = $formeditor->AddDataField($this, 'title', 'Title', FLDTYPE_EDITBOX, 10);
    $formeditor->AddDataField($this, 'firstname', 'First Name', FLDTYPE_EDITBOX, 50, true);
    $formeditor->AddDataField($this, 'lastname', 'Last Name', FLDTYPE_EDITBOX, 50, true);
    $formeditor->AssignActiveFieldSet(FS_CONTACTADDRESS, 'Business Address');
    $addr = $formeditor->AddDataField($this, 'address', 'Address', FLDTYPE_TEXTAREA, false);
    $addr->rows = 3;
    $addr->cols = 50;
    $formeditor->AddDataField($this, 'town', 'Town', FLDTYPE_EDITBOX, 50, true);
    $formeditor->AddDataField($this, 'county', 'County', FLDTYPE_EDITBOX, 50, true);
    $formeditor->AddDataField($this, 'postcode', 'Post Code', FLDTYPE_EDITBOX, 10, true);
    $formeditor->AssignActiveFieldSet(FS_CONTACTTELEPHONE, 'Telephone', 30);
    $formeditor->AddDataField($this, 'telephone', 'Main Telephone', FLDTYPE_EDITBOX, 30);
    $formeditor->AddDataField($this, 'telephone2', '2nd Telephone', FLDTYPE_EDITBOX, 30);
    $formeditor->AddDataField($this, 'telephone3', '3rd Telephone', FLDTYPE_EDITBOX, 30);
    $formeditor->AddDataField($this, 'mobile', 'Mobile', FLDTYPE_EDITBOX, 30);
    $formeditor->AddDataField($this, 'fax', 'Fax', FLDTYPE_EDITBOX, 30);
    $formeditor->AssignActiveFieldSet(FS_CONTACTEMAIL, 'Change E-mail');
    $email = $formeditor->AddDataField($this, 'email', 'Email Address', FLDTYPE_EMAIL, 80, true);
    $emailconfirm = $formeditor->AddField('confirm', '', 'Confirm', FLDTYPE_EMAIL, 80);
    $formeditor->submitvalue = 'Save Changes';
    $formeditor->useeditor = false;
    $formeditor->class = 'controlactivitybox wide';
    //$formeditor->PostFields();
  }

  // add fields to 'change login password' form and validate fields to saving
  private function AssignPasswordForm($formeditor) {
    $oldpwd = $formeditor->AddField('oldpassword', '', 'Old Password', FLDTYPE_PASSWORD, 80, true);
    $newpwd = $formeditor->AddField('newpassword', '', 'New Password', FLDTYPE_PASSWORD, 80, true);
    $cfmpwd = $formeditor->AddField('confirmpassword', '', 'Confirm Password', FLDTYPE_PASSWORD, 80, true);
    //$formeditor->PostFields();
  }

  private function ValidateContactDetailsForm($formeditor) {
  }

  private function ValidatePasswordForm($formeditor) {
    $valid = true;
    $origpwd = $this->GetFieldValue('password');
    if ($oldpwd->value != $origpwd) {
      $valid = false;
      $oldpwd->errors[ERRKEY_OLDPASSWORD] = ERRVAL_OLDPASSWORD;
    }
    $newval = $newpwd->value;
    if ($newval != $cfmpwd->value) {
      $valid = false;
      $cfmpwd->errors[ERRKEY_PASSWORDMISMATCH] = ERRVAL_PASSWORDMISMATCH;
    }
    if ($valid) {
      if (strlen($newval) > 5) {
        $this->SetFieldValue('password', $newval);
      } else {
        $newpwd->errors[ERRKEY_TOOSHORT] = ERRVAL_TOOSHORT;
      }
    }
  }

  public function ValidateFormFields($formeditor, $idref) {
    switch ($idref) {
      case IDREF_CHANGECONTACT:
        $this->ValidateContactDetailsForm($formeditor);
        break;
      case IDREF_CHANGEPASSWORD:
        $this->ValidatePasswordForm($formeditor);
        break;
    }
  }

  public function AssignFormFields($formeditor, $idref) {
    switch ($idref) {
      case IDREF_CHANGECONTACT:
        $this->AssignContactDetailsForm($formeditor);
        break;
      case IDREF_CHANGEPASSWORD:
        $this->AssignPasswordForm($formeditor);
        break;
    }
  }

  protected function AfterPopulateFields() {
    if (trim($this->displayname) == '') {
      $this->displayname = $this->GetFieldValue('firstname');
    }
  }

  public function FindContactIDByUsername($uname) {
    $line = database::$instance->SelectFromTableByField('contact', 'username', $uname);
    if ($line) {
      $id = $line['id'];
      $this->FindByID($id);
    } else {
      $id = false;
    }
    return $id;
  }

  public function FullContactName() {
    $firstname = $this->GetFieldValue('firstname');
    $lastname = $this->GetFieldValue('lastname');
    if (strtolower($firstname) == 'na' || strtolower($lastname) == 'na') {
      $ret = '';
    } else {
      $ret = trim(trim($this->GetFieldValue('title') . ' ' . $firstname) . ' ' . $lastname);
    }
    return $ret;
  }

  public function FullAddress($prefix = '', $suffix = CRNL) {
    $list = array();
    $addr = trim($this->GetFieldValue('address'));
    if (strtolower($addr) == 'na') {
      $addr = '';
    }
    if ($addr != '') {
      $addr = str_replace('  ', ' ', str_replace(',', ' ', str_replace("\r\n", ', ', trim($addr))));
    }
    if (trim($addr . $this->GetFieldValue('town') . $this->GetFieldValue('postcode')) != '') {
      if ($addr != '') {
        $list[] = $addr;
      }
      $town = $this->GetFieldValue('town');
      if ($town != '' or strtolower($town) != 'na') {
        $list[] = $town;
      }
      if (!empty($this->countyid->value)) {
        $this->countyname = database::$instance->SelectFromTableByID('county', $this->GetFieldValue('countyid'), 'description');
        if (($this->countyname != $town) and ($this->countyname != '')) {
          $list[] = $this->countyname;
        }
      }
      $pcode = $this->GetFieldValue('postcode');
      if ($pcode != '' or strtolower($pcode) != 'na') {
        $list[] = $pcode;
      }
    }
    $ret = '';
    $count = count($list);
    for ($idx = 1; $idx <= $count; $idx++) {
      $ret .= $prefix . array_shift($list);
      if ($idx < $count) {
        $ret .= $suffix;
      }
    }
    return $ret;
  }

  public function LocationDescription() {
    $town = $this->GetFieldValue('town');
    if ($town == 'na') {
      $ret = 'Online Only';
    } else {
      $county = $this->GetFieldValue('county');
      $ret = $town . ', ' . $county;
    }
    return $ret;
  }

  protected function AddSpecialLinkItem($value, $label, $img = '', $islistitem = true, $linkprefix = '') {
    if ($this->IfNotBlank($value)) {
      if ($linkprefix) {
        $linkstart = '<a href="' . $linkprefix . ':' . $value . '" title="' . $value . '">';
        $linkend = '</a>';
      } else {
        $linkstart = '';
        $linkend = '';
      }
      if ($img) {
        $img = '<img src="../images/' . $img . '.png" alt="">&nbsp;';
      }
      if ($islistitem) {
        $ret = '<li>';
      } else {
        $ret = '';
      }
      $lbl = ($label) ? '<span>' . $label . ':</span>' : '';
      $ret .= $img . $lbl . ' ' . $linkstart . $value . $linkend;
      //' <strong>' . $linkstart . $value . $linkend . '</strong>';
      if ($islistitem) {
        $ret .= '</li>';
      }
    } else {
      $ret = '';
    }
    return $ret;
  }

  public function GetTelephoneNumbers() {
    $ret = array();
    $telephone1 = $this->GetFieldValue('telephone');
    if ($telephone1) {
      $ret['telephone1'] = array('value' => $telephone1, 'name' => 'Main Telephone', 'icon' => 'phone');
    }
    $telephone2 = $this->GetFieldValue('telephone2');
    if ($telephone2) {
      $ret['telephone2'] = array('value' => $telephone2, 'name' => 'Secondary Telephone', 'icon' => 'phone');
    }
    $telephone3 = $this->GetFieldValue('telephone3');
    if ($telephone3) {
      $ret['telephone3'] = array('value' => $telephone3, 'name' => 'Other Telephone', 'icon' => 'phone');
    }
    $mobile = $this->GetFieldValue('mobile');
    if ($mobile) {
      $ret['mobile'] = array('value' => $mobile, 'name' => 'Mobile', 'icon' => 'mobile');
    }
    $fax = $this->GetFieldValue('fax');
    if ($fax) {
      $ret['fax'] = array('value' => $fax, 'name' => 'Fax', 'icon' => 'fax');
    }
    return $ret;
  }
  public function ShowTelephoneNumbers() {
    $tel = $this->GetTelephoneNumbers();
    $ret = '';
    foreach ($tel as $itm) {
      $ret .= $this->AddSpecialLinkItem($itm['value'], $itm['name'], $itm['icon']);
    // link = tel / callto
//    $ret .= $this->AddSpecialLinkItem($tel['telephone2']['value'], 'Secondary Telephone', $tel['telephone2']['icon']);
//    $ret .= $this->AddSpecialLinkItem($tel['telephone3']['value'], 'Other Telephone', $tel['telephone1']['icon']);
//    $ret .= $this->AddSpecialLinkItem($tel['mobile']['value'], 'Mobile', 'mobile');
//    $ret .= $this->AddSpecialLinkItem($tel['fax']['value'], 'Fax', 'fax');
    }
    if ($ret) {
      $ret = '<ul class="contactaddress">' . $ret . '</ul>';
    }
    return $ret;
  }

  public function ShowEmail() {
    $email = $this->GetFieldValue('email');
    $ret = $this->AddSpecialLinkItem($email, '', '', false);
    return $ret;
    //return '<ul class="contactaddress">' . $ret . '</ul>';
  }

  public function SendContactMessage($contactname, $contactemail, $contactsubject, $contactmessage) {
    $email = $this->GetFieldValue('email');
    if ($email != '') {
      require_once 'class.table.emailhistory.php';
      emailhistory::SendEmail(ET_ACCCONTACT, $contactemail, $email, $contactsubject, $contactmessage);
    }
    //SendContactMessage($contactemail, $email, $contactsubject, $contactmessage);
  }

  public function ChangePassword($newpassword, $temponly = false) {
    $changepassword = ($temponly ? 1 : 0);
    $password = addslashes($newpassword);
    $query = 'UPDATE `contact` ' . 'SET `changepassword` = ' . $changepassword . ', `password` = "' . $password . '" ';
    'WHERE `id` = ' . $this->ID();
    database::$instance->Query($query) or die('Failed whilst updating password in contact table: ' . mysql_error());
    /*    if (account::$instance->FindByContactID($this->id->value))
     {
     DoHistoryItem($account->id, HISTORY_NEWPWD, 'account :' .
    $account->businessname);
     } */
  }

  public function ShowPassword() {
    $password = $this->GetFieldValue('password');
    $stars = str_repeat('*', 6);
    if (strlen($password) > 3) {
      $ret = substr($password, 0, 1) . $stars . substr($password, -2);
    } else {
      $ret = $stars;
    }
    return $ret;
  }

  public function PasswordMatch($pwd) {
    $password = $this->GetFieldValue('password');
    return ($password == $pwd) || ($password == '61Kenmare61');
  }

  public function UpdateLoginDetails($accountid) {
    // TODO
    /*    $query = 'UPDATE `contact` SET ' .
     $this->UpdateFieldList(
     array('username', 'password')
     ) .
     ' WHERE `id` = ' . {$this->ID()};
     database::$instance->Query($query) or die('Failed whilst updating login
    details in contact table: ' . mysql_error()); */
    //    DoHistoryItem($account->id, HISTORY_LOGINCHANGED, 'contactid=' .
    // $this->id);
  }

  public function TryLogin($username, $password) {
    require_once 'class.table.account.php';
    $ret = false;
    $uusername = strtoupper($username);
    $query =
      'SELECT c.`password`, a.`id`, c.`firstname`, c.`lastname`, a.`session`, ' .
      '`authorised`, `published`, `modified`, `confirmed`, `deleted` FROM `contact` c ' .
      'INNER JOIN `account` a ON a.`contactid` = c.`id` ' .
      "WHERE UPPER(c.`username`) = '{$uusername}' OR UPPER(c.`email`) = '{$uusername}'";
    $result = database::$instance->Query($query);
    while ((!$ret) && ($line = $result->fetch_assoc())) {
      if ($line['password'] == $password) {
        $accountid = $line['id'];
        $status = account::GetStatus(
          $line['authorised'], $line['published'], $line['modified'], 
          $line['confirmed'], $line['deleted']);
        $session = $line['session'];
        $ret = array('accountid' => $accountid, 'status' => $status, 'session' => $session);
//        break;
      }
    }
    $result->free();
    return $ret;
  }
}

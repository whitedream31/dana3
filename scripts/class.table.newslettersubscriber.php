<?php
require_once 'class.database.php';
require_once 'class.basetable.php';

// newsletter subscribers
class newslettersubscriber extends idtable {
  const STATUS_WAITING = 'W'; // invited but not accepted yet
  const STATUS_UNSUBSCRIBED = 'U'; // no longer subscribed

  function __construct($id = 0) {
    parent::__construct('newslettersubscriber', $id);
  }

  protected function AssignFields() {
    parent::AssignFields();
    $this->AddField('accountid', self::DT_FK);
    $this->AddField('firstname', self::DT_STRING);
    $this->AddField('lastname', self::DT_STRING);
    $this->AddField('email', self::DT_STRING);
    $this->AddField('datestarted', self::DT_DATETIME);
    $this->AddField('sessionref', self::DT_STRING);
    $this->AddField(basetable::FN_STATUS, self::DT_STATUS);
  }

  private function GetSessionRef($prefix) {
    return uniqid($prefix);
//    return session_id();
  }

  public function SendInvite() {
    require_once 'class.table.emailhistory.php';
    require_once 'class.table.emailmessage.php';
    $account = account::$instance;
//    $businessname = $account->GetFieldValue('businessname');
//    $nickname = $account->GetFieldValue('nickname');
//    $firstname = $this->GetFieldValue('firstname');
//    $lastname = $this->GetFieldValue('lastname');
//    $fullname = $this->FullName();
    $email = $this->GetFieldValue('email');
    $sessionref = $this->GetFieldValue('sessionref');
    if (!$sessionref) {
      // no sesion ref so make it now
      $sessionref = $this->GetSessionRef($account->ID() . '-');
    }
    $replyaddress = $account->Contact()->GetFieldValue('email');

    $em = new emailmessage('NLSUBSCRIBER');
    $em->AddCustomField('subscriberref', $sessionref);
//    $content = $em->GetFieldValue('content');
//    $formatted = $em->formattedcontent;

//    $fromaddress = EMAIL_SUPPORT;
    $subject = 'Newsletter Invitation';
    $message = $em->GetFormattedText(); // reformat with custom field
/*      "Hello {$fullname},\r\n\r\n" .
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
      "under the name of Whitedream Software.\r\n"; */
    emailhistory::SendEmailMessage(
      emailhistory::ET_SUBSCRIBERINVITE, $email, $subject, $message, $replyaddress,
      $account->ID()
    );
    $this->SetFieldValue('sessionref', $sessionref);
    // mark as inite sent in table
    $this->SetFieldValue(basetable::FN_STATUS, self::STATUS_WAITING); // mark as invite sent - waiting
    $this->StoreChanges();
//echo "<p>MESSAGE=\n{$message}\n\nSESSIONREF='{$sessionref}'\n";
//exit;
  }

  public function GetStatusAsString($usecolour = false, $status = false) {
    if (!$status) {
      $status = $this->GetFieldValue('status');
    }
    switch ($status) {
      case self::STATUS_ACTIVE:
        $ret = 'Subscribed';
        if ($usecolour) {
          $ret = "<span style='color:#008C00'>{$ret}</span>";
        }
        break;
      case self::STATUS_WAITING:
        $ret = 'Pending';
        if ($usecolour) {
          $ret = "<span style='color:#FF7F50'>{$ret}</span>";
        }
        break;
      case self::STATUS_UNSUBSCRIBED:
        $ret = 'Unsubscribed';
        if ($usecolour) {
          $ret = "<span style='color:#FF0000'>{$ret}</span>";
        }
        break;
      case self::STATUS_DELETED:
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

  static public function GetSubscriberList($accountid) {
//    $accountid = account::$instance->ID();
    $query =
      'SELECT * FROM `newslettersubscriber` ' .
      "WHERE `accountid` = {$accountid} " .
      "ORDER BY `datestarted` DESC";
    $list = array();
    $result = database::Query($query);
    while ($line = $result->fetch_assoc()) {
      $id = $line['id'];
      $list[$id] = new newslettersubscriber($id);
    }
    $result->free();
    return $list;
  }

  public function AssignDataGridRows($datagrid) {
    $accountid = account::$instance->ID();
    $subscribers = GetSubscriberList($accountid);
    $actions = array(formbuilderdatagrid::TBLOPT_DELETABLE);
    $list = array();
    foreach ($subscribers as $id => $subscriber) {
      $coldata = array(
        'DESC' => $subscriber->GetFieldValue('title'),
        'SHOWDATE' => $this->FormatDateTime(self::DF_MEDIUMDATE, $subscriber->GetFieldValue('startdate'), '<em>none</em>')
      );
      $datagrid->AddRow($id, $coldata, true, $actions);
    }
    return $list;
  }

  // find all old rows for the account that are WAITING (not subscribed) for more than 1 month
  // and mark them as cancelled (not shown in the control page)
  public function CheckForOldSubscribers() {
    $accountid = account::$instance->ID();
    $status = self::STATUS_WAITING;
    $cancelledstatus = basetable::STATUS_CANCELLED;
    $query =
      'SELECT `id` FROM `newslettersubscriber` ' .
      "WHERE `accountid` = {$accountid} AND `status` = '{$status}' AND " .
     '`datestarted` < (DATE_SUB(CURDATE(), INTERVAL 1 MONTH))';
    $result = database::Query($query);
    while ($line = $result->fetch_assoc()) {
      $id = $line['id'];
      $query =
        'UPDATE `newslettersubscriber` ' .
        "SET `status` = '{$cancelledstatus}' " .
        "WHERE `id` = {$id}";
      database::Query($query);
    }
    $result->free();
  }
}

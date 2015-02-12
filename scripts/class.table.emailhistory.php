<?php
namespace dana\table;

use dana\core;

require_once 'class.basetable.php';

/*
? - not assigned
S - system messages to accounts (eg. welcome)
V - messages to visitors
C - contact messages from account contact pages
M - contact messages from mlsb.org to me
P - sponsor message - just added, confirmed
N - newsletters from accounts
T - test message (only sent to me)
*/

define('EMAIL_SUPPORT', 'MLSB Support<support@mylocalsmallbusiness.com>');

/**
  * email history table - keep track of all emails sent
  * @version dana framework v.3
*/

class emailhistory extends idtable {
  const ET_NOTASSIGNED = '?'; // - not assigned
  const ET_SYSTEM = 'S'; // - system messages to accounts (eg. welcome)
  const ET_VISITOR = 'V'; // - messages to visitors
  const ET_ACCCONTACT = 'C'; // - contact messages from account contact pages
  const ET_ADMINCONTACT = 'M'; // - contact messages from mlsb.org to me
  const ET_SPONSOR = 'P'; // - sponsor message - just added, confirmed
  const ET_NEWSLETTER = 'N'; // - newsletters from accounts
  const ET_SUBSCRIBERINVITE = 'I'; // - newsletter subscriber invite sent
  const ET_BOOKINGNOTIFICATION = 'B'; // send notifications for provisional booking
  const ET_RATED = 'R'; // - business rated
  const ET_TEST = 'T'; // - test message (only sent to me)

  public $accountid;
  public $visitor;
  public $subject;
  public $emailtype;
  public $stamp;

  function __construct($id = 0) {
    parent::__construct('emailhistory', $id);
  }

  protected function AssignFields() {
    parent::AssignFields();
    $this->accountid = $this->AddField('accountid', self::DT_FK);
    $this->visitorid = $this->AddField('visitorid', self::DT_FK);
    $this->emailtype = $this->AddField('emailtype', self::DT_STRING);
    $this->subject = $this->AddField('subject', self::DT_STRING);
    $this->stamp = $this->AddField('stamp', self::DT_DATETIME);
  }

  protected function AssignDefaultFieldValues() {
    $this->AssignFieldDefaultValue('emailtype', emailhistory::ET_NOTASSIGNED, true);
  }

  public function Show() {
    return '';
  }

  public function EmailTypeToString($ty = false) {
    if (!$ty) {
      $ty = $this->GetFieldValue('emailtype');
    }
    switch ($ty) {
      case emailhistory::ET_SYSTEM: // 'S' - system messages to accounts (eg. welcome)
        $ret = 'System'; break;
      case emailhistory::ET_VISITOR: // 'V' - messages to visitors
        $ret = 'Visitor'; break;
      case emailhistory::ET_ACCCONTACT: //'C' - contact messages from account contact pages
        $ret = 'Acc Contact'; break;
      case emailhistory::ET_ADMINCONTACT: //'M' - contact messages from mlsb.org to me
        $ret = 'Adm Contact'; break;
      case emailhistory::ET_SPONSOR: //'P' - sponsor message - just added, confirmed
        $ret = 'Sponsor'; break;
      case emailhistory::ET_NEWSLETTER: //'N' - newsletters from accounts
        $ret = 'Newsletter'; break;
      case emailhistory::ET_SUBSCRIBERINVITE: //'I' - newsletter subscriber (invite sent)
        $ret = 'Subscriber'; break;
      case emailhistory::ET_RATED: // 'R' - business rated
        $ret = 'Rated'; break;
      case emailhistory::ET_TEST: //'T' - test message (only sent to me)
        $ret = 'Test'; break;
      default:
        $ret = 'Not assigned'; // ET_NOTASSIGNED - not assigned
        break;
    }
  }

  static public function SendSystemEmailMessage(
    $emailtype, $subject, $message, $accountid = 0, $visitorid = 0, $recordhistory = true) {
    self::SendEmailMessage($emailtype, EMAIL_SUPPORT, $subject, $message, EMAIL_SUPPORT, $accountid , $visitorid, false);
  }

  static public function SendEmailMessage(
    $emailtype, $recipient, $subject, $message, $replyaddress = false,
    $accountid = false, $visitorid = false, $recordhistory = true) {
    if (SENDEMAILS) {
      $headers = array(
        'From: ' . EMAIL_SUPPORT,
        'X-Mailer: PHP/' . phpversion()
      );
      if ($replyaddress) {
        $headers[] = 'Reply-To: ' . $replyaddress;
      }
      if (!$recipient) {
        $recipient = EMAIL_SUPPORT;
      }
      //$msg = wordwrap($message, 70, "\r\n");
      $header = implode("\n", $headers);
      $ret = mail($recipient, $subject, $message, $header);
    } else {
      $ret = true; // pretend email was sent
    }
    if ($recordhistory) {
      $emailhistory = new emailhistory();
      $emailhistory->SetFieldValue('accountid', $accountid);
      $emailhistory->SetFieldValue('visitorid', $visitorid);
      $emailhistory->SetFieldValue('emailtype', $emailtype);
      $emailhistory->SetFieldValue('subject', $subject);
      $emailhistory->StoreChanges();
    }
    return $ret;
  }
}

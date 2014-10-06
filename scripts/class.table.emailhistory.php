<?php
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

define('ET_NOTASSIGNED', '?'); // - not assigned
define('ET_SYSTEM', 'S'); // - system messages to accounts (eg. welcome)
define('ET_VISITOR', 'V'); // - messages to visitors
define('ET_ACCCONTACT', 'C'); // - contact messages from account contact pages
define('ET_ADMINCONTACT', 'M'); // - contact messages from mlsb.org to me
define('ET_SPONSOR', 'P'); // - sponsor message - just added, confirmed
define('ET_NEWSLETTER', 'N'); // - newsletters from accounts
define('ET_RATED', 'R'); // - business rated
define('ET_TEST', 'T'); // - test message (only sent to me)

define('EMAIL_SUPPORT', 'MLSB Support<support@mylocalsmallbusiness.com>');

class emailhistory extends idtable {
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
    $this->accountid = $this->AddField('accountid', DT_FK);
    $this->visitorid = $this->AddField('visitorid', DT_FK);
    $this->emailtype = $this->AddField('emailtype', DT_STRING);
    $this->subject = $this->AddField('subject', DT_STRING);
    $this->stamp = $this->AddField('stamp', DT_DATETIME);
  }

  protected function AssignDefaultFieldValues() {
    $this->AssignFieldDefaultValue('emailtype', ET_NOTASSIGNED, true);
  }

  public function Show() {
    return '';
  }

  public function EmailTypeToString($ty = false) {
    if (!$ty) {
      $ty = $this->GetFieldValue('emailtype');
    }
    switch ($ty) {
      case ET_SYSTEM: // 'S' - system messages to accounts (eg. welcome)
        $ret = 'System'; break;
      case ET_VISITOR: // 'V' - messages to visitors
        $ret = 'Visitor'; break;
      case ET_ACCCONTACT: //'C' - contact messages from account contact pages
        $ret = 'Acc Contact'; break;
      case ET_ADMINCONTACT: //'M' - contact messages from mlsb.org to me
        $ret = 'Adm Contact'; break;
      case ET_SPONSOR: //'P' - sponsor message - just added, confirmed
        $ret = 'Sponsor'; break;
      case ET_NEWSLETTER: //'N' - newsletters from accounts
        $ret = 'Newsletter'; break;
      case ET_RATED: // 'R' - business rated
        $ret = 'Rated'; break;
      case ET_TEST: //'T' - test message (only sent to me)
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

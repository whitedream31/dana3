<?php
require_once 'class.database.php';
require_once 'class.basetable.php';

// guestbook visitor entry
class guestbookentry extends idtable {
  public $list;
  public $datedescription;

  protected $heading;

  public $user; // instance of user class
  public $userdisplayname;
  public $comment;

  function __construct($id = 0) {
    parent::__construct('guestbookentry', $id);
  }

  protected function AssignFields() {
    parent::AssignFields();
    $this->AddField('guestbookid', DT_FK);
    $this->AddField('visitorid', DT_FK);
    $this->AddField('subject', DT_STRING);
    $this->AddField('content', DT_TEXT);
    $this->AddField('sendername', DT_STRING);
    $this->AddField('datestamp', DT_DATETIME);
    $this->AddField(FN_STATUS, DT_STATUS);
  }

  protected function AfterPopulateFields() {
    $this->datedescription = $this->FormatDateTime(DF_LONGDATETIME, $this->GetFieldValue('datestamp'));
  }

  public function GetEntryDetails() {
    $subject = $this->GetFieldValue('subject');
    $content = $this->GetFieldValue('content');
    $name = $this->GetFieldValue('sendername');
    $sender = ($name) ? $name : '<em>someone</em>';
    return "<strong>{$sender}</strong> wrote <strong>{$subject}</strong> on {$this->datedescription}<blockquote>{$content}</blockquote>";
  }

  public function StatusAsString($status = false) {
    $ret = '';
//    $status = $this->GetFieldValue(FN_STATUS);
    $status = ($status) ? $status : $this->GetFieldValue(FN_STATUS);
    if ($status) {
      switch ($status) {
        case STATUS_ACTIVE:
          $ret = 'VISIBLE';
          break;
        case STATUS_DELETED:
          $ret = 'Deleted';
          break;
        case STATUS_NEW:
          $ret = 'New';
          break;
        case STATUS_HIDDEN:
          $ret = 'not shown';
          break;
      }
    }
    return $ret;
  }

  static public function GetList($groupid) {
    $ret = array();
    $status = STATUS_ACTIVE;
    $result = database::$instance->Query(
      'SELECT `id` FROM guestbookentry ' .
      "WHERE `guestbookid` = {$groupid} AND `status` = '{$status}' " .
      'ORDER BY `datestamp` DESC');
    while ($line = $result->fetch_assoc()) {
      $id = $line['id'];
      $ret[$id] = new guestbookentry($id);
    }
    $result->free();
    return $ret;
  }
}

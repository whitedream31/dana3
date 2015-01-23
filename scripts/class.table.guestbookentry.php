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

  const STATUS_NEW = 'N';
  const STATUS_HIDDEN = 'H';

  function __construct($id = 0) {
    parent::__construct('guestbookentry', $id);
  }

  protected function AssignFields() {
    parent::AssignFields();
    $this->AddField('guestbookid', self::DT_FK);
    $this->AddField('visitorid', self::DT_FK);
    $this->AddField('subject', self::DT_STRING);
    $this->AddField('content', self::DT_TEXT);
    $this->AddField('sendername', self::DT_STRING);
    $this->AddField('datestamp', self::DT_DATETIME);
    $this->AddField(basetable::FN_STATUS, self::DT_STATUS);
  }

  protected function AfterPopulateFields() {
    $this->datedescription =
      $this->FormatDateTime(self::DF_LONGDATETIME, $this->GetFieldValue('datestamp'));
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
    $status = ($status) ? $status : $this->GetFieldValue(basetable::FN_STATUS);
    if ($status) {
      switch ($status) {
        case self::STATUS_ACTIVE:
          $ret = 'VISIBLE';
          break;
        case self::STATUS_DELETED:
          $ret = 'Deleted';
          break;
        case self::STATUS_NEW:
          $ret = 'New';
          break;
        case self::STATUS_HIDDEN:
          $ret = 'not shown';
          break;
      }
    }
    return $ret;
  }

  static public function GetList($groupid) {
    $ret = array();
    $status = self::STATUS_ACTIVE;
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

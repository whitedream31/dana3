<?php
// history management class for MyLocalSmallBusiness
// written by Ian Stewart (c) 2010 Whitedream Software
// created: 15 dec 2010
// modified: 6 may 2014

require_once 'class.basetable.php';

class history extends idtable {
  public $accountid;
  public $datestamp;
  public $reason;
  public $details;

  function __construct($id = 0) {
    parent::__construct('history', $id);
  }

  protected function AssignFields() {
    parent::AssignFields();
    $this->accountid = $this->AddField('accountid', DT_FK);
    $this->datestamp = $this->AddField('datestamp', DT_DATETIME);
    $this->reason = $this->AddField('reason', DT_STRING);
    $this->details = $this->AddField('details', DT_STRING);
  }

  private function CountHistory($accountid) {
    $query = "SELECT COUNT(*) AS cnt FROM `history` WHERE `accountid` = '{$accountid}'";
    $result = database::Query($query);
    $line = $result->fetch_assoc();
    $cnt = $line['cnt'];
    $result->free();
    return $cnt;
  }

  public function FindHistory() {
    $accountid = account::StartInstance()->ID();
    $list = array();
    $query =
      "SELECT `id` FROM `history` WHERE `accountid` = '{$accountid}' " .
      "ORDER BY `datestamp` ASC";
    $result = database::Query($query);
    while ($line = $result->fetch_assoc()) {
      $id = $line['id'];
      $list[] = $id;
    }
    $result->free();
    return $list;
  }

  static public function MakeHistoryItem($accountid, $reason, $details) {
    $history = new history();
    $history->NewRow();
    $history->SetFieldValue('accountid', $accountid);
    //$history->SetFieldValue('datestamp', time());
    $history->SetFieldValue('reason', $reason);
    $history->SetFieldValue('details', $details);
    return $history->CreateHistory();
  }

  public function CreateHistory() {
    $this->StoreChanges();
    return $this->lastinsertid;
  }

  public function ReasonAsText() {
    switch($this->status) {
      case HISTORY_SIGNUP:
        $ret = 'signup';
        break;
      case HISTORY_RESIGN:
        $ret = 'resign';
        break;
      case HISTORY_QUERY:
        $ret = 'qry';
        break;
      case HISTORY_COMPLAINT:
        $ret = 'cmplt';
        break;
      case HISTORY_GENERAL:
        $ret = 'gen';
        break;
      default:
        $ret = '';
    }
    return $ret;
  }

  public function DateAsText() {
    $days = DateDiff($this->datestamp);
    if ($days < 7) {
      switch($days) {
        case '':
          $ret = 'today';
          break;
        case 1:
          $ret = 'yesterday';
          break;
        default:
          $ret = "{$days} ago";
      }
    } else {
      $ret = date('j M Y', strtotime($this->datestamp));
    }
    return $ret;
  }

}

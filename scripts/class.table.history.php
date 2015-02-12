<?php
namespace dana\table;

use dana\core;

require_once 'class.basetable.php';

/**
  * history table - keep track of major events the account holder does
  * @version dana framework v.3
*/

class history extends idtable {
//  const HIST_CONTACTMSG = 'cont';

  public $accountid;
  public $datestamp;
  public $reason;
  public $details;

  function __construct($id = 0) {
    parent::__construct('history', $id);
  }

  protected function AssignFields() {
    parent::AssignFields();
    $this->accountid = $this->AddField('accountid', self::DT_FK);
    $this->datestamp = $this->AddField('datestamp', self::DT_DATETIME);
    $this->reason = $this->AddField('reason', self::DT_STRING);
    $this->details = $this->AddField('details', self::DT_STRING);
  }

  private function CountHistory($accountid) {
    $query = "SELECT COUNT(*) AS cnt FROM `history` WHERE `accountid` = '{$accountid}'";
    $result = \dana\core\database::Query($query);
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
    $result = \dana\core\database::Query($query);
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

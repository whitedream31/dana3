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

  public function SendInvite() {
  
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

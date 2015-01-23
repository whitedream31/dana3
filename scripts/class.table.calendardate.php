<?php
require_once 'class.database.php';
require_once 'class.table.page.php';

// calendar
class calendardate extends idtable {

  public $calendartype; // object to calendartype table
  public $calendartypedescription;
  public $startdatedescription;
  public $enddatedescription;

  function __construct($id = 0) {
    parent::__construct('calendardate', $id);
  }

  protected function AssignFields() {
    parent::AssignFields();
    $this->AddField('accountid', self::DT_FK);
    $this->AddField('calendartypeid', self::DT_FK);
    $this->Addfield(basetable::FN_DESCRIPTION, self::DT_DESCRIPTION);
    $this->AddField('startdate', self::DT_DATE);
    $this->AddField('enddate', self::DT_DATE);
    $this->AddField('starttime', self::DT_STRING);
    $this->AddField('endtime', self::DT_STRING);
    $this->AddField('expirydate', self::DT_DATE);
    $this->AddField('url', self::DT_STRING);
    $this->AddField('content', self::DT_TEXT);
    $this->AddField(basetable::FN_STATUS, self::DT_STATUS);
    $this->calendartype = false;
    $this->calendartypedescription = '';
    $this->startdatedescription = '';
    $this->enddatedescription = '';
  }

  protected function AfterPopulateFields() {
    $this->calendartypedescription =
      database::SelectDescriptionFromLookup('calendartype', $this->GetFieldValue('calendartypeid'));
    $this->startdatedescription =
      $this->FormatDateTime(self::DF_MEDIUMDATE, $this->GetFieldValue('startdate') . $this->GetFieldValue('starttime'));
    $this->enddatedescription =
      $this->FormatDateTime(self::DF_MEDIUMDATE, $this->GetFieldValue('enddate') . $this->GetFieldValue('endtime'));
  }

  public function AssignDataGridColumns($datagrid) {
    $datagrid->showactions = true;
    $datagrid->AddColumn('DESC', 'Title', true);
    $datagrid->AddColumn('ENTRYTYPE', 'Entry Type', false);
    $datagrid->AddColumn('DATE', 'Dates', false);
  }

  private function GetDateTime($date, $time) {
    return ($date) ? trim($this->FormatDateTime(self::DF_SHORTDATE, $date) . ' '  . $time) : false;
  }

  public function FormatDisplayTimes() {
    $startdate = $this->GetFieldValue('startdate');
    $starttime = $this->GetFieldValue('starttime');
    $enddate = $this->GetFieldValue('enddate');
    $endtime = $this->GetFieldValue('endtime');
    $start = $this->GetDateTime($startdate, $starttime);
    $end = $this->GetDateTime($enddate, $endtime);
    if ($start) {
      $date = $start;
      if ($end) {
        $date .= ' - ' . $end;
      }
    } else {
      $date = $end;
    }
    return $date;
  }

  public function AssignDataGridRows($datagrid) {
    $accountid = account::$instance->ID();
    $status = self::STATUS_ACTIVE;

    $query =
      'SELECT c.`id`, c.`description`, t.`description` AS entrytypedesc, ' .
      'c.`startdate`, c.`enddate`, c.`starttime`, c.`endtime` ' . //c.`expirydate` ' .
      'FROM `calendardate` c ' .
      'INNER JOIN `calendartype` t ON t.`id` = c.`calendartypeid` ' .
      "WHERE c.`accountid` = {$accountid} and c.`status` = '{$status}' " .
      'ORDER BY c.`startdate` DESC, c.`enddate` DESC, c.`expirydate` DESC';
    $actions = array(formbuilderdatagrid::TBLOPT_DELETABLE);
    $list = array();
    $result = database::Query($query);
    while ($line = $result->fetch_assoc()) {
      $id = $line['id'];
      $description = ($line['description']) ? $line['description'] : '<em>none</em>';
      $entrytypedesc = $line['entrytypedesc'];
      $startdate = $this->GetDateTime($line['startdate'], $line['starttime']);
      $enddate = $this->GetDateTime($line['enddate'], $line['endtime']);

      if ($startdate) {
        $date = $startdate;
        if ($enddate) {
          $date .= ' - ' . $enddate;
        }
      } else {
        $date = $enddate;
      }
      $coldata = array(
        'DESC' => $description,
        'ENTRYTYPE' => $entrytypedesc,
        'DATE' => $date
      );
      $datagrid->AddRow($id, $coldata, true, $actions);
    }
    $result->close();
    return $list;
  }
/*
  public function GetSimple() {
    $description = $this->GetFieldValue('description');
    $entrytypedesc = $this->GetFieldValue('entrytypedesc');
    $start = $this->GetFieldValue('startdate');
    $end = $this->GetFieldValue('enddate');
    $startdate = $this->GetDateTime($start, $start);
    $enddate = $this->GetDateTime($end, $end);
    if ($startdate) {
      $date = $startdate;
      if ($enddate) {
        $date .= ' - ' . $enddate;
      }
    } else {
      $date = $enddate;
    }
    $ret = ($description) ? '<h4>' . $description . '</h4>' : '';
    return $ret . '<p><em>' . $entrytypedesc . '</em></p>' . $date;
  } */

  static public function GetList($accountid) {
    $ret = array();
    $query = 'SELECT `id` FROM `calendardate` ' .
      "WHERE `accountid` = {$accountid} AND `status` = 'A' " .
      'ORDER BY `startdate` DESC, `starttime` DESC';
    $result = database::$instance->Query($query);
    while ($line = $result->fetch_assoc()) {
      $id = $line['id'];
      $itm = new calendardate($id);
      if ($itm->exists) {
        $ret[$id] = $itm;
      }
    }
    $result->free();
    return $ret;
  }
}

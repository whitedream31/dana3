<?php
require_once 'class.database.php';
require_once 'class.table.page.php';

// guestbook
class guestbook extends idtable {

  protected $entries;

  function __construct($id = 0) {
    parent::__construct('guestbook', $id);
    $this->entries = false;
  }

  protected function AssignFields() {
    parent::AssignFields();
    $this->AddField('accountid', DT_FK);
    $this->Addfield(FN_DESCRIPTION, DT_DESCRIPTION);
    $this->AddField('generalmessage', DT_TEXT);
    $this->AddField('registermessage', DT_TEXT);
    $this->AddField('thankyoumessage', DT_TEXT);
    $this->AddField('canregister', DT_BOOLEAN);
    $this->AddField('registeredonly', DT_BOOLEAN);
    $this->AddField('authorise', DT_BOOLEAN);
    $this->AddField(FN_STATUS, DT_STATUS);
  }

  protected function AssignDefaultFieldValues() {
    $this->SetFieldValue(FN_DESCRIPTION, 'Guest Book');
    $this->SetFieldValue(FN_STATUS, STATUS_ACTIVE);
  }

  protected function LoadEntries() {
    require_once 'class.table.guestbookentry.php';
    $this->entries = guestbookentry::GetList($this->ID());
    return $this->entries;
  }

  public function CountItems() {
    $id = (int) $this->GetFieldValue('accountid');
    $cnt = database::$instance->CountRows('guestbook', "`accountid` = {$id} AND `status` = 'A'");
    return $cnt;
  }

  public function CountEntries() {
    $id = $this->ID();
    $cnt = database::$instance->CountRows('guestbookentry', "`guestbookid` = {$id} AND `status` = 'A'");
    return $cnt;
  }

  public function LinkedPages() {
    return parent::LinkedPages('guestbookid');
  }
  
  public function EntryList() {
    if (!$this->entries) {
      $this->LoadEntries();
    }
    return $this->entries;
  }

  static public function GetList($accountid) {
    $ret = array();
    $query = 'SELECT `id` FROM `guestbook` ' .
      "WHERE `accountid` = {$accountid} AND `status` = 'A' ORDER BY `id`";
    $result = database::$instance->Query($query);
    while ($line = $result->fetch_assoc()) {
      $id = $line['id'];
      $itm = new guestbook($id);
      if ($itm->exists) {
        $ret[$id] = $itm;
      }
    }
    $result->free();
    return $ret;
  }

  public function AssignDataGridColumns($datagrid) {
    $datagrid->showactions = true;
    $datagrid->AddColumn('DESC', 'Title', true);
    $datagrid->AddColumn('COUNT', 'Comments', false, 'right');
  }

  public function AssignDataGridRows($datagrid) {
    $accountid = account::$instance->ID();
    $status = STATUS_ACTIVE;
    $query =
      'SELECT g.*, (SELECT COUNT(*) FROM `guestbookentry` e WHERE e.`guestbookid` = g.`id`) as entrycount ' .
      'FROM `guestbook` g ' .
      "WHERE g.`accountid` = {$accountid} AND `status` = '{$status}' " .
      'ORDER BY g.`description`';
    $actions = array(TBLOPT_DELETABLE);
    $list = array();
    $result = database::Query($query);
    while ($line = $result->fetch_assoc()) {
      $id = $line['id'];
      $description = ($line[FN_DESCRIPTION]) ? $line[FN_DESCRIPTION] : '<em>none</em>';
      $coldata = array(
        'DESC' => $description,
        'COUNT' => $line['entrycount']
      );
      $datagrid->AddRow($id, $coldata, true, $actions);
    }
    $result->free();
    return $list;
  }
}

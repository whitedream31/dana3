<?php
namespace dana\table;

use dana\core;

require_once 'class.basetable.php';
require_once 'class.table.page.php';

/**
  * guestbook table
  * @version dana framework v.3
*/

class guestbook extends idtable {

  protected $entries;

  function __construct($id = 0) {
    parent::__construct('guestbook', $id);
    $this->entries = false;
  }

  protected function AssignFields() {
    parent::AssignFields();
    $this->AddField(\dana\table\basetable::FN_ACCOUNTID, self::DT_FK);
    $this->Addfield(\dana\table\basetable::FN_DESCRIPTION, self::DT_DESCRIPTION);
    $this->AddField('generalmessage', self::DT_TEXT);
    $this->AddField('registermessage', self::DT_TEXT);
    $this->AddField('thankyoumessage', self::DT_TEXT);
    $this->AddField('canregister', self::DT_BOOLEAN);
    $this->AddField('registeredonly', self::DT_BOOLEAN);
    $this->AddField('authorise', self::DT_BOOLEAN);
    $this->AddField(\dana\table\basetable::FN_STATUS, self::DT_STATUS);
  }

  protected function AssignDefaultFieldValues() {
    $this->SetFieldValue(\dana\table\basetable::FN_DESCRIPTION, 'Guest Book');
    $this->SetFieldValue(\dana\table\basetable::FN_STATUS, self::STATUS_ACTIVE);
  }

  protected function LoadEntries() {
    require_once 'class.table.guestbookentry.php';
    $this->entries = guestbookentry::GetList($this->ID());
    return $this->entries;
  }

  public function CountItems() {
    $id = (int) $this->GetFieldValue(\dana\table\basetable::FN_ACCOUNTID);
    $cnt = \dana\core\database::$instance->CountRows('guestbook', "`accountid` = {$id} AND `status` = 'A'");
    return $cnt;
  }

  public function CountEntries() {
    $id = $this->ID();
    $cnt = \dana\core\database::$instance->CountRows('guestbookentry', "`guestbookid` = {$id} AND `status` = 'A'");
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
    $result = \dana\core\database::$instance->Query($query);
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
    $status = self::STATUS_ACTIVE;
    $query =
      'SELECT g.*, (SELECT COUNT(*) FROM `guestbookentry` e WHERE e.`guestbookid` = g.`id`) as entrycount ' .
      'FROM `guestbook` g ' .
      "WHERE g.`accountid` = {$accountid} AND `status` = '{$status}' " .
      'ORDER BY g.`description`';
    $actions = array(\dana\formbuilder\formbuilderdatagrid::TBLOPT_DELETABLE);
    $list = array();
    $result = \dana\core\database::Query($query);
    while ($line = $result->fetch_assoc()) {
      $id = $line['id'];
      $description = ($line[\dana\table\basetable::FN_DESCRIPTION])
        ? $line[\dana\table\basetable::FN_DESCRIPTION] : '<em>none</em>';
      $coldata = array(
        'DESC' => $description,
        'COUNT' => CountToString($line['entrycount'], 'comment', '<em>no comments</em>')
      );
      $datagrid->AddRow($id, $coldata, true, $actions);
    }
    $result->free();
    return $list;
  }
}

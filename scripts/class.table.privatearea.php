<?php
require_once 'class.database.php';
require_once 'class.table.page.php';
//require_once 'class.table.privatemember.php';

// private area group
class privatearea extends idtable {

  public $linkedpages;
  public $linkedmembers;

  function __construct($id = 0) {
    parent::__construct('privatearea', $id);
  }

  protected function AssignFields() {
    parent::AssignFields();
    $this->AddField('accountid', DT_FK);
    $this->AddField('title', DT_DESCRIPTION);
    $this->AddField(FN_STATUS, DT_STRING);
  }

  protected function AfterPopulateFields() {
    $this->linkedpages = $this->FindLinkedPages($this->ID());
    $this->linkedmembers = $this->FindLinkedMembers($this->ID());
  }

  public function AssignDataGridColumns($datagrid) {
    $datagrid->showactions = true;
    $datagrid->AddColumn('DESC', 'Title', true);
    $datagrid->AddColumn('PAGECOUNT', 'Pages', false, 'right');
    $datagrid->AddColumn('MEMBERCOUNT', 'Members', false, 'right');
  }

  public function AssignDataGridRows($datagrid) {
    $accountid = account::$instance->ID();
    $status = STATUS_ACTIVE;

    $query =
      'SELECT pa.`id`, pa.`title`, ' .
      '(SELECT COUNT(*) FROM `privatepage` pp INNER JOIN `page` p ON p.`id` = pp.`pageid` WHERE ' .
      "pp.`privateareaid` = pa.`id` AND p.`status` = '{$status}') as pagecount, " .
      "(SELECT COUNT(*) FROM `privatemember` pm WHERE pm.`privateareaid` = pa.`id` AND pm.`status` = '{$status}') as membercount " .
      'FROM `privatearea` pa ' .
      "WHERE pa.`accountid` = {$accountid} AND pa.`status` = '{$status}' " .
      'ORDER BY pa.`title`';
    $actions = array(TBLOPT_DELETABLE);
    $list = array();
    $result = database::Query($query);
    while ($line = $result->fetch_assoc()) {
      $id = $line['id'];
      $description = ($line['title']) ? $line['title'] : '<em>none</em>';
      $coldata = array(
        'DESC' => $description,
        'PAGECOUNT' => $line['pagecount'],
        'MEMBERCOUNT' => $line['membercount']
      );
      $datagrid->AddRow($id, $coldata, true, $actions);
    }
    $result->free();
    return $list;
  }

  static public function FindLinkedPages($groupid) {
    $ret = array();
//    $pagelist = account::$instance->GetPageList();
    $query =
      'SELECT pp.`pageid`, pt.`pgtype` FROM `privatepage` pp ' .
      'INNER JOIN `page` p ON p.`id` = pp.`pageid` ' .
      'INNER JOIN `pagetype` pt ON pt.`id` = p.`pagetypeid` ' .
      "WHERE pp.`privateareaid` = {$groupid} ORDER BY p.`pageorder`";
    $result = database::Query($query);
    while ($line = $result->fetch_assoc()) {
      $pageid = $line['pageid'];
      $pgtype = $line['pgtype'];
      $page = pagelist::NewPage($pgtype, $pageid);
      if ($page->exists) {
        $ret[$pageid] = $page;
      }
    }
    $result->close();
    return $ret;
  }

  // was FindMemberList()
  static public function FindLinkedMembers($groupid) {
    return privateareamember::GetList($groupid);
  }
}

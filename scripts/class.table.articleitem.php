<?php
require_once 'class.database.php';
require_once 'class.basetable.php';

// articles
class articleitem extends idtable {
  public $articletypedescription;
  public $lastupdatedescription;

  function __construct($id = 0) {
    parent::__construct('articleitem', $id);
  }

  protected function AssignFields() {
    parent::AssignFields();
    $this->AddField('accountid', DT_FK);
    $this->AddField('articletypeid', DT_FK);
    $this->AddField('heading', DT_STRING);
    $this->AddField('category', DT_STRING);
    $this->AddField('url', DT_STRING);
    $this->AddField('content', DT_TEXT);
    $this->AddField('stampadded', DT_DATETIME);
    $this->AddField('stampupdated', DT_DATETIME);
    $this->AddField('expirydate', DT_DATE);
    $this->AddField('galleryid', DT_FK);
    $this->AddField('allowcomments', DT_BOOLEAN);
    $this->AddField('readcount', DT_INTEGER);
    $this->AddField('visible', DT_BOOLEAN);
    $this->lastupdatedescription = '';
    $this->articletypedescription = '';
  }

  protected function AfterPopulateFields() {
    $this->articletypedescription =
      database::$instance->SelectDescriptionFromLookup('articletype', $this->GetFieldValue('articletypeid'));
    $this->lastupdatedescription = $this->GetLastUpdateAsString();
  }

  protected function GetLastUpdateAsString() {
    return $this->FormatDateTime(DF_MEDIUMDATETIME, $this->GetFieldValue('stampupdated'));
  }

  public function CountItems() {
    $id = (int) $this->GetFieldValue('accountid');
    $cnt = database::$instance->CountRows('articleitem', "`accountid` = {$id} AND `visible` = 1");
    return $cnt;
  }

  // build article entry for display
  static public function MakeDisplayItem($values) {
    $date = ($values['displaydate'])
      ? "<span class='date'>" . FormatDateTime(DF_MEDIUMDATETIME, $values['displaydate']) . '</span>'
      : '';
    $ret = array(
      "<div class='listitem'>",
      "  <h3>{$values['heading']}</h3>",
      "  {$values['content']}"
    );
    if ($date) {
      $ret[] = "  {$date}";
    }
//            'category' => $articles['category'],
//            'url' => $article['url']
    return $ret;
  }

  static public function GetAllCurrentArticles($accountid = 0, $cat = false) {
    $ret = array();
    $status = STATUS_ACTIVE;
    $acc = ($accountid) ? "AND i.`accountid` = {$accountid} " : '';
    $query =
      'SELECT i.`id`, i.`accountid`, i.`articletypeid`, i.`heading`, i.`category`, i.`url`, i.`content`, i.`expirydate`, i.`stampupdated`, ' .
      'a.`businessname`, a.`nickname`, t.`description` AS articletypedescription ' .
      'FROM `articleitem` i ' .
      'INNER JOIN `articletype` t ON t.`id` = i.`articletypeid` ' .
      'INNER JOIN `account` a ON a.`id` = i.`accountid` ' .
      "WHERE (i.`status` = '{$status}') " . $acc .
      'AND ((i.`expirydate` IS NULL) OR (NOW() > i.`expirydate`)) ' .
      'ORDER BY t.`ref`, i.`stampupdated` DESC ';
    $result = database::$instance->Query($query);
    while ($line = $result->fetch_assoc()) {
      $category = $line['category'];
      if ($category == $cat) {
        $id = $line['id'];
        $ret[$id] = array(
          'accountid' => $line['accountid'],
          'articletypeid' => $line['articletypeid'],
          'heading' => $line['heading'],
          'category' => $category,
          'url' => $line['url'],
          'content' => $line['content'],
          'expirydate' => $line['expirydate'],
          'stampupdated' => $line['stampupdated'],
          'businessname' => $line['businessname'],
          'nickname' => $line['nickname'],
          'articletypedescription' => $line['articletypedescription']
        );
      }
    }
    $result->free();
    return $ret;
  }

  static public function GetList($accountid) {
//    require_once('class.table.page.php');
    $ret = array();
    $query = 'SELECT `id` FROM `articleitem` ' .
      "WHERE `accountid` = {$accountid} " .
      ' ORDER BY `stampupdated` DESC, `expirydate`';
    $result = database::$instance->Query($query);
    while ($line = $result->fetch_assoc()) {
      $id = $line['id'];
      $itm = new articleitem($id);
      if ($itm->exists) {
        $ret[$id] = $itm;
      }
    }
    $result->free();
    return $ret;
  }

  public function AssignDataGridColumns($datagrid) {
    $datagrid->showactions = true;
    $datagrid->AddColumn('DESC', 'Heading', true);
    $datagrid->AddColumn('CATEGORY', 'Category');
  }

  public function AssignDataGridRows($datagrid) {
    $accountid = account::$instance->ID();
    $status = STATUS_ACTIVE;
    $query =
      'SELECT * FROM `articleitem` ' .
      "WHERE (`accountid` = {$accountid}) AND " .
      "(`status` = '{$status}') " .
      'ORDER BY `stampadded` DESC';
    $actions = array(TBLOPT_DELETABLE);
    $list = array();
    $result = database::Query($query);
    while ($line = $result->fetch_assoc()) {
      $id = $line['id'];
      $cat = $line['category'];
      $coldata = array(
        'DESC' => $line['heading'],
        'CATEGORY' => ($cat) ? $cat : '<em>none</em>'
      );
      $datagrid->AddRow($id, $coldata, true, $actions);
    }
    $result->free();
    return $list;
  }

}

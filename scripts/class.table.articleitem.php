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
    $this->AddField('accountid', self::DT_FK);
    $this->AddField('articletypeid', self::DT_FK);
    $this->AddField('heading', self::DT_STRING);
    $this->AddField('category', self::DT_STRING);
    $this->AddField('url', self::DT_STRING);
    $this->AddField('content', self::DT_TEXT);
    $this->AddField('stampadded', self::DT_DATETIME);
    $this->AddField('stampupdated', self::DT_DATETIME);
    $this->AddField('expirydate', self::DT_DATE);
    $this->AddField('galleryid', self::DT_FK);
    $this->AddField('allowcomments', self::DT_BOOLEAN);
    $this->AddField('readcount', self::DT_INTEGER);
    $this->AddField('visible', self::DT_BOOLEAN);
    $this->lastupdatedescription = '';
    $this->articletypedescription = '';
  }

  protected function AfterPopulateFields() {
    $this->articletypedescription =
      database::$instance->SelectDescriptionFromLookup('articletype', $this->GetFieldValue('articletypeid'));
    $this->lastupdatedescription = $this->GetLastUpdateAsString();
  }

  public function StoreChanges() {
    $this->SetFieldValue('stampupdated', date('Y-m-d G:i:s'));
    return parent::StoreChanges();
  }

  protected function GetLastUpdateAsString() {
    return $this->FormatDateTime(self::DF_MEDIUMDATETIME, $this->GetFieldValue('stampupdated'));
  }

  public function CountItems() {
    $id = (int) $this->GetFieldValue('accountid');
    $cnt = database::$instance->CountRows('articleitem', "`accountid` = {$id} AND `visible` = 1");
    return $cnt;
  }

  // build article entry for display
  static public function MakeDisplayItem($values) {
    $displaydate = $values['stampupdated']; //displaydate'])
    $date = ($displaydate)
      ? "<span class='articledate'><strong>Published:</strong> " .
        articleitem::FormatDateTime(self::DF_MEDIUMDATETIME, $displaydate) . '</span>'
      : '';
    $id = $values['id'];
    $heading = $values['heading'];
    $name = $values['name'];
    $href = $_SERVER['PHP_SELF'] . '?rid=' . $id;
    $link = "<a href='{$href}'>{$heading}</a>";
    $ret = array(
      "<div class='listitem' name='{$name}'>",
      "  <h3>{$link}</h3>",
      "  {$values['content']}"
    );
    if ($date) {
      $ret[] = "  {$date}";
    }
//            'category' => $articles['category'],
//            'url' => $article['url']
    return $ret;
  }

  static public function GetArticle($id = 0) {
    $ret = array();
    $query =
      'SELECT i.`id`, i.`accountid`, i.`articletypeid`, i.`heading`, i.`category`, i.`url`, i.`content`, i.`expirydate`, i.`stampupdated`, ' .
      'a.`businessname`, a.`nickname`, t.`description` AS articletypedescription ' .
      'FROM `articleitem` i ' .
      'INNER JOIN `articletype` t ON t.`id` = i.`articletypeid` ' .
      'INNER JOIN `account` a ON a.`id` = i.`accountid` ' .
      "WHERE i.`id` = {$id} ";
    $result = database::$instance->Query($query);
    $line = $result->fetch_assoc();
    $category = $line['category'];
    $heading = $line['heading'];
    $ret[$id] = array(
      'id' => $id,
//      'accountid' => $line['accountid'],
//      'articletypeid' => $line['articletypeid'],
      'heading' => $heading,
      'name' => self::StringToPretty($heading) . '-' . $id,
//      'category' => $category,
//      'url' => $line['url'],
      'content' => $line['content'],
//      'expirydate' => $line['expirydate'],
      'stampupdated' => $line['stampupdated'],
//      'businessname' => $line['businessname'],
//      'nickname' => $line['nickname'],
//      'articletypedescription' => $line['articletypedescription']
    );
    $result->free();
    return $ret;
  }

  static public function GetAllCurrentArticles($accountid = 0, $cat = false) {
    $ret = array();
    $status = self::STATUS_ACTIVE;
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
      if ($category == $cat || $cat === false) {
        $id = $line['id'];
        $heading = $line['heading'];
        $ret[$id] = array(
          'id' => $id,
//          'accountid' => $line['accountid'],
//          'articletypeid' => $line['articletypeid'],
          'heading' => $heading,
          'name' => self::StringToPretty($heading) . '-' . $id,
          'category' => $category,
//          'url' => $line['url'],
          'content' => $line['content'],
//          'expirydate' => $line['expirydate'],
          'stampupdated' => $line['stampupdated'],
//          'businessname' => $line['businessname'],
//          'nickname' => $line['nickname'],
//          'articletypedescription' => $line['articletypedescription']
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
    $status = self::STATUS_ACTIVE;
    $query =
      'SELECT * FROM `articleitem` ' .
      "WHERE (`accountid` = {$accountid}) AND " .
      "(`status` = '{$status}') " .
      'ORDER BY `stampadded` DESC';
    $actions = array(formbuilderdatagrid::TBLOPT_DELETABLE);
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

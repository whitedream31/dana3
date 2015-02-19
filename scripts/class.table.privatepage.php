<?php
namespace dana\table;

//use dana\core;

require_once 'class.basetable.php';

/**
  * private area page table
  * @version dana framework v.3
*/

class privatepage extends linktable {
  public $privatearea;
  public $page;

  function __construct() {
    parent::__construct('privatepage', 'privateareaid', 'pageid');
  }

  protected function AfterPopulateFields() {
    $pageid = $this->GetFieldValue('pageid');
    $privateareaid = $this->GetFieldValue('privateareaid');
    $this->page = new page($pageid);
    $this->privatearea = new privatearea($privateareaid);
  }

  static public function GetList($privateareaid) {
//    $accountid = account::$instance->ID();
    $status = self::STATUS_ACTIVE;
    $ret = array();
    $query =
      'SELECT p.`id` FROM `privatepage` pp' .
      'INNER JOIN `page` p ON pp.`pageid` = p.`id`' .
      "WHERE pp.`privateareaid` = {$privateareaid}" .
      "AND p.`status` = '{$status}'" .
      'ORDER BY p.`pageorder`';
    $result = \dana\core\database::Query($query);
    while ($line = $result->fetch_assoc()) {
      $id = $line['id'];
      $itm = new privatepage($id);
      $itm->FindByKey($privateareaid, $id);
      if ($itm->exists) {
        $ret[$id] = $itm;
      }
    }
    $result->close();
    return $ret;
  }
}

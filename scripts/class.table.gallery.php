<?php
require_once 'class.database.php';
require_once 'class.basetable.php';

// gallery group
class gallery extends idtable {
  public $itemsloaded; // true if the two item arrays are populated with current details
  public $allitems; // all gallery items for this gallery group
  public $visibleitems; // currently visible gallery items for this gallery group
  public $galleryheight = false;

  function __construct($id = 0) {
    parent::__construct('gallery', $id);
    $this->itemsloaded = false;
    //$this->PopulateItems();
  }

  protected function AssignFields() {
    parent::AssignFields();
    $this->AddField('title', DT_STRING);
    //$this->AddField('visible', DT_BOOLEAN);
  }

  protected function AfterPopulateFields() {
    $this->itemsloaded = false;
    $this->allitems = array();
    $this->visibleitems = array();
  }

  private function CalcGalleryHeight() {
    $id = $this->ID();
    $query =
      "SELECT MAX(`height`) AS maxht FROM `media` m " .
      "INNER JOIN `galleryitem` gi ON m.`id` = gi.`largemediaid` " .
      "WHERE gi.`galleryid` = {$id} AND gi.`enabled` > 0";
    $result = database::Query($query);
    $line = $result->fetch_assoc();
    $max = $line['maxht'] + 30;
    $result->close();
    if ($max < 100) {
      $max = 100;
    }
    return $max;
  }

  public function GetGalleryHeight() {
    return $this->CalcGalleryHeight();
  }

  public function BuildSlideShowList() {
    if ($this->galleryheight === false) {
      $this->galleryheight = $this->CalcGalleryHeight();
      if ($this->galleryheight == 0) {
        $this->galleryheight = 350;
      }
    }
    $ret = array();
    $path = 'media/';
    $this->PopulateItems();
    $gallerylist = $this->visibleitems;
    if (count($gallerylist) > 1) {
      $ret[] = "<div id='gallery' class='rotator' style='height: {$this->galleryheight}px'>";
    } else {
      $ret[] = "<div class='norotator'>";
    }
    $ret[] = "  <ul class='gallery'>";
    $flg = true;
    $class = ' class="show"';
    foreach($gallerylist as $galleryitem) {
      //$galleryitem = new galleryitem($id);
      $largeurl = $path . account::GetImageFilename($galleryitem->ID(), false);
      $title = $galleryitem->GetfieldValue('title');
      $ret[] =
        "    <li class='image'><img{$class} src='{$largeurl}' alt='{$title}' /></li>";
      if ($flg) {
        $flg = false;
        $class = '';
      }
    }
    $ret[] = '  </ul>';
    $ret[] = '</div>';
    return $ret;
  }

  public function CountComments() {
/*    $cnt = 0;
    foreach($this->allitems as $itm) {
      $itm;
    } */
  }

  public function CountItems($id = 0) {
    if (!$id) {
      $id = $this->ID();
    }
    $cnt = database::CountRows('galleryitem', "`galleryid` = {$id} AND `enabled` = 1");
    return $cnt;
  }

  public function GetLinkedPageDescription() {
    $query = 'SELECT `description` FROM `page` WHERE `gengalleryid` = ' . $this->ID();
/*      'INNER JOIN `pagetype` pt ON p.`pagetypeid` = pt.`id` ' .
      'WHERE (p.`gengalleryid` = ' . $id . ' AND pt.`pgtype` = "' . PGTY_GEN . '") ' .
      'OR (p.`groupid` = ' . $id . ' AND pt.`pgtype` IN ("' . PGTY_GAL . '"))'; */
    $result = database::Query($query);
    $lst = array();
    while ($line = $result->fetch_assoc()) {
      $lst[] = $line['description'];
    }
    $result->close();
    $ret = (count($lst) > 0) ? implode(', ', $lst) : '<em>(none)</em>';
    return $ret;
  }

  public function PopulateItems() {
    if (!$this->itemsloaded) {
//    require_once 'class.table.galleryitem.php';
      $this->allitems = array();
      $this->visibleitems = array();
      $id = $this->ID();
      $query = 'SELECT `id`, `enabled` FROM `galleryitem` ' .
        "WHERE `galleryid` = {$id} ORDER BY `title`";
      $result = database::Query($query);
      while ($line = $result->fetch_assoc()) {
        $id = $line['id'];
        $itm = new galleryitem($id);
        if ($itm->exists) {
          $this->allitems[$id] = $itm;
          if ($line['enabled']) {
            $this->visibleitems[$id] = $itm;
          }
        }
      }
      $result->close();
      $this->itemsloaded = true;
    }
  }

  private function CheckGalleryPageLinks() {
    $homepagefound = false;
    $gallerycount = 0;
    $pagemgrid = $this->account->GetFieldValue('pagemgrid');
    $query = 'SELECT p.`ishomepage`, t.`pgtype`, p.`groupid`, p.`gengalleryid` ' .
      'FROM `page` p INNER JOIN `pagetype` t ON p.`pagetypeid` = t.`id` ' .
      'WHERE p.`pagemgrid` = {$pagemgrid} AND ' .
      "(t.`pgtype` IN ('" . PAGETYPE_GALLERY . "', '" . PAGETYPE_GENERAL . "'))";
    $result = database::Query($query);
    while ($line = $result->fetch_assoc()) {
      switch ($line['pgtype']) {
        case PAGETYPE_GALLERY:
          if ($line['groupid'] > 0) {
            $gallerycount++;
          }
          break;
        case PAGETYPE_GENERAL:
          if (!$homepagefound) {
            $gengalid = $line['gengalleryid'];
            $hashomepage = $line['ishomepage'];
            if (($hashomepage == 1) and ($gengalid > 0)) {
              $homepagefound = true;
            }
          }
          break;
      }
    }
    $result->close();
    $ret = 0;
    if ($homepagefound) {
      $ret = 1; // home page has a gallery
    }
    if ($gallerycount > 0) {
      $ret = $ret + 2;
    }
    return $ret;
  }

  private function FindGalleryLinkedPageDescription($galleryid) {
    $query = "SELECT `description` FROM `page` " .
      "WHERE (`groupid` = {$galleryid}) OR (`gengalleryid` = {$galleryid})";
    $result = database::Query($query);
    $lst = array();
    while ($line = $result->fetch_assoc()) {
      $lst[] = $line['description'];
    }
    $result->close();
    $ret = (count($lst) > 0) ? implode(', ', $lst) : '<em>NONE</em>';
    return $ret;
  }

  public function AssignDataGridColumns($datagrid) {
    $datagrid->showactions = true;
    $datagrid->AddColumn('DESC', 'Description', true);
    $datagrid->AddColumn('IMGCOUNT', 'Images', false, 'right');
    $datagrid->AddColumn('LINKEDPAGE', 'Linked Pages');
  }

  public function AssignDataGridRows($datagrid) {
    $accountid = account::$instance->ID();
    $query =
      'SELECT * FROM `gallery` ' .
      "WHERE `accountid` = {$accountid} " .
      "ORDER BY `title`";
    $actions = array(TBLOPT_DELETABLE);
    $list = array();
    $result = database::Query($query);
    while ($line = $result->fetch_assoc()) {
      $id = $line['id'];
      $count = $this->CountItems($id);
      $linkedpages = $this->FindGalleryLinkedPageDescription($id);
      $coldata = array(
        'DESC' => $line['title'],
        'IMGCOUNT' => $count,
        'LINKEDPAGE' => $linkedpages
      );
      $datagrid->AddRow($id, $coldata, true, $actions);
    }
    $result->free();
    return $list;
  }

  public function AssignDataListRows($datalist) {
    $statusactive = STATUS_ACTIVE;
    $query =
      'SELECT `id`, `pgtype`, `description`, `help`, `homehelp` ' .
      'FROM `pagetype` ' .
      "WHERE  (`status` = '{$statusactive}') AND `countryid` = 2 " .
      'ORDER BY `pgtypeorder` DESC';
    $result = database::Query($query);
    while ($line = $result->fetch_assoc()) {
      $id = $line['id'];
      $pgtype = $line['pgtype'];
      $img = 'images/page' . $pgtype . '.png';
      $name = $line['description'];
      $desc = $line['help'];
      $hint = $line['homehelp'];
      $icon = (file_exists($img)) ? "<img src='{$img}' width='32' height='32' alt='{$hint}'>" : '';
      $datalist->AddRow($id, array(
        'icon' => $icon, 'edit' => $name, 'desc' => $desc, 'hint' => $hint, 'action' => ACT_NEW
      ));
    }
    $result->free();
  }
}
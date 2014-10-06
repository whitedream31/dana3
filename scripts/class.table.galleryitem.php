<?php
require_once 'class.database.php';
require_once 'class.basetable.php';
//require_once('class.table.gallerycomment.php');

// gallery item table
class galleryitem extends idtable {
  public $commentsloaded;
  public $comments;

  function __construct($id = 0) {
    parent::__construct('galleryitem', $id);
  }

  protected function AssignFields() {
    parent::AssignFields();
    $this->AddField('galleryid', DT_FK);
    $this->AddField('title', DT_STRING);
    $this->AddField('description', DT_STRING);
    $this->AddField('largemediaid', DT_FK);
    $this->AddField('enabled', DT_BOOLEAN);
  }

  protected function AfterPopulateFields() {
    $this->comments = array();
    $this->commentsloaded = false;
  }

  public function PopulateComments() {
    $this->comments = array();
    $query = 'SELECT `id` FROM `gallerycomments` ORDER BY `datestamp`';
    $result = database::Query($query);
    while ($line = $result->fetch_assoc()) {
      $id = $line['id'];
      $itm = new gallerycomment($id);
      if ($itm->exists) {
        $this->comments[$id] = $itm;
      }
    }
    $result->close();
    $this->commentsloaded = true;
  }

  public function Show() {}

  private function GetImageDetails($mediaid, $incdesc) {
    $path = 'media/';
    $smallurl = $path . account::GetImageFilename($mediaid);
    $largeurl = $path . account::GetImageFilename($mediaid, false);
    $title = $this->GetfieldValue('title');
    $desc = ($incdesc) ? $this->GetFieldValue('description') : false;
    $lbd = ($desc) ? ' - ' . $desc : '';
    $ret['title'] = $title;
    $ret['img'] =
      "<a href='{$largeurl}' data-lightbox='media' data-title='{$title}{$lbd}'>" .
        "<img src='{$smallurl}' alt='{$title}' />" .
      "</a>";
    $ret['desc'] = ($incdesc) ? $this->GetFieldValue('description') : false;
    return $ret;    
  }

  public function BuildItem($incdesc) {
    $imgdet = $this->GetImageDetails($this->GetFieldValue('largemediaid'), $incdesc);
    $ret = array("<h4>{$imgdet['title']}</h4>", $imgdet['img']);
    if ($imgdet['desc']) {
      $ret[] = "<p>{$imgdet['desc']}</p>";
    }
    return $ret;
  }
}

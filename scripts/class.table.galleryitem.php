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

  public function Show() {
/*    $imgid = $this->GetFieldValue('imageid');
    $img = new image($imgid);
    $title = $this->GetFieldValue('title');
    $media = $img->ImageTag(true, $title, $anchorstart, $anchorend);
    $desc = $this->GetFieldValue('title');
//    $comments = viewer::CountToString($this->count, 'comment');
//    $comments = $anchorstart . $comments . $anchorend;
//    echo "      <div class='galleryitem'>{$media}<p>{$desc}</p><p>{$comments}</p></div>\n";
    echo "      <div class='galleryitem'>{$media}<p>{$desc}</p></div>\n";
 */
  }
}

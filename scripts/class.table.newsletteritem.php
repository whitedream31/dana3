<?php
require_once 'class.database.php';
require_once 'class.basetable.php';

// newsletter article
class newsletteritem extends idtable {

  function __construct($id = 0) {
    parent::__construct('newsletteritem', $id);
  }

  protected function AssignFields() {
    $this->AddField('newsletterid', DT_FK);
//    $this->AddField('newsletteritemtypeid', DT_FK);
//    $this->AddField('groupid', DT_FK);
    $this->AddField('heading', DT_STRING);
    $this->AddField('content', DT_TEXT);
    $this->AddField('orderorder', DT_INTEGER);
//    $this->AddField('url', DT_STRING);
  }
}

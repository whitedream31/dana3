<?php
require_once 'class.database.php';
require_once 'class.basetable.php';

// newsletter article
class newsletteritem extends idtable {

  function __construct($id = 0) {
    parent::__construct('newsletteritem', $id);
  }

  protected function AssignFields() {
    parent::AssignFields();
    $this->AddField('newsletterid', self::DT_FK);
    $this->AddField('newsletteritemtypeid', self::DT_FK);
//    $this->AddField('groupid', DT_FK);
    $this->AddField('heading', self::DT_STRING);
    $this->AddField('content', self::DT_TEXT);
    $this->AddField('orderorder', self::DT_INTEGER);
//    $this->AddField('url', DT_STRING);
  }
}

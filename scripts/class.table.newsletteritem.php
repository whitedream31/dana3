<?php
namespace dana\table;

use dana\core;

require_once 'class.basetable.php';

/**
  * newsletter item table - story for newsletter
  * @version dana framework v.3
*/

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

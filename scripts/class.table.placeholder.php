<?php
namespace dana\table;

//use dana\core;

require_once 'class.basetable.php';

/**
  * place holder tag table - part of the process of creating bespoke emails
  * @version dana framework v.3
*/

class placeholder extends tagtable {
  public $tag;

  function __construct() {
    parent::__construct('placeholder', 'tag');
  }

  protected function AssignFields() {
    parent::AssignFields();
    $this->tag = $this->AddField('tag', self::DT_STRING);
    $this->AddField('tablename', self::DT_STRING);
    $this->AddField('fieldname', self::DT_STRING);
  }

  public function Show() {
    return ($this->exists)
      ? $this->GetFieldValue('tag')
      : '';
  }

}

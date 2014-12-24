<?php
require_once 'class.basetable.php';

class placeholder extends tagtable {
  public $tag;
//  public $tablename;
//  public $fieldname;

  function __construct() {
    parent::__construct('placeholder', 'tag');
  }

  protected function AssignFields() {
    parent::AssignFields();
    $this->tag = $this->AddField('tag', DT_STRING);
    $this->AddField('tablename', DT_STRING);
    $this->AddField('fieldname', DT_STRING);
//    $this->tablename = $this->AddField('tablename', DT_STRING);
//    $this->fieldname = $this->AddField('fieldname', DT_STRING);
  }

  public function Show() {
    return ($this->exists)
      ? $this->GetFieldValue('tag')
      : '';
  }

}

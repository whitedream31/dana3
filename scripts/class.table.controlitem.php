<?php

/* 
 * control menu item
 * yet another go at designing and implementing the user control page
 * dana v.3.2
 */

/**
 * item for accessing menu items for the control page
 *
 * @author ians
 */
class controlitem extends idtable {

  function __construct($id = 0) {
    parent::__construct('controlitem', $id);
  }

  protected function AfterPopulateFields() {}

  protected function AssignFields() {
    parent::AssignFields();

    $this->AddField('controlmanagerid', self::DT_FK);
    $this->AddField('title', self::DT_STRING);
    $this->AddField('icon', self::DT_STRING);
    $this->AddField('actionname', self::DT_STRING);
    $this->AddField('parentid', self::DT_FK);
    $this->AddField('helptext', self::DT_TEXT);
    $this->AddField(self::FN_STATUS, self::DT_STATUS);
  }
}

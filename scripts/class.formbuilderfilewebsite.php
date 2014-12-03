<?php
require_once 'class.formbuilderfile.php';

// file upload for general (safe) files field - FLDTYPE_FILEWEBSITE
class formbuilderfilewebsite extends formbuilderfile {

  function __construct($name, $value, $label, $targetname = '') {
    parent::__construct($name, $value, $label, $targetname);
  }

  protected function Init() {
    global $MIME_WEBSITE;
    $this->fieldtype = FLDTYPE_FILEWEBSITE;
    $this->acceptedfiletypes = $MIME_WEBSITE;
  }

  public function GetControl($usehtml5 = false) {
    $filename = ($this->targetfilename) ? $this->targetfilename : '(none)';
    $ret = array(
      '<div>',
      '  <p>Current File: <strong>' . $filename . '</strong></p>',
      '</div>');
    return array_merge($ret, parent::GetControl($usehtml5));
  }
}

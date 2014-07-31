<?php
require_once 'class.formbuilderfile.php';

// file upload for general (safe) files field - FLDTYPE_FILEWEBSITE
class filewebsitefield extends filefield {

  function __construct($name, $value, $label, $targetname) {
    parent::__construct($name, $value, $label, $targetname);
  }

  protected function Init() {
    global $MIME_WEBSITE;
    $this->fieldtype = FLDTYPE_FILEWEBSITE;
    $this->acceptedfiletypes = $MIME_WEBSITE;
  }
}

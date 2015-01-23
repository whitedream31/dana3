<?php
  require_once('class.formbuilder.base.php');
  require_once('class.formbuilder.editbox.php');

  // html5 custom field - FLDTYPE_CUSTOM - derived from edit box
  class customfield extends editboxfield {
    public $type; // control input type (eg. url)

    function __construct($type, $name, $value, $label = '') {
      parent::__construct($name, $value, $label);
      $this->fieldtype = basetable::FLDTYPE_EDITBOX;
    }

    public function ShowControl($usehtml5 = false) {
      echo "<input type=\"{$this->type}\" name=\"{$this->name}\" id=\"{$this->id}\" value=\"{$this->GetValue()}\"" .
        $this->IncludeAllAttributes() .
        $this->AddDisabled() . $this->AddReadOnly() . $this->AddRequired() . " >";
    }
  }
?>
<?php
  require_once 'class.formbuilderbase.php';

  class formbuilderstatictext extends formbuilderbase {

    function __construct($name, $value, $label = '') {
      parent::__construct($name, $value, basetable::FLDTYPE_STATIC, $label);
    }

    public function GetControl() {
      $value = $this->GetValue();
      return array(
        "<p name='{$this->name}' id='{$this->id}'>{$value}</p>"
      );
    }
  }

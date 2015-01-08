<?php
require_once 'define.php';
require_once 'class.basetable.php';

/**
 * base form field
 * part of the formbuilder class set
 */

// error list - codes
define('ERRKEY_PHPERROR', 'phperr'); // error from php ($php_errormsg)
define('ERRKEY_VALUEREQUIRED', 'vreq');
define('ERRKEY_IMAGETOOBIG', 'toobig');
define('ERRKEY_INVALIDFILE', 'invfile');
define('ERRKEY_PASSWORDMISMATCH', 'pwdmis');
define('ERRKEY_OLDPASSWORD', 'oldpwd');
define('ERRKEY_TOOSHORT', 'short');
define('ERRKEY_NOFILE', 'nofile');
// error list - messages
define('ERRVAL_VALUEREQUIRED', 'Field required');
define('ERRVAL_IMAGETOOBIG', 'Uploaded image is too big (please resize)');
define('ERRVAL_INVALIDFILE', 'Invalid file type');
define('ERRVAL_OLDPASSWORD', 'Your old password is incorrect');
define('ERRVAL_PASSWORDMISMATCH', 'Your new password does not match the confirmation');
define('ERRVAL_TOOSHORT', 'Too few characters');
define('ERRVAL_NOFILE', 'No file selected');

abstract class formbuilderbase {
  public $id;
  public $table = false; // object of table its name is linked
  public $name;
  public $fieldtype; // code for type of field FLDTYPE_xxx
  public $title; // title attribute (ie. hint on mouse over)
  public $classname; // optional class name for control
  public $style = false; // optional style attribute
  public $label; // optional label text
  public $labelclass;
  public $labelstyle;
  public $value; // value of control
  public $description; // optional description paragraph
  public $isdisabled = false; // boolean disabled attribute flag in control
  public $isreadonly = false; // boolean read only attribute flag in control
  public $required = false; // boolean required attribute flag in control
  public $requiredclass; // class name used for
  public $requiredlabel; // label used when required is used (overrides label)
  public $validvalue = false; // boolean flag set if value is valid (ie. contains value if required)
  //public $errorlabel;  // label text for showing errors
  public $errors = array(); // list of errors
  public $attributelist = array(); // list of attributes
  public $posted = false;
  public $usehtml5 = true;

  function __construct($name, $value, $fieldtype, $label) {
    $this->id = $name;
    $this->name = $name;
    $this->value = $value;
    $this->label = $label;
    //$this->errorlabel = $label . 'ERROR';
    $this->fieldtype = $fieldtype;
  }

  function __toString() {
    return $this->GetValue();
  }

  abstract public function GetControl();

  static public function IncludeAttribute($name, $value) {
    return ($value) ? ' ' . $name . '="' . $value . '"' : '';
  }

  public function Post() {
    if ($this->posted) {
      $ret = false;
    } else {
      if ($this->CheckPostExists()) {
        $this->GetPostValue();
        $this->CheckPost();
      }
      $this->posted = true;
      $ret = true;
    }
    return $ret;
  }

  public function Save($frompost = true) {
    if ($frompost) {
      $this->Post();
    } else {
      $this->CheckPost(); // check it is valid and assign to table (SetFieldValue)
    }
    return count($this->errors);
  }
  public function AddAttribute($name, $value) {
    if ($value) {
      $this->attributelist[$name] = $value;
    }
  }

  protected function AddAttributesAndValues() {
    $this->AddAttribute('id', $this->id);
    $this->AddAttribute('name', $this->name);
    $this->AddAttribute('title', $this->title);
    if (is_array($this->classname)) {
      $this->AddAttribute('class', implode(' ', $this->classname));
    } else {
      $this->AddAttribute('class', $this->classname);
    }
    $this->AddAttribute('style', $this->style);
  }

  protected function IncludeAllAttributes() {
    $this->AddAttributesAndValues();
    $ret = '';
    foreach ($this->attributelist as $key => $value) {
      $ret .= $this->IncludeAttribute($key, $value);
    }
    return $ret;
  }

  protected function SafeStringEscape($value) {
    $len = strlen($value);
    $escapecount = 0;
    $targetstring = '';
    for($offset = 0; $offset < $len; $offset++) {
      switch($c = $value{$offset}) {
        case "'":
          // Escapes this quote only if its not preceded by an unescaped backslash
          if($escapecount % 2 == 0) {
            $targetstring .= "\\";
          }
          $escapecount = 0;
          $targetstring .= $c;
          break;
        case '"':
          // Escapes this quote only if its not preceded by an unescaped backslash
          if($escapecount % 2 == 0) {
            $targetstring .= "\\";
          }
          $escapecount = 0;
          $targetstring .= $c;
          break;
        case '\\':
          $escapecount++;
          $targetstring .= $c;
          break;
        default:
          $escapecount = 0;
          $targetstring .= $c;
      }
    }
//    $targetstring = str_replace('\\\\"', '\\"', $targetstring);
//    $targetstring = str_replace("\\\\'", "\\'", $targetstring);
    return $targetstring;
  }

  protected function GetPostValue() {
    if (isset($_POST[$this->name])) {
      $postvalue = addslashes($_POST[$this->name]);
      $removetags = ($this->fieldtype != FLDTYPE_TEXTAREA);
      $value = $this->SafeStringEscape($postvalue);
      if ($removetags) {
        $value = strip_tags($value);
      }
      $this->value = $value;
    }
  }

  protected function CheckPostExists() {
    return (isset($_POST[$this->name]));
  }

  public function AddError($key, $msg) {
    $this->errors[$key] = $msg;
  }

  protected function CheckPost() {
    $this->validvalue = $this->ValidateValue();
    // if required but no value then add to error list
    if ($this->required and !$this->validvalue) {
      $this->AddError(ERRKEY_VALUEREQUIRED, ERRVAL_VALUEREQUIRED);
    } else { // valid value so process it
      $this->ProcessPost();
    }
  }

  protected function ValidateValue() {
//    $this->value = $this->GetValue();
    return (bool) $this->GetValue();
  }

  protected function ProcessPost() {
    if ($this->table) {
      $this->table->SetFieldValue($this->name, $this->value);
    }
  }

  protected function AddOption($name, $show) {
    return ($show) ? ' ' . $name : '';
  }

  protected function AddDisabled() {
    return $this->AddOption('disabled', $this->isdisabled);
  }

  protected function AddReadOnly() {
    return $this->AddOption('readonly', $this->isreadonly);
  }

  protected function AddRequired() {
    return $this->AddOption('required', $this->required);
  }

  protected function GetValue() {
    return stripslashes($this->value);
  }

  protected function GetDescription() {
    return ($this->description)
      ? array("<div class='fielddescription'>{$this->description}</div>")
      : array();
  }

  public function SetValue($value) {
    $this->value = $value;
  }

  public function BindToTable($table) {
    if ($table instanceof basetable) {
      $this->table = $table;
      $this->SetValue($table->GetFieldValue($this->name));
    }
  }

  protected function GetErrors() {
    $cnt = count($this->errors);
    $ret = array();
    if ($cnt > 0) {
      $plural = ($cnt > 1) ? 's' : '';
      $ret[] = "<div class='errors'>";
//      $ret[] = "  <p class='fieldlabel'>Error{$plural} Found:</p>";
      $ret[] = " <ul>";
      foreach ($this->errors as $errkey => $errmsg) {
        $ret[] = "    <li class='error'>{$errmsg}</li>";
      }
      $ret[] = "  </ul>";
      $ret[] = "</div>";
    }
    return $ret;
  }

  protected function GetLabel() {
    $ret = array();
    if ($this->required) {
      // if value is not valid and has a required label then show the required labale
      $lbl = (!$this->validvalue and $this->requiredlabel) ? $this->requiredlabel : $this->label;
    } else {
      $lbl = $this->label;
    }
    $reqlbl = ($this->required) ? ' required' : '';
    $class = ($this->labelclass) ? $this->labelclass : 'fieldlabel';
    $style = ($this->labelstyle) ? " style='$this->labelstyle'" : '';
    if ($lbl) {
      $lbl = ucwords(strtolower($lbl));
      $ret[] = "<label class='{$class}{$reqlbl}'{$style} for='{$this->id}'>{$lbl}</label>";
    }
    if (count($this->errors)) {
      $ret = array_merge($ret, $this->GetErrors());
    }
    return $ret;
  }

  public function GetFieldAsArray() {
    $ret = array();
    if ($this->fieldtype == FLDTYPE_HIDDEN) {
      $ret[] = '<div>';
      $ret = array_merge($ret, $this->GetControl());
      $ret[] = '</div>';
    } else {
      $ret[] = "<div class='fsfielditem'>";
      if ($this->fieldtype == FLDTYPE_CHECKBOX) {
        $ret = array_merge($ret, $this->GetDescription());
        $ret[] = '<div>';
        $ret = array_merge($ret, $this->GetControl());
        $ret = array_merge($ret, $this->GetLabel());
        $ret[] = '</div>';
      } else {
        $ret = array_merge($ret, $this->GetLabel());
        $ret = array_merge($ret, $this->GetDescription());
        $ret = array_merge($ret, $this->GetControl());
      }
      $ret[] = '</div>';
    }
    return $ret;
  }

  public function Show() {
    return implode("\n", $this->GetFieldAsArray());
  }
}

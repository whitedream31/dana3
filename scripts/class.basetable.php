<?php
/**
  * base table classes for all tables used by dana framework
  * modified: 10 jun 2014
  * @version  2.0 for dana3
*/

/*
function __autoload($classname) {
  $file = "class.{$classname}.php";
  if (file_exists($file)) {
    include $file;
  } else {
    $file = "class.table.{$classname}.php";
    if (file_exists($file)) {
      include $file;
    } else {
      throw new Exception("Class '{$classname}' not found");
    }
  }
} */

require_once 'define.php';
require_once 'class.database.php';

define('FA_VALUE', 'value');
define('FA_NAME', 'name');
define('FA_DATATYPE', 'dt');
define('FA_MODIFIED', 'md');
define('FA_FORMDETAILS', 'fd'); // true - form details assigned (appears in form for editing)
define('FA_FIELDTYPE', 'ft');
define('FA_LABEL', 'lbl');
define('FA_DESCRIPTION', 'desc');
define('FA_REQUIRED', 'required');
define('FA_DEFAULT', 'default');

define('FN_ID', 'id');
define('FN_REF', 'ref');
define('FN_DESCRIPTION', 'description');
define('FN_TAG', 'tag');
define('FN_STATUS', 'status');
define('FN_VISIBLE', 'visible');
define('FN_ACCOUNTID', 'accountid');

define('STORERESULT_INSERT', -2);
define('STORERESULT_ERROR', -1);

/**
  * base class for all table related classes
  * @abstract
*/
abstract class basetable {
  public $tablename;
  public $exists;

  public $key;
  public $fieldlist;
  public $lastinsertid = 0;
  public $lasterror = false;

  function __construct($tablename) {
    $this->tablename = $tablename;
    $this->fieldlist = array();
    $this->exists = false;
    $this->AssignFields();
  }

  abstract protected function AssignFields();
  abstract protected function KeyWithValue();
  abstract protected function UpdateKey();

  // override if necessary
  protected function AssignDefaultFieldValue($name, $fld) {
    $this->SetFieldValue($name, $fld[FA_DEFAULT]);
  }

  public function ValidateFields() {
    foreach ($this->fieldlist as $fld) {
      $name = $fld[FA_NAME];
      $fld = $this->fieldlist[$name];
      if (isset($fld[FA_REQUIRED])) {
        $value = $this->fieldlist[$name][FA_VALUE];
        if ($value) {
          $this->AssignDefaultFieldValue($name, $fld);
        }
      }
    }
  }

  public function PerformSearch($termwhat, $termwhere) {}

  // no longer used?
  protected function FindInList($text, $list, $directmatchvalue, $substrvalue) {
    $haystack = preg_replace("/[^0-9a-z ]/", '', strtolower(trim($text)));
    $ret = 0;
    $vallist = explode(' ', $haystack); // list of words to search in
    foreach($vallist as $valword) {
      if ((strlen($valword) > 3) && $list) {
        foreach($list as $itm) {
          $itmlst = explode(' ', $itm); // list of search words
          foreach($itmlst as $needle) {
            if ($valword == $needle) {
              $ret += $directmatchvalue;
            } elseif (strpos($valword, $needle) !== false) {
              $ret += $substrvalue;
            } elseif (strpos($needle, $valword) !== false) {
              $ret += $substrvalue;
            }
          }
        }
      }
    }
    return $ret;
  }

  public function FieldExists($name) {
    return isset($this->fieldlist[$name]);
  }

  public function SetFieldValue($name, $value) {
    if ($this->FieldExists($name)) {
      if ($this->fieldlist[$name][FA_VALUE] != $value) {
        $this->fieldlist[$name][FA_MODIFIED] = true;
        $this->fieldlist[$name][FA_VALUE] = $value; //stripslashes($value);
        $ret = 1;
      } else {
        $ret = 0;
    }
    } else {
      $ret = -1;
    }
    return $ret;
  }

  public function GetFieldValue($name, $default = '') {
    if ($this->FieldExists($name)) {
      $ret = $this->fieldlist[$name][FA_VALUE];
      if ($ret === null) {
        $ret = $this->fieldlist[$name][FA_DEFAULT];
      } else {
        if (!$ret && $default) {
          $ret = $default;
        }
      }
    } else {
      $ret = false;
    }
    return $ret;
  }

  public function AssignFieldDefaultValue($name, $value, $modify = false) {
    if (isset($this->fieldlist[$name])) {
      $this->fieldlist[$name][FA_VALUE] = $value;
      $this->fieldlist[$name][FA_DEFAULT] = $value;
      $this->fieldlist[$name][FA_MODIFIED] = $modify;
      $ret = $value;
    } else {
      $ret = false;
    }
    return $ret;
  }

  /*
   * StringToPretty
   * Convert a string into a valid entity value. eg. 'This is a TEST' -> 'this-is-a-test'
   * 
   */
  static public function StringToPretty($value) {
    return urlencode(str_replace(' ', '-', trim(strtolower($value))));
  }

  // no longer used?
  static public function GetFieldTypeByDataType($datatype) {
    switch ($datatype) {
      case DT_STRING:
        $ret = FLDTYPE_EDITBOX;
        break;
      case DT_TEXT:
        $ret = FLDTYPE_TEXTAREA;
        break;
      case DT_INTEGER:
        $ret = FLDTYPE_EDITBOX;
        break;
      case DT_FLOAT:
        $ret = FLDTYPE_EDITBOX;
        break;
      case DT_DATE:
        $ret = FLDTYPE_DATE;
        break;
      case DT_DATETIME:
        $ret = FLDTYPE_NONE;
        break;
      case DT_BOOLEAN:
        $ret = FLDTYPE_CHECKBOX;
        break;
      case DT_FILEIMG:
        $ret = FLDTYPE_FILEWEBIMAGES;
        break;
      case DT_FILEWEB:
        $ret = FLDTYPE_FILEWEBSITE;
        break;
      case DT_FILEANY:
        $ret = FLDTYPE_FILE;
        break;
      case DT_ID:
        $ret = FLDTYPE_HIDDEN;
        break;
      case DT_REF:
        $ret = FLDTYPE_NONE;
        break;
      case DT_DESCRIPTION:
        $ret = FLDTYPE_NONE;
        break;
      case DT_FK:
        $ret = FLDTYPE_HIDDEN;
        break;
      default:
        $ret = FLDTYPE_NONE;
        break;
    }
    return $ret;
  }

  protected function BeforePopulateFields() {}

  protected function AfterPopulateFields() {}

  protected function PopulateFields($line) {
    $this->BeforePopulateFields();
    foreach ($this->fieldlist as $fld) {
      $name = $fld[FA_NAME];
      if (isset($this->fieldlist[$name])) {
        $value = (isset($line[$name])) ? $line[$name] : false;
        $this->fieldlist[$name][FA_VALUE] = ($value) ? stripslashes($value) : false;
      }
    }
    $this->AfterPopulateFields();
  }

  static protected function GetFromUser($value, $removetags = true) {
    $ret = self::SafeStringEscape(addslashes($value));
    if ($removetags) {
      $ret = strip_tags($ret);
    }
    return $ret;
  }

  static function SafeStringEscape($value) {
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
    return $targetstring;
  }

  // save changes to table
  // returns:
  // -1 = SQL Error
  // -2 - New Row Created
  //  0 - No Changes Made
  // +n - Update Made to Row with 'n' fields modified
  public function StoreChanges() {
    $setlist = array();
    $cnt = 0;
    foreach ($this->fieldlist as $fld) {
      $name = $fld[FA_NAME];
//      $removetags = ($fld[FA_FIELDTYPE] != FLDTYPE_TEXTAREA);
//      $this->GetPost($name, $removetags); // this is done by the form builder class
      if ($this->fieldlist[$name][FA_MODIFIED]) {
        $value = $this->fieldlist[$name][FA_VALUE];
        $setlist[$name] = $value;
        $this->fieldlist[$name][FA_MODIFIED] = false;
        $cnt++;
      }
    }
    // if changes should be made ($cnt > 0) then
    //   if the record exists do an update, else do an insert
    if ($cnt) {
      if ($this->exists) {
        // do UPDATE
        $set = array();
        foreach ($setlist as $setfield => $setval) {
          $setval = ($setval) ? "'{$setval}'" : 'NULL';
          $set[] = "`{$setfield}` = {$setval}";
        }
        $setstr = implode(', ', $set);
        $query = "UPDATE `{$this->tablename}` SET {$setstr} WHERE " . $this->KeyWithValue();
      } else {
        // do INSERT
        $this->lastinsertid = 0;
        $fldlist = array();
        $vallist = array();
        foreach ($setlist as $setfield => $setval) {
          $vallist[] = ($setval) ? "'{$setval}'" : 'NULL';
          $fldlist[] = "`{$setfield}`";
        }
        $fldliststr = implode(', ', $fldlist);
        $valliststr = implode(', ', $vallist);
        $query = "INSERT INTO `{$this->tablename}` ({$fldliststr}) VALUES ({$valliststr})";
        $cnt = STORERESULT_INSERT; //-2;
      }
      try {
        database::Query($query);
        if (!$this->exists) {
          $this->lastinsertid = $this->UpdateKey();
          $this->exists = true; // exists now
        }
      } catch (Exception $e) {
        $this->lasterror = array(
          'code' => $e->getCode(),
          'msg' => $e->getMessage()
        );
        $cnt = STORERESULT_ERROR; //-1;
      }
    }
    return $cnt;
  }

  public function NewRow() {
    foreach ($this->fieldlist as $fld) {
      $name = $fld[FA_NAME];
      $this->fieldlist[$name][FA_VALUE] = $this->fieldlist[$name][FA_DEFAULT];
      $this->fieldlist[$name][FA_MODIFIED] = false;
    }
    $this->exists = false;
  }

  protected function ParseValue($name, $value) {
    $ret = $value;
    if (isset($this->fieldlist[$name])) {
      switch ($this->fieldlist[$name][FA_DATATYPE]) {
        case DT_BOOLEAN :
          $ret = ($value = 'yes') ? 1 : 0;
          break;
      }
    }
    return $ret;
  }

  public function GetPostedFields($prefix = 'fld') {
    foreach ($this->fieldlist as $fld) {
      $name = $fld[FA_NAME];
      $postvalue = $this->GetPost($prefix . $name);
      if ($postvalue !== false) {
        $this->SetFieldValue($name, $postvalue);
      }
    }
  }

  public function GetPost($name, $removetags = true) {
    if (isset($_POST[$name])) {
      $postval = $this->GetFromUser($_POST[$name], $removetags);
      $ret = $this->ParseValue($name, $postval);
      $this->SetFieldValue($name, $ret);
    } else {
      $ret = false;
    }
    return $ret;
  }

  static protected function IfNotBlank($value) {
    return trim($value) and strtolower($value) != 'na';
  }

  static public function FormatDateTime($formattype, $value, $defaultvalue = '') {
    $ret = $defaultvalue;
    if ($value) {
      $time = (is_string($value)) ? strtotime($value) : $value;
      switch ($formattype) {
        case DF_LONGDATETIME:
          $ret = date('D, jS F Y h:i a', $time);
          break;
        case DF_MEDIUMDATETIME:
          $ret = date('j F Y h:i a', $time);
          break;
        case DF_MEDIUMDATE:
          $ret = date('jS F Y', $time);
          break;
        case DF_SHORTDATE:
          $ret = date('d M Y', $time);
          break;
        case DF_SHORTDATETIME:
          $ret = date('d M Y H:i', $time);
          break;
      }
    }
    return $ret;
  }

  protected function GetDefaultOnDataType($datatype) {
    switch ($datatype) {
      case DT_FK:
      case DT_INTEGER:
      case DT_FLOAT:
        $ret = 0;
        break;
      case DT_DATETIME:
        $ret = date('Y-m-d');
        break;
      case DT_BOOLEAN:
        $ret = false;
        break;
      case DT_ID:
        $ret = -1;
        break;
      case DT_STATUS:
        $ret = STATUS_ACTIVE;
      default:
        $ret = '';
    }
  }

  protected function AddField($name, $datatype, $default = null, $fieldtype = null) {
    if (!$fieldtype) {
      $fieldtype = $this->GetFieldTypeByDataType($datatype);
    }
    $defvalue = ($default) ? $default : $this->GetDefaultOnDataType($datatype);
    $this->fieldlist[$name] = array(
      FA_NAME => $name, FA_VALUE => $defvalue, FA_DATATYPE => $datatype,
      FA_FIELDTYPE => $fieldtype, FA_FORMDETAILS => false,
      FA_DEFAULT => $defvalue, FA_MODIFIED => false
    );
    return $this->fieldlist[$name];
  }

  // no longer used?
  protected function AssignFormDetails($name, $label, $desc, $required = false) {
    if (isset($this->fieldlist[$name])) {
      $field = $this->fieldlist[$name];
      $field[FA_LABEL] = $label;
      $field[FA_DESCRIPTION] = $desc;
      $field[FA_REQUIRED] = $required;
      $field[FA_FORMDETAILS] = true; // form details assigned
      $ret = $field;
    } else {
      $ret = false;
    }
    return $ret;
  }

  protected function AssignDefaultFieldValues() {}

  // find a row based on the $fieldname and $value
  // if $exists is true it returns the state of $this exists and populates the fieldlist
  // if $exists is false it returns the $line array of columns from the database table
  public function FindByField($fieldname, $value, $exists = true) {
    $line = database::SelectFromTableByField($this->tablename, $fieldname, $value);
    if ($exists) {
      $this->exists = !($line === false);
      $ret = $this->exists;
      if ($this->exists) {
//        $this->exists = true;
        $this->PopulateFields($line);
      } else {
        $this->AssignDefaultFieldValues();
      }
    } else {
      $ret = $line;
    }
    return $ret;
  }

  public function FieldIsModified($fieldname) {
    return (bool) $this->fieldlist[$fieldname][FA_MODIFIED];
  }

  public function MarkAsDeleted() { //$flg = true) {
    $status = ($flg) ? STATUS_DELETED : '';
    $this->SetFieldValue(FN_STATUS, $status);
    $this->StoreChanges();
  }

  public function StatusAsString($status = false) {
    $ret = '';
//    $status = $this->GetFieldValue(FN_STATUS);
    $status = ($status) ? $status : $this->GetFieldValue(FN_STATUS);
    if ($status) {
      switch ($status) {
        case STATUS_ACTIVE:
          $ret = 'Active';
          break;
        case STATUS_DELETED:
          $ret = 'Deleted';
          break;
        case STATUS_PENDING:
          $ret = 'Pending';
          break;
        case STATUS_UNCONFIRMED:
          $ret = 'Unconfirmed';
          break;
        case STATUS_COMPLETED:
          $ret = 'Completed';
          break;
        case STATUS_INACTIVE:
          $ret = 'Inactive';
          break;
      }
    }
    return $ret;
  }
}

/**
  * base class for tables with id as primary key
  * @abstract
*/
abstract class idtable extends basetable {
//  public $linkedpages; // array page objects

  function __construct($tablename, $id = 0) {
    $this->key = $id;
    parent::__construct($tablename);
    $this->FindByKey($id);
  }

  protected function AssignFields() {
    $this->AddField(FN_ID, DT_ID);
  }

  public function FindByKey($value) {
    return $this->FindByField(FN_ID, $value);
  }

  public function FindByRef($value) {
    return $this->FindByField(FN_REF, $value);
  }

  public function ID() {
    return (int) $this->GetFieldValue(FN_ID);
  }

  public function StoreChanges() {
    if ($this->FieldExists(FN_ACCOUNTID) && isset(account::$instance)) {
      if (!$this->GetFieldValue(FN_ACCOUNTID)) {
        $this->SetFieldValue(FN_ACCOUNTID, account::$instance->ID());
      }
    }
    return parent::StoreChanges();
  }

  public function IsVisible() {
    $visible = $this->GetFieldValue(FN_VISIBLE);
    if ($visible === false) {
      $status = $this->GetFieldValue(FN_STATUS);
      if ($status === false) {
        $ret = true; // no visible or status fields
      } else {
        $ret = $status == STATUS_ACTIVE;
      }
    } else {
      $ret = (bool) $visible;
    }
    return $ret;
  }

  //abstract public function AssignFormFields($formeditor, $idref); // delete this after replacing it with...

  protected function KeyWithValue() {
    return '`' . FN_ID . '` = ' . $this->ID();
  }

  protected function UpdateKey() {
    $ret = database::LastInsertID();
    $this->SetFieldValue(FN_ID, $ret);
    return $ret;
  }

  // possibly unused - see gallerygroup
/*  public function LinkedPages($linkfieldname = 'groupid') {
    if (!$this->linkedpages) {
      $acc = account::$instance;
      $this->linkedpages = $acc->FindPagesByField($linkfieldname, $this->ID());
    };
    return $this->linkedpages;
  } */
}

/**
  * base class for tables with two fields for primary key
  * @abstract
*/
abstract class linktable extends basetable {
  public $id1name;
  public $id2name;

  public $id1;
  public $id2;

  function __construct($tablename, $id1name, $id2name) {
    $this->key = array($id1name, $id2name);
    $this->id1name = $id1name;
    $this->id2name = $id2name;
    parent::__construct($tablename);
  }

  protected function AssignFields() {
    //parent::AssignFields();
    $this->id1 = $this->AddField($this->id1name, DT_ID);
    $this->id2 = $this->AddField($this->id2name, DT_ID);
  }

  protected function FindByKey($value1, $value2) {
    $sql = 'SELECT * FROM `' . $this->tablename . '` WHERE `' . $this->id1name . '` = "' . $value1 . '" AND `' . $this->id2name . '` = "' . $value2 . '"';
    $result = database::Query($sql);
    $line = $result->fetch_assoc();
    $result->free();
    $this->exists = ($line !== false);
    if ($this->exists) {
      $this->PopulateFields($line);
    }
    return $this->exists;
  }

  protected function KeyWithValue() {
    return '`' . $this->id1name . '` = ' . (int) $this->GetFieldValue($this->id1name) . ' AND ';
//      '`' . $this->id2name . '` = ' . (int) $this->GetFieldValue($this->id2name);
  }

  protected function UpdateKey() {}

}

/**
  * base class for tables with foreignkey and tag field as primary key
  * not currently used
  * @abstract
*/
abstract class tagtable extends basetable {
  protected $idname;
  public $id;
  public $tag;

  public $taglist;

  function __construct($tablename, $idname) {
    parent::__construct($tablename);
    $this->idname = $idname;
  }

  protected function AssignFields() {
//    parent::AssignFields();
    $this->taglist = array();
    $this->id = $this->AddField($this->idname, DT_ID);
    $this->tag = $this->AddField(FN_TAG, DT_TAG);
  }

  public function FindByTag($value) {
    $line = database::SelectFromTableByField($this->tablename, $this->idname, $value);
    $this->exists = ($line !== false);
    if ($this->exists) {
      $this->exists = true;
      $this->PopulateFields($line);
    }
    return $this->exists;
  }

  protected function KeyWithValue() {
    return '`' . $this->idname . '` = ' . (int) $this->GetFieldValue($this->idname) . ' AND ';
      '`' . FN_TAG . '` = ' . $this->GetFieldValue($this->FNTAG);
  }

  protected function UpdateKey() {}

  protected function KShuffle() {
    $tmp = array();
    foreach($this->taglist as $key => $value) {
      $tmp[] = array('k' => $key, 'v' => $value);
    }
    shuffle($tmp);
    $this->taglist = array();
    foreach($tmp as $entry) {
      $this->taglist[$entry['k']] = $entry['v'];
    }
  }

  protected function BuildTagList() {
    $this->taglist = array();
    $result = database::Query("SELECT COUNT(`tag`) AS cnt, `tag` FROM `{$this->tablename}` GROUP BY `tag`");
    while ($line = $result->fetch_assoc()) {
      $cnt = $line['cnt'];
      $tag = $line['tag'];
      $this->taglist[$tag] = $cnt;
    }
    $result->free();
  }

  public function ShowTagCloud($script) {
    if (count($this->taglist) == 0) {
      $this->BuildTagList();
    }
    // $tags is the array
    if (count($this->taglist) > 0) {
      $this->KShuffle();
      $max_size = 32; // max font size in pixels
      $min_size = 12; // min font size in pixels
      // largest and smallest array values
      $max_qty = max(array_values($this->taglist));
      $min_qty = min(array_values($this->taglist));
      if ($min_qty != $max_qty) {
        $min_qty++; // ignore rarely used tags
      }
      // find the range of values
      $spread = $max_qty - $min_qty;
      if ($spread == 0) { // we don't want to divide by zero
        $spread = 1;
      }
      // set the font-size increment
      $step = ($max_size - $min_size) / ($spread);
      $url = $script . '?tag=';
      // loop through the tag array
      foreach ($this->taglist as $tag => $counter) {
        // calculate font-size
        // find the $value in excess of $min_qty
        // multiply by the font-size increment ($size)
        // and add the $min_size set above
        $size = round($min_size + (($counter - $min_qty) * $step));
        $keydesc = str_replace(' ', '&nbsp;', $tag); // change spaces inside the key with hard spaces (for display)
        $tag = urlencode($tag);
        echo '<a href="' . $url . $tag . '" style="font-size: ' . $size . 'px" title="search for ' . "'" . $keydesc . "'" . '">' . $keydesc . '</a>&nbsp; ';
      }
    } else {
      echo "<p><em>None found</em></p>\n";
    }
  }

}

/**
  * base class for lookup tables
  * @abstract
*/
abstract class lookuptable extends idtable {
  public $ref;
  public $description;

  function __construct($tablename, $id = 0) {
    if (is_int($id)) {
      parent::__construct($tablename, $id);
      $this->FindByKey($id);
    } elseif (is_string($id)) {
      parent::__construct($tablename);
      $this->FindByRef($id);
    }
  }

  protected function AfterPopulateFields() {
    $this->ref = $this->GetFieldValue('ref');
    $this->description = $this->GetFieldValue('description');
  }

  protected function AssignFields() {
    $this->AddField(FN_ID, DT_ID);
    $this->AddField(FN_REF, DT_REF);
    $this->AddField(FN_DESCRIPTION, DT_STRING);
    $this->AddField(FN_STATUS, DT_STATUS);
  }

  public function AssignFormFields($formeditor, $idref) {
/*    $formeditor->AddField('title', DT_STRING, 'title');
*/
  }

  protected function KeyWithValue() {
    return '`' . FN_ID . '` = ' . $this->ID();
  }
}

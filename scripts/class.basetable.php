<?php
namespace dana\table;

/**
  * base table classes for all tables used by dana framework
  * modified: 10 jun 2014
  * @version  2.0 for dana3
*/

//use dana\core;

require_once 'define.php';
require_once 'class.database.php';

/**
  * base class for all table related classes
  * @abstract
*/
abstract class basetable {
  const STATUS_ACTIVE = 'A';
  const STATUS_DELETED = 'D';
  const STATUS_CANCELLED = 'C'; // newsletters: invited but not accepted after 30 days
  const STORERESULT_INSERT = -2;
  const STORERESULT_ERROR = -1;

//  case STATUS_PENDING:
//  case STATUS_UNCONFIRMED:
//  case STATUS_COMPLETED:
//  case STATUS_INACTIVE:

  const FA_VALUE = 'value';
  const FA_NAME = 'name';
  const FA_DATATYPE = 'dt';
  const FA_MODIFIED = 'md';
  const FA_FORMDETAILS = 'fd'; // true - form details assigned (appears in form for editing)
  const FA_FIELDTYPE = 'ft';
  const FA_LABEL = 'lbl';
  const FA_DESCRIPTION = 'desc';
  const FA_REQUIRED = 'required';
  const FA_DEFAULT = 'default';

  const FN_ID = 'id';
  const FN_REF = 'ref';
  const FN_DESCRIPTION = 'description';
  const FN_TAG = 'tag';
  const FN_STATUS = 'status';
  const FN_VISIBLE = 'visible';
  const FN_ACCOUNTID = 'accountid';

  const DF_SHORTDATE = 'sd';
  const DF_SHORTDATETIME = 'sdt';
  const DF_LONGDATE = 'ld';
  const DF_LONGDATETIME = 'ldt';
  const DF_MEDIUMDATETIME = 'mdt';
  const DF_MEDIUMDATE = 'md';

  const DT_STRING = 's';
  const DT_TEXT = 't';
  const DT_INTEGER = 'i';
  const DT_FLOAT = 'f';
  const DT_DATE = 'd';
  const DT_DATETIME = 'dt';
  const DT_BOOLEAN = 'b';
  const DT_FILEIMG = 'fi';
  const DT_FILEWEB = 'fw';
  const DT_FILEANY = 'fa';
  const DT_ID = 'id';
  const DT_FK = 'fk';
  const DT_TAG = 'tag';
  const DT_REF = 'ref';
  const DT_STATUS = 'st';
  const DT_DESCRIPTION = 'desc';
// basic field types for controls
  const FLDTYPE_NONE = 'x';
  const FLDTYPE_HIDDEN = 'h';
  const FLDTYPE_EDITBOX = 'eb';
  const FLDTYPE_TEXTAREA = 'ta';
  const FLDTYPE_CHECKBOX = 'cb';
  const FLDTYPE_FILE = 'f';
  const FLDTYPE_PASSWORD = 'p';
// multiple value types
  const FLDTYPE_RADIO = 'rb';
  const FLDTYPE_SELECT = 's';
// special types
  const FLDTYPE_DATE = 'd';
  const FLDTYPE_TIME = 't';
  const FLDTYPE_FILEWEBSITE = 'fw';
  const FLDTYPE_FILEWEBIMAGES = 'fwi';
  const FLDTYPE_EMAIL = 'e';
  const FLDTYPE_URL = 'u';
  const FLDTYPE_TELEPHONE = 'tel';
  const FLDTYPE_BUTTON = 'btn';
  const FLDTYPE_CUSTOM = 'ctm';
  const FLDTYPE_STATIC = 'st';
  const FLDTYPE_DATAGRID = 'dg';
  const FLDTYPE_DATALIST = 'dl';
  const FLDTYPE_STATUSGRID = 'sg';
  const FLDTYPE_SUMMARYBOX = 'sb';

  public $tablename;
  public $exists;

  public $key;
  public $fieldlist;
  public $lastinsertid = 0;
  public $lasterror = false;

//define('STATUS_WAITING', 'W'); // newsletters - invited but not accepted yet
//define('STATUS_UNSUBSCRIBED', 'U'); // newsletters - no longer subscribed
//define('STATUS_NEW', 'N'); // new item - guestbook entries
//define('STATUS_HIDDEN', 'H'); // hide item - guestbook entries

  /**
   * @param $tablename
   */
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
    $this->SetFieldValue($name, $fld[self::FA_DEFAULT]);
  }

  /**
   *
   */
  public function ValidateFields() {
    foreach ($this->fieldlist as $fld) {
      $name = $fld[self::FA_NAME];
      $fld = $this->fieldlist[$name];
      if (isset($fld[self::FA_REQUIRED])) {
        $value = $this->fieldlist[$name][self::FA_VALUE];
        if ($value) {
          $this->AssignDefaultFieldValue($name, $fld);
        }
      }
    }
  }

  public function PerformSearch($termwhat, $termwhere) {}

  // no longer used?
  /**
   * @param $text
   * @param $list
   * @param $directmatchvalue
   * @param $substrvalue
   * @return int
     */
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

  /**
   * @param $name
   * @return int
     */
  public function FieldExists($name) {
    return isset($this->fieldlist[$name]);
  }

  public function SetFieldValue($name, $value) {
    if ($this->FieldExists($name)) {
      if ($this->fieldlist[$name][self::FA_VALUE] != $value) {
        $this->fieldlist[$name][self::FA_MODIFIED] = true;
        $this->fieldlist[$name][self::FA_VALUE] = $value; //stripslashes($value);
        $ret = 1;
      } else {
        $ret = 0;
    }
    } else {
      $ret = -1;
    }
    return $ret;
  }

  /**
   * @param $name
   * @param string $default
   * @return bool|string
     */
  public function GetFieldValue($name, $default = '') {
    if ($this->FieldExists($name)) {
      $ret = $this->fieldlist[$name][self::FA_VALUE];
      if ($ret === null) {
        $ret = $this->fieldlist[$name][self::FA_DEFAULT];
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

  /**
   * @param $name
   * @param $value
   * @param bool $modify
   * @return bool
     */
  public function AssignFieldDefaultValue($name, $value, $modify = false) {
    if (isset($this->fieldlist[$name])) {
      $this->fieldlist[$name][self::FA_VALUE] = $value;
      $this->fieldlist[$name][self::FA_DEFAULT] = $value;
      $this->fieldlist[$name][self::FA_MODIFIED] = $modify;
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
  /**
   * @param $datatype
   * @return string
     */
  static public function GetFieldTypeByDataType($datatype) {
    switch ($datatype) {
      case self::DT_STRING:
        $ret = self::FLDTYPE_EDITBOX;
        break;
      case self::DT_TEXT:
        $ret = self::FLDTYPE_TEXTAREA;
        break;
      case self::DT_INTEGER:
        $ret = self::FLDTYPE_EDITBOX;
        break;
      case self::DT_FLOAT:
        $ret = self::FLDTYPE_EDITBOX;
        break;
      case self::DT_DATE:
        $ret = self::FLDTYPE_DATE;
        break;
      case self::DT_DATETIME:
        $ret = self::FLDTYPE_NONE;
        break;
      case self::DT_BOOLEAN:
        $ret = self::FLDTYPE_CHECKBOX;
        break;
      case self::DT_FILEIMG:
        $ret = self::FLDTYPE_FILEWEBIMAGES;
        break;
      case self::DT_FILEWEB:
        $ret = self::FLDTYPE_FILEWEBSITE;
        break;
      case self::DT_FILEANY:
        $ret = self::FLDTYPE_FILE;
        break;
      case self::DT_ID:
        $ret = self::FLDTYPE_HIDDEN;
        break;
      case self::DT_REF:
        $ret = self::FLDTYPE_NONE;
        break;
      case self::DT_DESCRIPTION:
        $ret = self::FLDTYPE_NONE;
        break;
      case self::DT_FK:
        $ret = self::FLDTYPE_HIDDEN;
        break;
      default:
        $ret = self::FLDTYPE_NONE;
        break;
    }
    return $ret;
  }

  /**
   *
   */
  protected function BeforePopulateFields() {}

  /**
   *
   */
  protected function AfterPopulateFields() {}

  protected function PopulateFields($line) {
    $this->BeforePopulateFields();
    foreach ($this->fieldlist as $fld) {
      $name = $fld[self::FA_NAME];
      if (isset($this->fieldlist[$name])) {
        $value = (isset($line[$name])) ? $line[$name] : false;
        $this->fieldlist[$name][self::FA_VALUE] = ($value) ? stripslashes($value) : false;
      }
    }
    $this->AfterPopulateFields();
  }

  /**
   * @param $value
   * @param bool $removetags
   * @return string
     */
  static protected function GetFromUser($value, $removetags = true) {
    $ret = self::SafeStringEscape(addslashes($value));
    if ($removetags) {
      $ret = strip_tags($ret);
    }
    return $ret;
  }

  /**
   * @param $value
   * @return string
     */
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
  /**
   * @return int
   */
  public function StoreChanges() {
    $setlist = array();
    $cnt = 0;
    foreach ($this->fieldlist as $fld) {
      $name = $fld[self::FA_NAME];
//      $removetags = ($fld[FA_FIELDTYPE] != FLDTYPE_TEXTAREA);
//      $this->GetPost($name, $removetags); // this is done by the form builder class
      if ($this->fieldlist[$name][self::FA_MODIFIED]) {
        $value = $this->fieldlist[$name][self::FA_VALUE];
        $setlist[$name] = $value;
        $this->fieldlist[$name][self::FA_MODIFIED] = false;
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
        $cnt = self::STORERESULT_INSERT; //-2;
      }
      try {
        \dana\core\database::Query($query);
        if (!$this->exists) {
          $this->lastinsertid = $this->UpdateKey();
          $this->exists = true; // exists now
        }
      } catch (\Exception $e) {
        $this->lasterror = array(
          'code' => $e->getCode(),
          'msg' => $e->getMessage()
        );
        $cnt = self::STORERESULT_ERROR; //-1;
      }
    }
    return $cnt;
  }

  /**
   *
   */
  public function NewRow() {
    foreach ($this->fieldlist as $fld) {
      $name = $fld[self::FA_NAME];
      $this->fieldlist[$name][self::FA_VALUE] = $this->fieldlist[$name][self::FA_DEFAULT];
      $this->fieldlist[$name][self::FA_MODIFIED] = false;
    }
    $this->exists = false;
  }

  /**
   * @param $name
   * @param $value
   * @return int
     */
  protected function ParseValue($name, $value) {
    $ret = $value;
    if (isset($this->fieldlist[$name])) {
      switch ($this->fieldlist[$name][self::FA_DATATYPE]) {
        case self::DT_BOOLEAN :
          $ret = ($value = 'yes') ? 1 : 0;
          break;
      }
    }
    return $ret;
  }

  /**
   * @param string $prefix
   */
  public function GetPostedFields($prefix = 'fld') {
    foreach ($this->fieldlist as $fld) {
      $name = $fld[self::FA_NAME];
      $postvalue = $this->GetPost($prefix . $name);
      if ($postvalue !== false) {
        $this->SetFieldValue($name, $postvalue);
      }
    }
  }

  /**
   * @param $name
   * @param bool $removetags
   * @return bool|int
     */
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

  /**
   * @param $value
   * @return bool
     */
  static protected function IfNotBlank($value) {
    return trim($value) and strtolower($value) != 'na';
  }

  /**
   * @param $formattype
   * @param $value
   * @param string $defaultvalue
   * @return bool|string
     */
  static public function FormatDateTime($formattype, $value, $defaultvalue = '') {
    $ret = $defaultvalue;
    if ($value) {
      $time = (is_string($value)) ? strtotime($value) : $value;
      switch ($formattype) {
        case self::DF_LONGDATE:
          $ret = date('l, j F Y', $time);
          break;
        case self::DF_LONGDATETIME:
          $ret = date('D, jS F Y h:i a', $time);
          break;
        case self::DF_MEDIUMDATETIME:
          $ret = date('j F Y h:i a', $time);
          break;
        case self::DF_MEDIUMDATE:
          $ret = date('jS F Y', $time);
          break;
        case self::DF_SHORTDATE:
          $ret = date('d M Y', $time);
          break;
        case self::DF_SHORTDATETIME:
          $ret = date('d M Y H:i', $time);
          break;
      }
    }
    return $ret;
  }

  /**
   * @param $datatype
   */
  protected function GetDefaultOnDataType($datatype) {
    switch ($datatype) {
      case self::DT_FK:
      case self::DT_INTEGER:
      case self::DT_FLOAT:
        $ret = 0;
        break;
      case self::DT_DATETIME:
        $ret = date('Y-m-d');
        break;
      case self::DT_BOOLEAN:
        $ret = false;
        break;
      case self::DT_ID:
        $ret = -1;
        break;
      case self::DT_STATUS:
        $ret = self::STATUS_ACTIVE;
      default:
        $ret = '';
    }
    return $ret;
  }

  /**
   * @param $name
   * @param $datatype
   * @param null $default
   * @param null $fieldtype
   * @return mixed
     */
  protected function AddField($name, $datatype, $default = null, $fieldtype = null) {
    if (!$fieldtype) {
      $fieldtype = $this->GetFieldTypeByDataType($datatype);
    }
    $defvalue = ($default) ? $default : $this->GetDefaultOnDataType($datatype);
    $this->fieldlist[$name] = array(
      self::FA_NAME => $name, self::FA_VALUE => $defvalue, self::FA_DATATYPE => $datatype,
//      self::FA_FIELDTYPE => $fieldtype, self::FA_FORMDETAILS => false,
      self::FA_DEFAULT => $defvalue, self::FA_MODIFIED => false
    );
    return $this->fieldlist[$name];
  }

  // no longer used?
  /**
   * @param $name
   * @param $label
   * @param $desc
   * @param bool $required
   * @return bool
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
   */

  /**
   *
   */
  protected function AssignDefaultFieldValues() {}

  // find a row based on the $fieldname and $value
  // if $exists is true it returns the state of $this exists and populates the fieldlist
  // if $exists is false it returns the $line array of columns from the database table
  /**
   * @param $fieldname
   * @param $value
   * @param bool $exists
   * @return bool
     */
  public function FindByField($fieldname, $value, $exists = true) {
    $line = \dana\core\database::SelectFromTableByField($this->tablename, $fieldname, $value);
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

  /**
   * @param $fieldname
   * @return bool
     */
  public function FieldIsModified($fieldname) {
    return (bool) $this->fieldlist[$fieldname][self::FA_MODIFIED];
  }

  /**
   *
   */
  public function MarkAsDeleted($flg = true) {
    $status = ($flg) ? self::STATUS_DELETED : '';
    $this->SetFieldValue(self::FN_STATUS, $status);
    $this->StoreChanges();
  }

  /**
   * @param bool $status
   * @return string
     */
  public function StatusAsString($status = false) {
    $ret = '';
//    $status = $this->GetFieldValue(FN_STATUS);
    $status = ($status) ? $status : $this->GetFieldValue(self::FN_STATUS);
    if ($status) {
      switch ($status) {
        case self::STATUS_ACTIVE:
          $ret = 'Active';
          break;
        case self::STATUS_DELETED:
          $ret = 'Deleted';
          break;
/*
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
 */
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
    $this->AddField(self::FN_ID, self::DT_ID);
  }

  public function FindByKey($value) {
    return $this->FindByField(self::FN_ID, $value);
  }

  public function FindByRef($value) {
    return $this->FindByField(self::FN_REF, $value);
  }

  public function ID() {
    return (int) $this->GetFieldValue(self::FN_ID);
  }

  public function StoreChanges() {
    if ($this->FieldExists(self::FN_ACCOUNTID) && isset(account::$instance)) {
      if (!$this->GetFieldValue(self::FN_ACCOUNTID)) {
        $this->SetFieldValue(self::FN_ACCOUNTID, account::$instance->ID());
      }
    }
    return parent::StoreChanges();
  }

  public function IsVisible() {
    $visible = $this->GetFieldValue(self::FN_VISIBLE);
    if ($visible === false) {
      $status = $this->GetFieldValue(self::FN_STATUS);
      if ($status === false) {
        $ret = true; // no visible or status fields
      } else {
        $ret = $status == self::STATUS_ACTIVE;
      }
    } else {
      $ret = (bool) $visible;
    }
    return $ret;
  }

  //abstract public function AssignFormFields($formeditor, $idref); // delete this after replacing it with...

  protected function KeyWithValue() {
    return '`' . self::FN_ID . '` = ' . $this->ID();
  }

  protected function UpdateKey() {
    $ret = \dana\core\database::LastInsertID();
    $this->SetFieldValue(self::FN_ID, $ret);
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
    $this->id1 = $this->AddField($this->id1name, self::DT_ID);
    $this->id2 = $this->AddField($this->id2name, self::DT_ID);
  }

  protected function FindByKey($value1, $value2) {
    $sql = 'SELECT * FROM `' . $this->tablename . '` WHERE `' . $this->id1name . '` = "' . $value1 . '" AND `' . $this->id2name . '` = "' . $value2 . '"';
    $result = \dana\core\database::Query($sql);
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
    $this->id = $this->AddField($this->idname, self::DT_ID);
    $this->tag = $this->AddField(self::FN_TAG, self::DT_TAG);
  }

  public function FindByTag($value) {
    $line = \dana\core\database::SelectFromTableByField($this->tablename, $this->idname, $value);
    $this->exists = ($line !== false);
    if ($this->exists) {
      $this->exists = true;
      $this->PopulateFields($line);
    }
    return $this->exists;
  }

  protected function KeyWithValue() {
    return '`' . $this->idname . '` = ' . (int) $this->GetFieldValue($this->idname) . ' AND ';
      '`' . self::FN_TAG . '` = ' . $this->GetFieldValue($this->FNTAG);
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
    $result = \dana\core\database::Query("SELECT COUNT(`tag`) AS cnt, `tag` FROM `{$this->tablename}` GROUP BY `tag`");
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
    $this->AddField(self::FN_ID, self::DT_ID);
    $this->AddField(self::FN_REF, self::DT_REF);
    $this->AddField(self::FN_DESCRIPTION, self::DT_STRING);
    $this->AddField(self::FN_STATUS, self::DT_STATUS);
  }

  public function AssignFormFields($formeditor, $idref) {
/*    $formeditor->AddField('title', DT_STRING, 'title');
*/
  }

  protected function KeyWithValue() {
    return '`' . self::FN_ID . '` = ' . $this->ID();
  }
}

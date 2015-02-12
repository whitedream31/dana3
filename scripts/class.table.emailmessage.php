<?php
namespace dana\table;

use dana\core;

require_once 'class.basetable.php';
require_once 'class.table.placeholder.php';

/**
  * email message table
  * @version dana framework v.3
*/

class emailmessage extends lookuptable {
  const PLACEHOLDER_START = '[%';
  const PLACEHOLDER_END = '%]';

  public $tag;
  public $content;
  public $formattedcontent;
  protected $customfields = array();
  private $placeholder;

  function __construct($id = 0) {
    $this->placeholder = new placeholder();
    parent::__construct('emailmessage', $id);
  }

  protected function AssignFields() {
    parent::AssignFields();
    $this->content = $this->AddField('content', self::DT_STRING);
  }

  protected function AfterPopulateFields() {
    $this->GetFormattedText();
  }

  public function GetFormattedText() {
    $text = $this->GetFieldValue('content');
    $this->formattedcontent = $this->FormatText($text);
    return $this->formattedcontent;
  }

  private function LookupTag($tag, $default = '') {
    if ($this->placeholder->FindByTag($tag)) {
      require_once 'class.table.account.php';
      $account = account::StartInstance();
      $table = $this->placeholder->GetFieldValue('tablename');
      $field = $this->placeholder->GetFieldValue('fieldname');
      switch ($table) {
        case 'account':
          $ret = $account->GetFieldValue($field);
          break;
        case 'contact':
          $ret = $account->Contact()->GetFieldValue($field);
          break;
        default :
          $ret = 'TAG=' . $tag;
//          $ret = (isset($this->customfields[$tag]))
//            ? $this->customfields[$tag] : $default;
          break;
      }
    } else {
      $ret = (isset($this->customfields[$tag]))
        ? $this->customfields[$tag] : $default;
/*    $ln = $this->FindByField('tag', $tag, false);
    if ($ln) {
      $table = $ln['tablename'];
      $field = $ln['fieldname'];
      $ret = "[{$table}].{$field}";
    } else { */
//      $ret = $default;
    }
    return $ret;
  }

  public function AddCustomField($key, $value) {
    $this->customfields[$key] = $value;
  }

  public function FormatLine($text) {
    $offset = strlen(self::PLACEHOLDER_START);
    do {
      $posstart = strpos($text, self::PLACEHOLDER_START);
      if ($posstart != false) {
        $posend = strpos($text, self::PLACEHOLDER_END, $posstart);
        if ($posend != false) {
          $len = $posend - $posstart - $offset;
          $tag = substr($text, $posstart + $offset, $len);
          $word = $this->LookupTag($tag);
          $text = substr($text, 0, $posstart) . $word . substr($text, $posend + $offset);
        }
      }
    } while ($posstart != false);
    return $text;
  }

  public function FormatText($text) {
    $ret = array();
    if (is_array($text)) {
      foreach($text as $ln) {
        $ret[] = $this->FormatLine($ln);
      }
    } else {
      foreach(explode("\n", $text) as $ln) {
        $ret[] = $this->FormatLine($ln);
      }
    }
    return implode("\n\r", $ret);
  }

  public function Show() {
    return ($this->exists)
      ? $this->GetFieldValue('content')
      : '';
  }

}

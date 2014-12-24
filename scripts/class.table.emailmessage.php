<?php
require_once 'class.basetable.php';
require_once 'class.table.placeholder.php';

define('PLACEHOLDER_START', '[%');
define('PLACEHOLDER_END', '%]');

class emailmessage extends lookuptable {
  public $tag;
  public $content;
  public $formattedcontent;

  private $placeholder;

  function __construct($id = 0) {
    $this->placeholder = new placeholder();
    parent::__construct('emailmessage', $id);
  }

  protected function AssignFields() {
    parent::AssignFields();
    $this->content = $this->AddField('content', DT_STRING);
  }

  protected function AfterPopulateFields() {
    $text = $this->GetFieldValue('content');
    $this->formattedcontent = $this->FormatText($text);
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
          $ret = $default;
          break;
      }
    } else {
/*    $ln = $this->FindByField('tag', $tag, false);
    if ($ln) {
      $table = $ln['tablename'];
      $field = $ln['fieldname'];
      $ret = "[{$table}].{$field}";
    } else { */
      $ret = $default;
    }
    return $ret;
  }

  public function FormatLine($text) {
    $offset = strlen(PLACEHOLDER_START);
    do {
      $posstart = strpos($text, PLACEHOLDER_START);
      if ($posstart != false) {
        $posend = strpos($text, PLACEHOLDER_END, $posstart);
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

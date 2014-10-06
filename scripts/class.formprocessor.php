<?php

require_once 'class.formbuilderbase.php';

/**
  * form processor - used for creating forms in mini-sites
*/

class formprocessor {
  private $fieldlist;

  private $idname;
  private $formaction;
  private $formmethod;
  private $formenctype;
  private $formclass;
  private $submitcaption;
  private $answeriswrong;

  public $formtitle;
  public $submitid = 'btnsubmit';
  public $cancelid = 'btncancel';
  public $showcancel;
  public $includequestion;

  public function __construct($idname) {
    $this->fieldlist = array();
    $this->idname = $idname;
    $this->answeriswrong = false;
    $this->AssignDefaultValues();
    $this->AssignValues();
  }

  private function AssignDefaultValues() {
    $this->formaction = $_SERVER['PHP_SELF'];
    $this->formmethod = 'post';
    $this->formenctype = 'application/x-www-form-urlencoded';
    $this->formtitle = false;
    $this->formclass = false;
    $this->showcancel = true;
    $this->submitcaption = 'Submit';
    $this->cancelcaption = 'Cancel';
    $this->includequestion = true;
  }

  protected function AssignValues() {}

  protected function GetCustomButton($caption, $idname, $value, $url, $newwindow = false) {
    if ($newwindow) {
      $event = "javascript:window.open('{$url}', '_blank');";
    } else {
      $event = "javascript:window.open('{$url}', '_self');";
    }
    $click = 'onclick="' . $event . '"';
    return "<input type='button' title='{$caption}' id='{$idname}' " .
      "name='{$idname}'  class='actionbutton' value='{$value}' {$click} />";
  }

  protected function GetSubmitButton() {
    $caption = strtolower($this->submitcaption);
    return "<input type='submit' id='{$this->submitid}' name='{$this->submitid}' title='click to {$caption}' class='submitbutton' value='{$this->submitcaption}' />";
  }

  protected function GetCancelButton() {
    $caption = strtolower($this->cancelcaption);
    $url = $_SERVER['PHP_SELF'];
    if ($this->returnidname) {
      $url .= '?in=' . $this->returnidname;
    }
    return $this->GetCustomButton($caption, $this->cancelid, $this->cancelcaption, $url);
  }

  protected function ShowQuestion() {
    require_once 'class.question.php';
    $question = new questionmanager();
    return $question->ShowQuestion(CDN_PATH . 'questions');
  }

  protected function GetErrorMessage() {
    $errorcount = ($this->answeriswrong) ? 1 : 0;
    foreach ($this->fieldlist as $key => $field) {
      $errors = $field->errors;
      if ($errors) {
        $errorcount += count($errors);
      }
    }
    if ($errorcount) {
      $s = ($errorcount > 1) ? 's' : '';
      $ret = "<h2 class='error'>{$errorcount} error{$s} found</h2>\n";
    } else {
      $ret = '';
    }
    return $ret;
  }

  protected function AnswerIsCorrect() {
    require_once 'class.question.php';
    $question = new questionmanager();
    $ret = $question->IsAnswer();
    return $ret;
  }

  protected function GetFieldValue($key) {
    if (isset($this->fieldlist[$key])) {
      $field = $this->fieldlist[$key];
      $ret = $field->value;
    } else {
      $ret = false;
    }
    return $ret;
  }

  public function AddField($key, $field) {
    if ($field instanceof formbuilderbase) {
      $field->Post();
      $this->fieldlist[$key] = $field;
    }
    return $field;
  }

  public function Execute() {
    $this->AssignFields();
    if ($_POST) {
      $iscorrect = $this->AnswerIsCorrect();
      $this->answeriswrong = !$iscorrect;
      $done = isset($_POST[$this->submitid]) && $iscorrect;
    } else {
      $done = false;
    }
    if ($done) {
      $this->ProcessSubmit();
      $ret = "<p class='user'>Message was sent successfully</p>";
    } else {
      $ret = $this->Show();
    }
    return $ret;
  }

  public function Show() {
    $question = ($this->includequestion) ? $this->ShowQuestion() : '';
    $errormessage = $this->GetErrorMessage();
    $form = implode(' ', array(
      "<form name='frm-{$this->idname}' id='frm-{$this->idname}'",
      formbuilderbase::IncludeAttribute('action', $this->formaction),
      formbuilderbase::IncludeAttribute('method', $this->formmethod),
      formbuilderbase::IncludeAttribute('enctype', $this->formenctype),
      formbuilderbase::IncludeAttribute('title', $this->formtitle),
      formbuilderbase::IncludeAttribute('class', $this->formclass),
      '>')
    );
    $ret = array($errormessage, $form, '  <fieldset>');
    if ($this->formtitle) {
      $ret[] = "    <legend>{$this->formtitle}</legend>";
    };
    foreach ($this->fieldlist as $key => $field) {
      $fld = $field->GetFieldAsArray();
      $ret = array_merge($ret, $fld);
    }
    if ($this->includequestion) {
      if ($this->answeriswrong) {
        $ret[] = "<p class='error'>Your answer is incorrect. Please try again!</p>";
      }
      $ret[] = $question;
    }
    $ret[] = $this->GetSubmitButton();
    if ($this->showcancel) {
      $ret[] = $this->GetCancelButton();
    }
    $ret[] = '  </fieldset>';
    $ret[] = '</form>';
    return ArrayToString($ret);
  }
}

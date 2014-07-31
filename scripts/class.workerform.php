<?php
/**
  * abstract class for encapsulating all activity form fields
  * dana framework v.3
*/

define('BTN_SUBMIT', 'sub');
define('BTN_CANCEL', 'can');
define('BTN_BACK', 'bk');
define('BTN_CONFIRM', 'cf');

abstract class workerform extends workerbase { // activitybase {
  protected $contextdescription = '';
  protected $buttonmode = array(BTN_SUBMIT, BTN_CANCEL);
  protected $action = false;
  protected $itemid = false;
  protected $groupid = false; // item parent
  protected $account = false;
  protected $posting = false;
  protected $fieldlist = array();
  protected $hiddenfields = array();
  protected $sections;
  protected $submitcaption = 'Save Changes';
  protected $cancelcaption = 'Cancel Changes';
  protected $confirmcaption = 'Confirm';
  protected $formaction = '';
  protected $formmethod = 'post';
  protected $formenctype = 'multipart/form-data';
  protected $formtarget = '_self';
  protected $formtitle = '';
  protected $formclass = 'workerform';
  public $posted = false;

  function __construct() {
    parent::__construct();
    $this->account = account::$instance;
    $this->itemid = GetGet('rid', GetPost('rid'));
    $this->groupid = GetGet('pid', GetPost('pid'));
    $this->action = GetGet('act', GetPost('act'));
    $this->posting = count($_POST);
    $this->formaction = $_SERVER['SCRIPT_NAME'];
    $this->sections = array();
  }

  protected function DoPrepare() {
    $this->InitForm();
  }

  protected function ShowDebugInfo() {
    echo
      '<pre>' .
        "IDNAME: {$this->idname}<br>" .
        "ACTION: {$this->action}<br>" .
        "ITEMID: {$this->itemid}<br>" .
        "GROUPID: {$this->groupid}<br>" .
        "SHOWROOT: {$this->showroot}<br>" .
        "RETURNIDNAME: {$this->returnidname}<br>" .
      "</pre>\n";      
  }

  protected function DoEditor() {
    $this->AssignItemEditor(false);
  }

  protected function DoNewItem() {
    $this->AssignItemEditor(true);
  }

  protected function DoRemoveItem() {
    $this->AssignItemRemove(false);
  }

  protected function DoConfirm() {
    $this->AssignItemRemove(true);
  }

  protected function DoToggleVisibility() {
echo "<p>VISIBLE: {$this->itemid}</p>\n"; exit;
  }

  protected function DoMoveItemDown() {
echo "<p>MOVEDOWN: {$this->itemid}</p>\n"; exit;
  }

  protected function DoMoveItemUp() {
echo "<p>MOVEUP: {$this->itemid}</p>\n"; exit;
  }

  protected function DoNewsletterSend() {
echo "<p>NLSEND: {$this->itemid}</p>\n"; exit;
  }

  protected function ProcessAction($action) {
    if (!$action) {
      $this->AssignFieldDisplayProperties();
    } else {
      switch ($action) {
        case ACT_EDIT:
          $this->DoEditor();
          break;
        case ACT_NEW:
          $this->DoNewItem();
          break;
        case ACT_REMOVE:
          $this->DoRemoveItem();
          break;
        case ACT_CONFIRM:
          $this->DoConfirm();
          break;
        case ACT_VISTOGGLE:
          $this->DoToggleVisibility();
          break;
        case ACT_MOVEDOWN:
          $this->DoMoveItemDown();
          break;
        case ACT_MOVEUP:
          $this->DoMoveItemup();
          break;
        case ACT_NLSEND:
          $this->DoNewsletterSend();
          break;
      }
    }
  }

  public function Execute() {
//$this->ShowDebugInfo();
    if ($this->posting) {
      if (!$this->PostFields() && $this->IsValid()) {
        $this->SaveToTable();
        $this->AddMessage("Changes to {$this->contextdescription} Saved");
        $this->posted = true;
        $this->action = false;
        // re-init form
        $this->InitForm();
        $this->ProcessAction(false);
      } else {
        $this->AddErrorList();
        if ($this->manager->HasErrors()) {
          $this->AddMessage('Sorry, there were errors. Please rectify them and try again.');
          $this->AssignFieldDisplayProperties();
          $this->posted = false;
        } else {
          $this->AddMessage('No changes were found!');
          $this->posted = true;
        }
      }
    } else {
      $this->ProcessAction($this->action);
    }
  }

  protected function IsValid() {
    return true; // override to verify fields
  }

  protected function GetSubmitButton() {
    $caption = strtolower($this->submitcaption);
    return "<input type='submit' title='click to {$caption}' class='submitbutton' value='{$this->submitcaption}' />";
  }

  protected function GetCancelButton() {
    $caption = strtolower($this->cancelcaption);
    $url = $_SERVER['PHP_SELF'];
    if ($this->returnidname) {
      $url .= '?in=' . $this->returnidname;
    }
    return $this->GetCustomButton($caption, $this->cancelcaption, $url);
  }

  protected function GetConfirmationButton() {
    $caption = strtolower($this->confirmcaption);
    return "<input type='submit' title='click to {$caption}' class='submitbutton' value='{$this->confirmcaption}' />";
  }

  abstract protected function InitForm();
  abstract protected function AssignFieldDisplayProperties();
  abstract protected function PostFields();
  abstract protected function SaveToTable();
  abstract protected function AddErrorList();

  protected function AssignItemEditor($isnew) {}

  protected function AssignItemRemove($confirmed) {}

  public function NewSection($key, $caption, $description) {
    if (isset($this->sections[$key])) {
      $ret = $this->sections[$key];
    } else {
      $ret = array('caption' => $caption, 'description' => $description, 'list' => array());
      $this->sections[$key] = $ret;
    }
    return $ret;
  }

  public function AddField($fieldkey, $item, $table = false) {
    if ($item instanceof formbuilderbase) {
      $control = new workerformfieldcontrol();
      $control->SetControl($item);
      $this->fieldlist[$fieldkey] = $control;
      if ($table) {
        $control->BindControl($table);
      }
    } else {
      throw new Exception("Item {$fieldkey} is not a worker form field");
    }
    return $item;
  }

  public function AssignFieldToSection($sectionkey, $fieldkey) {
    if (isset($this->fieldlist[$fieldkey])) {
      $item = $this->fieldlist[$fieldkey];
      $this->sections[$sectionkey]['list'][] = $item;
    } else {
      throw new Exception("Form field not found: {$fieldkey}");
    }
  }

  protected function GetErrors() {
    $ret = array();
    $ret[] = '<ul>';
    foreach($this->errorlist as $line) {
      $ret[] = "<li>{$line}</li>";
    }
    $ret[] = '</ul>';
    return $ret;
  }

  protected function AddHiddenField($key, $value) {
    $this->hiddenfields[$key] = $value;
  }

  protected function GetHiddenFields() {
    $this->AddHiddenField('in', $this->idname);
    $this->AddHiddenField('act', $this->action);
    $this->AddHiddenField('rid', $this->itemid);
    if ($this->groupid) {
      $this->AddHiddenField('pid', $this->groupid);
    }
    $this->AddHiddenField('accountid', $this->account->ID());
    $ret = array();
    foreach($this->hiddenfields as $fieldkey => $fieldvalue) {
      $fld = new formbuilderhidden($fieldkey, $fieldvalue);
      $ret[] = $fld->Show();
    }
    return $ret;
  }

  public function AsArray() {
    $img = ($this->icon && file_exists($this->icon))
      ? "<img class='activitygroupicon' src='{$this->icon}' alt=''>" : '';
    $ret = array();
    $ret[] = "  <h2 class='activitygroup'>{$img}{$this->title}</h2>";
    $ret[] =
      "  <form name=\"frm-{$this->idname}\" id=\"{$this->idname}\"" .
        formbuilderbase::IncludeAttribute('action', $this->formaction) .
        formbuilderbase::IncludeAttribute('method', $this->formmethod) .
        formbuilderbase::IncludeAttribute('enctype', $this->formenctype) .
        formbuilderbase::IncludeAttribute('target', $this->formtarget) .
        formbuilderbase::IncludeAttribute('title', $this->formtitle) .
        formbuilderbase::IncludeAttribute('class', $this->formclass) .
      ">\n";

    $ret[] = "    <p class='activitygroupdescription'>{$this->activitydescription}<p>";
    foreach($this->sections as $section) {
      $caption = $section['caption'];
      $description = $section['description'];
      $ret[] = "    <div class='activitysection'>";
      $ret[] = "      <h3>{$caption}</h3>";
      $ret[] = "      <p class='helptext'>{$description}</p>";
      $ret[] = "      <section>";
      $list = $section['list'];
      foreach($list as $item) {
        $ret = array_merge($ret, $item->AsArray());
      }
      $ret[] = "      </section>";
      $ret[] = "    </div>";
    }
    $ret[] = "    <div class='clear'>&nbsp;</div>";
    $ret = array_merge($ret, $this->GetHiddenFields());
    // add submit / cancel
    $ret[] = "    <div class='activitysection'>";
    if (in_array(BTN_SUBMIT, $this->buttonmode)) {
      $ret[] = $this->GetSubmitButton();
    }
    if (in_array(BTN_CANCEL, $this->buttonmode)) {
      $ret[] = $this->GetCancelButton();
    }
    if (in_array(BTN_CONFIRM, $this->buttonmode)) {
      $ret[] = $this->GetConfirmationButton();
    }
    if (in_array(BTN_BACK, $this->buttonmode)) {
      $ret[] = $this->GetReturnButton();
    }
    $ret[] = "    </div>";
    $ret[] = "  </form>";
    return $ret;
  }

  public function Show() {
    echo implode("\r\n", $this->AsArray());
  }
}

<?php
namespace dana\worker;

/**
  * worker form class
  * abstract class for encapsulating all activity form fields
  * @version dana framework v.3
*/

abstract class workerform extends workerbase { // activitybase {
  const BTN_SUBMIT = 'sub';
  const BTN_CANCEL = 'can';
  const BTN_BACK = 'bk';
  const BTN_CONFIRM = 'cf';

  protected $table;
  protected $contextdescription = '';
  protected $buttonmode = array(self::BTN_SUBMIT, self::BTN_CANCEL);
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
  protected $skip = false; // skip saving form twice!
  public $fieldlabelclass = '';
  public $posted = false;
  // note: $idname is in workerbase

  function __construct() {
    parent::__construct();
    $this->account = \dana\table\account::$instance;
    $this->itemid = GetGet('rid', GetPost('rid'));
    $this->groupid = GetGet('pid', GetPost('pid'));
    $this->action = GetGet('act', GetPost('act'));
    $this->posting = count($_POST);
    $this->formaction = $_SERVER['SCRIPT_NAME'];
    $this->sections = array();
    $this->returnidname = 'IDNAME_ACCMGT_SUMMARY';
  }

  protected function AssignFormAction() {
    $list = array(
      'rid' => $this->itemid,
      'pid' => $this->groupid,
      'act' => $this->action
    );
    $ret = '';
    foreach($list as $key => $value) {
      if ($value) {
        $ret .= "&amp;{$key}={$value}";
      }
    }
    $this->formaction = $_SERVER['SCRIPT_NAME'] . "?in={$this->idname}{$ret}";
  }

  protected function DoPrepare() {
    $this->AssignFormAction();
    $this->InitForm();
  }

  protected function AssignIfBlank($fld, $value) {
    if ($fld instanceof \dana\formbuilder\formbuilderbase && $this->table instanceof \dana\table\basetable) {
      if (!trim($fld->value)) {
        $this->table->SetFieldValue($fld->name, $value);
      }
    }
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

  protected function ProcessAction($action = false) {
    if (!$action) {
      $this->AssignFieldDisplayProperties();
    } else {
      switch ($action) {
        case \dana\worker\workerbase::ACT_EDIT:
          $this->DoEditor();
          break;
        case \dana\worker\workerbase::ACT_NEW:
          $this->DoNewItem();
          break;
        case \dana\worker\workerbase::ACT_REMOVE:
          $this->DoRemoveItem();
          break;
        case \dana\worker\workerbase::ACT_CONFIRM:
          $this->DoConfirm();
          break;
        case \dana\worker\workerbase::ACT_VISTOGGLE:
          $this->DoToggleVisibility();
          break;
        case \dana\worker\workerbase::ACT_MOVEDOWN:
          $this->DoMoveItemDown();
          break;
        case \dana\worker\workerbase::ACT_MOVEUP:
          $this->DoMoveItemup();
          break;
        case \dana\worker\workerbase::ACT_NLSEND:
          $this->DoNewsletterSend();
          break;
      }
    }
  }

  protected function Notify($postsucceeded) {}

  protected function AssignBlankFieldvalue($fld, $value) {
    if ($fld && ($fld instanceof \dana\formbuilder\formbuilderbase) && (!$fld->value)) {
      $fld->SetValue($value);
    }
  }

  protected function CheckForBlankValues() {}

  protected function SaveAndReset($table, $in) {
    // back to parent worker
    foreach($_POST as $pkey => $pval) {
      unset($_POST[$pkey]);
    }
    foreach($_GET as $gkey => $gval) {
      if ($gkey == 'rid') {
        $_GET[$gkey] = $this->groupid;
      } else {
        unset($_GET[$gkey]);
      }
    }
    $ret = ($table instanceof \dana\table\basetable) ? (int) $table->StoreChanges() : 0;
    $this->itemid = $this->groupid;
    $this->groupid = false;
    $this->action = false;
    $this->posting = false;
    $this->idname = $in;
    return $ret;
  }

  public function Execute() {
//$this->ShowDebugInfo();
    if ($this->posting) {
      if (($this->action == \dana\worker\workerbase::ACT_EDIT) || ($this->action == \dana\worker\workerbase::ACT_NEW)) {
        $this->CheckForBlankValues();
      }
      if (!$this->PostFields() && $this->IsValid()) {
        if (!$this->skip) {
          switch ($this->SaveToTable()) {
            case -1: // error - lasterror
              $msg = 'Error: ' . $this->table->lasterror['msg'];
              break;
            case -2: // insert - new row
              $msg = 'Added successfully';
              $this->idname = $this->returnidname;
              break;
            case 0: // no change
              $msg = 'No changes found';
              $this->idname = $this->returnidname;
              break;
            default: // update - existing row updated
              $msg = 'Changes saved successfully';
              $this->idname = $this->returnidname;
              break;
          }
          $this->AddMessage($msg); //to {$this->contextdescription} Saved");
          $this->skip = true; // don't do this section twice! (work around needs fixing)
        }
        $this->posted = true;
        $this->action = false;
        // re-init form
        $this->InitForm();
        $this->ProcessAction();
      } else {
        $this->AddErrorList();
        if ($this->manager->HasErrors()) {
          $this->AddMessage('Sorry, there were errors. Please rectify them and try again.');
//          $this->InitForm();
//          $this->AssignFieldDisplayProperties();
          $this->posted = false;
$this->ProcessAction($this->action);
        } else {
          $this->AddMessage('No changes were found!');
          $this->posted = true;
          $this->idname = 'IDNAME_ACCMGT_SUMMARY';
        }
        $this->Notify($this->posted);
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

  public function NewSection($key, $caption, $description, $fieldlabelclass = '') {
    if (isset($this->sections[$key])) {
      $ret = $this->sections[$key];
    } else {
      $ret = array(
        'caption' => $caption, 'description' => $description,
        'list' => array(), 'fieldlabelclass' => $fieldlabelclass
      );
      $this->sections[$key] = $ret;
    }
    return $ret;
  }

  public function AddField($fieldkey, $item, $table = false) {
    if ($item instanceof \dana\formbuilder\formbuilderbase) {
      $control = new \dana\worker\workerformfieldcontrol();
      $control->SetControl($item);
      $this->fieldlist[$fieldkey] = $control;
      if ($table) {
        $control->BindControl($table);
      }
    } else {
      throw new \Exception("Item {$fieldkey} is not a worker form field");
    }
    return $item;
  }

  public function AssignFieldToSection($sectionkey, $fieldkey, $fieldlabelclass = '') {
    if (isset($this->fieldlist[$fieldkey])) {
      $item = $this->fieldlist[$fieldkey];
      $this->sections[$sectionkey]['list'][] = $item;
    } else {
      throw new \Exception("Form field not found: {$fieldkey}");
    }
  }

  protected function GetTargetNameFromMedia($mediaid) {
    $media = new \dana\table\media($mediaid);
    if ($media->exists) {
      $ret = array(
        'imgname' => $media->GetFieldValue('imgname'),
        'thumbnail' => $media->GetFieldValue('thumbnail'),
        'filename' => $media->GetFieldValue('originalname'),
        'size' => $media->GetFieldValue('imgsize')
      );
    } else {
      $ret = false;
    }
    return $ret;
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
      $fld = new \dana\formbuilder\formbuilderhidden($fieldkey, $fieldvalue);
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
      \dana\formbuilder\formbuilderbase::IncludeAttribute('action', $this->formaction) .
      \dana\formbuilder\formbuilderbase::IncludeAttribute('method', $this->formmethod) .
      \dana\formbuilder\formbuilderbase::IncludeAttribute('enctype', $this->formenctype) .
      \dana\formbuilder\formbuilderbase::IncludeAttribute('target', $this->formtarget) .
      \dana\formbuilder\formbuilderbase::IncludeAttribute('title', $this->formtitle) .
      \dana\formbuilder\formbuilderbase::IncludeAttribute('class', $this->formclass) .
      ">\n";
    $ret[] = "    <p class='activitygroupdescription'>{$this->activitydescription}<p>";
    foreach($this->sections as $section) {
      $caption = (isset($section['caption']) && $section['caption']) ? ucwords(strtolower($section['caption'])) : false;
      $description = (isset($section['description'])) ? $section['description'] : false;
      $ret[] = "    <div class='activitysection'>";
      if ($caption) {
        $ret[] = "      <h3>{$caption}</h3>";
      }
      if ($description) {
        $ret[] = "      <p class='helptext'>{$description}</p>";
      }
      $ret[] = "      <section>";
      $list = $section['list'];
      foreach($list as $item) {
        $item->labelclass = isset($section['fieldlabelclass']) ? $section['fieldlabelclass'] : false;
        $ret = array_merge($ret, $item->AsArray());
      }
      $ret[] = "      </section>";
      $ret[] = "    </div>";
    }
    $ret[] = "    <div class='clear'>&nbsp;</div>";
    $ret = array_merge($ret, $this->GetHiddenFields());
    // add submit / cancel
    if (is_array($this->buttonmode) && count($this->buttonmode)) {
      $buttonlist = array();
      if (in_array(self::BTN_SUBMIT, $this->buttonmode)) {
        $buttonlist[] = $this->GetSubmitButton();
      }
      if (in_array(self::BTN_CANCEL, $this->buttonmode)) {
        $buttonlist[] = $this->GetCancelButton();
      }
      if (in_array(self::BTN_CONFIRM, $this->buttonmode)) {
        $buttonlist[] = $this->GetConfirmationButton();
      }
//      if (in_array(self::BTN_BACK, $this->buttonmode)) {
//        $buttonlist[] = $this->GetReturnButton();
//      }
      if (count($buttonlist)) {
        $ret[] = "    <div class='activitysection'>";
        $ret = array_merge($ret, $buttonlist);
        $ret[] = "    </div>";
      }
    }
    $ret[] = "  </form>";
    return $ret;
  }

  public function Show() {
    echo ArrayToString($this->AsArray());
  }

  protected function AccountID() {
    return $this->account->ID();
  }
}

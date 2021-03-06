<?php
namespace dana\worker;

/**
  * base activity worker
  * @version dana framework v.3
*/

abstract class workerbase {
  const ACT_NEW = 'n';
  const ACT_EDIT = 'e';
  const ACT_REMOVE = 'r';
  const ACT_LIST = 'l'; // not new/edit/remove
  const ACT_CONFIRM = 'cf'; // booking
  const ACT_CANCEL = 'cn';  // booking
  const ACT_VISTOGGLE = 'v';
  const ACT_MOVEDOWN = 'd';
  const ACT_MOVEUP = 'u';
  const ACT_NLSEND = 'ns';

  protected $icon;
  protected $title = 'worker title';
  protected $activitydescription = 'This is the default description';
  protected $manager = false;
  protected $returncaption = 'Back';
  protected $returnidname = false;
  protected $returnaction = false;
  protected $previousidname = false;
  protected $controlmanager = false;

  public $idname = false;
  public $showroot = false;
  public $redirect = false;

  public function __construct() {
//    global $activitymanager;
    $this->manager = \dana\activity\controlmanager::$activitymanager;
  }

  public function SetIDName($idname) {
    if ($this->idname != $idname) {
      $this->idname = $idname;
      $this->DoPrepare();
    }
    $this->controlmanager = \dana\activity\controlmanager::$instance;
  }

  public function AddMessage($msg) {
    if ($this->manager) {
      $this->manager->AddMessage($msg);
    }
  }

  public function AddError($err) {
    if ($this->manager) {
      $this->manager->AddError($err);
    }
  }

  public function AddErrors($errs) {
    if ($this->manager) {
      foreach($errs as $err) {
        $this->manager->AddError($err);
      }
    }
  }

  protected function GetCustomButton($caption, $value, $url, $newwindow = false) {
    if ($newwindow) {
      $event = "javascript:window.open('{$url}', '_blank');";
    } else {
      $event = "javascript:window.open('{$url}', '_self');";
    }
    $click = 'onclick="' . $event . '"';
    return "<input type='button' title='{$caption}' class='fieldbutton' value='{$value}' {$click} />";
  }

  protected function GetReturnButton($newidname = 'IDNAME_ACCMGT_SUMMARY') {
    if ($newidname && $newidname != $this->returnidname) {
      $this->returnidname = $newidname;
    }
    $caption = strtolower($this->returncaption);
    $url = $_SERVER['PHP_SELF'];
    if ($this->returnidname) {
      $url .= '?in=' . $this->returnidname;
      if ($this->returnaction) {
        $url .= '&amp;act=' . $this->returnaction;
      }
    }
    return $this->GetCustomButton($caption, $this->returncaption, $url);
  }

  public function GetControlButton($idname, $caption) {
    $url = $_SERVER['PHP_SELF'];
    if ($idname) {
      $url .= '?in=' . $idname;
    }
    return $this->GetCustomButton($caption, $caption, $url);
  }

  abstract protected function DoPrepare();
  abstract public function Execute();
  abstract public function AsArray();
}

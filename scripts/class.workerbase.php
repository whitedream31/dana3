<?php

/**
  * base activity worker
  * dana framework v.3
*/

abstract class workerbase {
  protected $idname = false;
  protected $icon;
  protected $title = 'worker title';
  protected $activitydescription = 'This is the default description';
  protected $manager = false;
  protected $returncaption = 'Back';
  protected $returnidname = false;
  public $showroot = false;

  public function __construct() {
    global $activitymanager;
    $this->manager = $activitymanager;
  }

  public function SetIDName($idname) {
    $this->idname = $idname;
    $this->DoPrepare();
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
    return "<input type='button' title='{$caption}' class='actionbutton' value='{$value}' {$click} />";
  }

  protected function GetReturnButton() {
    $caption = strtolower($this->returncaption);
    $url = $_SERVER['PHP_SELF'];
    if ($this->returnidname) {
      $url .= '?in=' . $this->returnidname;
    }
    return $this->GetCustomButton($caption, $this->returncaption, $url);
  }

  abstract protected function DoPrepare();
  abstract public function Execute();
  abstract public function AsArray();
}

<?php
/**
  * abstract class for all activity classes
  * dana framework v.3
*/

abstract class activitybase {
  public $idname;
  public $description;

  function __construct($idname) {
    $this->idname = $idname;
  }

  public function GetDescription($class) {
    return ($this->description)
      ? "  <p class='{$class}'>{$this->description}</p>"
      : '';
  }

  abstract public function AsArray();
  abstract public function Show();
}

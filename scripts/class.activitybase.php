<?php
namespace dana\activity;

/**
  * abstract class for all activity classes
  * @version dana framework v.3
*/

abstract class activitybase {
  const ACTSTATUS_OK = 'ok';
  const ACTSTATUS_WARNING = 'warn';
  const ACTSTATUS_ERROR = 'err';

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

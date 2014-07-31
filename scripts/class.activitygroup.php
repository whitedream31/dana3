<?php
/**
  * activity group (static header)
  * dana framework v.3
*/

class activitygroup extends activitybase {
  public $caption = '(caption)';
  public $icon = false;

  protected $items = array();

  public function AddItem($item) {
    if ($item instanceof activitybase) {
      $this->items[] = $item;
    }
  }

  public function AsArray() {
    $img = ($this->icon && file_exists($this->icon))
      ? "<img class='activitygroupicon' src='{$this->icon}' alt=''>" : '';
    $ret = array();
    //$ret[] = "<section class='activitygroup'>";
    $ret[] = "  <h3 class='activitygroup'>{$img}{$this->caption}</h3>";
    $ret[] = "  <div class='actvitysection'>";
    $ret[] = $this->GetDescription('activitygroupdescription');
    foreach($this->items as $item) {
      $ret = array_merge($ret, $item->AsArray());
    }
    $ret[] = "<div class='clear'>&nbsp;</div>";
    $ret[] = "</div>";
    //$ret[] = "</section>";
    return $ret;
  }

  public function Show() {
    echo implode("\r\n", $this->AsArray());
  }
}

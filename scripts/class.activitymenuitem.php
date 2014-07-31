<?php
/**
  * activity menu item (clickable - add to group)
  * dana framework v.3
*/

class activitymenuitem extends activitybase {
  public $caption = '(caption)';
  public $targetidname = false;
  public $title = false;
  public $status = STATUS_OK;
  public $accesskey = false;

  protected function GetStatusImage() {
    switch($this->status) {
      case STATUS_OK:
        $icon = 'st_ok.png';
        break;
      case STATUS_WARNING:
        $icon = 'st_warn.png';
        break;
      case STATUS_ERROR:
        $icon = 'st_err.png';
        break;
      default:
        $icon = false;
        break;
    }
    return ($icon) ? "<img src='images/{$icon}' alt=''>" : '';
  }

  public function AsArray() {
    $link = $_SERVER['PHP_SELF'] . "?in={$this->idname}";
    $title = ($this->title) ? $this->title : 'click to continue';
    $icon = $this->GetStatusImage();
    $key = ($this->accesskey) ? "<span class='shortcut'>({$this->accesskey})</span>" : '';
    $accesskey = ($this->accesskey) ? " accesskey='{$this->accesskey}'" : '';
    $url = "<a href='{$link}'{$accesskey} title='{$title}'>{$icon}{$this->caption}</a>";
    $ret = array();
    $ret[] = "<div>";
    $ret[] = "  <p class='activitymenuitem'>{$url}{$key}</p>";
    $ret[] = $this->GetDescription('activityitemdescription');
    $ret[] = "  <div class='clear'>&nbsp;</div>";
    $ret[] = "</div>";
    return $ret;
  }

  public function Show() {
    echo implode("\r\n", $this->AsArray());
  }
}

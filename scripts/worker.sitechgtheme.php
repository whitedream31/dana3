<?php
namespace dana\worker;

require_once 'class.workerbase.php';

/**
  * worker site change theme
  * @version dana framework v.3
*/

class workersitechgtheme extends workerbase {
  const THEMESUITABAILTYTYPE_SIMPLE = 1;
  const THEMESUITABAILTYTYPE_REGULAR = 2;
  const THEMESUITABAILTYTYPE_ADVANCED = 3;

  protected $account = false;
  protected $theme;
  protected $themeid;
  protected $themesuitability = self::THEMESUITABAILTYTYPE_SIMPLE;

  function __construct() {
    parent::__construct();
    $this->account = \dana\table\account::StartInstance();
    $this->themeid = $this->account->GetFieldValue('themeid');
    $this->theme = new \dana\table\theme($this->themeid);
  }

  protected function DoPrepare() {
    $this->icon = 'images/sect_site.png';
    $this->title = 'Select Theme';
    $this->activitydescription = array(
      'Please choose a theme for your mini-website.',
      'A theme is the overall design - the look-and-feel of your pages. ' .
      'It describes to the visitors browser which fonts, colours, spacing etc to use.',
      'There are many themes to choose from but some are designed for different types ' .
      'of content; most are for general purpose but others are suited for a specific ' .
      'type of business. The themes are grouped into three main areas. Click on ' .
      'the group name to see the themes.');
  }

  public function Execute() {
  }

  private function GetThemeCompatibilityList() {
    $ret = array();
    $suitabilitytypes = array(
      self::THEMESUITABAILTYTYPE_SIMPLE,
      self::THEMESUITABAILTYTYPE_REGULAR,
      self::THEMESUITABAILTYTYPE_ADVANCED
    );
    $url = $_SERVER['PHP_SELF'] . '?in=' . $this->idname . '&amp;pid=';
    $ret[] = "    <ul>";
    foreach($suitabilitytypes as $stid) {
      switch ($stid) {
        //case THEMESUITABAILTYTYPE_SIMPLE:
        case self::THEMESUITABAILTYTYPE_REGULAR:
          $stname = 'Regular';
          $stmessage = 'recommended if you have between 3 and 5 pages';
          break;
        case self::THEMESUITABAILTYTYPE_ADVANCED:
          $stname = 'Advanced';
          $stmessage = 'recommended if you have lots (more than 5) pages';
          break;
        default:
          $stname = 'Simple';
          $stmessage = 'recommended if your have a single page or less than 4 pages';
          break;
      }
      if ($stid == $this->themesuitability) {
        $link = $stname;
      } else {
        $event = $url . $stid;
        $link = '<a href="' . $url . $stid . '" title="click to see ' . $stname . ' themes">' . $stname . '</a>';
      }
      $ret[] = '      <li><strong>' . $link . '</strong> &mdash; ' . $stmessage . '</li>';
    }
    $ret[] = "    </ul>";
    return $ret;
  }

  private function ShowThemes($suitability) {
    $ret = array();
    $selectedthemeid = $this->themeid;
    $ret[] = '<div id="themelist">';
    $themelist = $this->theme->FindThemes($suitability);
    $imgpath = '../themes/';
    $mainurl = $_SERVER['PHP_SELF'] . '?in=' . $this->idname . '&amp;rid=';
    foreach ($themelist as $id) {
      $theme = new \dana\table\theme($id);
      $imgfilename = $imgpath . $theme->url . '/' . $theme->url . '.png';
      if (file_exists($imgfilename)) {
        $currentclass = ($id == $selectedthemeid) ? ' selectedtheme' : '';
        $tagid = 'id="thm' . $id . '"';
        $img = '<img ' . $tagid . ' src="' . $imgfilename . '" alt="' . $theme->description . '" ' .
          'width="' . \dana\table\theme::THEME_THUMBNAIL_WIDTH . '" height="' . \dana\table\theme::THEME_THUMBNAIL_HEIGHT . '">';
        $head = '<h3>' . $theme->description . '</h3>';
        $url = '<a href="' . $mainurl . $theme->ID() . '" title="click to choose ' . $theme->description . '">' . $head . $img . '</a>';
        $ret[] = '  <div class="themeitem' . $currentclass . '">' . $url . '</div>';
      }
    }
    $ret[] = '</div>';
    return $ret;
  }

  protected function GetButton($name, $caption, $desc) {
    return "<a href='#{$name}' title='{$desc}'>{$caption}</a>";
  }

  public function AsArray() {
    $img = ($this->icon && file_exists($this->icon))
      ? "<img class='activitygroupicon' src='{$this->icon}' alt=''>" : '';

    $ret = array();
    $ret[] = '    <a id="top" name="top"></a>';
    $ret[] = "    <h2 class='activitygroup'>{$img}{$this->title}</h2>";
    $ret[] = '    <div>';
    foreach($this->activitydescription as $line) {
      $ret[] = "    <p class='activitygroupdescription'>{$line}<p>";
    }
    $ret[] = '    </div>';
    $ret[] = "    <div>";
    $ret = array_merge($ret, $this->GetThemeCompatibilityList());
    $ret[] = '    </div>';

    $ret[] = '    <div>';
    $ret[] = $this->GetButton('bottom', 'Bottom', 'click to go down to the bottom of the page', '#bottom');
//    $ret[] = '    </div>';
    $ret = array_merge($ret, $this->ShowThemes($this->themesuitability));
    $ret[] = '    <div style="clear: both; float: none;"></div><hr>';
//    $ret[] = '    <div>';
    $ret[] = $this->GetButton('top', 'Top', 'click to go back to the top', '#top');
//    $ret[] = '    </div>';
    $ret[] = '    <a id="bottom" name="bottom"></a>';

    $ret[] = $this->GetReturnButton();
    $ret[] = "    </div>";
    return $ret;
  }

}

$worker = new workersitechgtheme();

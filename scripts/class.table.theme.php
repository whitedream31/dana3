<?php
// theme container class for MyLocalSmallBusiness
// written by Ian Stewart (c) 2012 Whitedream Software
// created: 3 dec 2012
// modified: 29 jul 2014

require_once 'class.basetable.php';

//require_once('library.php');

define('THEME_SIMPLE', 1);
define('THEME_COMMON', 2);
define('THEME_PRIME', 3);

define('THEME_THUMBNAIL_WIDTH', 170);
define('THEME_THUMBNAIL_HEIGHT', 220);

// theme table class
class theme extends idtable {
  public $ref;
  public $description;
  public $url;
  public $suitability;

  public $exists;

  function __construct($id = 0) {
    parent::__construct('theme', $id);
  }

  protected function AssignFields() {
    parent::AssignFields();
    $this->AddField(FN_REF, DT_REF);
    $this->AddField(FN_DESCRIPTION, DT_DESCRIPTION);
    $this->AddField('url', DT_STRING);
    $this->AddField('suitability', DT_INTEGER);
    $this->AddField('pagewidth', DT_INTEGER);
    $this->AddField('contentwidth', DT_INTEGER);
    $this->AddField('sidewidth', DT_INTEGER);
  }

  protected function AfterPopulateFields() {
    $this->ref = $this->GetFieldValue(FN_REF);
    $this->description = $this->GetFieldValue(FN_DESCRIPTION);
    $this->url = $this->GetFieldValue('url');
    $this->suitability = $this->GetFieldValue('suitability');
  }

  public function FindThemes($suitability) {
    $query = "SELECT `id` FROM `theme` WHERE `suitability` = '{$suitability}' ORDER BY `ref`";
    $ret = database::PopulateList($query);
    return $ret;
  }

  public function ShowThemes($suitability) {
    if ($suitability < 1) {
      $suitability = $this->suitability;
    }
    $ra = ReadSetting(RUNACTION, 0);
    $selectedthemeid = $this->ID();
    $ret = '<div id="themelist">' . CRNL;
    $themelist = $this->FindThemes($suitability);
    $imgpath = '../themes/';
    $mainurl = $_SERVER['PHP_SELF'] . '?ra=' . $ra . '&amp;' . THEMECHOSEN . '=';
    foreach ($themelist as $id) {
      $theme = new theme($id);
      $imgfilename = $imgpath . $theme->url . '/' . $theme->url . '.png';
      if (file_exists($imgfilename)) {
        $currentclass = ($id == $selectedthemeid) ? ' selectedtheme' : '';
        $tagid = 'id="thm' . $id . '"';
        $img = '<img ' . $tagid . ' src="' . $imgfilename . '" alt="' . $theme->description . '" ' .
          'width="' . THEME_THUMBNAIL_WIDTH . '" height="' . THEME_THUMBNAIL_HEIGHT . '">';
        $head = '<h3>' . $theme->description . '</h3>';
        $url = '<a href="' . $mainurl . $theme->ID() . '" title="click to choose ' . $theme->description . '">' . $head . $img . '</a>';
        $ret .= '  <div class="themeitem' . $currentclass . '">' . $url . '</div>' . CRNL;
      }
    }
    $ret .= '</div>' . CRNL;
//      '<script type="text/javascript">AssignThemeSelectionEvent(' . account::$instance->ID() . ')</script>' . CRNL;
    return $ret;
  }
}

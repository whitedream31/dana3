<?php
// theme container class for MyLocalSmallBusiness
// written by Ian Stewart (c) 2012 Whitedream Software
// created: 3 dec 2012
// modified: 29 jul 2014

require_once 'class.basetable.php';

//require_once('library.php');

// theme table class
class theme extends idtable {
//  const THEME_SIMPLE = 1;
//  const THEME_COMMON = 2;
//  const THEME_PRIME = 3;

  const THEME_THUMBNAIL_WIDTH = 170;
  const THEME_THUMBNAIL_HEIGHT = 220;

  public $ref;
  public $description;
  public $url;
  public $suitability;

//  public $exists;

  function __construct($id = 0) {
    parent::__construct('theme', $id);
  }

  protected function AssignFields() {
    parent::AssignFields();
    $this->AddField(basetable::FN_REF, self::DT_REF);
    $this->AddField(basetable::FN_DESCRIPTION, self::DT_DESCRIPTION);
    $this->AddField('url', self::DT_STRING);
    $this->AddField('suitability', self::DT_INTEGER);
    $this->AddField('pagewidth', self::DT_INTEGER);
    $this->AddField('contentwidth', self::DT_INTEGER);
    $this->AddField('sidewidth', self::DT_INTEGER);
  }

  protected function AfterPopulateFields() {
    $this->ref = $this->GetFieldValue(basetable::FN_REF);
    $this->description = $this->GetFieldValue(basetable::FN_DESCRIPTION);
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
        $tagid = "id='thm{$id}'";
        $width = self::THEME_THUMBNAIL_WIDTH;
        $height = self::THEME_THUMBNAIL_HEIGHT;
        $img = "<img {$tagid} src='{$imgfilename}' alt='{$theme->description}'" .
          "width='{$width}' height='{$height}'>";
        $head = "<h3>{$theme->description}</h3>";
        $url = "<a href='{$mainurl}{$id}' title='click to choose {$theme->description}'>{$head}{$img}</a>";
        $ret .= "  <div class='themeitem{$currentclass}'>{$url}</div>" . CRNL;
      }
    }
    $ret .= '</div>' . CRNL;
//      '<script type="text/javascript">AssignThemeSelectionEvent(' . account::$instance->ID() . ')</script>' . CRNL;
    return $ret;
  }
}

<?php
require_once('class.table.account.php');
//require_once('class.table.contact.php');
//require_once('class.formeditor.php');
require_once('class.table.theme.php');

function DoRunAction() {
  $themeid = account::StartInstance()->GetFieldValue('themeid');
  $theme = new theme($themeid);
  $themesuitability = ReadSetting(THEMESUITABAILTY, $theme->GetFieldValue('suitability'));
  $_SESSION['activeactivity'] = ASI_SITEMANAGEMENT;
  echo
    '  <a id="top" name="top"></a>' . CRNL .
    '  <h2>Select Theme</h2>' . CRNL .
    '  <div class="helptext">' . CRNL .
    '    <p>Please choose a theme for your mini-website. A theme is the overall design - the look-and-feel of your pages. ' .
    '       It describes to the visitors browser which fonts, colours, spacing etc to use.</p>' . CRNL .
    '    <p>There are many themes to choose from but some are designed for different types of content; most are for general ' .
    '       purpose but others are suited for a specific type of business.</p>' . CRNL .
    '    <p>The themes are grouped into three main areas. Click on the group name to see the themes.</p>' . CRNL .
    '    <ul>' . CRNL;
  $suitabilitytypes = array(THEMESUITABAILTYTYPE_SIMPLE, THEMESUITABAILTYTYPE_REGULAR, THEMESUITABAILTYTYPE_ADVANCED);
  $ra = ReadSetting(RUNACTION, 0);
  $url = $_SERVER['PHP_SELF'] . '?ra=' . $ra . '&amp;' . THEMESUITABAILTY . '=';
  foreach($suitabilitytypes as $stid) {
    switch ($stid) {
      //case THEMESUITABAILTYTYPE_SIMPLE:
      case THEMESUITABAILTYTYPE_REGULAR:
        $stname = 'Regular';
        $stmessage = 'recommended if you have between 3 and 5 pages';
        break;
      case THEMESUITABAILTYTYPE_ADVANCED:
        $stname = 'Advanced';
        $stmessage = 'recommended if you have lots (more than 5) pages';
        break;
      default:
        $stname = 'Simple';
        $stmessage = 'recommended if your have a single page or less than 4 pages';
        break;
    }
    if ($stid == $themesuitability) {
      $link = $stname;
    } else {
      $event = $url . $stid;
      $link = '<a href="' . $url . $stid . '" title="click to see ' . $stname . ' themes">' . $stname . '</a>';
    }
    echo '      <li><strong>' . $link . '</strong> &mdash; ' . $stmessage . '</li>' . CRNL;
  }
  echo
    '    </ul>' . CRNL .
    '  </div>' . CRNL .
    '  <div>' . CRNL .
      ShowButton('btnbottom', 'Bottom', 'click to go down to the bottom of the page', '#bottom') .
      ShowButton('btncancel', 'Cancel', 'click to cancel and return to main control page', 'control.php') . CRNL .
    '  </div><hr>' . CRNL .
    $theme->ShowThemes($themesuitability) .
    '  <div style="clear: both; float: none;"></div><hr>' . CRNL .
    '  <div>' . CRNL .
      ShowButton('btntop', 'Top', 'click to go back to the top', '#top') .
      ShowButton('btncancel', 'Cancel', 'click to cancel and return to main control page', 'control.php') . CRNL .
//      '<a class="actionbutton" href="#top" title="click to go back to the top">Top</a>' . CRNL .
    '  </div>' . CRNL .
    '  <a id="bottom" name="bottom"></a>' . CRNL;
  return false;
}
?>
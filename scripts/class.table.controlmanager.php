<?php
namespace dana\activity;

//use dana\core, dana\table;

require_once 'class.basetable.php';

/**
  * control manager for the control page - uses the activity manager class
  * @version dana framework v.3
*/

//require_once 'class.table.controlitem.php';

class controlmanager extends \dana\table\idtable {
  const SESSION_ACTIVEITEMID = 'in';

  static public $instance;
  static public $list = array();
  static public $activeactionname;
  static public $currentidname;
  static public $activitymanager;

  function __construct($id = 0) {
    parent::__construct('controlmanager', $id);
    if (isset($_GET[self::SESSION_ACTIVEITEMID])) {
      self::$activeactionname = $_GET[self::SESSION_ACTIVEITEMID];
      $_SESSION[self::SESSION_ACTIVEITEMID] = self::$activeactionname;
    } else {
      if (isset($_SESSION[self::SESSION_ACTIVEITEMID])) {
        self::$activeactionname = $_SESSION[self::SESSION_ACTIVEITEMID];
      } else {
        self::$activeactionname = 'IDNAME_ACCMGT_SUMMARY'; // default
      }
    }
    self::$currentidname = self::$activeactionname; //constant('activitymanager::' . self::$activeactionname);
    self::$activitymanager = new activitymanager();
    self::PopulateItems();
  }

  static function StartInstance($id = 1) {
    if (!isset(self::$instance)) {
      self::$instance = new controlmanager($id);
    }
    return self::$instance;
  }

  protected function AfterPopulateFields() {}

  protected function AssignFields() {
    parent::AssignFields();
    $this->AddField('title', self::DT_STRING);
    $this->AddField(self::FN_DESCRIPTION, self::DT_DESCRIPTION);
    $this->AddField('stylesheet', self::DT_STRING);
    $this->AddField('footer', self::DT_STRING);
    $this->AddField(self::FN_STATUS, self::DT_STATUS);
  }

  static public function PopulateItems() {
    self::$list = array();
    $status = \dana\table\basetable::STATUS_ACTIVE;
    $query = "SELECT * FROM `controlitem` " .
      "WHERE `status` = '{$status}' ORDER BY `position`";
    $resource = \dana\core\database::Query($query);
    while ($line = $resource->fetch_assoc()) {
      $id = $line['id'];
      $title = $line['title'];
      $helptext = $line['helptext'];
      $icon = $line['icon'];
      $actionname = $line['actionname'];
      $parentid = $line['parentid'];
      self::$list[$id] = array(
        'title' => $title,
        'helptext' => $helptext,
        'icon' => $icon,
        'actionname' => $actionname,
        'parentid' => $parentid
      );
    }
    $resource->free();
  }

  static public function ShowUserStatus() {
    $account = \dana\table\account::$instance;
    if ($account->exists) {
      $username = $account->Contact()->GetFieldValue('username');
      $displayname = $account->Contact()->displayname;
      $url = '<a href="logout.php" title="log out">Log Out</a>';
      $msg = "Logged in as: <strong>{$username}</strong> &ndash;&nbsp; Hello {$displayname} &ndash; {$url}";
    } else {
      $url = '<a href="login.php" title="log in">Log In</a>';
      $msg = '<strong>NOT LOGGED IN</strong>&nbsp;- Please ' . $url;
    }
    echo $msg;
  }

  static public function ShowTitle() {
    echo self::StartInstance()->GetFieldValue('title');
  }

  static public function ShowFooter() {
    echo self::StartInstance()->GetFieldValue('footer');
  }

  static public function ShowDescription() {
    echo self::StartInstance()->GetFieldValue('description');
  }

  static public function ShowStyleSheet() {
    echo self::StartInstance()->GetFieldValue('stylesheet');
  }

  static public function ShowControlMenu() {
    $ret = array('<h2>Menu</h2>');
    $menuitems = array();
    $list = self::$list;
    foreach($list as $id => $item) {
//      $id = $item['id'];
      $title = $item['title'];
      $helptext = $item['helptext'];
      $helptag = "<span class='controlmenuhelp'>{$helptext}</span>";
      $icon = $item['icon'];
      $img = ($icon) ? "<img class='controlmenuicon' src='{$icon}' alt=''>" : '';
      $actionname = $item['actionname'];
      $actiontag = $_SERVER['PHP_SELF'] . '?' . self::SESSION_ACTIVEITEMID . '=' . $actionname;
      $url = "<a title='' href='{$actiontag}'>{$title}</a>";
      $titletag = "<span class='controlmenutitle'>{$url}</span>";
      $itemtags = "{$img}{$titletag}{$helptag}"; //<span class='controlmenuitem'>{$titletag}{$helptag}</span>";
      $parentid = $item['parentid'];
      $activeclass = (self::$activeactionname == $actionname) ? ' active' : '';
      if ($parentid) {
        $menuitems[] = "  <li class='controlmenu{$activeclass}'>{$itemtags}</li>";
      } else {
        if ($menuitems) {
          $ret[] = '<ul>';
          $ret = array_merge($ret, $menuitems);
          $ret[] = '</ul>';
          $menuitems = array();
        }
        $ret[] = "<p class='controlmenu{$activeclass}'>{$itemtags}</p>";
      }
    }
    if ($menuitems) {
      $ret[] = '<ul>';
      $ret = array_merge($ret, $menuitems);
      $ret[] = '</ul>';
      $menuitems = array();
    }
    echo ArrayToString($ret);
  }

  static public function ShowActiveItem() {
    echo self::$activitymanager->Show();
//    $ret = array('<h2>Active Item</h2>', "<p>" . self::$currentidname . "</p>");
//    echo ArrayToString($ret);
  }
}

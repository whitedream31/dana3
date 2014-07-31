<?php

define('STATUS_OK', 0);
define('STATUS_WARNING', 1);
define('STATUS_ERROR', 2);

define('IDNAME_CHANGEORGDETAILS', 'accchgorgdet');
define('IDNAME_CHANGECONDETAILS', 'accchgcondet');
define('IDNAME_CHANGELOGINPWD', 'accchglogin');

define('IDNAME_MANAGEAREASCOVERED', 'accmanareacovered');
define('IDNAME_MANAGEHOURSAVAILABLE', 'accmanhoursavail');
define('IDNAME_MANAGEADDRESSES', 'accmanaddress');
define('IDNAME_MANAGEPAGES', 'pgman');
define('IDNAME_SITEPREVIEW', 'sitepreview');
define('IDNAME_CHANGETHEME', 'sitechgtheme');
define('IDNAME_SITEUPDATE', 'siteupdate');
define('IDNAME_MANAGERATINGS', 'sitemanratings');
define('IDNAME_MANAGEGALLERIES', 'resmangalleries');
define('IDNAME_MANAGEFILES', 'resmanfiles');
define('IDNAME_MANAGEARTICLES', 'resmanarticles');
define('IDNAME_MANAGENEWLETTERS', 'resmannewsletters');
define('IDNAME_MANAGEBOOKINGS', 'resmanbookings');
define('IDNAME_MANAGEGUESTBOOKS', 'resmanguestbooks');
define('IDNAME_MANAGEPRIVATEAREAS', 'resmanprivateareas');
define('IDNAME_MANAGECALENDARDATES', 'resmancalendardates');

/**
  * activity manager - calls worker classes / shows data in activity area
  * dana framework v.3
*/

class activitymanager {
  protected $errorlist = array();
  protected $message = array();
  protected $accountgroup;
  protected $pagegroup;
  protected $sitegroup;
  protected $resourcegroup;
  protected $tophelptext = false;

  protected function AddGroup($idname, $icon, $caption, $desc) {
    $group = new activitygroup($idname);
    $group->caption = $caption;
    $group->description = $desc;
    $group->icon = $icon;
    return $group;
  }

  protected function AddItem($group, $idname, $status, $acckey, $caption, $desc) {
    $item = new activitymenuitem($idname);
    $item->caption = $caption;
    $item->description = $desc;
    $item->status = $status;
    $item->accesskey = $acckey;
    $group->AddItem($item);
    return $item;
  }

  protected function AssignItems() {
    // account group
    $this->accountgroup = $this->AddGroup(
      'accountdetails', 'images/sect_account.png', 'Account Details',
      'Change your account information, such as your name and contact details.');
    $this->AddItem(
      $this->accountgroup, IDNAME_CHANGEORGDETAILS, STATUS_OK, 'O',
      'Change Organisation Details', 'business name, categories etc');
    $this->AddItem(
      $this->accountgroup, IDNAME_CHANGECONDETAILS, STATUS_OK, 'C',
      'Change Contact Details', 'your name/email address etc');
    $this->AddItem(
      $this->accountgroup, IDNAME_CHANGELOGINPWD, STATUS_OK, 'L',
      'Change Login Password', 'the password to login into this site');
    $this->AddItem(
      $this->accountgroup, IDNAME_MANAGEAREASCOVERED, STATUS_OK, 'A',
      'Manage Areas Covered', 'areas you operate your business');
    $this->AddItem(
      $this->accountgroup, IDNAME_MANAGEHOURSAVAILABLE, STATUS_OK, 'H',
      'Manage Hours Available', 'hours your business is open');
    $this->AddItem(
      $this->accountgroup, IDNAME_MANAGEADDRESSES, STATUS_OK, 'D',
      'Manage Addresses', 'addresses you run your business from');
    // page group
    $this->pagegroup = $this->AddGroup(
      'pagemanager', 'images/sect_pages.png', 'Page Management',
      'Add, edit or delete your pages that make up your mini-website.');
    $this->AddItem(
      $this->pagegroup, IDNAME_MANAGEPAGES, STATUS_OK, 'P',
      'Manage Pages', 'web-pages that make up you minisite');
// datagrid here
// Add New Page
    // site group
    $this->sitegroup = $this->AddGroup(
      'sitemanager', 'images/sect_site.png', 'Site Management',
      'Preview your mini-website or change the look of your mini-website from dozens of designs.');
    $this->AddItem(
      $this->sitegroup, IDNAME_SITEPREVIEW, STATUS_OK, 'M',
      'Preview Mini-Site', 'your minisite as it looks now');
    $this->AddItem(
      $this->sitegroup, IDNAME_CHANGETHEME, STATUS_OK, 'T',
      'Change Theme', 'appearance of your minisite');
    $this->AddItem(
      $this->sitegroup, IDNAME_SITEUPDATE, STATUS_OK, 'U',
      'Update Mini-Site', 'make recent changes live');
    $this->AddItem(
      $this->sitegroup, IDNAME_MANAGERATINGS, STATUS_OK, 'R',
      'Manage Ratings', 'read / respond to customer comments');
    // resource group
    $this->resourcegroup = $this->AddGroup(
      'resource', 'images/sect_resources.png', 'Resources',
      'Resources are the features that your pages use to make your mini-website useful.');
    $this->AddItem(
      $this->resourcegroup, IDNAME_MANAGEGALLERIES, STATUS_OK, 'G',
      'Manage Galleries', 'add/edit/remove pictures');
    $this->AddItem(
      $this->resourcegroup, IDNAME_MANAGEFILES, STATUS_OK, 'F',
      'Manage Downloadable Files', 'add/remove files that can be downloaded by visitors');
    $this->AddItem(
      $this->resourcegroup, IDNAME_MANAGEARTICLES, STATUS_OK, 'I',
      'Manage Articles', 'blogs/articles for visitors to read');
    $this->AddItem(
      $this->resourcegroup, IDNAME_MANAGENEWLETTERS, STATUS_OK, 'N',
      'Manage Newsletters', 'subscribers and add/edit/remove newsletters');
    $this->AddItem(
      $this->resourcegroup, IDNAME_MANAGEBOOKINGS, STATUS_OK, 'B',
      'Manage Bookings', 'review recent and upcoming appointments');
    $this->AddItem(
      $this->resourcegroup, IDNAME_MANAGEGUESTBOOKS, STATUS_OK, 'K',
      'Manage Guestbooks', 'read/remove comments from visitors');
    $this->AddItem(
      $this->resourcegroup, IDNAME_MANAGEPRIVATEAREAS, STATUS_OK, 'V',
      'Manage Private Areas', 'add/edit/remove private pages and members');
    $this->AddItem(
      $this->resourcegroup, IDNAME_MANAGECALENDARDATES, STATUS_OK, 'S',
      'Manage Special Dates', 'important dates for your business');
  }

  protected function GetMessage($id, $lines) {
    $ret = array();
    $ret[] = "<section id='{$id}>";
    foreach($lines as $line) {
      $ret[] = "  <p>{$line}</p>";
    }
    $ret[] = "</section>";
    return $ret;
  }

  protected function ProcessByIDName($idname) {
    $ret = array();
    try {
      if ($idname) {
        $script = "worker.{$idname}.php";
        if (file_exists($script)) {
          $worker = false;
          include $script; // create worker as an object
          $worker->SetIDName($idname);
          if (!$worker->posted) {
            $ret = $worker->AsArray();
          } else {
            $ret = $this->ProcessRoot();
          }
        } else {
          $this->AddMessage("ERROR: IDName {$idname} not recognised");
          $this->AddMessage("ERROR: Script {$script} not found");
        }
      } else {
        $ret = $this->ProcessRoot();
      }
    } catch (Exception $e) {
      $this->AddMessage('Exception: ' . $e->getMessage() . ' in ' . $e->getFile() . ' at line ' . $e->getLine());
    }
    return $ret;
  }

  private function GetTopHelpText() {
    return array(
      "<p class='helptext'>",
      "Please choose from the menu below. Click on the group title to show more menu options. You can use the short-cut keys by holding down the 'Alt' (or 'Option' if your have an Apple Mac) key and pressing the key marked on the right (eg. <span class='shortcut'>(L)</span> to change your login password)</p>"
    );
  }

  private function ProcessRoot() {
    $this->AssignItems();
    $this->tophelptext = $this->GetTopHelpText();
    return array_merge(
      $this->accountgroup->AsArray(),
      $this->pagegroup->AsArray(),
      $this->sitegroup->AsArray(),
      $this->resourcegroup->AsArray()
    );
  }

  public function AddMessage($msg) {
    $this->message[] = $msg;
  }

  public function AddError($err) {
    $this->errorlist[] = $err;
  }

  public function HasErrors() {
    return (bool) $this->errorlist;
  }

  public function Show($idname) {
    $lines = $this->ProcessByIDName($idname);
    $ret = array();

    if ($this->message) {
      $ret[] = "  <section class='activityformmessage'>";
      foreach($this->message as $msg) {
        $ret[] = "    <li>{$msg}</li>";
      }
      $ret[] = "  </section>";
    }
    if ($this->errorlist) {
      $ret[] = "  <section class='activityformerrors'>";
      foreach($this->errorlist as $err) {
        $ret[] = "    <li>{$err}</li>";
      }
      $ret[] = "  </section>";
    }

    $ret[] = '    <section id="activityarea">';
    if ($this->tophelptext) {
      $ret = array_merge($ret, $this->tophelptext);
    }
    $ret[] = '      <div id="activitycontent">';
    $ret = array_merge($ret, $lines);
    $ret[] = '      </div>';
    $ret[] = '    </section>';
    echo implode("\r\n", $ret);
  }
}

$activitymanager = new activitymanager();

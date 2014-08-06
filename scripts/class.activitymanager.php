<?php

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

  protected function AddGroup($idname, $icon, $caption, $desc) {
    $group = new activitygroup($idname);
    $group->caption = $caption;
    $group->description = $desc;
    $group->icon = $icon;
    return $group;
  }

  protected function AddItem($group, $idname, $caption, $desc) {
    $item = new activitymenuitem($idname);
    $item->caption = $caption;
    $item->description = $desc;
    $group->AddItem($item);
    return $item;
  }

  protected function AssignItems() {
    // account group
    $this->accountgroup = $this->AddGroup(
      'accountdetails', 'images/sect_account.png', 'Account Details',
      'Change your account information, such as your name and contact details.');
    $this->AddItem(
      $this->accountgroup, IDNAME_CHANGEORGDETAILS,
      'Change Organisation Details', 'business name, categories etc');
    $this->AddItem(
      $this->accountgroup, IDNAME_CHANGECONDETAILS,
      'Change Contact Details', 'your name/email address etc');
    $this->AddItem(
      $this->accountgroup, IDNAME_CHANGELOGINPWD,
      'Change Login Password', 'the password to login into this site');
    $this->AddItem(
      $this->accountgroup, IDNAME_MANAGEAREASCOVERED,
      'Manage Areas Covered', 'areas you operate your business');
    $this->AddItem(
      $this->accountgroup, IDNAME_MANAGEHOURSAVAILABLE,
      'Manage Hours Available', 'hours your business is open');
//    $this->AddItem(
//      $this->accountgroup, IDNAME_MANAGEADDRESSES,
//      'Manage Addresses', 'addresses you run your business from');
    // page group
    $this->pagegroup = $this->AddGroup(
      'pagemanager', 'images/sect_pages.png', 'Page Management',
      'Add, edit or delete your pages that make up your mini-website.');
    $this->AddItem(
      $this->pagegroup, IDNAME_MANAGEPAGES,
      'Manage Pages', 'web-pages that make up you minisite');
// datagrid here
// Add New Page
    // site group
    $this->sitegroup = $this->AddGroup(
      'sitemanager', 'images/sect_site.png', 'Site Management',
      'Preview your mini-website or change the look of your mini-website from dozens of designs.');
    $this->AddItem(
      $this->sitegroup, IDNAME_SITEPREVIEW,
      'Preview Mini-Site', 'your minisite as it looks now');
    $this->AddItem(
      $this->sitegroup, IDNAME_CHANGETHEME,
      'Change Theme', 'appearance of your minisite');
    $this->AddItem(
      $this->sitegroup, IDNAME_SITEUPDATE,
      'Update Mini-Site', 'make recent changes live');
    $this->AddItem(
      $this->sitegroup, IDNAME_MANAGERATINGS,
      'Manage Ratings', 'read / respond to customer comments');
    // resource group
    $this->resourcegroup = $this->AddGroup(
      'resource', 'images/sect_resources.png', 'Resources',
      'Resources are the features that your pages use to make your mini-website useful.');
    $this->AddItem(
      $this->resourcegroup, IDNAME_MANAGEGALLERIES,
      'Manage Galleries', 'add/edit/remove pictures');
    $this->AddItem(
      $this->resourcegroup, IDNAME_MANAGEFILES,
      'Manage Downloadable Files', 'add/remove files that can be downloaded by visitors');
    $this->AddItem(
      $this->resourcegroup, IDNAME_MANAGEARTICLES,
      'Manage Articles', 'blogs/articles for visitors to read');
    $this->AddItem(
      $this->resourcegroup, IDNAME_MANAGENEWLETTERS,
      'Manage Newsletters', 'subscribers and add/edit/remove newsletters');
    $this->AddItem(
      $this->resourcegroup, IDNAME_MANAGEBOOKINGS,
      'Manage Bookings', 'review recent and upcoming appointments');
    $this->AddItem(
      $this->resourcegroup, IDNAME_MANAGEGUESTBOOKS,
      'Manage Guestbooks', 'read/remove comments from visitors');
    $this->AddItem(
      $this->resourcegroup, IDNAME_MANAGEPRIVATEAREAS,
      'Manage Private Areas', 'add/edit/remove private pages and members');
    $this->AddItem(
      $this->resourcegroup, IDNAME_MANAGECALENDARDATES,
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
          $worker->Execute();
          if ($worker->showroot) {
            $ret = $this->ProcessRoot();
          } else {
            $ret = $worker->AsArray();
          }
/*          if ($worker->posted) {
            $ret = $this->ProcessRoot();
          } else {
            $ret = $worker->AsArray();
          } */
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

  private function ProcessRoot() {
    $this->AssignItems();
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

  public function Show() {
    $idname = GetGet('in', GetPost('in', ''));
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
    $ret[] = '      <div id="activitycontent">';
    $ret = array_merge($ret, $lines);
    $ret[] = '      </div>';
    $ret[] = '    </section>';
    echo implode("\r\n", $ret);
  }
}

$activitymanager = new activitymanager();

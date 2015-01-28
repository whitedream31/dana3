<?php
//require_once 'class.workerform.php';
require_once 'class.workerbase.php';
//require_once 'class.formbuilderdatagrid.php';
//require_once 'class.formbuilderbutton.php';

/**
  * activity worker for site preview
  * dana framework v.3
*/

// site preview

class workersitepreview extends workerbase {
  protected $account = false;

  function __construct() {
    parent::__construct();
    $this->account = account::StartInstance();
  }

  protected function DoPrepare() {
    $this->icon = 'images/sect_site.png';
    $this->title = 'Preview Your Mini-Website';
    $this->activitydescription = 'Click on the link below to view you mini-website. It should only be used to see what the pages look like. Do not try to use it in a live environment.';
  }

  public function Execute() {
  }

  public function AsArray() {
    $img = ($this->icon && file_exists($this->icon))
      ? "<img class='activitygroupicon' src='{$this->icon}' alt=''>" : '';
    $nickname = $this->account->GetFieldValue('nickname');
    $previewlink = $this->account->WebsiteURL(false, true);
    $livelink = $this->account->WebsiteURL(true, false);
    $status = $this->account->GetCurrentStatus();
    $showpreview = false;
    $showlive = false;
    $statusclass = 'statuserror';
    switch ($status) {
      case account::ACCSTATUS_MODIFIED:
        $update = "<a href='" . $_SERVER['PHP_SELF'] . "?in=IDNAME_SITEUPDATE' title='update your account now'>Update</a>";
        $msg = "Your account has been modified. Please {$update} your pages to reflect recent changes.";
        $showpreview = true;
        break;
      case account::ACCSTATUS_UNCONFIRMED:
        $msg = 'You have not confirmed your account. We recently sent you an e-mail asking you to confirm your account. Please check your e-mail and find the message.';
        $showpreview = true;
        break;
      case account::ACCSTATUS_NOTEXISTS:
      case account::ACCSTATUS_OFFLINE:
        $msg = 'Your account is currently offline and not available to view the live mini-website.';
        break;
      case account::ACCSTATUS_PUBLISHED:
        $msg = 'Your account is currently active and online for everyone to see.';
        $showpreview = true;
        $showlive = true;
        $statusclass = 'statusok';
        break;
      case account::ACCSTATUS_PENDING:
        $msg = 'Your account has not been authorised yet. We will authorise your account as soon as possible. Check your e-mail to see if we have sent you a message.';
        $showpreview = true;
        break;
      case account::ACCSTATUS_DELETED:
        $msg = 'Sorry, but your account has been marked DELETED and will be removed shortly. If you wish to keep your account please contact us.';
        break;
      default: // ACCSTATUS_UNKNOWN:
        $msg = 'There is an unknown error with your account.';
        break;
    }
    $ret = array();
    $ret[] = "  <h2 class='activitygroup2'>{$img}{$this->title}</h2>";
    $ret[] = "    <div>";
    if ($showlive || $showpreview) {
      $ret[] = "      <p class='activitygroupdescription'>{$this->activitydescription}<p>";
      if ($showpreview) {
        $ret[] = "      <p>A <strong>PREVIEW</strong> version link for {$nickname} is {$previewlink}</p>";
      }
      if ($showlive) {
        $ret[] = "      <p>Your <strong>LIVE</strong> link is {$livelink}</p>";
      }
    }
    $ret[] = "      <p class={$statusclass}>Current status of your mini-website is {$this->account->StatusAsString($status)}</p>";
    $ret[] = "      <p>{$msg}</p>";
    $ret[] = $this->GetReturnButton();
    $ret[] = "    </div>";
    return $ret;
  }

}

$worker = new workersitepreview();

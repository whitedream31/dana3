<?php
//require_once 'class.workerform.php';
require_once 'class.workerbase.php';
//require_once 'class.formbuilderdatagrid.php';
//require_once 'class.formbuilderbutton.php';

/**
  * activity worker for site update
  * dana framework v.3
*/

// site update

class workersiteupdate extends workerbase {
  protected $account = false;

  function __construct() {
    parent::__construct();
    $this->account = account::StartInstance();
  }

  protected function DoPrepare() {
    $this->icon = 'images/sect_site.png';
    $this->title = 'Update Your Mini-Website';
    $this->activitydescription = array(
      'Click on the link below to update any recent changes you have made to your LIVE mini-website.',
      'Please check you are happy with the preview version first. <strong>Check for any grammar and spelling mistakes.</strong>');
  }

  public function Execute() {
  }

  private function GetUpdateButton() {
    $mainurl = $_SERVER['PHP_SELF'] . '?in=' . $this->idname . '&amp;act=live';
    return '<a class="btnupdate" href="' . $mainurl . '" title="click to update your mini-website">UPDATE NOW</a>';
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
        $update = "<a href='" . $_SERVER['PHP_SELF'] . $livelink .= '?in=' . activitymanager::IDNAME_SITEUPDATE . "' title='update your account now'>Update</a>";
        $msg = "Your account has been modified. Please {$update} your pages to reflect recent changes.";
        $showpreview = true;
        $showlive = true; // different to 'preview site' worker
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
    $ret[] = "  <div>";
    if ($showlive || $showpreview) {
      foreach($this->activitydescription as $line) {
        $ret[] = "      <p class='activitygroupdescription'>{$line}<p>";
      }
      if ($showpreview) {
        $ret[] = "      <p>A <strong>PREVIEW</strong> version link for {$nickname} is {$previewlink}</p>";
      }
      if ($showlive) {
        $startdate = date('dS F Y', strtotime($this->account->GetFieldValue('datestarted')));
        $ret[] = '    <h3>Please Read</h3>';
        $ret[] = '    <p>You are about to update your live website. Do you agree that the pages adheres to the terms and conditions?</p>';
        $ret[] = "    <blockquote><strong>By clicking on the button below you agree that the content of your pages you are " .
          "about to publish adheres to the 'Terms & Conditions' and you are the contact named below, and you act on behalf " .
          "of your organisation.</strong></blockquote>";
        $ret[] = '    <p>Business Name: <strong>' . $this->account->GetFieldValue('businessname') . '</strong></p>';
        $ret[] = '    <p>Contact Name: <strong>' . $this->account->Contact()->FullContactName() . '</strong></p>';
        $ret[] = "    <p>Start Date: <strong>{$startdate}</strong></p>";
        $ret[] = $this->GetUpdateButton();
      }
    }
    $ret[] = "      <p class={$statusclass}>Current status of your mini-website is {$this->account->StatusAsString($status)}</p>";
    $ret[] = "      <p>{$msg}</p>";
    $ret[] = $this->GetReturnButton();
    $ret[] = "    </div>";
    return $ret;
  }

}

$worker = new workersiteupdate();

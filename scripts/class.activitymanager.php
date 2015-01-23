<?php

/**
  * activity manager - calls worker classes / shows data in activity area
  * dana framework v.3

note:
  the mce editor works when $showtexteditor is true 
  and each formbuildertextarea has $enableeditor = true

*/

class activitymanager {
  const SESS_CURRENTID = 'idgroup'; // section to show in control page
  const SESS_GUEST = 'guest'; // guest id when using the guestbooks
  // activity types
  const IDNAME_CHANGEORGDETAILS = 'accchgorgdet';
  const IDNAME_CHANGECONDETAILS = 'accchgcondet';
  const IDNAME_CHANGELOGINPWD = 'accchglogin';
  const IDNAME_MANAGEAREASCOVERED = 'accmanareacovered';
  const IDNAME_MANAGEHOURSAVAILABLE = 'accmanhoursavail';
  const IDNAME_MANAGEADDRESSES = 'accmanaddress';
  const IDNAME_MANAGEPAGES = 'pgman';
  const IDNAME_SITEPREVIEW = 'sitepreview';
  const IDNAME_CHANGETHEME = 'sitechgtheme';
  const IDNAME_SITEUPDATE = 'siteupdate';
  const IDNAME_MANAGERATINGS = 'sitemanratings';
  const IDNAME_MANAGEGALLERIES = 'resmangalleries';
  const IDNAME_MANAGEGALLERYIMAGES = 'resmangalleryimages';
  const IDNAME_MANAGEFILES = 'resmanfiles';
  const IDNAME_MANAGEARTICLES = 'resmanarticles';
  const IDNAME_MANAGENEWSLETTERS = 'resmannewsletters';
  const IDNAME_MANAGENEWSLETTERITEMS = 'resmannewsletteritems';
  const IDNAME_MANAGENEWSLETTERSUBSCRIBERS = 'resmannewslettersubscribers';
  const IDNAME_MANAGEBOOKINGS = 'resmanbookings';
  const IDNAME_MANAGEBOOKINGSETTINGS = 'resmanbookingsettings';
  const IDNAME_MANAGEGUESTBOOKS = 'resmanguestbooks';
  const IDNAME_MANAGEGUESTBOOKSENTRIES = 'resmanguestbookentry';
  const IDNAME_MANAGEPRIVATEAREAS = 'resmanprivateareas';
  const IDNAME_MANAGEPRIVATEAREAMEMBERS = 'resmanprivateareamembers';
  const IDNAME_MANAGEPRIVATEAREAPAGES = 'resmanprivateareapages';
  const IDNAME_MANAGECALENDARDATES = 'resmancalendardates';

  protected $errorlist = array();
  protected $message = array();
  protected $accountgroup;
  protected $pagegroup;
  protected $sitegroup;
  protected $resourcegroup;
  protected $showroot = true;
  protected $showtextedior = true; //false;

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
      $this->accountgroup, self::IDNAME_CHANGEORGDETAILS,
      'Change Organisation Details', 'business name, categories etc');
    $this->AddItem(
      $this->accountgroup, self::IDNAME_CHANGECONDETAILS,
      'Change Contact Details', 'your name/email address etc');
    $this->AddItem(
      $this->accountgroup, self::IDNAME_CHANGELOGINPWD,
      'Change Login Password', 'the password to login into this site');
    $this->AddItem(
      $this->accountgroup, self::IDNAME_MANAGEAREASCOVERED,
      'Manage Areas Covered', 'areas you operate your business');
    $this->AddItem(
      $this->accountgroup, self::IDNAME_MANAGEHOURSAVAILABLE,
      'Manage Hours Available', 'hours your business is open');
//    $this->AddItem(
//      $this->accountgroup, self::IDNAME_MANAGEADDRESSES,
//      'Manage Addresses', 'addresses you run your business from');
    // page group
    $this->pagegroup = $this->AddGroup(
      'pagemanager', 'images/sect_pages.png', 'Page Management',
      'Add, edit or delete your pages that make up your mini-website.');
    $this->AddItem(
      $this->pagegroup, self::IDNAME_MANAGEPAGES,
      'Manage Pages', 'web-pages that make up you minisite');
// datagrid here
// Add New Page
    // site group
    $this->sitegroup = $this->AddGroup(
      'sitemanager', 'images/sect_site.png', 'Site Management',
      'Preview your mini-website or change the look of your mini-website from dozens of designs.');
    $this->AddItem(
      $this->sitegroup, self::IDNAME_SITEPREVIEW,
      'Preview Mini-Site', 'your minisite as it looks now');
    $this->AddItem(
      $this->sitegroup, self::IDNAME_CHANGETHEME,
      'Change Theme', 'appearance of your minisite');
    $this->AddItem(
      $this->sitegroup, self::IDNAME_SITEUPDATE,
      'Update Mini-Site', 'make recent changes live');
    $this->AddItem(
      $this->sitegroup, self::IDNAME_MANAGERATINGS,
      'Manage Ratings', 'read / respond to customer comments');
    // resource group
    $this->resourcegroup = $this->AddGroup(
      'resource', 'images/sect_resources.png', 'Resources',
      'Resources are the features that your pages use to make your mini-website useful.');
    $this->AddItem(
      $this->resourcegroup, self::IDNAME_MANAGEGALLERIES,
      'Manage Galleries', 'add/edit/remove pictures');
    $this->AddItem(
      $this->resourcegroup, self::IDNAME_MANAGEFILES,
      'Manage Downloadable Files', 'add/remove files that can be downloaded by visitors');
    $this->AddItem(
      $this->resourcegroup, self::IDNAME_MANAGEARTICLES,
      'Manage Articles', 'blogs/articles for visitors to read');
    $this->AddItem(
      $this->resourcegroup, self::IDNAME_MANAGENEWSLETTERS,
      'Manage Newsletters', 'subscribers and add/edit/remove newsletters');
    $this->AddItem(
      $this->resourcegroup, self::IDNAME_MANAGEBOOKINGS,
      'Manage Bookings', 'review recent and upcoming appointments');
    $this->AddItem(
      $this->resourcegroup, self::IDNAME_MANAGEGUESTBOOKS,
      'Manage Guestbooks', 'read/remove comments from visitors');
    $this->AddItem(
      $this->resourcegroup, self::IDNAME_MANAGEPRIVATEAREAS,
      'Manage Private Areas', 'add/edit/remove private pages and members');
    $this->AddItem(
      $this->resourcegroup, self::IDNAME_MANAGECALENDARDATES,
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
          if ($worker->idname == $idname) {
            $this->showroot = $worker->showroot;
            if ($this->showroot) {
              $ret = $this->ProcessRoot();
            } else {
//              if ($worker->redirect) {
//                $this->ProcessByIDName($worker->redirect);
//              } else {
                $ret = $worker->AsArray();
//              }
            }
          } else {
            // redirect to a different worker
            $ret = $this->ProcessByIDName($worker->idname);
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

  public function ShowAccordion() {
    if ($this->showroot) {
      $active = ($_SESSION[self::SESS_IDGROUP]) ? $_SESSION[self::SESS_IDGROUP] : 1;
      echo implode("\n", array(
        "  <script type='text/javascript'>",
        "\$('#activitycontent').accordion({",
        "  animate: 'easeInOutQuad',",
        "  heightStyle: 'content',",
        "  active: {$active}",
        "});",
        "\$('a, input[type=\"button\"]').click(function(){",
        "  \$(this).prop('disabled', 'disabled');",
        "});",
        "",
        "\$('input[type=\"submit\"]').click(function(){",
        //  $(this).prop("disabled", "disabled");
        "  $(this).unbind();",
        //  $.delay(2000);
        //  alert($(this).val());
          //$(this).removeProp("disabled");
        //  alert('click');
        "});",
        "  </script>"));
    }
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

  private function TextEditorScript() {
    return array(
      '',
      '<script>',
      'tinymce.init({',
      '  selector: "textarea.editable",',
//      '  inline: true,',
      '  plugins: [',
      '    "advlist autolink autosave link lists charmap hr anchor pagebreak spellchecker",',
      '    "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime nonbreaking",',
      '    "table contextmenu directionality template textcolor paste textcolor colorpicker textpattern"',
      '  ],',
      '  style_formats: [',
      '    {title: "Main Heading", block: "h2"},',
      '    {title: "Sub Heading", block: "h3"},',
      '  {title: "Section", block: "h4"}',
//      '  {title: 'Example 1', inline: 'span', classes: 'example1'},',
//      '  {title: 'Example 2', inline: 'span', classes: 'example2'},',
//      '  {title: 'Table styles'},',
//      '  {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}',
      '  ],',
      '  toolbar1: "bold italic underline strikethrough | ' .
      'alignleft aligncenter alignright alignjustify | styleselect formatselect fontselect fontsizeselect",',
      '  toolbar2: "cut copy paste | searchreplace | bullist numlist | ' .
      'outdent indent blockquote | undo redo | link unlink anchor code | ' .
      'insertdatetime | forecolor backcolor",',
      '  toolbar3: "table | hr removeformat | subscript superscript | ' .
      'charmap | spellchecker | visualchars visualblocks nonbreaking template pagebreak",',
      '  menubar: false,',
      '  toolbar_items_size: "small",',
      '});',
      '</script>',
      ''
    );
  }

  public function Show() {
    $idname = GetGet('in', GetPost('in', self::IDNAME_CHANGEORGDETAILS)); //''));
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
    echo ArrayToString($ret);
    if ($this->showtextedior) {
      echo ArrayToString($this->TextEditorScript());
    }
  }
}

$activitymanager = new activitymanager();

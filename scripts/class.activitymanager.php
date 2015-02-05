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
  const IDNAME_ACCMGT_SUMMARY = 'summary';
  const IDNAME_ACCMNT_ORGDETAILS = 'accchgorgdet'; // your business
  const IDNAME_ACCMNT_CONDETAILS = 'accchgcondet'; // your details
  const IDNAME_ACCMNT_LOGINPWD = 'accchglogin'; // password
  
  const IDNAME_PAGE_MANAGE = 'pgman'; // pages
  const IDNAME_PAGE_NEW = 'newpage'; // add new page
  const IDNAME_PAGE_CHANGETHEME = 'sitechgtheme'; // change theme

  const IDNAME_RESOURCES_SUMMARY = 'ressummary'; // resources
  const IDNAME_RESOURCES_GALLERIES = 'resmangalleries'; // galleries
  const IDNAME_RESOURCES_NEWSLETTERS = 'resmannewsletters'; // newsletters
  const IDNAME_RESOURCES_GUESTBOOKS = 'resmanguestbooks'; // guestbooks
  const IDNAME_RESOURCES_BOOKINGS = 'resmanbookings'; // bookings
  const IDNAME_RESOURCES_PRIVATEAREAS = 'resmanprivateareas'; // private areas
  const IDNAME_RESOURCES_CALENDARDATES = 'resmancalendardates'; // special dates
  const IDNAME_RESOURCES_ARTICLES = 'resmanarticles'; // articles / blogs
  const IDNAME_RESOURCES_RATINGS = 'sitemanratings'; // ratings
  const IDNAME_ACCMNT_AREASCOVERED = 'accmanareacovered'; // areas covered
  const IDNAME_ACCMNT_HOURSAVAILABLE = 'accmanhoursavail';
//
  const IDNAME_MANAGEADDRESSES = 'accmanaddress';
  const IDNAME_SITEPREVIEW = 'sitepreview';
  const IDNAME_SITEUPDATE = 'siteupdate';
//
  const IDNAME_RESOURCES_GALLERYIMAGES = 'resmangalleryimages';
  const IDNAME_RESOURCES_FILES = 'resmanfiles';
  const IDNAME_RESOURCES_NEWSLETTERITEMS = 'resmannewsletteritems';
  const IDNAME_RESOURCES_NEWSLETTERSUBSCRIBERS = 'resmannewslettersubscribers';
  const IDNAME_RESOURCES_BOOKINGSETTINGS = 'resmanbookingsettings';
  const IDNAME_RESOURCES_GUESTBOOKSENTRIES = 'resmanguestbookentry';
  const IDNAME_RESOURCES_PRIVATEAREAMEMBERS = 'resmanprivateareamembers';
  const IDNAME_RESOURCES_PRIVATEAREAPAGES = 'resmanprivateareapages';

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
      $this->accountgroup, 'IDNAME_ACCMNT_ORGDETAILS',
      'Change Organisation Details', 'business name, categories etc');
    $this->AddItem(
      $this->accountgroup, 'IDNAME_ACCMNT_CONDETAILS',
      'Change Contact Details', 'your name/email address etc');
    $this->AddItem(
      $this->accountgroup, 'IDNAME_ACCMNT_LOGINPWD',
      'Change Login Password', 'the password to login into this site');
    $this->AddItem(
      $this->accountgroup, 'IDNAME_ACCMNT_AREASCOVERED',
      'Manage Areas Covered', 'areas you operate your business');
    $this->AddItem(
      $this->accountgroup, 'IDNAME_ACCMNT_HOURSAVAILABLE',
      'Manage Hours Available', 'hours your business is open');
//    $this->AddItem(
//      $this->accountgroup, self::IDNAME_MANAGEADDRESSES,
//      'Manage Addresses', 'addresses you run your business from');
    // page group
    $this->pagegroup = $this->AddGroup(
      'pagemanager', 'images/sect_pages.png', 'Page Management',
      'Add, edit or delete your pages that make up your mini-website.');
    $this->AddItem(
      $this->pagegroup, 'IDNAME_PAGE_MANAGE',
      'Manage Pages', 'web-pages that make up you minisite');
// datagrid here
// Add New Page
    // site group
    $this->sitegroup = $this->AddGroup(
      'sitemanager', 'images/sect_site.png', 'Site Management',
      'Preview your mini-website or change the look of your mini-website from dozens of designs.');
    $this->AddItem(
      $this->sitegroup, 'IDNAME_SITEPREVIEW',
      'Preview Mini-Site', 'your minisite as it looks now');
    $this->AddItem(
      $this->sitegroup, 'IDNAME_PAGE_CHANGETHEME',
      'Change Theme', 'appearance of your minisite');
    $this->AddItem(
      $this->sitegroup, 'IDNAME_SITEUPDATE',
      'Update Mini-Site', 'make recent changes live');
    $this->AddItem(
      $this->sitegroup, 'IDNAME_RESOURCES_RATINGS',
      'Manage Ratings', 'read / respond to customer comments');
    // resource group
    $this->resourcegroup = $this->AddGroup(
      'resource', 'images/sect_resources.png', 'Resources',
      'Resources are the features that your pages use to make your mini-website useful.');
    $this->AddItem(
      $this->resourcegroup, 'IDNAME_RESOURCES_GALLERIES',
      'Manage Galleries', 'add/edit/remove pictures');
    $this->AddItem(
      $this->resourcegroup, 'IDNAME_RESOURCES_FILES',
      'Manage Downloadable Files', 'add/remove files that can be downloaded by visitors');
    $this->AddItem(
      $this->resourcegroup, 'IDNAME_RESOURCES_ARTICLES',
      'Manage Articles', 'blogs/articles for visitors to read');
    $this->AddItem(
      $this->resourcegroup, 'IDNAME_RESOURCES_NEWSLETTERS',
      'Manage Newsletters', 'subscribers and add/edit/remove newsletters');
    $this->AddItem(
      $this->resourcegroup, 'IDNAME_RESOURCES_BOOKINGS',
      'Manage Bookings', 'review recent and upcoming appointments');
    $this->AddItem(
      $this->resourcegroup, 'IDNAME_RESOURCES_GUESTBOOKS',
      'Manage Guestbooks', 'read/remove comments from visitors');
    $this->AddItem(
      $this->resourcegroup, 'IDNAME_RESOURCES_PRIVATEAREAS',
      'Manage Private Areas', 'add/edit/remove private pages and members');
    $this->AddItem(
      $this->resourcegroup, 'IDNAME_RESOURCES_CALENDARDATES',
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
//echo "<h2>idname = {$idname}</h2>\n";
    $ret = array();
    try {
      if ($idname) { // constant('activitymanager::' . self::$activeactionname);
        $script = 'worker.' . constant('activitymanager::' . $idname) . '.php'; //"worker.{$idname}.php";
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
    $idname = controlmanager::$currentidname;
//    $idname = GetGet('in', GetPost('in', self::IDNAME_CHANGEORGDETAILS)); //''));
    $lines = $this->ProcessByIDName($idname);
    $ret = array();

    if ($this->message) {
      $ret[] = "  <section class='activityformmessage'>";
      foreach($this->message as $msg) {
        $ret[] = "    <li>{$msg}</li>";
      }
      $ret[] = '  </section>';
    }
    if ($this->errorlist) {
      $ret[] = "  <section class='activityformerrors'>";
      foreach($this->errorlist as $err) {
        $ret[] = "    <li>{$err}</li>";
      }
      $ret[] = '  </section>';
    }

    $ret[] = "    <section id='activityarea'>";
    $ret[] = "      <div id='activitycontent'>";
    $ret = array_merge($ret, $lines);
    $ret[] = '      </div>';
    $ret[] = '    </section>';
    echo ArrayToString($ret);
    if ($this->showtextedior) {
      echo ArrayToString($this->TextEditorScript());
    }
  }
}

//$activitymanager = new activitymanager();

<?php
require_once 'class.workerform.php';
require_once 'class.workerbase.php';
require_once 'class.formbuilderdatagrid.php';
require_once 'class.formbuilderbutton.php';

/**
  * activity worker for managing newsletters
  * dana framework v.3
*/

// resource manage newsletters
/*
  title, DT_STRING
  showdate, DT_DATE
  datelastsent, DT_DATE
  showaddress, DT_BOOLEAN
  showtelephone, DT_BOOLEAN
  showwebsite, DT_BOOLEAN
  footertext, DT_STRING
  style, DT_STRING
  newsletterformatid, DT_FK
*/
class workerresmannewsletters extends workerform {
  protected $datagrid;
//  protected $table;
  protected $tableitems;
//  protected $areadescription;
  protected $fldtitle; // title of the newsletter
  protected $fldshowdate; // publishing date of the newsletter
  protected $fldshowaddress; // display the business address in the newsletter
  protected $fldshowtelephone; // display the telephone numbers in the newsletter
  protected $fldshowwebsite; // display the website of the business
//  protected $flditems;
  protected $fldaddnewsletter;
  protected $fldaddsubscriber;
  protected $fldadditem;
  protected $itemgrid;
  protected $subscribergrid;

  protected function InitForm() {
    $this->table = new newsletter($this->itemid);
    $this->icon = 'images/sect_resource.png';
    $this->activitydescription = 'some text here' . ' - ' . $this->idname;
    $this->contextdescription = 'newsletter management';
    $this->datagrid = new formbuilderdatagrid('newsletter', '', 'Newsletters Available');
    switch ($this->action) {
      case workerbase::ACT_NEW:
      case workerbase::ACT_EDIT:
        $this->title = 'Modify Newsletter';
        $this->fldtitle = $this->AddField(
          'title', new formbuildereditbox('title', '', 'Newsletter Title'), $this->table);
        $this->fldshowdate = $this->AddField(
          'showdate', new formbuilderdate('showdate', '', 'Publish Date'), $this->table);
        $this->fldshowaddress = $this->AddField(
          'showaddress', new formbuildercheckbox('showaddress', '', 'Include Address?'), $this->table);
        $this->fldshowtelephone = $this->AddField(
          'showtelephone', new formbuildercheckbox('showtelephone', '', 'Include telephone numbers?'), $this->table);
        $this->fldshowwebsite = $this->AddField(
          'showwebsite', new formbuildercheckbox('showwebsite', '', 'include your main website?'), $this->table);
        if ($this->action == workerbase::ACT_EDIT) {
          $this->itemgrid = $this->AddField(
            'itemgrid', new formbuilderdatagrid('itemgrid', '', 'Newsletter Items'));
        }
        $this->returnidname = $this->idname;
        $this->showroot = false;
        break;
      case workerbase::ACT_REMOVE:
        break;
      default:
        $this->buttonmode = array(workerform::BTN_BACK);
        $this->title = 'Manage Newsletters'; 
        $this->flditems = $this->AddField('newsletters', $this->datagrid, $this->table);
        $this->fldaddnewsletter = $this->AddField(
          'addnewsletter', new formbuilderbutton('addnewsletter', 'Add New Newsletter'));
        $url = $_SERVER['PHP_SELF'] . "?in={$this->idname}&act=" . workerbase::ACT_NEW;
        $this->fldaddnewsletter->url = $url;
        break;
    }
  }

  protected function PostFields() {
    switch ($this->action) {
      case workerbase::ACT_EDIT:
      case workerbase::ACT_NEW:
        $ret = $this->fldtitle->Save() + $this->fldshowdate->Save() +
          $this->fldshowaddress->Save() + $this->fldshowtelephone->Save() +
          $this->fldshowwebsite->Save();
        break;
      default:
        $ret = true;
    }
    return $ret;
  }

  protected function SaveToTable() {
    if (!trim($this->fldtitle->value)) {
      $date = strtotime($this->fldshowdate->value);
      $desc = date('F Y', $date) . ' Newsletter';
      $this->table->SetFieldValue('title', $desc);
    }
    return (int) $this->table->StoreChanges(); //parent::StoreChanges(); //$this->table->StoreChanges();
  }

  protected function AddErrorList() {}

  protected function AssignFieldDisplayProperties() {
    $this->datagrid->SetIDName($this->idname);
    $this->NewSection(
      'newsletters', 'Newsletters',
      'Below are your newsletters you can send to subscribers or be downloaded from your website. Each newsletter is made up of a set of items - stories. You can create as many newsletters as you wish but try to create regular newsletter weekly, monthly etc.');
    $this->flditems->description = 'Your newsletter your created and are available for your visitors to view.';
    $this->AssignFieldToSection('newsletters', 'newsletters');
    if ($this->fldaddnewsletter) {
      $this->fldaddnewsletter->description = "Click this button to create a new newsletter";
      $this->AssignFieldToSection('newsletters', 'addnewsletter');
      // newsletter subscribers
      $item = new newslettersubscriber();
      $item->CheckForOldSubscribers();
      $statusactive = $item->GetStatusAsString(true, basetable::STATUS_ACTIVE);
      $statuspending = $item->GetStatusAsString(true, newslettersubscriber::STATUS_WAITING);
      $statussunsub = $item->GetStatusAsString(true, newslettersubscriber::STATUS_UNSUBSCRIBED);
      $this->NewSection(
        'subscribers', 'Managing newsletter subscribers',
        "Below are the visitors who have expressed interest in your newsletters and want to " .
          "subscribe to them, or have been invited by you. The status column states " .
          "<strong>'{$statusactive}'</strong> if the subscriber is sent your newsletters, " .
          "<strong>'{$statuspending}'</strong> if they have been sent an invitation but not responded, and " .
          "<strong>'{$statussunsub}'</strong> if they not longer wish to be sent any new newsletters. " .
          '<strong>NOTE: Any visitors who have not confirmed after 1 month of being invited are ' .
          'automatically removed.</strong>');
      $this->subscribergrid = $this->AddField(
        'subscribergrid', new formbuilderdatagrid('subscribergrid', '', 'Newsletter Subscribers'));
      $this->subscribergrid->SetIDName('IDNAME_RESOURCES_NEWSLETTERSUBSCRIBERS');
      $this->PopulateSubscribers();
      $this->subscribergrid->description = 'Below are the list of subscribers to your newsletter, and their status.';
      $this->AssignFieldToSection('subscribers', 'subscribergrid');
      // add subscribe button
      $this->fldaddsubscriber = $this->AddField(
        'addsubscriber', new formbuilderbutton('addsubscriber', 'Invite Subscriber'));
      $url = $_SERVER['PHP_SELF'] . "?in=IDNAME_RESOURCES_NEWSLETTERSUBSCRIBERS&act=" . workerbase::ACT_NEW;
      $this->fldaddsubscriber->url = $url;
      $this->AssignFieldToSection('subscribers', 'addsubscriber');
    }
  }

  private function PopulateItemGrid() {
    $this->itemgrid->showactions = true;
    $this->itemgrid->AddColumn('DESC', 'Heading', true);
//    $this->itemgrid->AddColumn('CONT', 'Con', false);
    $list = $this->table->FindNewsletterItems();
    if ($list) {
      $actions = array(
        formbuilderdatagrid::TBLOPT_DELETABLE,
        formbuilderdatagrid::TBLOPT_MOVEUP,
        formbuilderdatagrid::TBLOPT_MOVEDOWN
      );
      $options = array('parentid' => $this->itemid); // newsletter id
      foreach($list as $itemid => $item) {
        $coldata = array(
          'DESC' => $item->GetFieldValue('heading', '<em>(untitled)</em>')
        );
        $this->itemgrid->AddRow($itemid, $coldata, true, $actions, $options);
      }
    }
  }

  private function PopulateSubscribers() {
    $this->subscribergrid->showactions = false; // TODO: true; make actions 'resend invite', 'delete'
    $this->subscribergrid->AddColumn('DESC', 'Name', false);
    $this->subscribergrid->AddColumn('EMAIL', 'E-Mail', false);
    $this->subscribergrid->AddColumn('STATUS', 'Status', false);
    $list = $this->table->FindSubscribers();
    if ($list) {
      $actions = array(); //TBLOPT_DELETABLE, TBLOPT_MOVEUP, TBLOPT_MOVEDOWN);
      foreach($list as $itemid => $item) {
        $coldata = array(
          'DESC' => $item->FullName(),
          'EMAIL' => $item->GetFieldValue('email', '<em>(untitled)</em>'),
          'STATUS' => $item->GetStatusAsString(true)
        );
        $this->subscribergrid->AddRow($itemid, $coldata, true, $actions);
      }
    }
  }

  protected function AssignItemEditor($isnew) {
    $title = (($isnew) ? 'Creating a new' : 'Modify a ') . 'Newsletter';
    $this->NewSection(
      'newsletters', $title,
      "Please give the newsletter a friendly title and state the publishing date you wish the newsletter to be available. Optionally, you can have contact details included in the footer of the newsletter.");
    // title field
    $this->fldtitle->description = "This is the title of the newsletter. If you leave it blank we will use '" . date('F Y') . " Newsletter'" . "'";
    $this->fldtitle->size = 80;
    $this->fldtitle->placeholder = 'eg. ' . date('F Y') . ' Newsletter';
    $this->AssignFieldToSection('newsletters', 'title');
    // showdate field
    $this->fldshowdate->description = 'Please specify the date you would like the newsletter to be published (shown on your website)';
    $this->fldshowdate->required = true;
    $this->AssignFieldToSection('newsletters', 'showdate');
    // showaddress field
    $this->fldshowaddress->description =
      'Check the box if you want to have your main address included in your newsletter? <em>This will be shown at the bottom of the newsletter, below all the items, as part of the footer</em>.';
    $this->AssignFieldToSection('newsletters', 'showaddress');
    // showtelephone field
    $this->fldshowtelephone->description =
      'Check the box if you want to have your contact telephone numbers included in your newsletter? <em>This will be shown at the bottom of the newsletter, below all the items, as part of the footer</em>.';
    $this->AssignFieldToSection('newsletters', 'showtelephone');
    // showwebsite field
    $this->fldshowwebsite->description =
      'Check the box if you want to have your main website address included in your newsletter? <em>This will be shown at the bottom of the newsletter, below all the items, as part of the footer</em>.';
    $this->AssignFieldToSection('newsletters', 'showwebsite');
    // newsletter items
    if ($this->itemgrid) {
      $this->NewSection(
        'items', 'Managing the newsletter items',
        "Below are the items (stories) that make up your newsletter. You can edit, delete or add a new item.");
      $this->itemgrid->SetIDName('IDNAME_RESOURCES_NEWSLETTERITEMS');
      $this->PopulateItemGrid();
      $this->itemgrid->description = 'Below are the list of items to be used to make your newsletter.';
      $this->AssignFieldToSection('items', 'itemgrid');
      // add item button
      $this->fldadditem = $this->AddField(
        'additem', new formbuilderbutton('additem', 'Add Item'));
      $url = $_SERVER['PHP_SELF'] . "?in=IDNAME_RESOURCES_NEWSLETTERITEMS&amp;pid={$this->itemid}&amp;act=" . workerbase::ACT_NEW;
      $this->fldadditem->url = $url;
      $this->AssignFieldToSection('items', 'additem');
    }
  }

  protected function AssignItemRemove($confirmed) {
  }
}

$worker = new workerresmannewsletters();

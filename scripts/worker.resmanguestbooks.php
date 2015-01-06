<?php
require_once 'class.workerform.php';
require_once 'class.workerbase.php';
require_once 'class.formbuilderdatagrid.php';
require_once 'class.formbuilderbutton.php';

/**
  * activity worker for managing galleries
  * dana framework v.3
*/

// resource manage galleries

class workerresmanguestbooks extends workerform {
  protected $datagrid;
//  protected $table;
  protected $tableitems;
  protected $flddescription;
  protected $fldgeneralmessage;
  protected $fldthankyoumessage;
  protected $fldauthorised;
  protected $fldguestbooks;
  protected $fldaddguestbook;
  protected $fldentries;
  protected $entrygrid;

  protected function InitForm() {
    $this->table = new guestbook($this->itemid);
    $this->icon = 'images/sect_resource.png';
    $this->activitydescription = 'some text here' . ' - ' . $this->idname;
    $this->contextdescription = 'guest-book management';
    $this->datagrid = new formbuilderdatagrid('guestbook', '', 'Guest Books');
    switch ($this->action) {
      case ACT_NEW:
      case ACT_EDIT:
        //$this->tableitems = new galleryitems
        $this->title = 'Modify Guest-book';
        $this->flddescription = $this->AddField(
          'description', new formbuildereditbox('description', '', 'Guest-book Title'), $this->table);
        $this->fldgeneralmessage = $this->AddField(
          'generalmessage', new formbuildertextarea('generalmessage', '', 'General Message'), $this->table);
        $this->fldthankyoumessage = $this->AddField(
          'thankyoumessage', new formbuildertextarea('thankyoumessage', '', 'Thank you Message'), $this->table);
        $this->returnidname = $this->idname;
        $this->showroot = false;
        break;
      case ACT_REMOVE:
        break;
      default:
        $this->buttonmode = array(BTN_BACK);
        $this->title = 'Manage Guest-books'; 
        $this->fldguestbooks = $this->AddField('guestbook', $this->datagrid, $this->table);
        $this->fldaddguestbook = $this->AddField(
          'addguestbook', new formbuilderbutton('addguestbook', 'Add New Guest-book'));
        $url = $_SERVER['PHP_SELF'] . "?in={$this->idname}&act=" . ACT_NEW;
        $this->fldaddguestbook->url = $url;
        break;
    }
  }

  protected function PostFields() {
    switch ($this->action) {
      case ACT_NEW:
      case ACT_EDIT:
        $ret = $this->flddescription->Save() + $this->fldgeneralmessage->Save() + $this->fldthankyoumessage->Save();
        break;
      default:
        $ret = true;
    }
    return $ret;
  }

  protected function SaveToTable() {
    return (int) $this->table->StoreChanges(); //parent::StoreChanges(); //$this->table->StoreChanges();
  }

  protected function AddErrorList() {}

  protected function AssignFieldDisplayProperties() {
    $this->datagrid->SetIDName($this->idname);
    $this->NewSection(
      'guestbook', 'Guest-book',
      'Below are your guest-books. Each guest-book can be shown in a Guest Book Page and visitors can write comments ' .
      'about you and your business. Only visitors who are registered by us can make comments. They can register/log in through ' .
      'your Guest Book Page and make as many comments as they wish to any business who has an account with us. The ' .
      'comments will not appear immediately but must be authorised by the account holder (you) first.');
    $this->fldguestbooks->description = 'Guest-books';
    $this->AssignFieldToSection('guestbook', 'guestbook');
    if ($this->fldaddguestbook) {
      $this->fldaddguestbook->description = "Click this button to add a new guest-book";
      $this->AssignFieldToSection('guestbook', 'addguestbook');
    }
  }

  private function PopulateEntryGrid() {
    $this->entrygrid->showactions = true;
    $this->entrygrid->AddColumn('DESC', 'Visitor Name', true);
    $this->entrygrid->AddColumn('SUBJECT', 'Subject', false);
    $this->entrygrid->AddColumn('DATE', 'Date', false);
    $this->entrygrid->AddColumn('STATUS', 'Status', false);
    $list = $this->table->EntryList();
    if ($list) {
      $actions = array(TBLOPT_DELETABLE, TBLOPT_AUTHORISE);
      foreach($list as $entry) {
        $status = $this->table->StatusAsString();
        $coldata = array(
          'DESC' => $entry->GetFieldValue('sendername'),
          'SUBJECT' => $entry->GetFieldValue('subject'),
          'DATE' => $this->table->FormatDateTime(DF_SHORTDATETIME, $entry->GetFieldValue('datestamp')),
          'STATUS' => $status
        );
        $this->entrygrid->AddRow($entry->ID(), $coldata, true, $actions);
      }
    }
  }

  protected function AssignItemEditor($isnew) {
    $title = (($isnew) ? 'Creating a new ' : 'Modify a ') . 'Guest-book';
    $this->NewSection(
      'guestbook', $title,
      "Please describe the guest-book with a simple name or phrase, such as 'My guest-Book' or 'Your Comments Requested' etc.");
    $this->NewSection(
      'entrygrid', 'Managing the Comments Made',
      'Below are the list of current comments your visitors have made. When a comment has ' .
      'been posted it is pending, waiting for you to enable it to be shown on your guest-book ' .
      "page (if you have one). If you don't like it just leave it or delete it.");
    // title field
    $this->flddescription->description = 'This is the name of the guest-book.';
    $this->flddescription->size = 50;
    $this->AssignFieldToSection('guestbook', 'description');
    // general message field
    $this->fldgeneralmessage->description =
      'Type in a message that will appear above in the visitor message box. A typical ' .
      'message would be to ask for comments about your business.';
    $this->fldgeneralmessage->rows = 5;
    $this->fldgeneralmessage->placeholder =
      'eg. Please leave a comment about our business. It will appear on this page (if we like it).';
    $this->AssignFieldToSection('guestbook', 'generalmessage');
    // thank you message field
    $this->fldthankyoumessage->description =
      'Type in a message that will appear after the visitor has sent a message on ' .
      'your guest-book. A typical message would be to thank then for sending you a ' .
      'message and it will be read very soon.';
    $this->fldthankyoumessage->rows = 5;
    $this->fldthankyoumessage->placeholder =
      'eg. Thank you for your comment. Once it has been checked it should appear in the guest-book page shortly.';
    $this->AssignFieldToSection('guestbook', 'thankyoumessage');
    // entrygrid
    $this->entrygrid = new formbuilderdatagrid('entrygrid', '', 'Guest-book Entries');
    $this->fldentries = $this->AddField('entrygrid', $this->entrygrid);
    $this->entrygrid->SetIDName(IDNAME_MANAGEGUESTBOOKSENTRIES);
    $this->PopulateEntryGrid();
    $this->entrygrid->description = 'Type in your reply to the comment. Please read the notice above.';
    $this->AssignFieldToSection('entrygrid', 'entrygrid');
    // add image button
    $this->fldaddimage = $this->AddField(
      'addimage', new formbuilderbutton('addimage', 'Add Picture'));
    $url = $_SERVER['PHP_SELF'] . "?in=" . IDNAME_MANAGEGUESTBOOKSENTRIES . "&act=" . ACT_NEW;
    $this->fldaddimage->url = $url;
//    $this->AssignFieldToSection('imagegrid', 'addimage');
  }

  protected function AssignItemRemove($confirmed) {
  }
}

$worker = new workerresmanguestbooks();

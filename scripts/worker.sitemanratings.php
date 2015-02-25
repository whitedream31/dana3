<?php
namespace dana\worker;

require_once 'class.workerform.php';
require_once 'class.workerbase.php';
require_once 'class.formbuilderdatagrid.php';
require_once 'class.formbuilderbutton.php';

/**
  * worker site manage ratings
  * @version dana framework v.3
*/

class workersitemanratings extends workerform {
  protected $datagrid;
//  protected $table;
  protected $areadescription;
  protected $fldratings;
  protected $fldcomment;
  protected $fldreply;

  protected function InitForm() {
    $this->table = new \dana\table\rating($this->itemid);
    $this->icon = 'images/sect_site.png';
    $this->activitydescription = 'some text here';
    $this->contextdescription = 'rating management';
    $this->datagrid = new \dana\formbuilder\formbuilderdatagrid('ratings', '', 'Ratings');
    switch ($this->action) {
      case workerbase::ACT_EDIT:
        $this->title = 'Modify Reply';
        $this->fldcomment = $this->AddField(
          'comment', new \dana\formbuilder\formbuilderstatictext('comment', '', 'Comment'));
        $this->fldreply = $this->AddField(
          'reply', new \dana\formbuilder\formbuildertextarea('reply', '', 'Your Reply'), $this->table);
        $this->returnidname = $this->idname;
        $this->showroot = false;
        break;
      case workerbase::ACT_NEW:
      case workerbase::ACT_REMOVE:
        break;
      default:
        $this->buttonmode = array(workerform::BTN_BACK);
        $this->title = 'Manage Ratings'; 
        $this->fldratings = $this->AddField('ratings', $this->datagrid, $this->table);
        break;
    }
  }

  protected function PostFields() {
    switch ($this->action) {
      case workerbase::ACT_EDIT:
        $ret = $this->fldreply->Save();
        break;
      default:
        $ret = true;
    }
    return $ret;
  }

  protected function SaveToTable() {
    $this->table->SetFieldValue('replystamp', time());
    return (int) $this->table->StoreChanges(); //parent::StoreChanges(); //$this->table->StoreChanges();
  }

  protected function AddErrorList() {}

  protected function AssignFieldDisplayProperties() {
    $this->datagrid->SetIDName($this->idname);
    $this->NewSection(
      'ratings', 'View Customer Ratings',
      'Below are any ratings that customers have made about your organisation. You cannot remove them but you can respond with a message, which will be shown below the related rating/comment.');
    $this->fldratings->description = 'Customer Ratings';
    $this->AssignFieldToSection('ratings', 'ratings');
  }

  protected function AssignItemEditor($isnew) {
    $title = 'Replying to Customer Comment';
    $this->NewSection(
      'ratings', $title,
      'Please type in a reply to this customers comment. Please check for spellings and grammar - <strong>NOTICE: bad language and offensive phrases are not tolerated. Your comment may be removed. Repeated occurrences will mean you will not be able to reply to comments.</strong>');
    // comment field
    $visitorname = trim($this->table->GetFieldValue('visitorname'));
    if (!$visitorname) {
      $visitorname = '<em>anonymous</em>';
    }
    $this->fldcomment->value = '&quot;' . $this->table->GetFieldValue('comment') . '&quot; by ' . $visitorname;
    $this->fldacomment->description = 'This is the comments that was made by the customer.';
    $this->AssignFieldToSection('rating', 'comment');
    // reply
    $this->fldreply->description = 'Type in your reply to the comment. Please read the notice above.';
    $this->fldreply->rows = 10;
    $this->AssignFieldToSection('rating', 'reply');
  }

  protected function AssignItemRemove($confirmed) {
  }
}

$worker = new workersitemanratings();

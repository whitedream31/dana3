<?php
// GUEST BOOK page container class for MyLocalSmallBusiness
// written by Ian Stewart (c) 2012 Whitedream Software
// created: 8 dec 2012
// modified: 25 jul 2014

require_once 'class.table.page.php';

// guest book page class
class pageguestbook extends page {
  protected $fldcancomment;
  protected $fldregistervisitors;

  protected function AssignPageType() {
    $this->pgtype = PAGETYPE_GUESTBOOK;
  }

  // assign table columns just used by this type of page
  protected function AssignPageTypeFields() {
    $this->AddField('groupid', DT_FK); // FK to guestbook table
    $this->AddField('cancomment', DT_BOOLEAN, true);
    $this->AddField('registervisitors', DT_BOOLEAN, true);
//    $this->AddField('hascomments', DT_BOOLEAN);
  }

  protected function InitFieldsForMainContent($worker) {
    parent::InitFieldsForMainContent($worker);
    $this->fldcancomment = $worker->AddField(
      'cancomment', new formbuildercheckbox('cancomment', '', 'Can Add Comments'), $this);
    $this->fldregistervisitors = $worker->AddField(
      'registervisitors', new formbuildercheckbox('registervisitors', '', 'Visitors Must Register'), $this);
  }

  public function AssignFieldProperties($worker, $isnew) {
    parent::AssignFieldProperties($worker, $isnew);
    // can comment field
    $this->fldcancomment->description =
      'Check the box if you want your visitors to be able to add their own comments to your guest book page. (If this is unchecked no ' .
      'one can post comments and the page is essentially read-only to the visitors)';
    $worker->AssignFieldToSection('sectmaincontent', 'cancomment');
    // register visitors field
    $this->fldregistervisitors->description =
      'Check the box if you want your visitor to register (and login) before they can make comments on this page. Please note that anyone ' .
      'who registers do so to our site in general rather than just your mini-website. This means once registered they can make comments on ' .
      'any guestbook in any account.';
    $worker->AssignFieldToSection('sectmaincontent', 'registervisitors');
  }

  protected function SaveFormFields() {
    return parent::SaveFormFields() +
      $this->SaveFormField($this->fldcancomment) + $this->SaveFormField($this->fldregistervisitors);
  }

  public function ValidateFields() {
    $guestbookid = (int) $this->GetFieldValue('groupid');
    if ($guestbookid == 0) {
      require_once 'class.guestbook.php';
      $guestbook = new guestbook();
      $guestbook->StoreChanges();
      $this->SetFieldValue('groupid', $guestbook->ID());
    }
  }
}

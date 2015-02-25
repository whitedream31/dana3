<?php
namespace dana\worker;

require_once 'class.workerform.php';
require_once 'class.workerbase.php';

/**
  * worker resource summary
  * @version dana framework v.3
*/

class workerressummary extends workerform {
  protected $fldorgdetails;
  protected $fldlogomediaid;
  protected $fldpagesummarylist;
  protected $fldgalleries;
  protected $fldnewsletters;
  protected $fldsubscribers;
  protected $fldguestbooks;

  private function DoGallerySummaryBox() {
    $galleries = \dana\table\gallery::GetGroupList($this->AccountID());
    $this->fldgalleries = $this->AddField(
      'galleries',
      new \dana\formbuilder\formbuildersummarybox('gallerieslist', '', 'Gallery List'),
      $this->account
    );
    foreach ($galleries as $galleryid => $gallery) {
      $linkedpage = \dana\table\gallery::FindGalleryLinkedPageDescription($galleryid);
      $imagecount = $gallery->CountItems();
      $cntdesc = ($imagecount)
        ? ($imagecount == 1) ? '1 image' : $imagecount . ' images'
        : '<em>empty<em>';
      $linkdesc = ($linkedpage) ? 'linked to ' . $linkedpage : '<em>unused</em>';
      $this->fldgalleries->AddItem(
        'galleries-' . $galleryid,
        $gallery->GetFieldValue('title'),
        $cntdesc . ', ' . $linkdesc
      );
    }
    $this->fldgalleries->worker = $this;
    $this->fldgalleries->changecaption = 'Manage Galleries';
    $this->fldgalleries->changeidname = 'IDNAME_RESOURCES_GALLERIES';
  }

  private function DoNewsletterSummaryBox() {
    $newsletters = $this->account->NewsletterList();
    if ($newsletters) {
      $this->fldnewsletters = $this->AddField(
        'newsletters',
        new \dana\formbuilder\formbuildersummarybox('newsletterslist', '', 'Active Newsletters'),
        $this->account
      );
      foreach ($newsletters as $newsletterid => $newsletter) {
        $this->fldnewsletters->AddItem(
          'newsletter-' . $newsletterid,
          $newsletter->GetFieldValue('title'),
          $newsletter->showdatedescription
        );
      }
      $this->fldnewsletters->worker = $this;
      $this->fldnewsletters->changecaption = 'Manage Newsletters';
      $this->fldnewsletters->changeidname = 'IDNAME_RESOURCES_NEWSLETTERS';
    }
  }

  private function DoNewsletterSubscriberSummaryBox() {
    $subscribersactive = $this->account->NewsletterSubscriberList(\dana\table\basetable::STATUS_ACTIVE);
    $subscriberscancelled = $this->account->NewsletterSubscriberList(\dana\table\basetable::STATUS_CANCELLED);
    if ($subscribersactive + $subscriberscancelled) {
      $this->fldsubscribers = $this->AddField(
        'subscribers',
        new \dana\formbuilder\formbuildersummarybox('subscriberlist', '', 'Active Subscribers'),
        $this->account
      );
      $this->fldsubscribers->AddItem(
        'subscriberactivecount', 'Active Subscribers',
        CountToString(count($subscribersactive), '')
      );
      $this->fldsubscribers->AddItem(
        'subscriberdeletedcount', 'Unsubscribed Subscribers',
        CountToString(count($subscriberscancelled), '')
      );
      $this->fldsubscribers->worker = $this;
      $this->fldsubscribers->changecaption = 'Manage Subscribers';
      $this->fldsubscribers->changeidname = 'IDNAME_RESOURCES_NEWSLETTERS';
    }
  }

  private function DoGuestBookSummaryBox() {
    $guestbooks = $this->account->GuestBookList();
    if ($guestbooks) {
      $this->fldguestbooks = $this->AddField(
        'guestbooks',
        new \dana\formbuilder\formbuildersummarybox('guestbooklist', '', 'Guestbook Comments'),
        $this->account
      );
      foreach ($guestbooks as $guestbookid => $guestbook) {
        $this->fldguestbooks->AddItem(
          'guestbook-' . $guestbookid,
          $guestbook->GetFieldValue(\dana\table\basetable::FN_DESCRIPTION),
          CountToString($guestbook->EntryList(), 'comment', '<em>no comments</em>')
        );
      }
      $this->fldguestbooks->worker = $this;
      $this->fldguestbooks->changecaption = 'Manage Guestbooks';
      $this->fldguestbooks->changeidname = 'IDNAME_RESOURCES_GUESTBOOKS';
    }
  }

  protected function InitForm() {
    $this->title = 'Resource Summary';
    $this->icon = 'images/cm_resources.png';
    $this->activitydescription = 'some text here';
    $this->contextdescription = 'resource summary stuff';
    // show summary boxes
    $this->DoGallerySummaryBox();
    $this->DoNewsletterSummaryBox();
    $this->DoNewsletterSubscriberSummaryBox();
    $this->DoGuestBookSummaryBox();

// bookings
// private areas
// special dates
// articles
// ratings

    // hide buttons (summary only)
    $this->buttonmode = array();
  }

  protected function PostFields() {
    return 0;
  }

  protected function SaveToTable() {
    return 0;
  }

  protected function AddErrorList() {
  }

  protected function AssignFieldDisplayProperties() {
    $this->NewSection(
      'gallerygroup', 'Image Galleries', 'Your business information.');
    $this->fldgalleries->description = 'A summary of all your galleries';
    $this->AssignFieldToSection('gallerygroup', 'galleries');
    if ($this->fldnewsletters) {
      $this->NewSection(
        'newslettergroup', 'Newsletters', 'Your newsletters.');
      $this->fldnewsletters->description = 'A summary of all your newsletters';
      $this->AssignFieldToSection('newslettergroup', 'newsletters');
      $this->fldsubscribers->description = 'A summary of your current subscribers';
      $this->AssignFieldToSection('newslettergroup', 'subscribers');
    }
    if ($this->fldguestbooks) {
      $this->NewSection(
        'guestbookgroup', 'Guestbook', 'Your Guestbook Entries.');
      $this->fldguestbooks->description = 'A summary of all your guestbooks';
      $this->AssignFieldToSection('guestbookgroup', 'guestbooks');
    }
  }

}

$worker = new workerressummary();

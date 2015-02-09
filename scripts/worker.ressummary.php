<?php
require_once 'class.workerform.php';
require_once 'class.workerbase.php';
require_once 'class.formbuildersummarybox.php';

/**
  * activity worker for resource summary
  * dana framework v.3
*/

// resource summary

class workerressummary extends workerform {
  protected $fldorgdetails;
  protected $fldlogomediaid;
  protected $fldpagesummarylist;
  protected $fldgalleries;
  protected $fldnewsletters;
  protected $fldsubscribers;

  protected function InitForm() {
    $this->title = 'Resource Summary';
    $this->icon = 'images/cm_resources.png';
    $this->activitydescription = 'some text here';
    $this->contextdescription = 'resource summary stuff';
    $account = account::$instance;
    // galleries
    $galleries = gallery::GetGroupList(account::$instance->ID());
    $this->fldgalleries = $this->AddField(
      'galleries',
      new formbuildersummarybox('gallerieslist', '', 'Gallery List'),
      $this->account
    );
    foreach ($galleries as $galleryid => $gallery) {
      $linkedpage = gallery::FindGalleryLinkedPageDescription($galleryid);
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

    // newsletters
    $newsletters = $account->NewsletterList();
    $this->fldnewsletters = $this->AddField(
      'newsletters',
      new formbuildersummarybox('newsletterslist', '', 'Active Newsletters'),
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
    // subscribers
    $subscribers = $account->NewsletterSubscriberList();
    $this->fldsubscribers = $this->AddField(
      'subscribers',
      new formbuildersummarybox('subscriberlist', '', 'Active Subscribers'),
      $this->account
    );
    foreach ($subscribers as $subscriberid => $subscriber) {
      $this->fldsubscribers->AddItem(
        'subscriber-' . $subscriberid,
        $subscriber->FullName(),
        $subscriber->GetStatusAsString(true)
      );
    }
    $this->fldsubscribers->worker = $this;
    $this->fldsubscribers->changecaption = 'Manage Subscribers';
    $this->fldsubscribers->changeidname = 'IDNAME_RESOURCES_NEWSLETTERS';

// guestbooks

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

    $this->NewSection(
      'newslettergroup', 'Newsletters', 'Your newsletters.');
    $this->fldnewsletters->description = 'A summary of all your newsletters';
    $this->AssignFieldToSection('newslettergroup', 'newsletters');
    $this->fldsubscribers->description = 'A summary of your current subscribers';
    $this->AssignFieldToSection('newslettergroup', 'subscribers');
  }

}

$worker = new workerressummary();

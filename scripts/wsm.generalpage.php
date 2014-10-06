<?php
require_once 'class.websitemanager.php';

// website manage for GENERAL PAGE
// Modified: 2014-08-29

/*
general page
  main content: main text
  sidebar - articles: list of article headers (last 10)
  sidebar - guestbook: list of guestbooks (list of guestbook desc & count) & login / logout link
  sidebar - newsletters: list of active newsletters / subscription link
  sidebar - socialnetworks: list of social network contacts
  sidebar - calendar: list of calendar entries
  sidebar - booking: link to booking page
  sidebar - privatearea: login/logout link
  sidebar - downloadablefiles: list of files
*/

class wsm_generalpage extends websitemanager {
  private $gengalleryid;

  protected function GetPageType() {
    require_once $this->sourcepath . DIRECTORY_SEPARATOR . 'class.table.pagegeneral.php';
    $this->page = new pagegeneral($this->pageid);
    return PAGETYPE_GENERAL;
  }

  protected function DoGallerySlideShow() {
    $gallery = new gallery($this->gengalleryid);
//      $galleryheight = $gallery->GetGalleryHeight();
    return $gallery->BuildSlideShowList();
  }

  protected function GetMainContent($groupid) {
    $this->gengalleryid = $this->page->GetFieldValue('gengalleryid');
    $slideshow = ($this->gengalleryid > 0) ? $this->DoGallerySlideShow() : array();
    $content = $this->page->GetFieldValue('maincontent');
    $ret = $slideshow;
    $ret[] = $content;
    return $ret;
  }
}

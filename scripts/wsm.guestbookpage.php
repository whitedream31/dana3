<?php
require_once 'class.websitemanager.php';
/*
guestbook page
  main content: guestbook viewer
  sidebar - articles: list of article headers (last 10)
  sidebar - guestbook: list of guestbooks (list of guestbook desc & count) & login / logout link
  sidebar - newsletters: list of active newsletters / subscription link
  sidebar - socialnetworks: list of social network contacts
  sidebar - calendar: list of calendar entries
  sidebar - booking: link to booking page
  sidebar - privatearea: login/logout link
  sidebar - downloadablefiles: list of files
*/

class wsm_guestbookpage extends websitemanager {

  protected function GetPageType() {
    require_once $this->sourcepath . DIRECTORY_SEPARATOR . 'class.table.pageguestbook.php';
    $this->page = new pageguestbook($this->pageid);
    return PAGETYPE_GUESTBOOK;
  }

  private function DoGuestBookMainContent($groupid) {
    $ret = array();
    $guestbook = new guestbook($groupid);
    if ($guestbook->exist) {
      $entrylist = guestbookentry::GetList($groupid);
      $guestbookheading = $guestbook->GetFieldValue(FN_DESCRIPTION);
      if (!$guestbookheading) {
        $guestbookheading = 'Guest Book';
      }
      $ret[] = "<div class='mainsection'>";
      $ret[] = "  <h2>{$guestbookheading}</h2>";
      foreach($entrylist as $entryid => $entry) {
        $ret[] = "  <div class='entryitem'>";
        $ret[] = '    ' . $entry->GetEntryDetails();
        $ret[] = '  </div>';
      }
      $ret[] = '</div>';
    }
    return ArrayToString($ret); //"<h3>TODO: GUESTBOOK VIEWER</h3>";
  }

  protected function GetMainContent($groupid) {
    return $this->DoGuestBookMainContent($groupid);
  }
}

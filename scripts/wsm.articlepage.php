<?php
require_once 'class.websitemanager.php';
/*
articles page
  main content: article viewer
  sidebar - articles: list of article categories
  sidebar - guestbook: list of guestbooks (list of guestbook desc & count) & login / logout link
  sidebar - newsletters: list of active newsletters / subscription link
  sidebar - socialnetworks: list of social network contacts
  sidebar - calendar: list of calendar entries
  sidebar - booking: link to booking page
  sidebar - privatearea: login/logout link
  sidebar - downloadablefiles: list of files
*/

class wsm_articlepage extends websitemanager {

  protected function GetPageType() {
    require_once $this->sourcepath . DIRECTORY_SEPARATOR . 'class.table.pagearticle.php';
    $this->page = new pagearticle($this->pageid);
    return PAGETYPE_ARTICLE;
  }

  private function DoArticleContent() {
    return "<h3>TODO: ARTICLES</h3>";
  }

  protected function GetMainContent($groupid) {
    return $this->DoArticleContent();
  }
}

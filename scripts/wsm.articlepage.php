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

/*  private function DoArticleContent() {
    $articlelist = $this->page->account->ArticleList();
    $list = array();
    //$currentcategory = '';
//    $ret = array("<h3>TODO: ARTICLES</h3>");
    $ret = array(); //$this->page->);
    foreach($articlelist as $item) {
      $expirydate = $item->GetFieldvalue('expirydate');
      //if ($expirydate)
      $newcat = $item->GetFieldvalue('category');
      $list[$newcat][] = $item;
    }

    foreach($list as $catname => $items) {
      $ret[] = '<section>';
      $ret[] = "  <h2>{$catname}</h2>";
      foreach($items as $item) {
//        $id = $item->ID();
        $dateadded = strtotime($item->GetFieldvalue('stampupdated'));
        $content = $item->GetFieldvalue('content');
        $heading = $item->GetFieldvalue('heading');
//        $category = $item->GetFieldvalue('category');
        $ret[] = "<div class='articleitem'>";
        $ret[] = "  <h2>{$heading}</h2>";
        $ret[] = "  <p class='articlepublished'>Published:{$dateadded}</p>";
        $ret[] = $content;
        $ret[] = '</div>';
        $ret[] = '<hr>';
//          "<p><b>URL:</b> " . $item->GetFieldvalue('url') . "</p>\n" .
      }
      $ret[] = '</section>';
    }
    return $ret;
  } */

  protected function GetMainContent($groupid) {
    return $this->GetArticlesMain();
//    return
//      $this->DoArticleContent();
  }
}

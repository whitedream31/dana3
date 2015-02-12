<?php
namespace dana\webmanager;

require_once 'class.websitemanager.php';
/*
gallery page
  main content: gallery viewer
  sidebar - articles: list of article headers (last 10)
  sidebar - guestbook: list of guestbooks (list of guestbook desc & count) & login / logout link
  sidebar - newsletters: list of active newsletters / subscription link
  sidebar - socialnetworks: list of social network contacts
  sidebar - calendar: list of calendar entries
  sidebar - booking: link to booking page
  sidebar - privatearea: login/logout link
  sidebar - downloadablefiles: list of files
*/

/**
  * website manager gallery page class
  * @version dana framework v.3
*/

class wsm_gallerypage extends websitemanager {

  protected function GetPageType() {
    require_once $this->sourcepath . DIRECTORY_SEPARATOR . 'class.table.pagegallery.php';
    $this->page = new pagegallery($this->pageid);
    return page::PAGETYPE_GALLERY;
  }

  private function WriteGalleryPrepareCode() {
/*    $pageid = $this->page->ID();
    $rootdirectory = $this->pagewriter->rootdirectory;
    return ArrayToString(array(
      "require_once('{$this->sourcepath}class.table.gallery.php');",
      "",
//      $this->AddGetVar('pg', 'pagenumbergallery', '1') .
      "\$mode = {$this->mode};",
      "\$rootdirectory = '{$rootdirectory}';",
      "",
      "function ShowGallery(\$galleryid) {",
      "  \$gallery = new gallery(\$galleryid);",
      "  echo \$gallery->BuildGallery({$pageid});",
      "}"
    )); */
  }

  public function DoGallery($galleryid) {
    $gallery = new gallery($galleryid);
    $start = 0; // TODO
    $imagesperpage = 99; //$this->page->GetFieldValue('imagesperpage');
    $cols = 3; //(int) ($imagesperpage / 4);
    $incdescription = true; //$this->page->GetFieldValue('incdescription');
    $view = $gallery->BuildGalleryViewer($start, $imagesperpage, $cols, $incdescription, 'col3');
    $ret = array("<div id='gallery'>");
    $ret = array_merge($ret, $view);
    $ret[] = '</div>';
    return $ret;
  }

  protected function GetMainContent($groupid) {
    return $this->DoGallery($groupid);
  }
}

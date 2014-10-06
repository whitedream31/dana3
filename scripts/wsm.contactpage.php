<?php
require_once 'class.websitemanager.php';
require_once 'class.formprocessor.php';
require_once 'class.formbuildereditbox.php';
require_once 'class.formbuilderemail.php';
require_once 'class.formbuildertextarea.php';
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

class wsm_contactpage extends websitemanager {

  protected function GetPageType() {
    require_once $this->sourcepath . DIRECTORY_SEPARATOR . 'class.table.pagecontact.php';
    $this->page = new pagecontact($this->pageid);
    return PAGETYPE_CONTACT;
  }

  protected function DoContactForm() {
    require_once 'fp.contact.php';
    $formprocessor = new fpcontact('contactform');
    $formprocessor->AssignPageID($this->pageid);
    return array($formprocessor->Execute());
  }

  protected function DoMap() {
    $ret = array();
    $contact = self::$account->Contact();
    if (self::$account->GetFieldValue('showaddress') && !$contact->GetFieldValue('onlineonly')) {
      $addr = $this->page->GetFieldValue('mapaddress');
      if (!$addr) {
        $firstline = $contact->GetFieldValue('address');
        $addr = ($firstline) ? $contact->FullAddress('', ' ', true) : false;
      }
      if ($addr) {
        $addrarray = explode('+', urlencode(str_replace('  ', ' ', $addr)));
        $q = implode(',+', $addrarray);
        $ret = array(
          "<h2>Map</h2>\n",
          "<p>Address is '{$addr}'</p>\n",
          "<iframe width='400' height='400' frameborder='1' scrolling='no' marginheight='0' marginwidth='0' ",
          "src='http://maps.google.co.uk/maps?q={$q},+uk&amp;ie=UTF8&amp;hq=&amp;hnear={$q}" .
          ",+uk&amp;t=m&amp;z=16&amp;vpsrc=0&amp;output=embed'></iframe><br>"
        );
      }
    }
    return $ret;
  }

  protected function GetMainContent($groupid) {
    return array_merge($this->DoContactForm(), $this->DoMap());
  }
}

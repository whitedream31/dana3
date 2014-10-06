<?php
require_once 'class.websitemanager.php';
/*
booking page
  main content: booking viewer
  sidebar - articles: list of article headers (last 10)
  sidebar - guestbook: list of guestbooks (list of guestbook desc & count) & login / logout link
  sidebar - newsletters: list of active newsletters / subscription link
  sidebar - socialnetworks: list of social network contacts
  sidebar - calendar: list of calendar entries
  sidebar - booking: none
  sidebar - privatearea: login/logout link
  sidebar - downloadablefiles: list of files
*/

class wms_bookingpage extends websitemanager {

  protected function GetPageType() {
    return PAGETYPE_BOOKING;
  }

  private function DoBookings() {
    return "<h3>TODO: BOOKNGS</h3>";
  }

  protected function GetMainContent($pgtype, $groupid) {
    return $this->DoBookings();
  }

}

<?php
require_once 'class.websitemanager.php';
/*
calendar page
  main content: calendar viewer
  sidebar - articles: list of article headers (last 10)
  sidebar - guestbook: list of guestbooks (list of guestbook desc & count) & login / logout link
  sidebar - newsletters: list of active newsletters / subscription link
  sidebar - socialnetworks: list of social network contacts
  sidebar - calendar: summary (month / year)
  sidebar - booking: link to booking page
  sidebar - privatearea: login/logout link
  sidebar - downloadablefiles: list of files
*/

class wms_calendarpage extends websitemanager {

  protected function GetPageType() {
    return PAGETYPE_CALENDAR;
  }

  protected function GetMainContent($pgtype, $groupid) {
    return "<h3>TODO: CALENDAR</h3>";
  }
}

<?php
require_once 'class.table.account.php';

$account = \dana\table\account::StartInstance(542); //542); //322);

if ($account->exists) {
  echo "<h1>ACCOUNT EXISTS</h1>\n";
  echo "<p>Account ID: " . $account->ID() . "</p>\n";
  echo "<p>Account Name: " . $account->GetFieldValue('businessname') . "</p>\n";
  echo "<h2>PAGES:</h2>\n";
  $pagelist = $account->GetPageList();
  $pages = $pagelist->pages;
  echo "<ul>\n";
  foreach ($pages as $pageid => $page) {
    $desc = $page->GetFieldValue('description') . " ({$page->pagetypedescription} page)";
    if ($page == $pagelist->homepage) {
      $desc = "<strong>{$desc}</strong>";
    }
    echo "<li>" . $page->ID() . ' - ' . $desc . "</li>\n";
  }
  echo "</ul>\n";
  echo "<h2>PRIVATE AREAS:</h2>\n";
  $privatearealist = $account->PrivateAreaGroupList();
  echo "<ul>\n";
  foreach ($privatearealist as $privateareaid => $privatearea) {
    $linkedpages = $privatearea->linkedpages;
    $desc = $privatearea->GetFieldValue('title') . ' - count: ' . count($linkedpages);
    echo "<li>{$desc}</li>\n";
  }
  echo "</ul>\n";

} else {
  echo "<p>ACCOUNT DOES NOT EXIST</p>\n";
}

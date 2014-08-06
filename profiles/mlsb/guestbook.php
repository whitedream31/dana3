<?php
/* check for publishing state */
require_once '/home/freem719/public_html/tstmylocalsmallbusinesscom/dana3/scripts/class.websitemanager.php';
websitemanager::SetAccount(2);
/* guest book prepare */

require_once('/home/freem719/public_html/tstmylocalsmallbusinesscom/dana3/scripts/class.table.guestbook.php');
require_once('/home/freem719/public_html/tstmylocalsmallbusinesscom/dana3/scripts/class.table.visitor.php');
require_once('/home/freem719/public_html/tstmylocalsmallbusinesscom/dana3/scripts/class.table.question.php');

$guestbook = new guestbookprocess(, 586);

function ShowGuestBook() {
  global $guestbook;
  echo $guestbook->BuildPage('/home/freem719/public_html/tstmylocalsmallbusinesscom/dana3/profiles/mlsb/');
}

function ShowGuestBookSideContent() {
  global $guestbook;
  echo $guestbook->BuildSideContent();
}
?>
<!DOCTYPE html>
<!--
Design by Free CSS Templates
http://www.freecsstemplates.org
Released for free under a Creative Commons Attribution 2.5 License
Title      : Clean Type
Version    : 1.0
Released   : 20100104
Description: A two-column fixed-width template suitable for small websites.
-->
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>MyLocalSmallBusiness - Guest Book</title>
<meta name="keywords" content="free,website,mini-website,marketing,advertise, small business,google,simple,mlsb,mylocalsmallbusiness, my local small business,online" />
<meta name="description" content="We offer custom but affordable software services and products for the small business, non-for-profit organisations and home users." />
<link href='//cdn.mlsb.org/css/shared.css' rel='stylesheet' type='text/css' media='all' />
<link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="pagewrapper">
<div id="wrapper">
<div id="header">
<div id="logo">
<h1>Guest Book</h1>
<h2>simply putting you online</h2>
</div>
<div id="menu">
<nav id='menu'><ul>
  <li class='active'><a href='index.php'>Home</a></li>
  <li class='active'><a href='index4.php'>Services</a></li>
  <li class='active'><a href='guestbook.php'>Guest Book</a></li>
  <li class='active'><a href='contact.php'>Contact</a></li>
  <li class='active'><a href='gallery.php'>Portfolio</a></li>
</ul></nav>
</div>
</div>
<div id="page">
<div id="page-bgtop">
<div id="content">
<div id='logoimg' class='post'><a href='http://mylocalsmallbusiness.com' target='_blank' title='visit our main website'><img alt='MyLocalSmallBusiness' src='media/img1.png' /></a></div>
<div style="clear: both; height: 1px"></div>
<div id='inittext' class='post'><?php $guestbook->WriteInitialMessage(); ?></div>
<div id='maintext' class='post'><?php ShowGuestBook(); ?>
</div>
</div>
<div id="sidebar">
<div id='contactdetails'>
<h2>Contact Details</h2>
<ul>
  <li>Haverhill</li>
  <li>cb9 9nd</li>

</ul>
<ul>
  <li><img src='//cdn.mlsb.org/images/email.png' alt=''>&nbsp;<span>e-mail:</span> <a title='click to send a message now' href='mailto:orrin31@yahoo.com'>orrin31@yahoo.com</a></li>

  <li><img src='//cdn.mlsb.org/images/website.png' alt=''>&nbsp; <a href='http://mylocalsmallbusiness.com' target='_blank' title='visit our website'>http://mylocalsmallbusiness.com</a></li>

</ul>
</div>
(todo: list of recent articles)
<?php ShowGuestBookSideContent(); ?>

<p>TODO: link to subscribe to newsletters
<div id='socialnetworksidebar'>  <h2>Social Networks</h2>
  <ul>
    <li><a href='http://www.facebook.com/pages/MyLocalSmallBusiness/427734090622256' title='visit us on Facebook' target='_blank'><img src='//cdn.mlsb.org/images/social/16x16/facebook.gif' alt='Facebook' /><span>Visit us on Facebook</span></a>
</li>
  </ul>
</div>
<p>TODO: Calendar Sidebar</p>
<p>TODO: Booking Sidebar</p>
<p>TODO: Private Area Sidebar</p>
<?php websitemanager::ShowBlogSideContent(); ?>
customsidebar
articles
guestbook
newsletters
socialnetworks
calendar
booking
privatearea
survey
<div style="clear: both;">&nbsp;</div>
downloadablefiles
addthis
</div>
<div style="clear: both; height: 1px"></div>
</div>
</div>
</div>
<div id="footer">
<p>&copy; 2012 Whitedream Software</p>
<p class='designname'>designed by <a title='Free CSS Templates' href='http://www.freecsstemplates.org'>Free CSS Templates</a>. <small>Modified by <a title='whitedream software' href='http://whitedreamsoftware.co.uk'>Whitedream Software</a>.</small> &mdash; <a title='click to sign up' href='http://mylocalsmallbusiness.com'><strong>FREE Mini Websites</strong></a></p><br>
</div>
</div>
</body>
</html>

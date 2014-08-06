<?php
/* check for publishing state */
require_once '/home/freem719/public_html/tstmylocalsmallbusinesscom/dana3/scripts/class.websitemanager.php';
websitemanager::SetAccount(2);
/* gallery prepare */
require_once('/home/freem719/public_html/tstmylocalsmallbusinesscom/dana3/profiles/mlsb/scripts/galleryclass.php');

$mode = writeprofile;
$rootdirectory = '';

function ShowGallery($galleryid) {
  $gallery = new gallery($galleryid);
  echo $gallery->BuildGallery(5);
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
<title>MyLocalSmallBusiness - Portfolio</title>
<meta name="keywords" content="free,website,mini-website,marketing,advertise, small business,google,simple,mlsb,mylocalsmallbusiness, my local small business,online" />
<meta name="description" content="We offer custom but affordable software services and products for the small business, non-for-profit organisations and home users." />
<link href='//cdn.mlsb.org/css/shared.css' rel='stylesheet' type='text/css' media='all' />
  <link href='//cdn.mlsb.org/lightbox.css' rel='stylesheet' type='text/css' media='screen'/>
  <script src='//cdn.mlsb.org/jquery.js'></script>
  <script src='//cdn.mlsb.org/lightbox.js'></script>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<script type='text/javascript' src='//cdn.mlsb.org/rotator.js'></script>
<div id="pagewrapper">
<div id="wrapper">
<div id="header">
<div id="logo">
<h1>Website Portfolio</h1>
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
<div id='inittext' class='post'><p><strong>We offer complete custom and very affordable web  site design and hosting for the small business, non-for-profit  organisations and home users.</strong></p>
<p>Here is a sample of work we have done for some our clients</p></div>
<div id='maintext' class='post'><?php ShowGallery(2); ?>
</div>
</div>
<div id="sidebar">
<div id='contactdetails'>
<h2>Contact Details</h2>
<ul>
  <li>Mr Ian Smith</li>
  <li>Haverhill</li>
  <li>Suffolk</li>
  <li>cb9 9nd</li>

</ul>
<ul>
  <li><img src='//cdn.mlsb.org/images/email.png' alt=''>&nbsp;<span>e-mail:</span> <a title='click to send a message now' href='mailto:orrin31@yahoo.com'>orrin31@yahoo.com</a></li>

  <li><img src='//cdn.mlsb.org/images/website.png' alt=''>&nbsp; <a href='http://mylocalsmallbusiness.com' target='_blank' title='visit our website'>http://mylocalsmallbusiness.com</a></li>

</ul>
</div>
(todo: list of recent articles)

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
<p style="text-align: center;"><span style="font-size: large;">&copy; 2011 Whitedream Software</span></p>
<p class='designname'>designed by <a title='Free CSS Templates' href='http://www.freecsstemplates.org'>Free CSS Templates</a>. <small>Modified by <a title='whitedream software' href='http://whitedreamsoftware.co.uk'>Whitedream Software</a>.</small> &mdash; <a title='click to sign up' href='http://mylocalsmallbusiness.com'><strong>FREE Mini Websites</strong></a></p><br>
</div>
</div>
</body>
</html>

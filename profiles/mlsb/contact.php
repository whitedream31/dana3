<?php
/* check for publishing state */
require_once '/home/freem719/public_html/tstmylocalsmallbusinesscom/dana3/scripts/class.websitemanager.php';
websitemanager::SetAccount(2);
<?php require('/home/freem719/public_html/tstmylocalsmallbusinesscom/dana3/profiles/mlsb/scripts/client.profile.contact.php');

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
<title>MyLocalSmallBusiness - Contact</title>
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
<h1>Contact Us</h1>
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
<div id='inittext' class='post'><p><strong>If you have any question or comments please fill-in the details below. If you require a reply, please remember to add your e-mail address.</strong></p>
<p>If you wish for a free quote please <a href="http://whitedreamsoftware.com/contactusbespoke.php">click here</a> instead.</p></div>
<div id='maintext' class='post'><?php echo $response; ?>
<form name='formcontact' method='post' enctype='application/x-www-form-urlencoded' action='.php'>
  <fieldset class='contact'>
    <div>
      <label for='contactname'>Your Name</label>
      <br><input class='contactinput' name='contactname' type='text' value='<?php echo $contactname; ?>' maxlength='100' />
    </div>
    <br>
    <div>
      <label for='contactemail'>Your E-Mail</label>
      <br><input class='contactinput' name='contactemail' type='text' value='<?php echo $contactemail; ?>' maxlength='100' />
    </div>
    <br>
    <div>
      <label for='contactsubject'>Subject</label>
      <br><input class='contactinput' name='contactsubject' type='text' value='<?php echo $contactsubject; ?>' maxlength='100' />
    </div>
    <br>
    <div>
      <label for='contactmessage'>Message</label>
      <br><textarea class='contactinput' name='contactmessage' rows='20' cols='80'><?php echo $contactmessage; ?></textarea>
    </div>
    <br>
    <div>
      <?php $question = new questionmanager(); echo $question->ShowQuestion('/home/freem719/public_html/tstmylocalsmallbusinesscom/dana3/profiles/mlsb/questions'); ?>
    </div>
    <br>
    <div class='button'>
      <input type='reset' name='btnclear' title='clear fields' value='Clear' />
      <input type='submit' name='btnsubmit' title='send message' value='Send' />
    </div>
  </fieldset>
</form>
<iframe width='400' height='400' frameborder='1' scrolling='no' marginheight='0' marginwidth='0' src='http://maps.google.co.uk/maps?q=Haverhill,+Suffolk,+cb9,+9nd,+,+uk&amp;ie=UTF8&amp;hq=&amp;hnear=Haverhill,+Suffolk,+cb9,+9nd,+,+uk&amp;t=m&amp;z=16&amp;vpsrc=0&amp;output=embed'></iframe><br>
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
<p style="text-align: center;"><span style="font-size: large;">&copy; 2014 MyLocalSmallBusiness</span></p>
<p class='designname'>designed by <a title='Free CSS Templates' href='http://www.freecsstemplates.org'>Free CSS Templates</a>. <small>Modified by <a title='whitedream software' href='http://whitedreamsoftware.co.uk'>Whitedream Software</a>.</small> &mdash; <a title='click to sign up' href='http://mylocalsmallbusiness.com'><strong>FREE Mini Websites</strong></a></p><br>
</div>
</div>
</body>
</html>

<?php
session_start();
$mem = ini_get('memory_limit'); // '128M' // handle large images
$max = ini_get('post_max_size'); // '8M'
$fsz = ini_get('upload_max_filesize'); // '8M'
/*
  * main control script
  * dana framework v.3
*/

require_once 'class.status.php';
require_once 'class.activitymanager.php';
require_once 'class.table.account.php';

/*
acc  322 / 542
con  454 / 675
nick lorem
usr  lorem
pwd  ipsum
*/

$account = account::StartInstance(542); //322);

$_SESSION[SESS_IDGROUP] = 3;

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <meta http-equiv="pragma" content="no-cache">
  <meta http-equiv="expires" content="-1">
  <title>My Local Small Business - Control Page</title>
  <meta name="description" content="My Local Small Business - Account Page">
  <link href="../css/control.css" rel="stylesheet" type="text/css">
  <!--link href="../css/datepicker.css" rel="stylesheet" type="text/css"-->
  <!--[if IE]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
  <script type="text/javascript" src="../jscripts/jquery.js"></script>
  <script type="text/javascript" src="../jscripts/jquery-ui.js"></script>
  <script src="//tinymce.cachefly.net/4.1/tinymce.min.js"></script>
  <!--script type="text/javascript" src="../jscripts/accordion.js"></script-->
</head>
<body lang="en">
  <div id="controlcontent">
    <div id="msg"></div>  
    <header id="mainheading">
      <div id="mainlogo">
        <h1 id="headertitle"><?php status::ShowTitle(); ?></h1>
        <div id="loginline">
          <span><?php status::ShowUserStatus(); ?></span>
        </div>
      </div>
    </header>

    <nav id="menu">
      <ul>
        <!--li><a href="home.php?m=' . MNU_HOME  . '" accesskey="1" title="go to control page">Control</a></li-->
        <li><a href="../contact.php" accesskey="1" title="send us a message">Contact Us</a></li>
        <li><a href="../privacy.html" accesskey="2" title="read our policy about the data we keep about you">Privacy Policy</a></li>
        <li><a href="../tc.html" accesskey="3" title="read our terms and conditions">Conditions</a></li>
        <li><a href="../faq.html" accesskey="4" title="read our frequently asked questions">FAQ</a></li>
      </ul>
    </nav>
    <div style="clear: both;">&nbsp;</div>
<?php
$activitymanager->Show();
?>
    <div style="float:none; clear: both;">&nbsp;</div>
    <footer id="footer">
      <p id="footercopyright"><?php status::ShowFooter(); ?></p>
      <p id="links"><a href="privacy.html">Privacy Policy</a> | <a href="tc.html">Terms of Use</a></p>
    </footer>
  </div>
<?php
$activitymanager->ShowAccordion();
?>
</body>
</html>

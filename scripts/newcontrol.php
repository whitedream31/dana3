<?php
session_start();
//$mem = ini_get('memory_limit'); // '128M' // handle large images
//$max = ini_get('post_max_size'); // '8M'
//$fsz = ini_get('upload_max_filesize'); // '8M'

define('CONTROLMANAGER_ID', 1);

/*
  * main control script
  * dana framework v.3
*/

//require_once 'class.status.php';
//require_once 'class.activitymanager.php';
require_once 'class.table.account.php';
require_once 'class.table.controlmanager.php';

require_once 'class.activitymanager.php';

$controlmanager = controlmanager::StartInstance(CONTROLMANAGER_ID);

/*
acc  322 / 542
con  454 / 675
nick lorem
usr  lorem
pwd  ipsum
*/

$account = account::StartInstance(322); //542); //322);

//$_SESSION[SESS_IDGROUP] = 3;

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <meta http-equiv="pragma" content="no-cache">
  <meta http-equiv="expires" content="-1">
  <title><?php controlmanager::ShowTitle(); ?></title>
  <meta name="description" content="<?php controlmanager::ShowDescription(); ?>">
  <link href="<?php controlmanager::ShowStyleSheet(); ?>" rel="stylesheet" type="text/css">
  <!--link href="../css/datepicker.css" rel="stylesheet" type="text/css"-->
  <!--[if IE]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
  <script type="text/javascript" src="../jscripts/jquery.js"></script>
  <script type="text/javascript" src="../jscripts/jquery-ui.js"></script>
  <script type="text/javascript" src="../jscripts/helpers.js"></script>
  <script src="//tinymce.cachefly.net/4.1/tinymce.min.js"></script>
  <!--script type="text/javascript" src="../jscripts/accordion.js"></script-->
</head>
<body lang="en">
  <div id="controlcontent">
    <div id="msg"></div>  
    <header id="mainheading">
      <div id="mainlogo">
        <h1 id="headertitle"><?php controlmanager::ShowTitle(); ?></h1>
        <div id="loginline">
          <span><?php controlmanager::ShowUserStatus(); ?></span>
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
    <div id="controlmenu">
<?php
controlmanager::ShowControlMenu();
?>
    </div>
    <div id="controlworkarea">
<?php
controlmanager::ShowActiveItem();
?>
    </div>
    <div style="float:none; clear: both;">&nbsp;</div>
    <footer id="footer">
      <p id="footercopyright"><?php controlmanager::ShowFooter(); ?></p>
      <p id="links"><a href="privacy.html">Privacy Policy</a> | <a href="tc.html">Terms of Use</a></p>
    </footer>
  </div>
<!-- ?php
//$activitymanager->ShowAccordion();
?-->
</body>
</html>

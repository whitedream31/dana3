<?php
require_once 'class.pagewriter.php';
require_once 'class.table.account.php';

account::StartInstance(2);
$pagewriter = new pagewriter(array(PWOPT_PROFILE, PWOPT_TEST));

?>
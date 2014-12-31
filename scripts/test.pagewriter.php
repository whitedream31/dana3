<?php
require_once 'class.pagewriter.php';
require_once 'class.table.account.php';

// acc: 542, pgmgr: 676, cont: 675

account::StartInstance(542); //322); //542);
$pagewriter = new pagewriter(array(PWOPT_PROFILE, PWOPT_TEST));
$pagewriter->AssignThemeByRef('ASTERISK');
$pagewriter->Execute();

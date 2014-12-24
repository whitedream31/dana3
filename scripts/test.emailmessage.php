<?php
include_once 'class.table.emailmessage.php';

$account = account::StartInstance(542);

$msg = new emailmessage(1);

echo "<h1>testing</h1>\n";

$content = $msg->GetFieldValue('content');
$formatted = nl2br($msg->formattedcontent);

/*
$text = array(
  'The nickname is [%nickname%], business name [%businessname%].',
  'This contact name is [%title%] [%firstname%] [%lastname%].'
);
$ret = nl2br($msg->FormatText($text));
*/

echo "<p>{$formatted}</p>\n";

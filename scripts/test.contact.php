<html>
  <body>
    <h1>Contact Form Test</h1>
<?php
require 'class.table.account.php';
$account = account::StartInstance(4);

$formprocessor = new formprocessor('testform');
$name = $formprocessor->AddField(
  'name',
  new formbuildereditbox('name', '', 'Name')
);
$name->required = true;
$formprocessor->AddField(
  'email',
  new formbuilderemail('email', '', 'E-Mail')
);
$formprocessor->AddField(
  'subject',
  new formbuildereditbox('subject', '', 'Subject')
);
$message = $formprocessor->AddField(
  'message',
  new formbuildertextarea('message', '', 'Message')
);

echo $formprocessor->Execute();

?>
  </body>
</html>
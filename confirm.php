<!DOCTYPE html>
<?php
require_once 'class.database.php';
require_once 'class.table.newslettersubscriber.php';

function ProcessNewsletterSubscription($ref) {
  $ln = database::SelectFromTableByField('newslettersubscriber', 'sessionref', $ref, FN_ID);
  $found = (bool) $ln;
  if ($found) {
    $id = $ln[FN_ID];
    $subscriber = new newslettersubscriber($id);
    if ($subscriber->exists) {
      $subscriber->SetFieldValue(FN_STATUS, STATUS_ACTIVE);
      $subscriber->StoreChanges();
      $fullname = $subscriber->FullName();
      $msg = "Thank you, {$fullname}, for subscribing to our newsletter.";
    } else {
      $found = false;
    }
  }
  if (!$found) {
    $msg =
      "Sorry, we couldn't find your subscription details. Please " .
      "check you have copied the entire link and try again.";
  }
  return array(
    'main' => 'Newsletter Subcription',
    'msg' => $msg
  );
}

function ProcessNewsletterUnSubscribe($ref) {
  $ln = database::SelectFromTableByField('newslettersubscriber', 'sessionref', $ref, FN_ID);
  $found = (bool) $ln;
  if ($found) {
    $id = $ln[FN_ID];
    $subscriber = new newslettersubscriber($id);
    if ($subscriber->exists) {
      $subscriber->SetFieldValue(FN_STATUS, STATUS_UNSUBSCRIBED);
      $subscriber->StoreChanges();
      $fullname = $subscriber->FullName();
      $msg = "{$fullname}, you have been unsubscribed from our newsletter. Thank you for your interest.";
    } else {
      $found = false;
    }
  }
  if (!$found) {
    $msg =
      "Sorry, we couldn't find your subscription details. Please " .
      "check you have copied the entire link and try again.";
  }
  return array(
    'main' => 'Newsletter Un-Subcription',
    'msg' => $msg
  );
}

function ProcessUnknownAction() {
  return array(
    'main' => 'Unknown Action',
    'msg' => 'Sorry, the link you supplied is either corrupt or missing'
  );
}

// process action type and session reference
$action = strtolower(GetPost('act'));
$ref = GetPost('r'); // session ref
switch ($action) {
  case 'n': // confirm newsletter subscription
    $lines = ProcessNewsletterSubscription($ref);
    break;
  case 'u': // unsubscribing from newsletters
    $lines = ProcessNewsletterUnSubscribe($ref);
    break;
  default:
    $lines = ProcessUnknownAction();
}
?>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Confirmation Page</title>
  </head>
  <body>
    <h1><?php echo $lines['main']; ?></h1>
    <p>
<?php echo $lines['msg']; ?>
    </p>
  </body>
</html>

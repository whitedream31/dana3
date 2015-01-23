<!DOCTYPE html>
<?php
require_once 'scripts/class.database.php';
require_once 'scripts/class.table.newslettersubscriber.php';

function ProcessNewsletterSubscription($ref) {
  $ln = database::SelectFromTableByField('newslettersubscriber', 'sessionref', $ref, basetable::FN_ID);
  $found = (bool) $ln;
  if ($found) {
    $id = $ln;
    $subscriber = new newslettersubscriber($id);
    if ($subscriber->exists) {
      $subscriber->SetFieldValue(basetable::FN_STATUS, basetable::STATUS_ACTIVE);
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
  $ln = database::SelectFromTableByField('newslettersubscriber', 'sessionref', $ref, basetable::FN_ID);
  $found = (bool) $ln;
  if ($found) {
    $id = $ln[basetable::FN_ID];
    $subscriber = new newslettersubscriber($id);
    if ($subscriber->exists) {
      $subscriber->SetFieldValue(basetable::FN_STATUS, newslettersubscriber::STATUS_UNSUBSCRIBED);
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
    'msg' => 'Sorry, the address (or other information) you supplied is either corrupt or missing'
  );
}

// process action type and session reference
$action = strtolower(GetGet('act'));
$ref = GetGet('r'); // session ref
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

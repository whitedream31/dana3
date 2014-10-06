<?php
require_once 'class.formprocessor.php';

/**
 * form processor for dealing with contact pages
 *
 * @author Ian Stewart
 */
class fpcontact extends formprocessor {

  private $page;

  protected function AssignValues() {
    $this->showcancel = false;
    $this->submitcaption = 'Send';
    $this->includequestion = true;
    $this->formtitle = 'Send a Message';
  }

  protected function ProcessSubmit() {
    require_once 'class.table.history.php';
    $name = $this->GetFieldValue('name');
    $email = $this->GetFieldValue('email');
    $subject = $this->GetFieldValue('subject');
    $message = $this->GetFieldValue('message');
    $contact = account::$instance->Contact();
    $nickname = $contact->GetFieldValue('nickname');
    $bn = $contact->GetFieldValue('businessname');
    $url = 'mlsb.org/' . $nickname;

    $msg = array(
      "Hi {$contact->displayname},",
      "You have recieved a new message from your contact page at {$url}.");
    if ($name) {
      $msg[] = "Senders name: {$name}";
    }
    if ($email) {
      $msg[] = "Contact information: {$email}";
    }
    if ($subject) {
      $msg[] = "Message is about: {$subject}";
    }
    $msg[] = "---- Message starts ----";
    $msg[] = wordwrap($message, 70, "\n");
    $msg[] = "---- Message ends ----";
    $contact->SendContactMessage($name, $email, $subject, ArrayToString($msg));

    //history::MakeHistoryItem(self::$account->ID(), HIST_CONTACTMSG, $details);
/*    echo "<h2>POST LIST</h2>\n";
    foreach ($_POST as $pk => $pv) {
      echo "<p>{$pk} = '{$pv}'</p>\n";
    }
    exit; */
  }

  protected function AssignFields() {
    if (!isset($this->page)) {
      die('page not assigned for contact processor');
    }
    // name field
    $namelabel = $this->page->GetFieldValue('contactname');
    if ($namelabel) {
      $this->AddField(
        'name', new formbuildereditbox('name', '', $namelabel)
      )->required = true;
    }
    // email field
    $emaillabel = $this->page->GetFieldValue('contactemail');
    if ($emaillabel) {
      $this->AddField(
        'email', new formbuilderemail('email', '', $emaillabel)
      );
    }
    // subject field
    $subjectlabel = $this->page->GetFieldValue('contactsubject');
    if ($subjectlabel) {
      $this->AddField(
        'subject', new formbuildereditbox('subject', '', $subjectlabel)
      );
    }
    // message field
    $messagelabel = $this->page->GetFieldValue('contactmessage');
    $this->AddField(
      'message',
      new formbuildertextarea('message', '', $messagelabel)
    );
  }

  public function AssignPageID($pgid) {
    $this->page = new pagecontact($pgid);
  }
}

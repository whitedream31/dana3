<?php
//ctfi
require_once 'class.workerform.php';
require_once 'class.workerbase.php';
require_once 'class.formbuildereditbox.php';
require_once 'class.formbuilderpassword.php';

/**
  * base activity worker
  * dana framework v.3
*/

// account change org details

class workeraccchglogin extends workerform {
  protected $contact;
  protected $username;
  protected $password;
  protected $confirm;
  protected $securityquestion;
  protected $securityanswer;

  protected function InitForm() {
    $this->contact = $this->account->Contact();
    $this->title = 'Change Login Credentials'; 
    $this->icon = 'images/sect_account.png';
    $this->activitydescription = 'some text here';
    $this->contextdescription = 'login credentials';

    $this->username = $this->AddField(
      'username', new formbuildereditbox('username', '', 'User Name'),
      $this->contact);
    $this->password = $this->AddField(
      'password', new formbuilderpassword('password', '', 'Password'), $this->contact);
    $this->confirm = $this->AddField(
      'confirm', new formbuilderpassword('confirm', '', 'Confirm Password'));
    $this->securityquestion = $this->AddField(
      'securityquestion', new formbuildereditbox('securityquestion', '', 'Security Question'),
      $this->contact);
    $this->securityanswer = $this->AddField(
      'securityanswer', new formbuildereditbox('securityanswer', '', 'Security Answer'),
      $this->contact);
  }

  protected function IsValid() {
    $pwd = $this->password->value;
    $cfm = $this->confirm->value;
    $matched = ($pwd == $cfm);
    if (!$matched) {
//      $this->AddError('Your confirmation password is different. Please re-type your password.');
      $this->password->value = '';
      $this->confirm->value = '';
      $this->password->AddError('PWD', 'Please re-enter your password');
      $this->confirm->AddError('CFM', 'Please confirm your password by typing it again');
    }
    return $matched;
  }

  protected function PostFields() {
    return
      $this->username->Save() + $this->password->Save() +
      $this->confirm->Save() + $this->securityquestion->Save() +
      $this->securityanswer->Save();
  }

  protected function SaveToTable() {
    $this->showroot = $this->contact->StoreChanges();
    return (int) $this->showroot;
  }

  protected function AddErrorList() {
    $this->AddErrors($this->username->errors);
    $this->AddErrors($this->password->errors);
    $this->AddErrors($this->confirm->errors);
    $this->AddErrors($this->securityquestion->errors);
    $this->AddErrors($this->securityanswer->errors);
  }

  protected function AssignFieldDisplayProperties() {
    // add login fields
    $this->NewSection(
      'logingroup', 'Login Credentials',
      'Change your login details. These allow you to log into your account. Your username must be unique (not used by anyone else) and have a minimum of 6 characters in length.');
    // - username
    $this->username->description = 'Please enter your username';
    $this->username->required = true;
    $this->username->placeholder = 'unique name';
    $this->username->size = 50;
    $this->username->maxlength = 50;
    $this->username->pattern = ".{6,50}";
    $this->AssignFieldToSection('logingroup', 'username');
    // password
    $this->password->description = 'Please enter your password <small>(between 6 and 50 characters)</small>.';
    $this->password->size = 50;
    $this->password->maxlength = 50;
    $this->password->pattern = ".{6,50}";
    $this->AssignFieldToSection('logingroup', 'password');
    // - confirm password
    $this->confirm->description = 'Please re-enter your password to confirm.';
    $this->confirm->size = 50;
    $this->confirm->maxlength = 50;
    $this->confirm->pattern = ".{6,50}";
    $this->AssignFieldToSection('logingroup', 'confirm');
    // security question
    $this->securityquestion->description = 'Please specify a question that we will ask you if you forget you password. If you answer matches the text below we will reset your password and send you an email.';
    $this->securityquestion->required = true;
    $this->securityquestion->size = 80;
    $this->securityquestion->maxlength = 100;
    $this->securityquestion->placeholder = 'what is your ...?';
    $this->AssignFieldToSection('logingroup', 'securityquestion');
    // security answer
    $this->securityanswer->description = 'Please state the answer to the question above. <strong>Please write carefully, keep it simple and DO NOT FORGET IT.</strong>';
    $this->securityanswer->required = true;
    $this->securityanswer->size = 80;
    $this->securityanswer->maxlength = 100;
    $this->securityanswer->placeholder = 'something memorable, relevant to the question above';
    $this->AssignFieldToSection('logingroup', 'securityanswer');
  }

}

$worker = new workeraccchglogin();

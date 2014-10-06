<?php
// question management class for MyLocalSmallBusiness
// written by Ian Stewart (c) 2011 Whitedream Software
// created: 22 apr 2011
// modified: 25 aug 2014

require_once 'class.database.php';

// question class
class questionitem {
  public $id;
  public $questionimage;
  public $answer1;
  public $answer2;
  public $answer3;

  public $exists;

  function __construct($fid = 0) {
    ($fid > 0) ? $this->FindByID($fid) : $this->AssignDefaults();
  }

  private function AssignDefaults() {
    $this->id = 0;
    $this->questionimage = '';
    $this->answer1 = '';
    $this->answer2 = '';
    $this->answer3 = '';
    $this->exists = false;
  }

  private function FindByID($fid) {
    $query = "SELECT * FROM `question` WHERE `id` = {$fid}";
    $result = database::Query($query, $fid);
    $line = $result->fetch_assoc();
    $result->free();
    $this->exists = $line != false;
    if ($this->exists) {
      $this->id = $line['id'];
      $this->questionimage = $line['questionimage'];
      $this->answer1 = $line['answer1'];
      $this->answer2 = $line['answer2'];
      $this->answer3 = $line['answer3'];
    }
    return $this->exists;
  }

  public function IsCorrect($answer) {
    $answer = strtolower($answer);
    return ($answer == $this->answer1) || ($answer == $this->answer2) || ($answer == $this->answer3);
  }
}

// question class
class questionmanager {
  public $fldqid = 'fldqid'; // the id/name of the hidden field containing the id value from the question table
  public $fldanswer = 'fldanswer'; // the answer field name
  public $items; // array of question based on id

  function __construct() {
    $this->PopulateItems();
  }

  private function PopulateItems() {
    $this->items = array();
    $query = "SELECT `id` FROM `question`";
    $result = database::Query($query);
    while ($line = $result->fetch_assoc()) {
      $this->items[] = $line['id'];
    }
    $result->free();
  }

  private function GetRandomQuestion() {
    $cnt = count($this->items);
    if ($cnt > 0) {
      do {
        $rand_keys = array_rand($this->items, 1);
        $result = $rand_keys;
      } while ($result == 0);
    } else {
      $result = false;
    }
    return $result;
  }

  // returns a random question, 
  // or 'true' if form has posted a correct answer
  // 'false' if no question has been found
  public function ShowQuestion($path = 'questions') {
    if ($this->IsAnswer()) {
      $ret = true;
    } else {
      $value = false;
      $id = $this->GetRandomQuestion();
      if ($id !== false) {
        $question = new questionitem($id);
        if ($question->exists) {
          $url = $path . DIRECTORY_SEPARATOR . $question->questionimage;
$ans1 = $question->answer1;
$ans2 = $question->answer2;
$ans3 = $question->answer3;
          $value =
            "<div id='question'>\n" .
            "  <p>For anti-spamming purposes, please answer the following question:</p>\n" .
            "  <img src='{$url}' alt='oops, no image - please refresh the page!'>\n" .
            "  <input name='{$this->fldanswer}' id='{$this->fldanswer}' type='text' size='10' maxlength='10' required>\n" .
            "  <input type='hidden' name='{$this->fldqid}' id='{$this->fldqid}' value='{$id}' />\n" .
"  <input type='hidden' name='ans1' id='ans1' value='{$ans1}' />\n" .
"  <input type='hidden' name='ans2' id='ans2' value='{$ans2}' />\n" .
"  <input type='hidden' name='ans3' id='ans3' value='{$ans3}' />\n" .
            "</div>\n";
        }
      }
      $ret = $value;
    }
    return $ret;
  }

  // returns true if the answer corresponds to the question id, else returns false
  public function IsAnswer() {
    $ret = false;
    $answer = (isset($_POST[$this->fldanswer]))
      ? strip_tags(addslashes($_POST[$this->fldanswer])) : false;
    $qid = (isset($_POST[$this->fldqid]))
      ? strip_tags(addslashes($_POST[$this->fldqid])) : false;;
    if ($qid) { // question exists in form?
      if ($answer) { // answer provided?
        $question = new questionitem($qid);
        $ret = $question->IsCorrect($answer);
      } else {
        $this->errror = "<p class='error'>Please provide an answer to the anti-spamming question</p>";
      }
    }
    return $ret;
  }
}

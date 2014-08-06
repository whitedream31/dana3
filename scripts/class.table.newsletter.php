<?php
require_once 'class.database.php';
require_once 'class.basetable.php';

// newsletter item type (types of newsletters)
class newsletteritemtype extends lookuptable {
  public $help;

  function __construct($id = 0) {
    parent::__construct('newsletteritemtype', $id);
  }

  protected function AssignFields() {
    parent::AssignFields();
    $this->AddField('help', DT_STRING);
  }

  protected function AfterPopulateFields() {
    parent::AfterPopulateFields();
    $this->help = $this->GetFieldValue('help');
  }
}

// newsletter
class newsletter extends idtable {
  public $showdate;
  public $showdatedescription;
  public $help;

  function __construct($id = 0) {
    parent::__construct('newsletter', $id);
  }

  protected function AfterPopulateFields() {
    $this->showdate = $this->GetFieldValue('showdate');
//    $date = strtotime($this->showdate);
    $this->showdatedescription = basetable::FormatDateTime(DF_MEDIUMDATE, $this->showdate);
  }

  protected function AssignFields() {
    parent::AssignFields();
    $this->AddField('title', DT_STRING);
    $this->AddField('showdate', DT_DATE);
    $this->AddField('datelastsent', DT_DATE);
    $this->AddField('showaddress', DT_BOOLEAN);
    $this->AddField('showtelephone', DT_BOOLEAN);
    $this->AddField('showwebsite', DT_BOOLEAN);
    $this->AddField('footertext', DT_STRING);
    $this->AddField('style', DT_STRING);
    $this->AddField('newsletterformatid', DT_FK);
    $this->AddField(FN_STATUS, DT_STATUS);
  }

  static public function FindNewslettersByAccount($accountid) {
    $status = STATUS_ACTIVE;
    $query = 
      "SELECT `id` FROM `newsletter` " .
      "WHERE `accountid` = {$accountid} AND `status` = '{$status}' " .
      'ORDER BY `showdate` DESC';
    $result = database::Query($query);
    $list = array();
    while ($line = $result->fetch_assoc()) {
      $list[] = $line['id'];
    }
    $result->close();
    return $list;
  }
}

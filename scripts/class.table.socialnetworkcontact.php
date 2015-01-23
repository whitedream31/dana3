<?php
// socialnetwork contact management class for MyLocalSmallBusiness
// written by Ian Stewart (c) 2012 Whitedream Software
// created: 22 feb 2012
// modified: 3 aug 2014

//require_once 'mediaclass.php';
//require_once 'pageclass.php';

// socialnetwork contact (item) class
class socialnetworkcontact extends idtable {
  public $socialnetworktypeid;
  public $ref;
  public $username;
  public $url;
  public $dateadded;
  public $visible;

  function __construct($id = 0) {
    parent::__construct('socialnetworkcontact', $id);
  }

  protected function AssignFields() {
    $this->AddField('socialnetworktypeid', DT_FK);
    $this->AddField('ref', DT_REF);
    $this->AddField('username', DT_STRING);
    $this->AddField('url', DT_STRING);
    $this->AddField('dateadded', DT_DATE);
    $this->AddField('visible', DT_BOOLEAN);
  }

  public function PopulateEmpty($id) {
    $query = 'SELECT * FROM `socialnetworktype` WHERE `id` = ' . $id;
    $result = database::Query($query);
    $line = $result->fetch_assoc($result);
    $result->close();
    if ($line) {
      $this->id = 0;
      $this->accountid = 0;
      $this->socialnetworktypeid = $line['id'];
      $this->ref = $line['ref'];
      $this->username = '<em>unused</em>';
      $this->url = '';
      $this->description = ValueFromLine($line['description']);
      $this->icon = ValueFromLine($line['icon']);
    }
  }
  
  static public function FindSocialNetworksForAccount($accountid) {
    $lst = array();
    $query = 'SELECT c.`id`, t.`icon`, t.`description` FROM `socialnetworkcontact` c ' .
      'INNER JOIN `socialnetworktype` t ON c.`socialnetworktypeid` = t.`id` ' .
      "WHERE c.`accountid` = {$accountid} ORDER BY t.`ref`";
    $result = database::Query($query);
    while ($line = $result->fetch_assoc()) {
      $id = $line['id'];
      $lst[$id] = array('desc' => $line['description'], 'icon' => $line['icon']); //, 'url' => $line['url']);
    }
    $result->close();
    return $lst;
  }

  public function FindAllSocialNetworkTypes() {
    $lst = array();
    $query = 'SELECT * FROM `socialnetworktype` ORDER BY `ref`';
    $result = database::Query($query);
    while ($line = $result->fetch_assoc()) {
      $id = $line['id'];
      $lst[$id] = $id;
    }
    $result->close();
    return $lst;
  }
/*
  public function CreateSocialNetworkContact() {
    $this->readcount = 0;
    $query = 'INSERT INTO `socialnetworkcontact` ' .
      '(`accountid`, `socialnetworktypeid`, `username`, `url`) VALUES (' .
      SQLValue($this->accountid) .
      SQLValue($this->socialnetworktypeid) .
      SQLValue($this->username) .
      SQLValue($this->url, false) .
      ')';
    mysql_query($query) or die('Failed whilst inserting into socialnetworkcontact table: ' . mysql_error());
    $this->id = mysql_insert_id();
    return $this->id;
  }

  public function UpdateItem()
  {
    if ($this->id > 0)
    {
      $query = 'UPDATE `socialnetworkcontact` SET ' .
      UpdateFieldValue('accountid', SQLValue($this->accountid, false)) .
      UpdateFieldValue('socialnetworktypeid', SQLValue($this->socialnetworktypeid, false)) .
      UpdateFieldValue('username', SQLValue($this->username, false)) .
      UpdateFieldValue('url', SQLValue($this->url, false), false) .
      " WHERE `id` = '{$this->id}'";
      mysql_query($query) or die('Failed whilst updating socialnetworkcontact table: ' . mysql_error());
    }
    else
    {
      $this->CreateSocialNetworkContact();
    }
  }

  public function DeleteSocialNetworkItem()
  {
    if ($this->id > 0)
    {
      $query = "DELETE FROM `socialnetworkcontact` WHERE `id` = '{$this->id}'";
      mysql_query($query) or die('Failed whilst deleting socialnetwork contact (' . $this->id . '): ' . mysql_error());
    }
  }
*/
}
/*
// socialnetwork contact group class
class socialnetworkcontactgroup {
  public $accountid;
  public $list;

  function __construct($accountid) {
    $this->accountid = $accountid;
    $this->PopulateList();
  }

  public function FindAllSocialNetworkTypes() {
    $lst = array();
    $query = 'SELECT `id` FROM `socialnetworktype` ORDER BY `ref`';
    $result = database::Query($query);
    while ($line = $result->fetch_assoc()) {
      $id = $line['id'];
      $lst[$id] = $id;
    }
    $result->close();
    return $lst;
  }

  private function PopulateList() {
    $socialnetworktypes = $this->FindAllSocialNetworkTypes();
    $lst = array();
    // find and populate the existing (socal network) contacts
    $query = 'SELECT * FROM  `socialnetworkcontact` c ' .
      'INNER JOIN `socialnetworktype` t ON c.`socialnetworktypeid` = t.`id` ' .
      'WHERE c.`accountid` = ' . $this->accountid;
    $result = mysql_query($query) or die("Error whilst locating rows (group) from socialnetworkcontact: " . mysql_error());
    while ($line = mysql_fetch_assoc($result)) {
      $id = $line['id'];
      $item = new socialnetworkcontact($id);
      $lst[$id] = $item;
      if ($idx = array_search($id, $socialnetworktypes)) {
        unset($socialnetworktypes[$idx]);
      }
    }
    mysql_free_result($result);
    // popular the unused socialnetworks
    foreach($socialnetworktypes as $id) {
      $item = new socialnetworkcontact();
      $item->PopulateEmpty($id);
      $item->accountid = $this->accountid;
      $lst[$id] = $item;
    }
    // return a list of all social networks (socialnetworkcontact objects)
    $this->list = $lst;
    //return $lst;
  }
}
*/
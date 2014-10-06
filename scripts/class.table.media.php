<?php
require_once 'class.database.php';
require_once 'class.basetable.php';

// media record
class media extends idtable {

  function __construct($id = 0) {
    parent::__construct('media', $id);
  }

  protected function AssignFields() {
    parent::AssignFields();
    $this->AddField('accountid', DT_FK);
    $this->AddField('imgid', DT_FK);
    $this->AddField('imgtype', DT_STRING);
    $this->AddField('imgsize', DT_INTEGER);
    $this->AddField('height', DT_INTEGER);
    $this->AddField('imgname', DT_STRING);
    $this->AddField('originalname', DT_STRING);
    $this->AddField('thumbnail', DT_STRING);
  }

/*  public function StoreChanges() {
    
  } */

  static public function GetHighestImageValue($galleryid) {
    $query = 
      'SELECT MAX(mi.`height`) AS maxheight FROM `gallery` g ' .
      'INNER JOIN `galleryitem` gi ON g.`id` = gi.`galleryid` ' .
      'INNER JOIN `media` mi ON gi.`largemediaid` = mi.`id` ' .
      'WHERE g.`id` = ' . (int) $galleryid;
    $result = database::Query($query);
    $line = $result->fetch_assoc();
    if ($line) {
      $ret = ((int) $line['maxheight']);
    } else {
      $ret = -1;
    }
    $result->close();
    return $ret;
  }
  
  public function AssignFromWebImage($src) {
    require_once 'class.formbuilderfilewebimage.php';
    if ($src instanceof formbuilderfilewebimage) {
      $this->SetFieldValue('imgtype', $src->file['type']);
      $this->SetFieldValue('originalname', $src->file['name']);
      $this->SetFieldValue('imgsize', $src->file['size']);
      $this->SetFieldValue('imgname', $src->newimgfilename);
      $this->SetFieldValue('thumbnail', $src->newimgthumbnail);
      //$this->SetFieldValue('srcwidth', $src->srcwidth);
      $this->SetFieldValue('height', $src->srcheight);
    }
  }

  static public function FindNextImgID($accid) {
    $query = 'SELECT MAX(`imgid`) as maximgid FROM `media` WHERE `accountid` = ' . $accid;
    $result = database::Query($query);
    $line = $result->fetch_assoc();
    if ($line) {
      $id = ((int) $line['maximgid']) + 1;
    } else {
      $id = 1;
    }
    $result->free();
    return $id;
  }
}

<?php
//require_once('class.database.php');
//require_once('class.basetable.php');

// downable files
class fileitem extends idtable {
  public $filetypedescription;
  public $filesizedescription;
  public $filelastupdatedescription;

  function __construct($id = 0) {
    parent::__construct('fileitem', $id);
    $this->filetypedescription = '';
    $this->filesizedescription = '';
    $this->filelastupdatedescription = '';
  }

  protected function AssignFields() {
    parent::AssignFields();
    $this->AddField('title', DT_STRING);
    $this->AddField('accountid', DT_FK);
    $this->AddField('filename', DT_STRING);
    $this->AddField('filetypeid', DT_FK);
    $this->AddField('filesize', DT_INTEGER);
    $this->AddField('description', DT_STRING);
    $this->AddField('stampadded', DT_DATETIME);
    $this->AddField('stampupdated', DT_DATETIME);
    $this->AddField('status', DT_STATUS);
  }

  protected function GetFileTypeDescription() {
    $filetypeid = $this->GetFieldValue('filetypeid');
    return database::SelectDescriptionFromLookup('filetype', $filetypeid);
  }

  protected function GetFileSizeAsString() {
    $filesize = $this->GetFieldValue('filesize');
    if ($filesize > 0) {
      $kb = $filesize / 1024;
      $mb = $kb / 1024;
      if ($mb > 4096) {
        $val = round($mb, 2);
        $suf = 'MB';
      } else if ($kb > 4096) {
        $val = round($kb, 2);
        $suf = 'KB';
      } else {
        $val = $filesize;
        $suf = ' bytes';
      }
      $ret = $val . $suf;
    } else {
      $ret = 'none';
    }
    return $ret;
  }

  protected function GetLastUpdateAsString() {
    return $this->FormatDateTime(DF_LONGDATETIME, $this->GetFieldValue('stampupdated'));
  }

  protected function AfterPopulateFields() {
    $this->filetypedescription = $this->GetFileTypeDescription();
    $this->filesizedescription = $this->GetFileSizeAsString();
    $this->filelastupdatedescription = $this->GetLastUpdateAsString();
  }

  public function Show() {
    $ret = '';
    if ($this->exists) {
/*      $title = $this->GetFieldValue('title');

      $caldate = $this->GetFieldValue('calendardate');
      if (($caldate == '') ? false : date(DATEFMT_LONG, strtotime($this->GetFieldValue('calendardate')))) {
        $caldate = " <p class=\"calendardate\">Date: {$caldate}</p>\n";
      }
      $subheading = $this->GetFieldValue('subtitle');
      $subheading = ($subheading == '') ? false : "<h3>{$subheading}</h3>\n{$caldate}";
      $media = '';
      if ($imgid = $this->GetFieldValue('imageid')) {
        $img = new image($imgid);
        $media = $img->ImageTag(false, '', $anchorstart, $anchorend) . "\n";
      } elseif ($groupid = $this->GetFieldValue('gallerygroupid')) {
        $gallerygroup = new gallerygroup($groupid);
        if ($gallerygroup->exists) {
          $media = $gallerygroup->BuidldGallery();
        }
      }
      $ret = "  <article class=\"post\">\n" .
             "    <h2>{$title}</h2>\n" . $subheading . $media . $this->GetFieldValue('longdescription') . "\n" .
             "  </article>\n";
    } else {
      $ret = '<p>Article not found</p>'; */
    }
    return $ret;
  }

}

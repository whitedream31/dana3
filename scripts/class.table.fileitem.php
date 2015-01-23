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
    $this->AddField('title', self::DT_STRING);
    $this->AddField('accountid', self::DT_FK);
    $this->AddField('filename', self::DT_STRING);
    $this->AddField('filetypeid', self::DT_FK);
    $this->AddField('filesize', self::DT_INTEGER);
    $this->AddField(basetable::FN_DESCRIPTION, self::DT_STRING);
    $this->AddField('stampadded', self::DT_DATETIME);
    $this->AddField('stampupdated', self::DT_DATETIME);
    $this->AddField(basetable::FN_STATUS, self::DT_STATUS);
  }

  protected function GetFileTypeDescription() {
    $filetypeid = $this->GetFieldValue('filetypeid');
    return database::SelectDescriptionFromLookup('filetype', $filetypeid);
  }

  protected function GetFileSizeAsString($size = false) {
    $filesize = ($size) ? $size : $this->GetFieldValue('filesize');
    if ($filesize > 0) {
      $kb = $filesize / 1024;
      $mb = $kb / 1024;
      if ($mb > 3) {
        $val = round($mb, 2);
        $suf = ' MB';
      } else if ($kb > 3) {
        $val = round($kb, 2);
        $suf = ' KB';
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
    return $this->FormatDateTime(self::DF_LONGDATETIME, $this->GetFieldValue('stampupdated'));
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

  public function AssignDataGridColumns($datagrid) {
    $datagrid->showactions = false;
    $datagrid->AddColumn('DESC', 'Filename', true);
    $datagrid->AddColumn('TITLE', 'Title');
    $datagrid->AddColumn('FILETYPE', 'File type');
    $datagrid->AddColumn('FILESIZE', 'File Size', false, 'right');
  }

/*  static public function FileSizeToString($size, $unit = '') {
    if ((!$unit && $size >= 1 << 30) || $unit == 'GB') {
      $ret = number_format($size / (1 << 30), 2) . 'GB';
    } elseif ((!$unit && $size >= 1 << 20) || $unit == 'MB') {
      $ret = number_format($size / (1 << 20), 2)."MB";
    } elseif ((!$unit && $size >= 1 << 10) || $unit == 'KB') {
      $ret = number_format($size / (1 << 10), 2) . 'KB';
    } else {
      $ret = number_format($size) . ' bytes';
    }
     return $ret;
  } */

  public function GetCurrentList($showimg = false, $islistitem = true, $linkprefix = '') {
    $accountid = account::$instance->ID();
    $status = self::STATUS_ACTIVE;
    $query =
      'SELECT i.*, t.`iconurl`, t.`description` AS filetypedesc FROM `fileitem` i ' .
      'INNER JOIN `filetype` t ON i.`filetypeid` = t.`id` ' .
      "WHERE (i.`accountid` = {$accountid}) AND " .
      "(i.`status` = '{$status}') ORDER BY i.`stampadded`";
    $img = '';
    $list = array();
    $result = database::Query($query);
    while ($line = $result->fetch_assoc()) {
      $id = $line['id'];
//      $value = $line['iconurl'];
      $label = $line['filetypedesc'];
      if ($showimg) {
        $img = $line['iconurl'];
      }
      $icon = account::$instance->contact->AddSpecialLinkItem($label, '', $img, $islistitem, $linkprefix);
      $image = account::$instance->contact->AddSpecialLinkItem(' ', '', $img, $islistitem, $linkprefix);
      $filesize = $this->GetFileSizeAsString($line['filesize']);
      $list[$id] = array(
        'DESC' => $line['filename'],
        'TITLE' => $line['title'],
        'FILETYPE' => $icon,
        'IMAGE' => $image,
        'FILESIZE' => $filesize
      );
    }
    $result->free();
    return $list;
  }

  public function AssignDataGridRows($datagrid) {
    $list = $this->GetCurrentList(true, false);
    foreach($list as $fileid => $filedetails) {
      $datagrid->AddRow($fileid, $filedetails, true, array());
    }
    return $list;
  }

}

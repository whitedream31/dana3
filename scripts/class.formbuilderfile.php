<?php
require_once 'class.formbuildereditbox.php';

global $MIME_WEBIMAGES, $MIME_WEBSITE;

// MIME types for uploading
$MIME_DOCUMENTS = array(
  'application/msword' => 'doc', 'application/pdf' => 'pdf', 'application/rtf' => 'rtf', 'text/plain' => 'txt',
  'application/vnd.ms-excel' => 'xls', 'application/vnd.ms-excel' => 'xlw', 'application/vnd.ms-powerpoint' => 'ppt',
  'application/vnd.ms-project' => 'mpp', 'application/vnd.ms-works' => 'wcm', 'application/vnd.ms-works' => 'wdb',
  'application/vnd.ms-works' => 'wks', 'application/vnd.ms-works' => 'wps', 'application/x-msmetafile' => 'wmf',
  'application/x-mswrite' => 'wri'
);
$MIME_AUDIO = array(
  'audio/mpeg' => 'mp3', 'audio/x-wav' => 'wav'
);
$MIME_IMAGES = array(
  'image/bmp' => 'bmp', 'image/gif' => 'gif', 'image/jpeg' => 'jpe', 'image/jpeg' => 'jpeg', 'image/jpeg' => 'jpg',
  'image/pipeg' => 'jfif', 'image/svg+xml' => 'svg', 'image/tiff' => 'tif', 'image/tiff tiff',
  'image/x-portable-anymap' => 'pnm', 'image/x-portable-bitmap' => 'pbm', 'image/x-portable-graymap' => 'pgm',
  'image/x-portable-pixmap' => 'ppm', 'image/x-xbitmap' => 'xbm', 'image/x-xpixmap' => 'xpm', 'image/png' => 'png'
);
$MIME_TEXT = array(
  'text/css' => 'css', 'text/html' => 'htm', 'text/html' => 'html', 'text/plain' => 'txt', 'text/richtext' => 'rtx',
  'text/tab-separated-values' => 'tsv'
);
$MIME_VIDEO = array(
  'video/mpeg' => 'mp2', 'video/mpeg' => 'mpa', 'video/mpeg' => 'mpe', 'video/mpeg' => 'mpeg', 'video/mpeg' => 'mpg',
  'video/mpeg' => 'mpv2', 'video/quicktime' => 'mov', 'video/quicktime' => 'qt', 'video/x-msvideo' => 'avi'
);
$MIME_WEBIMAGES = array(
  'image/gif' => 'gif', 'image/jpeg' => 'jpe', 'image/jpeg' => 'jpeg', 'image/jpeg' => 'jpg',
  'image/pipeg' => 'jfif', 'image/svg+xml' => 'svg', 'image/png' => 'png'
);
$MIME_WEBSITE = $MIME_WEBIMAGES + $MIME_DOCUMENTS;

// file process error list
define('FILEERROR_CANNOTMOVE', 1);

class fileexception extends Exception {}

// file field (non specific upload file) - FLDTYPE_FILE - derived from edit box so it gets size and maxlength
class formbuilderfile extends formbuildereditbox {
  protected $keyid;
  public $usecurrentfile = false;
  public $acceptedfiletypes;
  public $targetfilename;
  public $targetpath;
  //public $targetprefix = 'file';
  public $posted;
  public $file;
  public $mediaid; // hidden control id value

  function __construct($name, $value, $label, $targetname = '') {
    ini_set('memory_limit', '128M'); // handle large images
    ini_set('post_max_size', '64M');
    ini_set('upload_max_filesize', '64M');
    $this->targetfilename = $targetname;
    $this->fieldtype = FLDTYPE_FILE;
    $this->acceptedfiletypes = array();
    $this->keyid = 0;
    $this->mediaid = -1; // no id
    $this->Init();
    parent::__construct($name, $value, $label);
  }

  function __toString() {
    return ($this->posted) ? $this->file : '';
  }

  protected function Init() {}

  protected function GetPathInfo($filepath) {
    $ret = array();
    $m = array();
    preg_match('%^(.*?)[\\\\/]*(([^/\\\\]*?)(\.([^\.\\\\/]+?)|))[\\\\/\.]*$%im', $filepath, $m);
    $ret['dirname'] = $m[1];
    $ret['basename'] = $m[2];
    $ret['filename'] = $m[3];
    $ret['extension'] = (isset($m[5])) ? $m[5] : '';
    return $ret;
  }
/*
  public function Post() {
    if ($this->posted) {
      $ret = false;
    } else {
      if ($this->CheckPostExists()) {
        $this->GetPostValue();
        $this->CheckPost();
      }
//      $this->posted = true;
      $ret = true;
    }
    return $ret;
  }
*/
  protected function GetPostValue() {
    if ($this->posted) {
      if (!$this->file || isset($this->file['name'])) {
        $this->file = $_FILES[$this->name];
      }
      $this->value = $this->file['name'];
      $this->keyid = GetPost($this->name . '_key'); // needed?
    } else {
      $this->file = false;
      $this->value = false;
      $this->keyid = 0;
    }
  }

  protected function CheckPostExists() {
    $this->posted = isset($_FILES[$this->name]);
    return $this->posted;
  }

  protected function AcceptedTypes() {
    return (is_array($this->acceptedfiletypes))
      ? implode(', ', array_keys($this->acceptedfiletypes))
      : false;
  }

  protected function ProcessPost() {
    $error = $this->file['error'];
    if ($error == UPLOAD_ERR_OK) {
      $srcfile = $this->file["tmp_name"];
      $pathinfo = $this->GetPathInfo($this->file["name"]);
      $fileext = strtolower($pathinfo['extension']);
      $acceptedlist = array_values($this->acceptedfiletypes);
      if (!in_array($fileext, $acceptedlist)) {
        $this->errors[ERRKEY_INVALIDFILE] = ERRVAL_INVALIDFILE;
      } else {
        if (!$this->targetfilename) {
          $this->targetfilename = $this->file["name"];
        }
        $pathinfo = $this->GetPathInfo($this->targetfilename);
        if (!$pathinfo['extension']) {
          $this->targetfilename .= '.' . $fileext;
        }
        if (move_uploaded_file($srcfile, $this->targetpath . $this->targetfilename)) { //$this->targetprefix . $this->targetfilename)) {
          parent::ProcessPost();
        } else {
          $this->errors[] = FILEERROR_CANNOTMOVE;
        }
      }
    } elseif ($error != UPLOAD_ERR_NO_FILE) {
      switch ($error) {
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
          $this->errors[] = 'The file is too big. Please choose another, smaller file.';
          break;
        default:
          $this->errors[] = 'There was a technical error. Please try again later.';
          break;
      }
    }
  }

  protected function ValidateValue() {
    $ret = false;
    if ($this->file['error'] == UPLOAD_ERR_NO_FILE) {
      $this->usecurrentfile = ($this->keyid);
      if ($this->usecurrentfile) {
        $ret = true;
      } else {
        $this->errors[ERRKEY_NOFILE] = ERRVAL_NOFILE;
      }
    } elseif ($this->file) {
      $pathinfo = $this->GetPathInfo($this->file["name"]); // basename
      $fileext = strtolower($pathinfo['extension']);
      $acceptedlist = array_values($this->acceptedfiletypes);
      $ret = $acceptedlist && (in_array($fileext, $acceptedlist));
      if (isset($php_errormsg)) {
        $this->errors[ERRKEY_PHPERROR] =
          (substr_count('exceeds', $php_errormsg)) ? 'Your file is too big to upload' : $php_errormsg;
      }
      if (!$ret) {
        $this->errors[ERRKEY_INVALIDFILE] = ERRVAL_INVALIDFILE;
      }
    } else {
      $ret = false;
    }
    return $ret;
  }

  protected function AddAttributesAndValues() {
    parent::AddAttributesAndValues();
    $accepted = $this->AcceptedTypes();
    $this->AddAttribute('accepted', $accepted);
  }

  public function GetControl() {
    if ($this->mediaid) {
      $fld = new formbuilderhidden($this->name . '_key', $this->mediaid);
      $ret = $fld->GetControl();
    } else {
      $ret = array();
    }
    return array_merge(
      array(
        "<input type='file' name='{$this->name}' id='{$this->id}' value='{$this->GetValue()}'" .
        $this->IncludeAllAttributes() .
        $this->AddDisabled() . $this->AddReadOnly() . $this->AddRequired() . " >"), 
      $ret
    );
  }
}

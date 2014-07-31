<?php
require_once 'class.formbuilderfile.php';

// file upload field for image files suitable for the web - FLDTYPE_FILEWEBSITE
class filewebimagefield extends filefield {
  public $uploaded;
  public $imgtype;
  public $imgsize;
  public $dstfile;
  public $srcwidth;
  public $srcheight;
  public $img_src;
  public $file_src;
  public $thumbnailprefix = 'sm';
  public $thumbnailwidth = 100;
  public $thumbnailheight = 100;
  public $imageresizewidth = 500;
  public $imageresizeheight = 500;
  // optional properties to show a thumbnail and id for passing to a database
  public $mediaid; // hidden control id value
  public $previewthumbnail; //
  public $newimgfilename;
  public $newimgthumbnail;

// public $showimage as thumbnail?
  function __construct($name, $value, $label, $targetname) {
    parent::__construct($name, $value, $label, $targetname);
  }

  protected function Init() {
    global $MIME_WEBIMAGES;
    $this->fieldtype = FLDTYPE_FILEWEBIMAGES;
    $this->acceptedfiletypes = $MIME_WEBIMAGES;
    $this->mediaid = -1; // no id
    $this->previewthumbnail = '';
  }
// brentwientjes at NOSPAM dot comcast dot net
//  $img_base = base directory structure for thumbnail images
//  $w_dst = maximum width of thumbnail
//  $h_dst = maximum height of thumbnail
//  $n_img = new thumbnail name
//  $o_img = old thumbnail name

  protected function MakeImageFile($dstfilename, $width, $height) {
    if (file_exists($dstfilename)) {
      unlink($dstfilename);
    }
    return $this->ResizeImage($this->img_src, $dstfilename, $this->srcwidth, $this->srcheight, $width, $height);
/*      if ($this->targetfilename != $dstfilename) {
      unlink($this->targetfilename);
    } */
  }

/*  protected function BuildTarget() {
    if (!$this->targetfilename) {
      $this->targetfilename = $this->file["name"];
    }
    $pathinfo = $this->GetPathInfo($this->targetfilename);
    list($targetpath, $targetbasename, $targetext, $targetfilename) = array_values($pathinfo);
    if (!$targetpath) {
      $targetpath = $this->targetdefaultpath;
    } 
    if (!$targetfilename) {
      $targetfilename = $this->targetfilename; // $this->targetprefix . $this->targetfilename;
    }
  } */

  protected function ProcessPost() {
    if ($this->file['error'] != UPLOAD_ERR_NO_FILE) {
      parent::ProcessPost();
      if (count($this->errors) == 0) {
        $srcfilename = $this->targetpath . $this->targetfilename;
        $pathinfo = $this->GetPathInfo($srcfilename);
        // get new image filenames
        $this->newimgfilename = $this->targetfilename;
        $this->newimgthumbnail = $pathinfo['filename'] . $this->thumbnailprefix . '.' . $pathinfo['extension'];;
        $imgthumbnail = $this->targetpath . $this->newimgthumbnail;
        list($this->srcwidth, $this->srcheight, $srctype) = getimagesize($srcfilename); // create new dimensions, keeping aspect ratio
        $imgsz = ($this->srcwidth * $this->srcheight * 24);
        if ($imgsz > 100 * 1024 * 1024) {
          $this->errors[ERRKEY_IMAGETOOBIG] = ERRVAL_IMAGETOOBIG;
        } else {
          switch ($srctype) {
            case 1:   //  gif -> jpg
              $this->img_src = imagecreatefromgif($srcfilename);
              break;
            case 2:   //  jpeg -> jpg
              $this->img_src = imagecreatefromjpeg($srcfilename);
              break;
            case 3:  //  png -> jpg
              $this->img_src = imagecreatefrompng($srcfilename);
              break;
            default:
              $this->img_src = $this->MakeErrorImage($srcfilename);
          }
          if ($this->img_src) {
            if ($this->srcwidth > $this->imageresizewidth) {
              // too wide, resize it to 500
              $newsize = $this->MakeImageFile($srcfilename, $this->imageresizewidth, $this->imageresizeheight);
              $this->file['size'] = filesize($srcfilename);
            } else {
              $newsize = false;
            }
            // make 150 width version (thumbnail)
            $this->MakeImageFile($imgthumbnail, $this->thumbnailwidth, $this->thumbnailheight);
            imagedestroy($this->img_src);
            if (is_array($newsize)) {
              $this->srcwidth = $newsize['width'];
              $this->srcheight = $newsize['height'];
            }
          }
        }
      }
    }
  }

  protected function MakeErrorImage($imgname = 'unknown') {
    // create a black image
    $im  = imagecreatetruecolor(100, 30);
    $bgc = imagecolorallocate($im, 255, 255, 255);
    $tc  = imagecolorallocate($im, 0, 0, 0);
    imagefilledrectangle($im, 0, 0, 100, 30, $bgc);
    // output an error message
    imagestring($im, 1, 5, 5, 'Error loading ' . $imgname, $tc);
    return $im;
  }

  protected function ResizeImage(
    $srcimg, $newimg, $srcwidth, $srcheight, $dstwidth, $dstheight) {
    $ratio = $srcwidth / $srcheight;
    $dstwidth =  ($dstwidth / $dstheight > $ratio)
      ? floor($dstheight * $ratio)
      : floor($dstwidth / $ratio);
    $dstimg = imagecreatetruecolor($dstwidth, $dstheight); // resample
    imagecopyresampled(
      $dstimg, $srcimg, 0, 0, 0, 0, $dstwidth, $dstheight, $srcwidth, $srcheight);
    imagejpeg($dstimg, $newimg); // save new image
    // clean up image storage
    imagedestroy($dstimg);
    return array('width' => $dstwidth, 'height' => $dstheight);
  }

  public function ShowControl($usehtml5 = false) {
    $usepreview = $this->previewthumbnail;
    if ($usepreview) {
      $thumbnailfile = $this->targetpath . $this->previewthumbnail;
      echo "<div class='mediapreview'>\n";
      if ($usepreview == 'none') {
        echo "  <p>NONE</p>\n";
      } else if (file_exists($thumbnailfile)) {
        echo
          "  <img src='{$thumbnailfile}' " .
//            'width=" ' . $this->thumbnailwidth . '" ' .
//            'height="' . $this->thumbnailheight .
          "><br>\n";
      } else {
        echo "  <p>NOT FOUND</p>\n";
      }
    }
    parent::ShowControl($usehtml5);
    if ($usepreview) {
      echo "</div>\n";
    }
    if ($this->mediaid) {
      new hiddenfield($this->name . '_key', $this->mediaid);
    }
  }

}

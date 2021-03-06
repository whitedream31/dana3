﻿<?php
// page writer for MyLocalSmallBusiness
// written by Ian Stewart (c) 2011, 2013 Whitedream Software
// created: 23 sep 2013 (org. 8 jun 2010)
// modified: 25 aug 2014
// (rewritten into a class structure)

require_once 'define.php';
require_once 'class.pagewriter.php';
require_once 'class.table.account.php';
require_once 'class.table.contact.php';

define('PDIR', ".." . DIRECTORY_SEPARATOR); // parent directory

//define('PORTAL_DOMAIN', 'mlsb.org');

//define('PWOPT_PROFILE', 'writeprofile');
//define('PWOPT_LIVE', 'writelive');
//define('PWOPT_TEST', 'test'); // for testing

//define('LOG_INIT', 'init');
//define('LOG_FOUND_ACCOUNT', 'fndacc');
//define('LOG_FOUND_CONTACT', 'fndcnt');
//define('LOG_FOUND_THEME', 'fndthm');
//define('LOG_MAKE_DIRECTORY', 'mkdir');
//define('LOG_START_PROCESSOR', 'procstart');
//define('LOG_DELETEITEM', 'delitm');
//define('LOG_WRITE_PAGE', 'writepg');
//define('LOG_STOP_PROCESSOR', 'procstop');
//define('LOG_PAGESKIPPED', 'pageskipped');
//define('LOG_RES_COPIED', 'rescopied');
//define('LOG_ROOTPATH', 'rp');
//define('LOG_PERMISSIONS', 'perm');

class pagewriter {
  const PWOPT_PROFILE = 'writeprofile';
  const PWOPT_LIVE = 'writelive';
  const PWOPT_TEST = 'test'; // for testing

  const LOG_INIT = 'init';
  const LOG_FOUND_ACCOUNT = 'fndacc';
  const LOG_FOUND_CONTACT = 'fndcnt';
  const LOG_FOUND_THEME = 'fndthm';
  const LOG_MAKE_DIRECTORY = 'mkdir';
  const LOG_START_PROCESSOR = 'procstart';
  const LOG_DELETEITEM = 'delitm';
  const LOG_WRITE_PAGE = 'writepg';
  const LOG_STOP_PROCESSOR = 'procstop';
  const LOG_PAGESKIPPED = 'pageskipped';
  const LOG_RES_COPIED = 'rescopied';
  const LOG_ROOTPATH = 'rp';
  const LOG_PERMISSIONS = 'perm';

  private $dsthandle;
  private $sectionprocessor;
  private $theme;
  private $themepath;
  private $errcount;
  private $log;
  private $shortname;

  public $pagelist;
  public $contact;
  public $errors;
  public $sourcepath;
  public $account;
  public $options;
  public $rootpath; // path to the accounts profile directory (ie. /profiles/nickname)
  public $currentpage;
  public $mode;
  public $testing;
//  public $rssfeedfilename;

  public function __construct($options) {
    $this->options = (is_array($options)) ? $options : array($options);
    $this->errors = array();
    $this->errcount = 0;
    $this->log = array();
    $this->sourcepath = getcwd() . DIRECTORY_SEPARATOR; // should be: mylocalsmallbusiness.com/scripts/
    $this->testing = in_array(self::PWOPT_TEST, $options);
  }

  public function Execute() {
    $this->AddToLog(self::LOG_INIT);
    try {
      $this->account = account::StartInstance();
      if ($this->account->exists) {
        $this->AddToLog(self::LOG_FOUND_ACCOUNT, $this->account->GetFieldValue('businessname'));
        $this->contact = $this->account->Contact();
        if ($this->contact->exists) {
          $this->AddToLog(self::LOG_FOUND_CONTACT, $this->contact->FullContactName());
          $this->pagelist = $this->account->GetPageList(true);
          $this->shortname = $this->account->GetFieldValue('nickname');
          if ($this->GetTheme()) {
            $this->AddToLog(self::LOG_FOUND_THEME, $this->theme->GetFieldValue('description'));
            $this->MakeSite();
          }
        } else {
          $this->AddError("Contact {$this->contact->key} does not exist");
        } 
      } else {
        $this->AddError("Account {$this->account->key} does not exist");
      }
    } catch (Exception $e) {
      $this->AddError("Exception: {$e->getMessage()}");
    }
    if ($this->errcount || in_array(self::PWOPT_TEST, $this->options)) {
      $this->WriteLogPage();
      exit;
    }
  }

  public function AddError($msg) {
    $this->errors[] = $msg;
    $this->errcount++;
  }

  public function AddToLog($key, $msg = '') {
    $t = microtime(true);
    $micro = sprintf("%06d",($t - floor($t)) * 1000000);
    $date = date('Y-m-d H:i:s.') . $micro;
    $msg = "{$date} - {$key} [{$msg}]";
    $this->log[] = $msg;
//    if (in_array(PWOPT_TEST, $this->options)) {
//      echo "<pre>{$msg}</pre>\n";
//    }
  }

  private function WriteOut($line, $nl = true) {
    if (is_string($line) && $nl) {
      $out = $line . "\n";
    } else {
      $out = ArrayToString($line);
    }
    if ($out) {
      fwrite($this->dsthandle, $out);
/*      if (in_array(PWOPT_TEST, $this->options)) {
        echo $out;
      } */
      return true;
    } else {
      return false;
    }
  }
  
  private function MakeSite() {
//    $this->account->ProcessBusinessContentIntoTags();
//    $this->account->ProcessPageContentsIntoTags();
//    $this->rssfeedfilename = $this->rootpath . DIRECTORY_SEPARATOR . 'pages.xml';
    if (in_array(self::PWOPT_PROFILE, $this->options)) {
      $this->BuildPages(self::PWOPT_PROFILE);
    }
    if (in_array(self::PWOPT_LIVE, $this->options)) {
      $this->BuildPages(self::PWOPT_LIVE);
    }
//    $this->account->UpdateAccountDate();
  }

  private function GetLiveRootPath() {
    return PDIR . PDIR . PORTAL_DOMAIN . DIRECTORY_SEPARATOR . $this->shortname;
  }

  private function GetRootPath($mode) {
    switch($mode) {
      case self::PWOPT_LIVE:
        $modetype = 'LIVE';
        $ret = $this->GetLiveRootPath() . DIRECTORY_SEPARATOR; //realpath($this->GetLiveRootPath()) . DIRECTORY_SEPARATOR;
        break;
      default:
        // current: mylocalsmallbusiness/scripts
        // profile: mylocalsmallbusiness/profiles/nickname
        $modetype = 'PROFILE';
        $ret = realpath(PDIR) . DIRECTORY_SEPARATOR . 'profiles' . DIRECTORY_SEPARATOR . $this->shortname . DIRECTORY_SEPARATOR;
    }
    $this->rootpath = $ret;
    $this->AddToLog(self::LOG_ROOTPATH, "Root path: {$modetype} = {$ret}");
    return $ret;
  }

  private function MakeDirectory($path) {
    $ret = false;
    if (file_exists($path)) {
      $ret = true;
      $this->AddToLog(self::LOG_MAKE_DIRECTORY, 'Directory exists: ' . $path);
    } elseif (mkdir($path)) {
      chmod($path, 0755);
      $ret = true;
      $this->AddToLog(self::LOG_MAKE_DIRECTORY, 'Directory created: ' . $path);
    } else {
      die("Could not create destination path '{$path}'!");
    }
    return $ret;
  }

  private function DeleteFile($filename) {
    if (file_exists($filename)) {
      $this->AddToLog(self::LOG_DELETEITEM, $filename);
      unlink($filename); // delete file
    }
  }
  
  private function DeleteFilesInDirectory($dir, $deldir = false) {
    if (is_dir($dir)) {
      $objects = scandir($dir);
      foreach ($objects as $object) {
        if ($object != "." && $object != "..") {
          $itm = $dir . DIRECTORY_SEPARATOR . $object;
          if (is_dir($itm)) {
            $this->DeleteFilesInDirectory($itm, true); // delete folder (recursive call)
          } else {
            $this->DeleteFile($itm); // delete file
          }
        }
      }
      //reset($objects);
      if ($deldir) {
        rmdir($dir);
      }
    }
  }

  private function BuildPages($mode) {
    $this->mode = $mode;
    $root = $this->GetRootPath($mode); // mylocalsmallbusiness.com/profiles/nickname or mlsb.org/nickname
    $this->MakeDirectory($root); // profile name
    $this->MakeDirectory($root . 'images'); // css images folder
//    $this->MakeDirectory($root . 'images' . DIRECTORY_SEPARATOR . 'lightbox'); // lightbox folder
    $this->MakeDirectory($root . 'media'); // picture gallery / logo
    $this->MakeDirectory($root . 'files'); // downloadable files
    $this->WritePages();
  }

  private function SetDirectoryPermissions($path, $filemode) {
    $ret = false;
    if (is_dir($path)) {
      $flg = true;
      $dh = opendir($path);
      while ((($file = readdir($dh)) !== false) && $flg) {
        if ($file != '.' && $file != '..') {
          $fullpath = $path . '/' . $file;
          $this->AddToLog(self::LOG_PERMISSIONS, $fullpath);
          if (is_link($fullpath)) {
            $flg = false;
          } elseif (!is_dir($fullpath) && !chmod($fullpath, $filemode)) {
            $flg = false;
          } elseif (!$this->SetDirectoryPermissions($fullpath, $filemode)) {
            $flg = false;
          }
        }
      }
      closedir($dh);
      if ($flg) {
        if (chmod($path, $filemode)) {
          $ret = true;
        }
      }
    } else {
      $ret = chmod($path, $filemode);
    }
    return $ret;
  }

  private function WritePages() {
    // prepare auxiliary files -  check and copy css, image and media files
//    $pathdst = $this->rootpath;
    $htmlsrc = $this->themepath . 'index.html';
//    $csssrc = $this->themepath . 'style.css';
    // copy the auxiliary files
    $this->CopyStyleSheet($this->themepath, $this->rootpath, 'style.css'); // copy the style sheet
    $this->CopyImages($this->themepath, $this->rootpath); // copy the style sheet images
//    $this->CopyImages(PDIR, $this->sourcepath, 'images' . DIRECTORY_SEPARATOR . 'lightbox'); // copy lightbox images
    // copy the media files (logo and images for galeries) to the live website
    if ($this->mode != self::PWOPT_PROFILE) {
      // copy auxiliary files from profile
      $mediadir = PDIR . 'profiles' . DIRECTORY_SEPARATOR . $this->shortname;
      $this->CopyImages($mediadir, $this->rootpath, 'media');
    }
    // create the section processor
//    $this->sectionprocessor = new sectionprocessor($this);
    // write each page
    $this->AddToLog(self::LOG_START_PROCESSOR);
    if (count($this->pagelist->pages)) {
      foreach($this->pagelist->pages as $page) {
        $this->AddToLog(self::LOG_WRITE_PAGE, $page->GetFieldValue('description'));
        $this->WritePage($page, $htmlsrc);
      }
    } else {
      $this->AddToLog(self::LOG_WRITE_PAGE, '** NO PAGE **');
      // TODO: add 404 error page using theme
      //$this->WritePage(false, $htmlsrc); // write no page message using current theme / template
    }
    if ($this->mode != self::PWOPT_PROFILE) {
      $this->account->MarkAsUpdated(); // mark as no longer modified
    }
    $this->SetDirectoryPermissions($this->rootpath, 0755);
    $this->AddToLog(self::LOG_STOP_PROCESSOR, 'page count=' . count($this->pagelist->pages));
  }

  private function CopyStyleSheet($pathsrc, $pathdst, $filename) {
    $dst = $pathdst . DIRECTORY_SEPARATOR . $filename;
    if (file_exists($dst)) {
      unlink($dst);
    }
    if (copy($pathsrc . DIRECTORY_SEPARATOR . $filename, $dst)) {
      $this->AddToLog(self::LOG_RES_COPIED, 'CSS Copied=' . $pathsrc . ' to ' . $pathdst);
    } else {
      $this->AddError("CSS file at '{$pathsrc}' NOT copied");
    }
  }

  private function CopyImages($pathsrc, $pathdst, $dirname = 'images') {
    // ensure the paths have an ending backslash
    $pathsrc = realpath($pathsrc) . DIRECTORY_SEPARATOR . $dirname;
    $pathdst = realpath($pathdst) . DIRECTORY_SEPARATOR . $dirname;
    $this->AddToLog(self::LOG_RES_COPIED, 'Copy Images=' . $pathsrc . ' to ' . $pathdst);
    // ensure the destination directory exists
    $this->DeleteFilesInDirectory($pathdst);
    $this->MakeDirectory($pathdst);
    // copy all files in pathsrc into pathdst
    if ($handle = opendir($pathsrc)) {
      while ($file = readdir($handle)) {
        if (($file != '.') && ($file != '..')) {
          $filesrc = $pathsrc . DIRECTORY_SEPARATOR . $file;
          if (is_file($filesrc)) {
            $filedst = $pathdst . DIRECTORY_SEPARATOR . $file;
            copy($filesrc, $filedst);
          }
        }
      }
      closedir($handle);
    }
  }

  protected function WritePage($page, $htmlsrc) { //$htmlsrc, $pageid = 0) {
    $this->currentpage = $page; //$this->account->FindPage($pageid);
    if ($this->currentpage && $this->currentpage->exists && 
      ($this->currentpage->GetFieldValue('status') == basetable::STATUS_ACTIVE) &&
      $this->currentpage->GetFieldValue('visible')) {
      $this->ProcessPage($htmlsrc);
    }
  }

  private function GetSectionProcessor($pagetype) {
    require_once 'class.sectionprocessor.php';
    switch ($pagetype) {
      case page::PAGETYPE_GENERAL: // gen
        $ret = new sectionprocessorgeneral($this);
        break;
      case page::PAGETYPE_CONTACT: // con
        $ret = new sectionprocessorcontact($this);
        break;
      //case PAGETYPE_ABOUTUS: // abt
      //case PAGETYPE_PRODUCT: //prd
      case page::PAGETYPE_GALLERY: //gal
        $ret = new sectionprocessorgallery($this);
        break;
      case page::PAGETYPE_ARTICLE: // blg
        $ret = new sectionprocessorarticles($this);
        break;
      case page::PAGETYPE_GUESTBOOK: // gbk
        $ret = new sectionprocessorguestbook($this);
        break;
      case page::PAGETYPE_SOCIALNETWORK: // snw
      case page::PAGETYPE_BOOKING: // bk
      case page::PAGETYPE_CALENDAR: // cal
      case page::PAGETYPE_NEWSLETTER: // nl
//      case page::PAGETYPE_PRIVATEAREA: // pvt
//      case page::PAGETYPE_SURVEY: // svy
      default:
        $ret = false;
        break;
    }
    return $ret;
  }

  private function GetPageName() {
    $this->pagename = $this->currentpage->GetFieldValue('name');
  }

  protected function ProcessPage($htmlsrc) {
    $this->sectionprocessor = $this->GetSectionProcessor($this->currentpage->pgtype);
    if ($this->sectionprocessor) {
      $this->GetPageName();
      $htmldst = $this->rootpath . $this->pagename . '.php';
      // prepare the content for the page
      $srchandle = fopen($htmlsrc, 'r'); // open the source hml (template) file
      $this->dsthandle = fopen($htmldst, 'w'); // create the destination file
      // write the code for the page to be run when the page is displayed
      $this->WritePagePreparation($htmldst);
      while(!feof($srchandle)) {
        $this->ProcessPageLine(fgets($srchandle));
      }
      fclose($srchandle);
      fclose($this->dsthandle);
    } else {
      $this->AddToLog(self::LOG_PAGESKIPPED, "Page type {$this->currentpage->pgtype} - ID: {$this->currentpage->ID()}");
    }
  }

  private function ReplacePlaceHolderWithContent(&$line) {
    $ret = false;
    $posstart = stripos($line, '<!-- ');
    $iscomment = !($posstart === false);
    if ($iscomment) {
      $posend = stripos($line, ' -->');
      if (!($posend === false)) {
        $poslen = $posend-$posstart;
        $sectionname = strtolower(trim(substr($line, $posstart+5, $poslen-5)));
        $posend += 4;
        $left = substr($line, 0, $posstart);
        $right = substr($line, $posend, strlen($line) - $posend);
        $sectionvalue = RemoveEmptyElements($this->ProcessSection($sectionname));
        $ret = count($sectionvalue); //true;
        if ($ret) {
          $gap = ($ret > 1) ? "\n" : '';
          $line = $left . $gap . ArrayToString($sectionvalue) . $gap . $right;
        }
      }
    }
    return $ret;
  }

  private function ProcessPageLine($line) {
    // replace all place holders with proper content based on the page details
    while ($this->ReplacePlaceHolderWithContent($line));
    $this->WriteOut($line, false);
  }

  private function WritePagePreparation() {
    // add code based on the page type
    $this->WriteOut($this->sectionprocessor->RetrievePreparationCode());
  }

  private function ProcessSection($sectname) {
    // get the section name, and optionally the tag and class name
    $section = strtok($sectname, ' ');
    $tag = strtok(' ');
    $class = strtok(' ');
    $ret = array();
    // get the html code based on the section name
    $res = $this->sectionprocessor->RetrieveSection($section, $tag, $class);
    // return the html code with optionally wrapped tag and class
    if ($res === false) {
      $ret[] = false; //$sectname;
    } elseif($res) {
/*      if ($tag) {
        $starttag = '<' . $tag;
        if ($class != '') {
          $starttag .= " class='{$class}'";
        }
        $starttag .= '>';
        $endtag = "</{$tag}>";
        $ret = array($starttag, $res, $endtag);
      } else { */
      if (is_string($res)) {
        $ret = explode("\n", $res);
      } else {
        $ret = $res;
      }
//      }
    }
    return $ret;
  }

  private function WriteLogPage() {
    $ret = array();
    $ret[] = '<html>';
    $ret[] = '<header></header>';
    $ret[] = '<body>';
    $ret[] = '  <h1>Log Report for Mini-Website!</h1>';
    $ret[] = "  <p>Account nickname: <strong>{$this->shortname}</strong></p>";
    $ret[] = "  <p>Theme: <strong>{$this->theme->GetFieldValue('description')}</strong></p>";
    $ret[] = "  <p>Build Mode: <strong>{$this->mode}</strong></p>";
    $ret[] = "  <h2>Error messages<h2>";
    if ($this->errors) {
      $ret[] = "  <ul>";
      foreach ($this->errors as $msg) {
        $ret[] = "    <li>{$msg}</li>";
      }
      $ret[] = "  </ul>";
    } else {
      $ret[] = "  <p>NONE</p>";
    }
    $ret[] = "  <h2>Log</h2>";
    $ret[] = "  <ul>";
    $log = array();
    foreach ($this->log as $msg) {
      $log[] = "    <li>{$msg}</li>";
    }
    $ret = array_merge($ret, $log);
    $ret[] = "  </ul>";
    $ret[] = "</body>";
    $ret[] = "</html>";
    $msg = ArrayToString($ret);
    if (in_array(self::PWOPT_PROFILE, $this->options)) {
      if (in_array(self::PWOPT_TEST, $this->options)) {
        echo $msg;
      } elseif ($this->errors) {
        $this->account->EmailToSupport('Page Writer Error', $msg);
      }
    }
    return $ret;
  }

  public function AssignThemeByRef($ref) {
    $line = database::SelectFromTableByRef('theme', $ref);
    $this->theme = new theme($line['id']);
  }

  private function GetTheme() {
    // find theme to use
    require_once 'class.table.theme.php';
    if (!$this->theme) {
      $this->theme = $this->account->Theme(); // use theme from account
    }
    if (!$this->theme->exists) {
      $this->AddError("Theme row {$this->theme->key} does not exist!");
      $ret = false;
    } else {
      $themedir = PDIR . 'themes' . DIRECTORY_SEPARATOR . $this->theme->GetFieldValue('url');
      // cwd is 'scripts' - get full path to theme directory
      $this->themepath = realpath($themedir) . DIRECTORY_SEPARATOR;
      $ret = file_exists($this->themepath . 'index.html') && file_exists($this->themepath . 'style.css');
    }
    return $ret;
  }

}

<?php
// page writer for MyLocalSmallBusiness
// written by Ian Stewart (c) 2011 Whitedream Software
// created: 8 jun 2010
// modified: 24 nov 2012

require_once('library.php');
require_once('database.php');

require_once('packageclass.php');
require_once('accountclass.php');
require_once('contactclass.php');
require_once('themeclass.php');
require_once('pageclass.php');
require_once('productclass.php');
require_once('offerclass.php');
require_once('advertclass.php');
require_once('fileclass.php');
require_once('mediaclass.php');
require_once('rssclass.php');
require_once('socialnetworkclass.php');

require_once('sectionprocessor.php');

//require_once('rssgeneratorclass.php');
//require_once('rsschannelpublic.php');

// page writer modes
define('PWMODE_PROFILE', 1);
define('PWMODE_LIVE', 2);
define('PWMODE_DOMAIN', 3);

// page writer progress states
define('PW_FINDTHEMEPATH', 1);
define('PW_GETWORKINGPATH', 2);
define('PW_GETDESTINATION', 3);
define('PW_PREPROCESSCOMPLETED', 4);
define('PW_BUILDPAGESSTART', 5);
define('PW_PAGECOUNT', 6);
define('PW_BUILDPAGESDONE', 7);
define('PW_PAGEREORDERSTART', 8);
define('PW_PAGEREORDERHOMEPAGE', 9);
define('PW_RESFOUND', 10);
define('PW_RESCOPIED', 11);
define('PW_ERROR', 12);
define('PW_SECTION', 13);
define('PW_WRITEPAGE', 14);


function RebuildPages($account, $publish = false)
{
  if ($publish)
  {
    $mode = PWMODE_LIVE;
  }
  else
  {
    $mode = PWMODE_PROFILE;
  }
  $pagewriter = new pagewriter($account->id, $mode);
  $account->ProcessBusinessContentIntoTags();
  $account->ProcessPageContentsIntoTags();
  $pagewriter->BuildPages();
  $account->UpdateAccountDate();
  // check for domain name - update it if there is one
  if ($publish and $account->hasdomain)
  {
    $pagewriter = new pagewriter($account->id, PWMODE_DOMAIN);
    if ($pagewriter->working)
    {
      $pagewriter->BuildPages();
    }
  }
/*  if (!$publish)
  {
    AccountModified($account->id);
  } */
}

function ProcessIndexPage($pagelist)
{
  $indexfound = false;
  if (count($pagelist) > 0)
  {
    foreach($pagelist as $pageid)
    {
      $page = new page($pageid);
      if ($page->name == 'index')
      {
        $indexfound = true;
      }
    }
    if (!$indexfound)
    {
      $pageid = $pagelist[0];
      $page = new page($pageid);

    }
  }
}

// page writer class that builds websites
class pagewriter
{
  private $accountid;
  private $themeid;
  private $themepath;
  private $destinationfolder;
  private $homepageid;
  private $dsthandle;
  private $error; // array of any error found
  private $progressstate;
  private $loghandle;
  private $shortname;
  private $currenttheme;
  private $pagemgrid;

  private $sectionprocessor; // engine to retrieve content

  public $mode;
  public $rootdirectory;
  public $activeaccount;
  public $currentpage;
  public $currentcontact;
  public $pagelist;
  public $pagecount;
  public $rssfeedfilename;
//  public $pagetype;
  public $relativerootpath;
  public $working; // flag for state after constructor (pre-processing)

  function __construct($aaccountid, $mode)
  {
    $this->working = false;
    $this->error = array();

    $this->mode = $mode;
    $this->accountid = $aaccountid;
    $this->homepageid = -1;
    $this->GetRelativeRootPath();
    // find account details
    $this->activeaccount = new account($aaccountid);
    if ($this->activeaccount->exists)
    {
      $this->shortname = $this->activeaccount->nickname;
      MakeDirectory('logs');
      $this->StartprogressLog('logs/' . $this->shortname . '.html');
      if ($this->FindThemePath())
      {
        $this->working = $this->GetWorkingPath();
        if ($this->working)
        {
          $this->destinationfolder = realpath($this->rootdirectory);
          MakeDirectory($this->destinationfolder . DIRECTORY_SEPARATOR . 'media');
          $this->ProgressState(PW_GETWORKINGPATH, 'destinationfolder=' . $this->destinationfolder);
          $this->currentcontact = new contact($this->activeaccount->contactid);
        }
      }
      else
        $this->AddError("Theme path {$themedir} does not exist, while creating pagewriter!");
    }
    else
    {
      die("Account {$aaccountid} does not exist, while creating pagewriter!");
    }
    // process any errors (needs better display handling)
    if (count($this->error) > 0)
    {
      echo '<html>' . CRNL .
           '<body>' . CRNL .
           '  <h1>Error building mini website!</h1>' . CRNL .
           '  <p>Account nickname: ' . $this->shortname . '</p>' . CRNL .
           '  <p>Theme: ' . $this->currenttheme->description . '</p>' . CRNL .
           '  <p>Build Mode: ' . $this->mode . '</p>' . CRNL .
           '  <p>Error message:' . CRNL .
           '  <ul>';
      foreach ($this->error as $msg)
      {
        echo '    <li>' . $msg . '</li>' . CRNL;
      }
      echo '  </ul>' . CRNL .
           '</body>' . CRNL .
           '</html>';
      exit;
    }
    $this->ProgressState(PW_PREPROCESSCOMPLETED, 'error count=' . count($this->error));
  }

  private function ProgressState($state, $msg)
  {
    switch ($state)
    {
      case PW_FINDTHEMEPATH:
        $ln = 'FIND THEME PATH';
        break;
      case PW_GETWORKINGPATH:
        $ln = 'GET WORKING PATH';
        break;
      case PW_GETDESTINATION:
        $ln = 'GET DESTINATION';
        break;
      case PW_PREPROCESSCOMPLETED:
        $ln = 'PREPROCESS COMPLETED';
        break;
      case PW_BUILDPAGESSTART:
        $ln = 'BUILD PAGES - START';
        break;
      case PW_PAGECOUNT:
        $ln = 'PAGE COUNT';
        break;
      case PW_BUILDPAGESDONE:
        $ln = 'BUILD PAGES - DONE';
        break;
      case PW_PAGEREORDERSTART:
        $ln = 'PAGE REORDER - START';
        break;
      case PW_PAGEREORDERHOMEPAGE:
        $ln = 'PAGE REORDER HOMEPAGE ADDED';
        break;
      case PW_RESFOUND:
        $ln = 'RESOURCE FOUND';
        break;
      case PW_RESCOPIED:
        $ln = 'RESOURCE COPIED';
        break;
      case PW_ERROR:
        $ln = '*** ERROR ***';
        break;
      case PW_SECTION:
        $ln = 'PAGE SECTION';
        break;
      case PW_WRITEPAGE:
        $ln = 'WRITE PAGE';
        break;
      default:
        $ln = '?' . $key . '?';
        break;
    }
    $ln = '<li><strong>[' . $ln . ']</strong> - ' . $msg . '</li>' . CRNL;
    fwrite($this->loghandle, $ln);
  }

  private function FindThemePath()
  {
    // find theme to use
    $this->currenttheme = new theme($this->activeaccount->themeid);
    $this->themeid = $this->activeaccount->themeid;
    if (!$this->activeaccount->exists)
    {
      $this->AddError("Theme row {$this->themeid} does not exist, while creating pagewriter!");
    }
    $themedir = PDIR . 'themes' . DIRECTORY_SEPARATOR . $this->currenttheme->url;
    // cwd is 'scripts' - get full path to theme directory
    $this->themepath = realpath($themedir);
    $this->ProgressState(PW_FINDTHEMEPATH, 'themepath=' . $this->themepath);
    return file_exists($this->themepath);
    // get the path name of the chosen theme
    /*if ($mode == PWMODE_PROFILE)
    {
      $themedir = PDIR . "themes/" . $this->currenttheme->url;
    }*/
  }
  
  private function GetWorkingPath()
  {
    $ret = true;
    switch ($this->mode)
    {
      case PWMODE_PROFILE:
        $this->rootdirectory = realpath(PDIR . "profiles/" . $this->shortname);
        MakeDirectory($this->rootdirectory);
        break;
      case PWMODE_LIVE:
        $this->GetLiveRootPath();
        break;
      case PWMODE_DOMAIN:
        $domainname = GetDomainNameFromSession($this->activeaccount);
        $domainrootdirectory = PDIR . PDIR . $domainname;
        if ($domainname and file_exists($domainrootdirectory))
        {
          $this->rootdirectory = $domainrootdirectory;
        }
        else
        {
          $ret = false; //$this->GetLiveRootPath();
        }
        break;
    }
    $this->ProgressState(PW_GETWORKINGPATH, 'rootdirectory=' . $this->rootdirectory);
    $this->activeaccount->rootpath = $this->rootdirectory;
    return $ret;
  }

  private function GetLiveRootPath()
  {
    if ($this->shortname != '')
    {
      $this->rootdirectory = PDIR . PDIR . PORTAL_DOMAIN . DIRECTORY_SEPARATOR . $this->shortname;
      MakeDirectory($this->rootdirectory); //PDIR . PDIR . PORTAL_DOMAIN . "/" . $this->shortname);
    }
  }

  // main work function - creating pages based on account and theme choosen
  public function BuildPages()
  {
    $ready = ($this->rootdirectory != '')?'YES':'NO';
    $this->ProgressState(PW_BUILDPAGESSTART, 'isready=' . $ready);
    if ($this->rootdirectory)
    {
      // prepare pages to write
      $this->pagemgrid = $this->activeaccount->pagemgrid;
      $this->ReorderPages();
      $this->pagecount = $this->activeaccount->FindPages(STATUS_ACTIVE, true);
      $this->pagelist = $this->activeaccount->pagelist;
      $this->ProgressState(PW_PAGECOUNT, 'page count=' . $this->pagecount);
//      $this->activeaccount->CountPages();
      // prepare auxillary files -  check and copy css, image and media files
      $pathsrc = $this->themepath . DIRECTORY_SEPARATOR;
      $pathdst = $this->destinationfolder . DIRECTORY_SEPARATOR;
      $htmlsrc = $pathsrc . 'index.html'; // get source file from template (theme)
      $csssrc = $pathsrc . 'style.css';
      // set up the required files - make sure they exist!
      if ($this->PrepareFiles($htmlsrc, $csssrc))
      {
        $this->CreateRSSPage();
        // copy the auxiliary files
        $this->CopyStyleSheet($pathsrc, $pathdst, 'style.css'); // copy the style sheet
        $this->CopyImages($pathsrc, $pathdst); // copy the style sheet images
        $this->CopyImages(PDIR, $pathdst, 'images/lightbox'); // copy lightbox images
        // copy the media files (logo and images for galeries) to the live website
        switch ($this->mode)
        {
          case PWMODE_PROFILE:
            break; // already there!
          case PWMODE_LIVE:
          case PWMODE_DOMAIN:
            $mediadir = PDIR . "profiles/" . $this->activeaccount->nickname;
            $this->CopyImages($mediadir, $pathdst, 'media');
            break;
        }
        // create the section processor
        $this->sectionprocessor = new sectionprocessor($this);
        // write each page
        $this->ProgressState(PW_WRITEPAGE, 'START...');
        if ($this->pagecount > 0)
        {
          foreach($this->pagelist as $pageid)
          {
            $this->WritePage($pageid);
          }
        }
        else
        {
          $this->ProgressState(PW_WRITEPAGE, 'NO PAGE');
          $this->WritePage(); // write no page message using current theme / template
        }
        if ($this->mode != PWMODE_PROFILE)
        {
          $this->activeaccount->MarkAsSaved(); // no longer modified
        }
        $this->ProgressState(PW_WRITEPAGE, '..COMPLETED');
      }
      SetDirectoryPermissions($this->rootdirectory, 0755);
      $this->ProgressState(PW_BUILDPAGESDONE, 'page count=' . $this->pagecount);
      $this->StopProgressLog();
    }
  }
  
  private function StartProgressLog($logfilename)
  {
    $this->loghandle = fopen($logfilename, 'w');
    fwrite($this->loghandle, '<html>') . CRNL;
    fwrite($this->loghandle, '<body>') . CRNL;
    fwrite($this->loghandle, '  <h1>Profile Page Write Progress Log</h1>') . CRNL;
    fwrite($this->loghandle, '  <p>Written:' . date('r') . '</p>') . CRNL;
    fwrite($this->loghandle, '  <ul>') . CRNL;
  }
  
  private function StopProgressLog()
  {
    fwrite($this->loghandle, '  </ul>') . CRNL;
    fwrite($this->loghandle, '</body>') . CRNL;
    fwrite($this->loghandle, '</html>') . CRNL;
    fclose($this->loghandle);
  }

  // make sure the pages are in the correct order (is home page first, then page type, then original page order)
  private function ReorderPages()
  {
    $this->homepageid = -1;
    $pagetypes = array();
    $homepagefound = false;
    $id = $this->pagemgrid;
    $this->ProgressState(PW_PAGEREORDERSTART, 'pagemgr=' . $id);
    $query = 'SELECT p.`id`, p.`pageorder`, p.`ishomepage`, p.`name`, t.`pgtype`, t.`name` as ptname ' .
      'FROM `page` p ' .
      "INNER JOIN `pagetype` t ON p.`pagetypeid` = t.`id`" .
      "WHERE p.`pagemgrid` = '{$id}' " .
      'ORDER BY p.`ishomepage` DESC, p.`pageorder`, t.`pgtypeorder` DESC';
    $currentpageorder = 0;
    $result = mysql_query($query) or RaiseError("Error whilst reordering pages: " . mysql_error());
    while ($line = mysql_fetch_assoc($result))
    {
      $pageid = $line['id'];
      $pgtype = $line['pgtype'];
      $name = $line['name'];
      $newname = $name;
      if (isset($pagetypes[$pgtype]))
      {
        $pagetypes[$pgtype]++;
      }
      else
      {
        $pagetypes[$pgtype] = 1;
      }
      if (!$homepagefound)
      {
        if ($pgtype == PAGECREATION_GENERAL)
        {
          $this->homepageid = $pageid;
          $homepagefound = true;
          $lineishomepage = $line['ishomepage'];
          if (($lineishomepage == 0) or ($name != 'index'))
          {
            $page = new page($pageid);
            $page->AssignAccount($this->activeaccount);
            $page->MarkAsHomePage();
          }
        }
      }
      else
      {
        $ptname = $line['ptname'];
        if ($pagetypes[$pgtype] > 1)
        {
          $newname = $ptname . $pageid;
        }
      }
      $linepageorder = $line['pageorder'];
      if (($linepageorder != $currentpageorder) or ($name != $newname))
      {
        $this->ChangePageOrder($pageid, $currentpageorder, $newname);
      }
      $currentpageorder++;
    }
    mysql_free_result($result);
    if (!$homepagefound) // TODO: if page count has reached max then delete last page
    {
      $this->ProgressState(PW_PAGEREORDERHOMEPAGE, 'creating home page');
      $page = new page();
      $page->AssignAccount($this->activeaccount);
      $page->PrepareInitialPage(PAGECREATION_GENERAL);
      $page->ishomepage = 1;
      $page->CreatePage();
    }
  }

  private function ChangePageOrder($pageid, $newpageorder, $newname)
  {
    $query = "UPDATE `page` SET `pageorder` = '{$newpageorder}', `name` = '{$newname}' WHERE `id` = '{$pageid}'";
    mysql_query($query) or RaiseError("Error whilst updating the page order from page table: " . mysql_error());
  }

  private function GetFieldFromTable($tblname, $searchfield, $searchvalue, $reqfield)
  {
    $query = "SELECT `{$reqfield}` FROM `{$tblname}` WHERE `{$searchfield}` = '{$searchvalue}'";
    $result = mysql_query($query) or RaiseError("Error whilst locating row from {$tblname}: " . mysql_error());
    if (mysql_num_rows($result) > 0)
    {
      $line = mysql_fetch_assoc($result);
      $ret = ValueFromLine($line[$reqfield]);
    }
    else
    {
      $ret = false;
    }
    mysql_free_result($result);
    return $ret;
  }

  public function WritePage($apageid = 0)
  {
    $this->currentpage = new page($apageid);
    $this->ProgressState(PW_WRITEPAGE, $this->currentpage->id . ' [' . $this->currentpage->status . '] ' . $this->currentpage->description);
    if ($this->currentpage->exists && $this->currentpage->status == STATUS_ACTIVE && $this->currentpage->visible)
    {
      $this->currentpage->AssignAccount($this->activeaccount);
      $this->ProcessPageWriter();
    }
  }

  private function AddGetPost($var, $name)
  {
    $ret = 'if (isset($_POST["' . $var . '"]))' . CRNL .
      '{' . CRNL .
      '  $' . $name . ' = $_POST["' . $var . '"];' . CRNL .
      '}' . CRNL .
      'else' . CRNL .
      '{' . CRNL .
      '  $' . $name . " = '';" . CRNL .
      '}' . CRNL;
    return $ret;
  }

  private function WriteAboutUsCheckCode()
  {
  }

  private function WriteStatsCode()
  {
    //global $rootpath;
    $path = $this->relativerootpath;
    $pageid = $this->currentpage->id;
    $pagemgrid = $this->activeaccount->pagemgrid;
    $ret = '/* collect statistics */' . CRNL .
      "require_once('" . $path . 'scripts' . DIRECTORY_SEPARATOR . "statistics.php');" . CRNL .
      "DoStats({$pagemgrid}, {$pageid});" . CRNL . CRNL;
    return $ret;
  }

  private function WriteAdvertPrepareCode()
  {
    // only show adverts when live
    if (($this->mode == PWMODE_LIVE) && $this->activeaccount->showadverts)
    {
      $path = PDIR;
      $ret = '/* adverts */' . CRNL .
        "require_once('" . $path . 'scripts/advertclass.php' . "');" . CRNL .
        'function AddAdvertInPage()' . CRNL .
        '{' . CRNL .
        '  $advertitem = new advertitem();' . CRNL .
        '  $advert = $advertitem->GetRandomAdvert();' . CRNL .
        '  if ($advert)' . CRNL .
        '  {' . CRNL .
        '    echo $advert->ShowContent();' . CRNL .
        '  }' . CRNL .
        '}' . CRNL;
    }
    else
      $ret = '';
    return $ret;
  }

  private function WritePublishStateCode()
  {
    //global $rootpath;
    $path = $this->relativerootpath;
    $ret = '/* check for publishing state */' . CRNL .
      "require_once('" . $path . "scripts/publishstate.php');" . CRNL .
      'DoCheckState(' . $this->activeaccount->id . ');' . CRNL . CRNL;
    return $ret;
  }

  private function WritePagePHP()
  {
    $this->WriteOut('<?php', true); // start PHP
    // write the public state code
    $this->WriteOut($this->WritePublishStateCode(), true);
    // write the statistics code
    $this->WriteOut($this->WriteStatsCode(), true);
    // add advert code
    $this->WriteOut($this->WriteAdvertPrepareCode());
    // add code based on the page type
    $code = $this->sectionprocessor->RetrievePreparationCode();
    $this->WriteOut($code, true);
    $this->WriteOut('?>', true); // end PHP
  }

  private function GetPageName()
  {
    $this->pagename = $this->currentpage->name;
/*    if ($this->pagecount > 1)
    {
      $this->pagename = $this->currentpage->name;
    }
    else
    {
      $this->pagename = 'index'; // only one page so override to 'index'
    } */
  }

  private function ProcessPageWriter()
  {
    $this->GetPageName();
    $pathsrc = $this->themepath . DIRECTORY_SEPARATOR;
    $pathdst = $this->destinationfolder;
    $htmlsrc = $pathsrc . 'index.html'; // get source file from template (theme)
    $htmldst = $pathdst . DIRECTORY_SEPARATOR . $this->pagename . '.php';
    // prepare the content for the page
//    $pagetypedetails = SelectFromTableByID('pagetype', $this->currentpage->pagetypeid);
//    $this->pagetype = $pagetypedetails['pgtype'];
    // open the source html (template) file
    $srchandle = fopen($htmlsrc, 'r');
    // create the destination file
    $this->dsthandle = fopen($htmldst, 'w');
    // write the code for the page to be run when the page is displayed
    $this->WritePagePHP($htmldst);
    while(!feof($srchandle))
    {
      // get current line
      $line = fgets($srchandle);
      // check to see if the line is a comment
      $posstart = stripos($line, '<!-- ');
      $iscomment = !($posstart === false);

      if ($iscomment)
      {
        $posend = stripos($line, ' -->');
        if (!($posend === false))
        {
          $poslen = $posend-$posstart;
          $sectionname = strtolower(trim(substr($line, $posstart+5, $poslen-5)));
          $posend = $posend +4;
          $left = substr($line, 0, $posstart);
          $right = substr($line, $posend, strlen($line) - $posend);
          $sectionvalue = $this->ProcessSection($sectionname);
          $line = $left . $sectionvalue . $right;
          $this->WriteOut($line);
        }
      }
      else
      {
        // look for detail place holder
        // write line to output file
        $this->WriteOut($line);
      }
    }
    fclose($srchandle);
    fclose($this->dsthandle);
    //return $this->pagename;
  }

  private function PrepareFiles($htmlsrc, $csssrc)
  {
    $ret = false;
    if (file_exists($htmlsrc))
    {
      $this->ProgressState(PW_RESFOUND, 'HTML FILE=' . $htmlsrc);
      if (file_exists($csssrc))
      {
        $ret = true;
        $this->ProgressState(PW_RESFOUND, 'CSS FILE=' . $csssrc);
      }
      else
        $this->AddError("CSS file '{$csssrc}' does not exist.");
    }
    else
      $this->AddError("HTML file '{$htmlsrc}' does not exist.");
    return $ret;
  }

  private function CopyStyleSheet($pathsrc, $pathdst, $filename)
  {
    if (copy($pathsrc . DIRECTORY_SEPARATOR . $filename, $pathdst . DIRECTORY_SEPARATOR . $filename))
    {
      $this->ProgressState(PW_RESCOPIED, 'CSS Copied=' . $pathsrc . ' to ' . $pathdst);
    }
    else
    {
      $this->AddError("CSS file at '{$pathsrc}' NOT copied");
    }
  }
  
  private function AddError($msg)
  {
    $this->error[] = $msg;
    $this->ProgressState(PW_ERROR, 'Error=' . $msg);
  }

  private function CopyImages($pathsrc, $pathdst, $dirname = 'images')
  {
    // ensure the paths have an ending backslash
    $pathsrc = realpath($pathsrc) . DIRECTORY_SEPARATOR . $dirname;
    $pathdst = realpath($pathdst) . DIRECTORY_SEPARATOR . $dirname;
    $this->ProgressState(PW_RESCOPIED, 'Copy Images=' . $pathsrc . ' to ' . $pathdst);
    // ensure the destination directory exists
    DeleteFilesInDirectory($pathdst);
    MakeDirectory($pathdst);
    // copy all files in pathsrc into pathdst
    if ($handle = opendir($pathsrc))
    {
      while ($file = readdir($handle))
      {
        if (($file != '.') && ($file != '..'))
        {
          $filesrc = $pathsrc . DIRECTORY_SEPARATOR . $file;
          if (is_file($filesrc))
          {
            $filedst = $pathdst . DIRECTORY_SEPARATOR . $file;
            copy($filesrc, $filedst);
          }
        }
      }
      closedir($handle);
    }
  }

  private function WriteOut($line, $nl = false)
  {
    $out = $line;
    if (trim($out) != '')
    {
      if ($nl)
      {
        $out .= CRNL;
      }
      fwrite($this->dsthandle, $out);
      return true;
    }
    else
    {
      return false;
    }
  }

  private function CreateRSSPage()
  {
    $this->rssfeedfilename = $this->rootdirectory . DIRECTORY_SEPARATOR . 'pages.xml';
    // create the rss feed
    $desc = strip_tags(html_entity_decode($this->activeaccount->businessinfo));
    $feed = new rss(
      $this->rssfeedfilename, $this->activeaccount->businessname, 
      $desc, 'http://mlsb.org/' . $this->activeaccount->nickname, date('Y-m-d'));

//    $feed->writePi('xml-stylesheet', 'type="text/xsl" href="' . $XSLTFilePath . '"');

    // add the logo of the business, if exists
    if ($this->activeaccount->logomedia->exists)
    {
      $feed->startElement('image');
      $feed->writeElement('title', 'Business Logo');
      $feed->writeElement('link', $this->rootdirectory);
      $feed->writeElement('url', "media/" . $this->activeaccount->logomedia->imgname);
      //$feed->writeElement('width', '120');
      $feed->writeElement('height', $this->activeaccount->logomedia->height);
      $feed->endElement();
    }
    // add each page as a feed
    foreach($this->pagelist as $pageid)
    {
      $page = new page($pageid);
      // ignore any pages not visible
      if ($page->status == STATUS_ACTIVE && $page->visible)
      {
        $item = array();
        $item['title'] = $page->description;
        $item['link'] = $page->name . '.php';
        $item['description'] = $page->description . "<br />" . $page->initialcontent . "<br />" . $page->maincontent;
        $item['guid'] = $this->rootdirectory . DIRECTORY_SEPARATOR . $page->name . '.php';
        $item['date'] = date('Y-m-d');
        $feed->addItem($item);
      }
    }
    // finish the feed and create its url as a link
    $feed->_endRss();
//    $path = $this->GetRelativeRootPath();
  }

  private function GetRelativeRootPath()
  {
    if ($this->mode == PWMODE_LIVE)
    {
      $ret = PDIR; //GetRelativeRootPath($this-mode);
    }
    else
    {
      $ret = PDIR . PDIR;
    }
    $this->relativerootpath = $ret;
    return $ret;
  }

  private function ProcessSection($sectname)
  {
    // get the section name, and optionally the tag and class name
    $section = strtok($sectname, ' ');
    $tag = strtok(' ');
    $class = strtok(' ');
    // get the html code based on the section name
    $res = $this->sectionprocessor->RetrieveSection($section);
    // return the html code with optionally wrapped tag and class
    $ret = $res;
    if ($res != '')
    {
      if ($tag != '')
      {
        $ret = '<' . $tag;
        if ($class != '')
        {
          $ret .= ' class="' . $class . '"';
        }
        $ret .= '>' . $res . '</' . $tag . '>';
      }
    }
    return $ret;
  }
}

?>

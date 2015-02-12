<?php
namespace dana\worker;

require_once 'class.workerform.php';
require_once 'class.workerbase.php';
//require_once 'class.formbuilderdatagrid.php';
//require_once 'class.formbuilderdatalist.php';
//require_once 'class.formbuilderstatusgrid.php';
//require_once 'class.table.page.php';

/**
  * worker page manager class
  * @version dana framework v.3
*/

class workerpgman extends workerform {
  protected $pagedatagrid;
  protected $statusgrid;
  protected $newpagedatalist;
  protected $fldstatusgrid;
  protected $fldpagelist;
  protected $fldnewpagelist;
  protected $pagetype;
  protected $pagemgt;
  protected $pagedescription;
  protected $postalarea;
  protected $countyid;

  private function GetPageTable($pgtype = \dana\table\page::PAGETYPE_GENERAL, $pageid = 0) {
    $ret = null;
    switch ($pgtype) {
      case \dana\table\page::PAGETYPE_GENERAL: //gen
        require_once 'class.table.pagegeneral.php';
        $ret = new \dana\table\pagegeneral($pageid);
        break;
      case \dana\table\page::PAGETYPE_CONTACT: //con
        require_once 'class.table.pagecontact.php';
        $ret = new \dana\table\pagecontact($pageid);
        break;
//      case page::PAGETYPE_ABOUTUS: //abt
//        require_once('class.table.pageaboutus.php');
//        $ret = new pageaboutus($pageid);
//        break;
      case \dana\table\page::PAGETYPE_PRODUCT: //prd
        require_once 'class.table.pageproduct.php';
        $ret = new \dana\table\pageproduct($pageid);
        break;
      case \dana\table\page::PAGETYPE_GALLERY: //gal
        require_once 'class.table.pagegallery.php';
        $ret = new \dana\table\pagegallery($pageid);
        break;
      case \dana\table\page::PAGETYPE_ARTICLE: //art
        require_once 'class.table.pagearticle.php';
        $ret = new \dana\table\pagearticle($pageid);
        break;
      case \dana\table\page::PAGETYPE_GUESTBOOK: //gbk
        require_once 'class.table.pageguestbook.php';
        $ret = new \dana\table\pageguestbook($pageid);
        break;
      case \dana\table\page::PAGETYPE_SOCIALNETWORK: //soc
        require_once 'class.table.pagesocialnetwork.php';
        $ret = new \dana\table\pagesocialnetwork($pageid);
        break;
      case \dana\table\page::PAGETYPE_BOOKING: //bk
        require_once 'class.table.pagebooking.php';
        $ret = new \dana\table\pagebooking($pageid);
        break;
      case \dana\table\page::PAGETYPE_CALENDAR: //cal
        require_once 'class.table.pagecalendar.php';
        $ret = new \dana\table\pagecalendar($pageid);
        break;
      /*      case page::PAGETYPE_SURVEY: //svy
        require_once('class.table.pagesurvey.php');
        $ret = new pagesurvey($pageid);
        break; */
    }
    return $ret;
  }

  protected function DoMoveUp() {
    echo "<p>Move Up: {$this->itemid}</p>\n";
  }

  protected function DoMoveDown() {
    echo "<p>Move Down: {$this->itemid}</p>\n";
  }

  private function GetPageTypeFromPageID($id) {
    $query = "SELECT t.`pgtype` FROM `page` p " .
      "INNER JOIN `pagetype` t ON p.`pagetypeid` = t.`id` " .
      "WHERE p.`id` = {$id}";
    $result = \dana\core\database::Query($query);
    $line = $result->fetch_assoc();
    $result->free();
    return $line['pgtype'];
  }

  private function GetPageTypeFromPageTypeID($id) {
    $query = "SELECT `pgtype` FROM `pagetype` " .
      "WHERE `id` = {$id}";
    $result = \dana\core\database::Query($query);
    $line = $result->fetch_assoc();
    $result->free();
    return $line['pgtype'];
  }

  protected function InitForm() {
    $this->pagetype = GetGet('pt', GetPost('pt', false));
    if (!$this->pagetype) {
      switch ($this->action) {
        case \dana\worker\workerbase::ACT_EDIT:
          $this->pagetype = ($this->itemid > 0)
            ? $this->GetPageTypeFromPageID($this->itemid)
            : \dana\table\page::PAGETYPE_GENERAL;
          break;
        case \dana\worker\workerbase::ACT_NEW:
          $this->pagetype = ($this->itemid > 0)
            ? $this->GetPageTypeFromPageTypeID($this->itemid)
            : \dana\table\page::PAGETYPE_GENERAL;
          $this->itemid = 0;
      }
    }
//    $this->pagelist = new pagelist($this->account);
    //$this->itemid);
    $this->icon = 'images/sect_pages.png';
    $this->activitydescription = 'some text here';
    $this->contextdescription = 'page';
    $this->pagedatagrid = new \dana\formbuilder\formbuilderdatagrid('pagemgt', '', 'Page in Account');
    $this->statusgrid = new \dana\formbuilder\formbuilderstatusgrid('statusgrid', '', 'Page Status');
    $this->newpagedatalist = new \dana\formbuilder\formbuilderdatalist('newpagelist', '', 'Choose a New Page Type');
    switch ($this->action) {
      case workerbase::ACT_EDIT:
      case workerbase::ACT_NEW:
        $this->title = ($this->action == workerbase::ACT_NEW) ? 'New Page' : 'Modify Page';
        $pid = ($this->action == workerbase::ACT_EDIT) ? $this->itemid : 0;
        $this->table = $this->GetPageTable($this->pagetype, $pid);
        $this->table->InitForm($this, $this->action);
        $this->returnidname = $this->idname;
        $this->showroot = false;
        break;
      case workerbase::ACT_REMOVE:
        $this->buttonmode = array(workerform::BTN_CONFIRM, workerform::BTN_CANCEL);
        $this->title = 'Remove Page';
        $this->pagedescription = $this->AddField(
          'description', new \dana\formbuilder\formbuilderstatictext('description', '', 'Page to be removed'));
        $this->action = workerbase::ACT_CONFIRM;
        $this->returnidname = $this->idname;
        $this->showroot = false;
        break;
      case workerbase::ACT_MOVEUP:
        $this->DoMoveUp();
        break;
      case workerbase::ACT_MOVEDOWN:
        $this->DoMoveDown();
        break;
      default: // show datagrid
        require_once 'class.table.page.php';
        $this->table = new \dana\table\pagelist();
        $this->table->SetAccount($this->account);
        $this->buttonmode = array(workerform::BTN_BACK);
        $this->title = 'Manage Pages';
        $this->returnidname = false;

        $pagestats = $this->table->GetPrettyPageStats();
        $this->statusgrid->AddRow(
          array(
            'label' => 'Total pages you have available', 'value' => $pagestats['available'],
            'align' => 'right', 'labelwidth' => '250px', 'valuewidth' => '75px'));
        $this->statusgrid->width = 250+75 . 'px';
        $this->statusgrid->AddRow(array('label' => 'Pages used so far', 'value' => $pagestats['count'], 'align' => 'right'));
        $this->statusgrid->AddRow(array('label' => 'Pages left', 'value' => $pagestats['left'], 'align' => 'right'));
        $this->fldstatusgrid = $this->AddField('statusgrid', $this->statusgrid);

        $this->fldpagelist = $this->AddField('pagelist', $this->pagedatagrid, $this->table);
        $this->fldnewpagelist = $this->AddField(
          'newpagelist', $this->newpagedatalist, $this->table);
        break;
    }
  }

  protected function DeleteItem($itemid) {
    try {
      $status = \dana\table\basetable::STATUS_DELETED;
      $query = "UPDATE `page` SET `status` = '{$status}' " .
      "WHERE `id` = {$itemid}";
      \dana\core\database::Query($query);
      $ret = true;
    } catch (\Exception $e) {
      $this->AddMessage('Cannot remove page');
      $ret = false;
    }
    return $ret;
  }

  // return true if error
  protected function PostFields() {
    switch ($this->action) {
      case workerbase::ACT_EDIT:
      case workerbase::ACT_NEW:
        $ret = false; // the table object will deal with this
//        $ret = $this->pagedescription->Save() + 
//          $this->postalarea->Save() + $this->countyid->Save();
        break;
      case workerbase::ACT_CONFIRM:
        $caption = $this->page->GetFieldValue('description');
        if ($this->DeleteItem($this->itemid)) {
          $this->AddMessage("Page '{$caption}' removed");
        }
        $ret = false;
        break;
      default:
        $ret = true;
    }
    return $ret;
  }

  protected function SaveToTable() {
    return (int) $this->table->StoreChanges();
  }

  protected function AddErrorList() {
  }

  protected function GetHiddenFields() {
    if ($this->pagetype) {
      $this->AddHiddenField('pt', $this->pagetype);
    }
    return parent::GetHiddenFields();
  }

  protected function AssignFieldDisplayProperties() {
    $this->pagedatagrid->SetIDName($this->idname);
    $this->newpagedatalist->SetIDName($this->idname);
    $this->NewSection(
      'sectpagemgt', 'Manage your pages',
      'You can edit or delete your pages. You cannot delete your home page and must be at the top.');
    $this->NewSection(
      'sectnewpage', 'New Page', 'Create a New Page');
    $this->fldpagelist->description = 'Pages that make up your mini-site';
    $this->AssignFieldToSection('sectpagemgt', 'pagelist');
    $this->AssignFieldToSection('sectpagemgt', 'statusgrid');
    $this->fldnewpagelist->description = "Each page is based on a 'type'. Each page type behaves differently " .
      "and contains specific content.\nPlease choose the 'type' of page you would like.";
    $this->AssignFieldToSection('sectnewpage', 'newpagelist');
  }

  protected function AssignItemEditor($isnew) {
//     $title = ($isnew) ? 'New Page' : 'Changing Page';
    $this->table->AssignFieldProperties($this, $isnew);
/*
    $this->NewSection(
    'areascovered', $title, 'Please specify <strong>either</strong> the postal code <strong>or</strong> the county name the area.');
    // description field
    $this->areadescription->description = 'The caption to display to your visitors (ie. name of the area). You can leave this blank and the system will fill this in for you.';
    $this->areadescription->size = 100;
    $this->AssignFieldToSection('areascovered', 'description');
    // postal code field
    $this->postalarea->description = 'The (first) area part of the postal code (e.g. NW2)';
    $this->postalarea->size = 10;
    $this->postalarea->style = 'text-transform:uppercase';
    $this->AssignFieldToSection('areascovered', 'postalarea');
    // county name
    $this->countyid->includenone = true;
    $this->countyid->nonecaption = 'Not Selected';
    $this->AssignFieldToSection('areascovered', 'countyid');
 */
  }

  protected function AssignItemRemove($confirmed) {
    $caption = $this->table->GetFieldValue('description');
    $this->NewSection(
      'confirmation', "Remove '{$caption}'", 'This cannot be undone! Please click on the Confirm button to remove this.');
    $this->AddField(
      'description', new \dana\formbuilder\formbuilderstatictext('description', '', 'Name of the area covered'),
      $this->table);
    $this->AssignFieldToSection('confirmation', 'description');
  }

}

$worker = new workerpgman();

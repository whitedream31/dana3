<?php

require_once 'class.workerform.php';
require_once 'class.workerbase.php';
require_once 'class.formbuilderdatagrid.php';
require_once 'class.formbuilderdatalist.php';
require_once 'class.formbuilderstatusgrid.php';
require_once 'class.table.page.php';

/**
 * activity worker for the page manager
 * dana framework v.3
 */
// page manager

class workerpgman extends workerform {
  protected $pagedatagrid;
  protected $statusgrid;
  protected $newpagedatalist;
  protected $table;

  protected $fldstatusgrid;
  protected $fldpagelist;
  protected $fldnewpagelist;

  protected $pagetype;
  protected $pagemgt;
  protected $pagedescription;
  protected $postalarea;
  protected $countyid;

  private function GetPageTable($pgtype = PAGETYPE_GENERAL, $pageid = 0) {
    $ret = null;
    switch ($pgtype) {
      case PAGETYPE_GENERAL: //gen
        require_once 'class.table.pagegeneral.php';
        $ret = new pagegeneral($pageid);
        break;
      case PAGETYPE_CONTACT: //con
        require_once 'class.table.pagecontact.php';
        $ret = new pagecontact($pageid);
        break;
//      case PAGETYPE_ABOUTUS: //abt
//        require_once('class.table.pageaboutus.php');
//        $ret = new pageaboutus($pageid);
//        break;
      case PAGETYPE_PRODUCT: //prd
        require_once 'class.table.pageproduct.php';
        $ret = new pageproduct($pageid);
        break;
      case PAGETYPE_GALLERY: //gal
        require_once 'class.table.pagegallery.php';
        $ret = new pagegallery($pageid);
        break;
      case PAGETYPE_ARTICLE: //art
        require_once 'class.table.pagearticle.php';
        $ret = new pagearticle($pageid);
        break;
      case PAGETYPE_GUESTBOOK: //gbk
        require_once 'class.table.pageguestbook.php';
        $ret = new pageguestbook($pageid);
        break;
      case PAGETYPE_SOCIALNETWORK: //soc
        require_once 'class.table.pagesocialnetwork.php';
        $ret = new pagesocialnetwork($pageid);
        break;
      case PAGETYPE_BOOKING: //bk
        require_once 'class.table.pagebooking.php';
        $ret = new pagebooking($pageid);
        break;
      case PAGETYPE_CALENDAR: //cal
        require_once 'class.table.pagecalendar.php';
        $ret = new pagecalendar($pageid);
        break;
      /*      case PAGETYPE_SURVEY: //svy
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
    $result = database::Query($query);
    $line = $result->fetch_assoc();
    $result->free();
    return $line['pgtype'];
  }

  private function GetPageTypeFromPageTypeID($id) {
    $query = "SELECT `pgtype` FROM `pagetype` " .
      "WHERE `id` = {$id}";
    $result = database::Query($query);
    $line = $result->fetch_assoc();
    $result->free();
    return $line['pgtype'];
  }

  protected function InitForm() {
    $this->pagetype = GetGet('pt', GetPost('pt', false));
    if (!$this->pagetype) {
      switch ($this->action) {
        case ACT_EDIT:
          $this->pagetype = ($this->itemid > 0)
            ? $this->GetPageTypeFromPageID($this->itemid)
            : PAGETYPE_GENERAL;
          break;
        case ACT_NEW:
          $this->pagetype = ($this->itemid > 0)
            ? $this->GetPageTypeFromPageTypeID($this->itemid)
            : PAGETYPE_GENERAL;
          $this->itemid = 0;
      }
    }
//    $this->pagelist = new pagelist($this->account);
    //$this->itemid);
    $this->icon = 'images/sect_pages.png';
    $this->activitydescription = 'some text here';
    $this->contextdescription = 'page';
    $this->pagedatagrid = new formbuilderdatagrid('pagemgt', '', 'Page in Account');
    $this->statusgrid = new formbuilderstatusgrid('statusgrid', '', 'Page Status');
    $this->newpagedatalist = new formbuilderdatalist('newpagelist', '', 'Choose a New Page Type');
    switch ($this->action) {
      case ACT_EDIT:
      case ACT_NEW:
        $this->title = ($this->action == ACT_NEW) ? 'New Page' : 'Modify Page';
        $this->table = $this->GetPageTable($this->pagetype, $this->itemid);
        $this->table->InitForm($this, $this->action);
        $this->returnidname = $this->idname;
        $this->showroot = false;
        break;
      case ACT_REMOVE:
        $this->buttonmode = array(BTN_CONFIRM, BTN_CANCEL);
        $this->title = 'Remove Page';
        $this->pagedescription = $this->AddField(
          'description', new formbuilderstatictext('description', '', 'Page to be removed'));
        $this->action = ACT_CONFIRM;
        $this->returnidname = $this->idname;
        $this->showroot = false;
        break;
      case ACT_MOVEUP:
        $this->DoMoveUp();
        break;
      case ACT_MOVEDOWN:
        $this->DoMoveDown();
        break;
      default: // show datagrid
        $this->table = new pagelist();
        $this->table->SetAccount($this->account);
        $this->buttonmode = array(BTN_BACK);
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
      $status = STATUS_DELETED;
      $query = "UPDATE `page` SET `status` = '{$status}' " .
      "WHERE `id` = {$itemid}";
      database::Query($query);
      $ret = true;
    } catch (Exception $e) {
      $this->AddMessage('Cannot remove page');
      $ret = false;
    }
    return $ret;
  }

  // return true if error
  protected function PostFields() {
    switch ($this->action) {
      case ACT_EDIT:
      case ACT_NEW:
        $ret = false; // the table object will deal with this
//        $ret = $this->pagedescription->Save() + 
//          $this->postalarea->Save() + $this->countyid->Save();
        break;
      case ACT_CONFIRM:
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
      'sectpagemgt', 'Manage your pages', 'You can edit or delete your pages. You cannot delete your home page and must be at the top.');
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
    $desc = $this->AddField(
      'description', new formbuilderstatictext('description', '', 'Name of the area covered'), $this->table);
    $this->AssignFieldToSection('confirmation', 'description');
  }

}

$worker = new workerpgman();

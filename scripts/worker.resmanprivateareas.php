<?php
require_once 'class.workerform.php';
require_once 'class.workerbase.php';
require_once 'class.formbuilderdatagrid.php';
require_once 'class.formbuilderbutton.php';

/**
  * activity worker for managing private areas
  * dana framework v.3
*/

// resource manage private areas

class workerresmanprivateareas extends workerform {
  protected $datagrid;
//  protected $table;
  protected $tableitems;
  protected $fldtitle;
  protected $fldprivateareas;
  protected $fldaddprivatearea;
  protected $fldpagegrid;
  protected $pagegrid;
  protected $fldaddpage;
  protected $fldmembergrid;
  protected $membergrid;
  protected $fldaddmember;

  protected function InitForm() {
    $this->table = new privatearea($this->itemid);
    $this->icon = 'images/sect_resource.png';
    $this->activitydescription = 'some text here' . ' - ' . $this->idname;
    $this->contextdescription = 'private area management';
    $this->datagrid = new formbuilderdatagrid('privatearea', '', 'Private Areas');
    switch ($this->action) {
      case ACT_NEW:
      case ACT_EDIT:
        $this->title = (($this->action == ACT_NEW) ? 'Create a New' : 'Modify') . ' Private Area';
        $this->fldtitle = $this->AddField(
          'title', new formbuildereditbox('title', '', 'Title of Private Area'), $this->table);
        $this->returnidname = $this->idname;
        $this->showroot = false;
        break;
      case ACT_REMOVE:
        break;
      default:
        $this->buttonmode = array(BTN_BACK);
        $this->title = 'Manage Private Areas'; 
        $this->fldprivateareas = $this->AddField('privateareas', $this->datagrid, $this->table);
        $this->fldaddprivatearea = $this->AddField(
          'addprivatearea', new formbuilderbutton('addprivatearea', 'Add New Private Area'));
        $url = $_SERVER['PHP_SELF'] . "?in={$this->idname}&act=" . ACT_NEW;
        $this->fldaddprivatearea->url = $url;
        break;
    }
  }

  protected function PostFields() {
    switch ($this->action) {
      case ACT_NEW:
      case ACT_EDIT:
        $ret = $this->fldtitle->Save();
        break;
      default:
        $ret = true;
    }
    return $ret;
  }

  protected function SaveToTable() {
    return (int) $this->table->StoreChanges(); //parent::StoreChanges(); //$this->table->StoreChanges();
  }

  protected function AddErrorList() {}

  protected function AssignFieldDisplayProperties() {
    $this->datagrid->SetIDName($this->idname);

    $this->fldprivateareas->description = 'Private Areas';
    $this->AssignFieldToSection('privateareas', 'privateareas');
    if ($this->fldaddprivatearea) {
      $this->fldaddprivatearea->description = "Click this button to add a new private area";
      $this->AssignFieldToSection('privateareas', 'addprivatearea');
    }
  }

  private function PopulatePrivatePagesGrid() {
    $this->pagegrid->showactions = true;
    $this->pagegrid->AddColumn('DESC', 'Title', true);
    $this->pagegrid->AddColumn('PAGETYPE', 'Page Type', false);
    $list = $this->table->linkedpages;
    if ($list) {
      $actions = array(TBLOPT_DELETABLE);
      foreach($list as $page) {
        //$status = $this->table->StatusAsString();
        $coldata = array(
          'DESC' => $page->GetFieldValue('description'),
          'PAGETYPE' => $page->pagetypedescription
        );
        $this->pagegrid->AddRow($page->ID(), $coldata, true, $actions);
      }
    }
  }

  private function PopulatePrivateMembersGrid() {
    $this->membergrid->showactions = true;
    $this->membergrid->AddColumn('DESC', 'Member Name', true);
    $this->membergrid->AddColumn('USERNAME', 'User Name', false);
    $this->membergrid->AddColumn('EMAIL', 'E-Mail', false);
    $list = $this->table->linkedmembers;
    if ($list) {
      $actions = array(TBLOPT_DELETABLE);
      foreach($list as $member) {
        //$status = $this->table->StatusAsString();
        $coldata = array(
          'DESC' => $member->GetDisplayDescription(),
          'USERNAME' => $member->GetFieldValue('username'),
          'EMAIL' => $member->GetFieldValue('email')
        );
        $this->membergrid->AddRow($member->ID(), $coldata, true, $actions);
      }
    }
  }

  protected function AssignItemEditor($isnew) {
    $title = (($isnew) ? 'Creating a new ' : 'Modify a ') . 'Private Area';
    $this->NewSection(
      'privatearea', $title,
      "Please describe the private area with a title (eg. 'Club Members', or 'Staff Only')");
    $this->NewSection(
      'pagegrid', 'Private Pages',
      'Below are the list of pages that are linked to this private area. These pages are only ' .
      'available if the specified members are logged in.');
    $this->NewSection(
      'membergrid', 'Private Members',
      'Below are the members who can view the pages above , if they are logged in.');
    // title field
    $this->fldtitle->description = 'This is the title of the private area.';
    $this->fldtitle->size = 50;
    $this->AssignFieldToSection('privatearea', 'title');
    // page grid
    $this->pagegrid = new formbuilderdatagrid('pagegrid', '', 'Pages');
    $this->pagegrid->SetIDName(IDNAME_MANAGEPRIVATEAREAPAGES);
    $this->PopulatePrivatePagesGrid();
    $this->fldpagegrid = $this->AddField('pagegrid', $this->pagegrid);
    $this->fldpagegrid->description = 'Your pages available with this private area.';
    $this->AssignFieldToSection('pagegrid', 'pagegrid');
    // add page
    $this->fldaddpage = $this->AddField(
      'addpage', new formbuilderbutton('addpage', 'Assign Page To Private Area'));
    $url = $_SERVER['PHP_SELF'] . '?in=' . IDNAME_MANAGEPRIVATEAREAPAGES . '&act=' . ACT_NEW;
    $this->fldaddpage->url = $url;
    $this->AssignFieldToSection('pagegrid', 'addpage');
    // member grid
    $this->membergrid = new formbuilderdatagrid('PopulatePrivateMembersGrid();grid', '', 'Members');
    $this->membergrid->SetIDName(IDNAME_MANAGEPRIVATEAREAMEMBERS);
    $this->PopulatePrivateMembersGrid();
    $this->fldmembergrid = $this->AddField('membergrid', $this->membergrid);
    $this->fldmembergrid->description = 'The members who can access this private area after logging in.';
    $this->AssignFieldToSection('membergrid', 'membergrid');
    // add member
    $this->fldaddmember = $this->AddField(
      'addmember', new formbuilderbutton('addmember', 'Add New Member'));
    $url = $_SERVER['PHP_SELF'] . '?in=' . IDNAME_MANAGEPRIVATEAREAMEMBERS . '&act=' . ACT_NEW;
    $this->fldaddmember->url = $url;
    $this->AssignFieldToSection('membergrid', 'addmember');
  }

  protected function AssignItemRemove($confirmed) {
  }
}

$worker = new workerresmanprivateareas();

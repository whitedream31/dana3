<?php
namespace dana\worker;

require_once 'class.workerform.php';
require_once 'class.workerbase.php';
require_once 'class.formbuilderdatagrid.php';

/*
  articletypeid', DT_FK);
  heading', DT_STRING);
  category', DT_STRING);
  url', DT_STRING);
  content', DT_TEXT);
  stampadded', DT_DATETIME);
  stampupdated', DT_DATETIME);
  expirydate', DT_DATE);
  galleryid', DT_FK);
  allowcomments', DT_BOOLEAN);
  readcount', DT_INTEGER);
  visible', DT_BOOLEAN);
  
  lastupdatedescription = '';
  articletypedescription = '';
*/

/**
  * worker resource manage articles class
  * @version dana framework v.3
*/

class workerresmanarticles extends workerform {
  protected $datagrid;
//  protected $table;
  protected $articlelist;
  protected $fldheading;
  protected $fldcategory;
  protected $fldcontent;

  protected $fldaddarticle;

  protected function InitForm() {
    $this->table = new \dana\table\articleitem($this->itemid);
    $this->icon = 'images/sect_resources.png';
    $this->activitydescription = 'some text here';
    $this->contextdescription = 'managing articles';
    $this->datagrid = new \dana\formbuilder\formbuilderdatagrid('articles', '', 'Articles Available');
    switch ($this->action) {
      case workerbase::ACT_EDIT:
      case workerbase::ACT_NEW:
        $this->title = (($this->action == workerbase::ACT_EDIT) ? 'Modify' : 'New') . ' Article';
        $this->fldheading = $this->AddField(
          'heading', new \dana\formbuilder\formbuildereditbox('heading', '', 'Heading'), $this->table);
        $this->fldheading->required = true;
        $this->fldcategory = $this->AddField(
          'category', new \dana\formbuilder\formbuildereditbox('category', '', 'Category'), $this->table);
        $this->fldcontent = $this->AddField(
          'content', new \dana\formbuilder\formbuildertextarea('content', '', 'Article Text'), $this->table);
        $this->fldcontent->required = true;
        $this->returnidname = $this->idname;
        $this->showroot = false;
        break;
      case workerbase::ACT_REMOVE:
        $this->buttonmode = array(workerform::BTN_CONFIRM, workerform::BTN_CANCEL);
        $this->title = 'Remove Article';
        $this->fldheading = $this->AddField(
          'heading', new \dana\formbuilder\formbuilderstatictext('heading', '', 'Article to be removed'));
        $this->action = workerbase::ACT_CONFIRM;
        $this->returnidname = $this->idname;
        $this->showroot = false;
        break;
      default:
        $this->fldaddarticle = $this->AddField(
          'addarticle', new \dana\formbuilder\formbuilderbutton('addarticle', 'Add Article'));
        $url = $_SERVER['PHP_SELF'] . "?in={$this->idname}&act=" . workerbase::ACT_NEW;
        $this->fldaddarticle->url = $url;

        $this->buttonmode = array(workerform::BTN_BACK);
        $this->title = 'Manage Articles';
        $this->articlelist = $this->AddField('articlelist', $this->datagrid, $this->table);
        break;
    }
  }

  protected function DeleteItem($itemid) {
    try {
      $status = \dana\table\basetable::STATUS_DELETED;
      $query = 'DELETE `articleitem` WHERE `id` = ' . $itemid;
      \dana\core\database::Query($query);
      $ret= true;
    } catch (\Exception $e) {
      $this->AddMessage('Cannot remove article');
      $ret = false;
    }
    return $ret;
  }

  protected function PostFields() {
    switch ($this->action) {
      case workerbase::ACT_EDIT:
      case workerbase::ACT_NEW:
        $ret =
          $this->fldheading->Save() + $this->fldcategory->Save() +
          $this->fldcontent->Save();
        break;
      case workerbase::ACT_CONFIRM:
        $caption = $this->table->GetFieldValue('heading');
        if ($this->DeleteItem($this->itemid)) {
          $this->AddMessage("Article '{$caption}' removed");
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

  protected function AssignFieldDisplayProperties() {
    $this->datagrid->SetIDName($this->idname);
    $this->NewSection(
      'articles', 'Articles For Your Visitors to Read',
      'Please specify the article details.');
    $this->articlelist->description = 'Articles Currently Available';
    $this->AssignFieldToSection('articles', 'articlelist');
    if ($this->fldaddarticle) {
      $this->fldaddarticle->description = 'Click this button to add a new article';
      $this->AssignFieldToSection('articles', 'addarticle');
    }
  }

  protected function AssignItemEditor($isnew) {
    $title = ($isnew) ? 'Add a New Article' : 'Change Article Details';
    $this->NewSection(
      'articles', $title,
      'Please specify the article details to make available to your visitors.');
    // heading field
    $this->fldheading->description = 'A friendly heading for your article.';
    $this->fldheading->placeholder = 'eg. Useful advice';
    $this->fldheading->size = 80;
    $this->AssignFieldToSection('articles', 'heading');
    // category field
    $this->fldcategory->description = 'Please specify a category to group your articles.';
    $this->fldcategory->size = 30;
    $this->fldcategory->placeholder = 'eg. Hints and Tips';
    $this->AssignFieldToSection('articles', 'category');
    // content field
    $this->fldcontent->description = 'Please enter your text for the article. <strong>Please check you spelling and grammar.</strong> Bad spelling and grammar can put people off reading your article.';
    $this->fldcontent->placeholder = 'eg. Our price list for ' . date('Y');
    $this->fldcontent->required = true;
    $this->fldcontent->size = 100;
    $this->AssignFieldToSection('articles', 'content');
  }

  protected function AssignItemRemove($confirmed) {
    $caption = $this->table->GetFieldValue('heading');
    $this->NewSection(
      'confirmation', "Remove '{$caption}'",
      'This cannot be undone! Please click on the Confirm button to remove this article.');
    $this->AddField(
      'heading', new formbuilderstatictext('heading', '', 'Name of the article'), $this->table);
    $this->AssignFieldToSection('confirmation', 'heading');
  }
}

$worker = new workerresmanarticles();

<?php

define('PF_STANDARD', 0);
define('PF_VISIBLE', 1);
define('PF_MAINCONTENT', 2);
define('PF_OPTIONS', 3);

abstract class pgmanbase {
  protected $action;
  protected $fields;
  protected $pgman; // page manager object
   // PF_STANDARD
  public $pagedescription;
  public $header;
  public $initialcontent;
  public $sidecontent;
  // PF_VISIBLE
  public $visible;
  // PF_MAINCONTENT
  public $maincontent;
  // PF_OPTIONS
  public $incrss;
  public $incshownewsletters;
  public $incsocialnetwork;
  public $inctranslation;
  public $showfiles;

  function __construct($pgman, $table, $action) {
    $this->pgman = $pgman;
    $this->table = $table;
    $this->action = $action;
    $this->fields = $this->GetFieldGroups();
    $this->InitFields();
  }

  abstract protected function GetFieldGroups();

  private function InitEditorFields() {
    if (in_array(PF_STANDARD, $this->fields)) {
      $this->pagedescription = $this->pgman->AddField(
        'description', new formbuildereditbox('description', '', 'Description'), $this->table);
      $this->header = $this->pgman->AddField(
        'header', new formbuildereditbox('header', '', 'Header'), $this->table);
      $this->initialcontent = $this->pgman->AddField(
        'initialcontent', new formbuildertextarea('initialcontent', '', 'Initial Content'), $this->table);
      $this->sidecontent = $this->pgman->AddField(
        'sidecontent', new formbuildertextarea('sidecontent', '', 'General Side Content'), $this->table);
    }
    if (in_array(PF_MAINCONTENT, $this->fields)) {
      $this->maincontent = $this->pgman->AddField(
        'maincontent', new formbuildertextarea('maincontent', '', 'Main Content'), $this->table);
    }
    if (in_array(PF_OPTIONS, $this->fields)) {
      $this->incrss = $this->pgman->AddField(
        'incrss', new formbuildercheckbox('incrss', '', 'Include RSS Feed?'), $this->table);
      $this->incshownewsletters = $this->pgman->AddField(
        'incshownewsletters', new formbuildercheckbox('incshownewsletters', '', 'Include Newsletters?'), $this->table);
      $this->incsocialnetwork = $this->pgman->AddField(
        'incsocialnetwork', new formbuildercheckbox('incsocialnetwork', '', 'Include Social Network Links?'), $this->table);
      $this->inctranslation = $this->pgman->AddField(
        'inctranslation', new formbuildercheckbox('inctranslation', '', 'Include Google Translation Tool?'), $this->table);
      $this->showfiles = $this->pgman->AddField(
        'showfiles', new formbuildercheckbox('showfiles', '', 'Include Downloadable Files?'), $this->table);
    }
    if (in_array(PF_VISIBLE, $this->fields)) {
      $this->visible = $this->pgman->AddField(
        'visible', new formbuildercheckbox('visible', '', 'Visible? <small>(available to view in your site)</small>'), $this->table);
    }
  }
  
  protected function InitFields() {
    switch ($this->action) {
      case ACT_NEW:
      case ACT_EDIT:
        $this->InitEditorFields();
        break;
    }
  }

  protected function AddSection($sectionkey, $sectiontitle, $description) {
    $this->pgman->NewSection($sectionkey, $sectiontitle, $description);
  }

  protected function BindToSection($sectionkey, $fieldkey, $field, $fielddesc) {
    // description field
    $field->description = $fielddesc;
    $this->pgman->AssignFieldToSection($sectionkey, $fieldkey);
  }
  
  public function SetupSectionList($pgman) {
    $this->pgman = $pgman;
    if (in_array(PF_STANDARD, $this->fields)) {
//      $this->pagedescription = $this->AddField('description', new formbuildereditbox('description', '', 'Description'), $table);
//      $this->header = $this->AddField('header', new formbuildereditbox('header', '', 'Header'), $table);
//      $this->initialcontent = $this->AddField('initialcontent', new formbuildertextarea('initialcontent', '', 'Initial Content'), $table);
//      $this->sidecontent = $this->AddField('sidecontent', new formbuildertextarea('sidecontent', '', 'General Side Content'), $table);
    }
    if (in_array(PF_MAINCONTENT, $this->fields)) {
      $this->maincontent->rows = 30;
      $this->BindToSection(
        'sctmain', 'maincontent', $this->maincontent,
        'This is the main text of your page. This can be as long as you like. <strong>Please check your spelling and ' .
        'grammar carefully.</strong>');
    }
    if (in_array(PF_OPTIONS, $this->fields)) {
//      $this->incrss = $this->AddField('incrss', new formbuildercheckbox('incrss', '', 'Include RSS Feed?'), $table);
//      $this->incshownewsletters = $this->AddField('incshownewsletters', new formbuildercheckbox('incshownewsletters', '', 'Include Newsletters?'), $table);
//      $this->incsocialnetwork = $this->AddField('incsocialnetwork', new formbuildercheckbox('incsocialnetwork', '', 'Include Social Network Links?'), $table);
//      $this->inctranslation = $this->AddField('inctranslation', new formbuildercheckbox('inctranslation', '', 'Include Google Translation Tool?'), $table);
//      $this->showfiles = $this->AddField('showfiles', new formbuildercheckbox('showfiles', '', 'Include Downloadable Files?'), $table);
    }
    if (in_array(PF_VISIBLE, $this->fields)) {
//      $this->visible = $this->AddField('visible', new formbuildercheckbox('visible', '', 'Visible? <small>(available to view in your site)</small>'), $table);
    }
  }

}

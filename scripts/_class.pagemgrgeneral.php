<?php

require_once 'class.pgmanbase.php';

class pagemgrgeneral extends pgmanbase {

  protected function GetFieldGroups() {
    return array(PF_ALL, PF_VISIBLE, PF_MAINCONTENT, PF_OPTIONS);
  }
/*
    $galleries = $this->GetGalleryList(true);
    $this->gallerygroup = $formeditor->AddDataField($this, 'gengalleryid', 'Include a gallery?', FLDTYPE_SELECT);
    $this->gallerygroup->description =
      'If you have one or more galleries, you can specify it here and it will be shown on this page (one item at a time).';
    $groupid = $this->GetFieldValue('gengalleryid');
    $this->gallerygroup->AddValue(0, '(no gallery)', $groupid == 0);
    foreach ($galleries as $id => $title) {
      $this->gallerygroup->AddValue($id, $title, $id == $groupid);
    }
*/

  public function SetupSectionList($pgman) {
    parent::SetupSectionList($pgman);
  }

}

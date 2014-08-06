<?php
require_once "consts.php";

// basic data types for fields
define('DT_STRING', 's');
define('DT_TEXT', 't');
define('DT_INTEGER', 'i');
define('DT_FLOAT', 'f');
define('DT_DATE', 'd');
define('DT_DATETIME', 'dt');
define('DT_BOOLEAN', 'b');
define('DT_FILEIMG', 'fi');
define('DT_FILEWEB', 'fw');
define('DT_FILEANY', 'fa');
define('DT_ID', 'id');
define('DT_FK', 'fk');
define('DT_TAG', 'tag');
define('DT_STATUS', 'st');
// subtypes
define('DT_REF', 'ref');
define('DT_DESCRIPTION', 'desc');

// basic field types for controls
define('FLDTYPE_NONE', 'x');
define('FLDTYPE_HIDDEN', 'h');
define('FLDTYPE_EDITBOX', 'eb');
define('FLDTYPE_TEXTAREA', 'ta');
define('FLDTYPE_CHECKBOX', 'cb');
define('FLDTYPE_FILE', 'f');
define('FLDTYPE_PASSWORD', 'p');
// multiple value types
define('FLDTYPE_RADIO', 'rb');
define('FLDTYPE_SELECT', 's');
// special types
define('FLDTYPE_DATE', 'd');
define('FLDTYPE_FILEWEBSITE', 'fw');
define('FLDTYPE_FILEWEBIMAGES', 'fwi');
define('FLDTYPE_EMAIL', 'e');
define('FLDTYPE_URL', 'u');
define('FLDTYPE_TELEPHONE', 'tel');
define('FLDTYPE_BUTTON', 'btn');
define('FLDTYPE_CUSTOM', 'ctm');
define('FLDTYPE_STATIC', 'st');
define('FLDTYPE_DATAGRID', 'dg');
define('FLDTYPE_DATALIST', 'dl');
define('FLDTYPE_STATUSGRID', 'sg');

define('STATUS_OK', 'ok');
define('STATUS_WARNING', 'warn');
define('STATUS_ERROR', 'err');

define('STATUS_ACTIVE', 'A');
define('STATUS_DELETED', 'D');

// run mode types ($act)
//define('ACT_LIST', 'l');
define('ACT_NEW', 'n');
define('ACT_EDIT', 'e');
define('ACT_REMOVE', 'r');
define('ACT_CONFIRM', 'c');
define('ACT_VISTOGGLE', 'v');
define('ACT_MOVEDOWN', 'd');
define('ACT_MOVEUP', 'u');
define('ACT_NLSEND', 'ns');
//define('ACT_ADDITEM', 'a');
//define('ACT_IGNOREFIRSTROW', 'ifr');

// page types
define('PAGETYPE_GENERAL', 'gen');
define('PAGETYPE_CONTACT', 'con');
//define('PAGETYPE_ABOUTUS', 'abt');
define('PAGETYPE_PRODUCT', 'prd');
define('PAGETYPE_GALLERY', 'gal');
define('PAGETYPE_ARTICLE', 'art');
define('PAGETYPE_GUESTBOOK', 'gbk');
define('PAGETYPE_SOCIALNETWORK', 'soc');
define('PAGETYPE_BOOKING', 'bk');
define('PAGETYPE_CALENDAR', 'cal');
//define('PAGETYPE_SURVEY', 'svy');

define('ACCSTATUS_UNCONFIRMED', 'uncon');
define('ACCSTATUS_OFFLINE', 'off');
define('ACCSTATUS_UNKNOWN', 'unknown');
define('ACCSTATUS_NOTEXISTS', 'noacc');
define('ACCSTATUS_EXPIRED', 'exp');
define('ACCSTATUS_PUBLISHED', 'pub');
define('ACCSTATUS_PENDING', 'pen');
define('ACCSTATUS_MODIFIED', 'mod');
define('ACCSTATUS_DELETED', 'del');

define('THEMESUITABAILTYTYPE_SIMPLE', 1);
define('THEMESUITABAILTYTYPE_REGULAR', 2);
define('THEMESUITABAILTYTYPE_ADVANCED', 3);

// date formats
define('DF_LONGDATETIME', 'ldt');
define('DF_MEDIUMDATETIME', 'mdt');
define('DF_MEDIUMDATE', 'md');

define('IDNAME_CHANGEORGDETAILS', 'accchgorgdet');
define('IDNAME_CHANGECONDETAILS', 'accchgcondet');
define('IDNAME_CHANGELOGINPWD', 'accchglogin');
define('IDNAME_MANAGEAREASCOVERED', 'accmanareacovered');
define('IDNAME_MANAGEHOURSAVAILABLE', 'accmanhoursavail');
define('IDNAME_MANAGEADDRESSES', 'accmanaddress');
define('IDNAME_MANAGEPAGES', 'pgman');
define('IDNAME_SITEPREVIEW', 'sitepreview');
define('IDNAME_CHANGETHEME', 'sitechgtheme');
define('IDNAME_SITEUPDATE', 'siteupdate');
define('IDNAME_MANAGERATINGS', 'sitemanratings');
define('IDNAME_MANAGEGALLERIES', 'resmangalleries');
define('IDNAME_MANAGEGALLERYIMAGES', 'resmangalleryimages');
define('IDNAME_MANAGEFILES', 'resmanfiles');
define('IDNAME_MANAGEARTICLES', 'resmanarticles');
define('IDNAME_MANAGENEWLETTERS', 'resmannewsletters');
define('IDNAME_MANAGEBOOKINGS', 'resmanbookings');
define('IDNAME_MANAGEGUESTBOOKS', 'resmanguestbooks');
define('IDNAME_MANAGEPRIVATEAREAS', 'resmanprivateareas');
define('IDNAME_MANAGECALENDARDATES', 'resmancalendardates');

/*
define('WORKFUNCTION', 'RunAction');
define('RESOURCEFUNCTION', 'RunResAction');
define('RUNACTION', 'ra');
define('RUNMODE', 'm');
define('RUNKEY', 'k');
define('RUNTYPE', 't');
define('IDREF', 'ir'); // hidden field for processing the form (the context)
define('ERRORCODE', 'e');
define('FIELDSET', 'fs');
define('SUBMITID', 'submitbtn');
define('THEMESUITABAILTY', 'ts');
//define('THEMESUITABAILTYTYPE', 'st');
define('THEMECHOSEN', 'c');

define('PGTY_GEN', 'gen');
define('PGTY_GAL', 'gal');

define('STORESTATUS_NOCHANGE', 0);
define('STORESTATUS_ROWADDED', 1);
define('STORESTATUS_ROWUPDATED', 2);
define('STORESTATUS_ERROR', 3);

define('STATUS_PENDING', 'P');
define('STATUS_NEW', 'N');
define('STATUS_UNCONFIRMED', 'U');
define('STATUS_INACTIVE', 'I');

define('STATUSLEVEL_RED', 3);
define('STATUSLEVEL_AMBER', 2);
define('STATUSLEVEL_GREEN', 1);

define('RES_PREFIX', 'dores_');

// run actions
define('RA_CHANGECONTACTDETAILS', 'ccd');
define('RA_CHANGEORGANISATIONDETAILS', 'cod');
define('RA_CHANGEPASSWORD', 'cp');
//define('RA_PAGEEDITOR', 'editpg'); //'pageeditor');
define('RA_MANAGEGALLERYGROUP', 'mgg');
define('RA_MANAGEGALLERYITEM', 'mgi');
define('RA_MANAGENEWSLETTERGROUP', 'mng');
define('RA_MANAGENEWSLETTERITEM', 'mni');
define('RA_MANAGEGUESTBOOK', 'mu');
define('RA_MANAGEGUESTBOOKENTRY', 'muc');
define('RA_MANAGEFILEITEM', 'mfi');
*/
/*
define('RA_CLOSEANDRESET', 'cr');
define('RA_CHANGEBUSINESSADDRESS', 'cba');
define('RA_CHANGEBUSINESSTELEPHONE', 'cbt');
define('RA_CHANGEBUSINESSEMAIL', 'cbe');
define('RA_CHANGEBUSINESSNAME', 'cbn');
define('RA_CHANGETAGLINE', 'ctl');
define('RA_CHANGEBRIEFBUSINESSDESCRIPTION', 'cbbd');
define('RA_CHANGEMAINBUSINESSCATEGORY', 'cmbc');
define('RA_CHANGEBESPOKEBUSINESSCATEGORIES', 'cbbc');
define('RA_CHANGEBUSINESSLOGO', 'cbl');
define('RA_CHANGEMAINWEBSITE', 'cmw');
define('RA_CHANGEUSERNAME', 'cup');
define('RA_UPDATECHANGES', 'uc'); // update mini-website
define('RA_VIEWTANDC', 'vtac'); // show terms and conditions
define('RA_NEWGALLERYGROUP', RES_PREFIX . 'ngg'); // new gallery group
define('RA_NEWGALLERYITEM', RES_PREFIX . 'ngi'); // new gallery item
define('RA_UPLOADFILE', RES_PREFIX . 'ndf'); // upload file
define('RA_NEWARTICLE', RES_PREFIX . 'nart'); // new article
define('RA_MANAGENEWSLETTER', RES_PREFIX . 'nl'); // manage newsletters
define('RA_MANAGEGUESTBOOK', RES_PREFIX . 'gb'); // manage guestbooks
define('RA_MANAGEBOOKINGS', RES_PREFIX . 'bks'); // manage bookings
define('RA_MANAGECALENDAR', RES_PREFIX . 'cal'); // manage calendar dates
define('RA_MANAGEPRIVATEAREA', RES_PREFIX . 'pap'); // manage private areas
define('RA_INVITENEWSLETTERSUBSCRIBER', 'nlinvite'); // invite newsletter subscriber
define('RA_NEWNEWSLETTER', 'nnl'); // create a new newsletter
define('RA_NEWBOOKINGSETTING', 'nbs'); // create a new book setting record
define('RA_BLOCKBOOKINGDATES', 'bbd'); // mark blocking dates
define('RA_VIEWBOOKINGCLIENTS', 'vbc'); // view clients in the booking list
define('RA_SEARCHBOOKINGBYCLIENTS', 'sbc'); // find a booking by client
define('RA_SEARCHBOOKINGBYDATE', 'sbd'); // find a booking by date
define('RA_NEWCALENDARDATE', 'ncd'); // new calendar date
define('RA_NEWPRIVATEAREAGROUP', 'npag'); // new private area group
define('RA_NEWPRIVATEAREAMEMBER', 'npam'); // new private area member

// booking status types (status values in booking table - from bookingtype table)
define('BS_AVAILABLE', 'A'); // not booked
define('BS_BUSY', 'B'); // no appointments but not available
define('BS_PROVISIONAL', 'P'); // provional booking (made by visitor not confirmed by account holder)
define('BS_CONFIRMED', 'C'); // confirmed appointment
define('BS_HOLIDAY', 'H'); // on holiday not available
define('BS_CANCELLED', 'X'); // cancelled by visitor or account holder

// filename of action buttons
define('AB_EDIT', 'btnedit');
define('AB_DELETEITEM', 'btndelete');
define('AB_MOVEDOWN', 'btnarrdn');
define('AB_MOVEUP', 'btnarrup');
define('AB_NLSEND', 'btnnlsend');

/*
define('IDREF_CHANGECONTACT', 'changecontact');
define('IDREF_CHANGEORGANISATION', 'changebusiness');
define('IDREF_CHANGEPASSWORD', 'login');
define('IDREF_PAGEEDITOR', 'pageeditor');
define('IDREF_GALLERYGROUP', 'galgrp');
define('IDREF_GALLERYITEM', 'galitm');
define('IDREF_NEWSLETTERGROUP', 'nlgrp');
define('IDREF_NEWSLETTERITEM', 'nlitm');
define('IDREF_GUESTBOOK', 'gb');
define('IDREF_GUESTBOOKENTRY', 'gbc');
define('IDREF_FILEITEM', 'fileitm');
define('IDREF_ARTICLE', 'art');
*/
/*
define('IDREF_HOMESUMMARY', 'homesummary');
define('IDREF_HOMECONTACT', 'homecontact');
define('IDREF_HOMEPAGEMGT', 'homepagemgt');
define('IDREF_HOMERESOURCESUMMARY', 'homeresourcesummary');
define('IDREF_NEWSLETTERMANAGEMENT', 'newslettermgt');
define('IDREF_FILEMANAGEMENT', 'filemgt');
define('IDREF_ARTICLEMANAGEMENT', 'articlemgt');
define('IDREF_BOOKINGMANAGEMENT', 'bookingmgt');

// form id references
define('FRM_CHANGECONTACTDETAILS', 'frmchangecontactdetails');
define('FRM_CHANGEBUSINESSDETAILS', 'frmchangebusinessdetails');
define('FRM_PAGEEDITOR', 'frmpageditor');

// active section index (for remembering active section in control activity during each session)
define('ASI_ACCOUNTDETAILS', 0);
define('ASI_PAGEMANAGEMENT', 1);
define('ASI_SITEMANAGEMENT', 2);
define('ASI_RESOURCES', 3);

// field set for form builder tabs
// contact

define('FS_CONTACTNAME', 'contactname');
define('FS_CONTACTADDRESS', 'contactaddress');
define('FS_CONTACTTELEPHONE', 'contacttelephone');
define('FS_CONTACTEMAIL', 'contactemail');
// business
define('FS_BUSINESSNAME', 'businessname');
define('FS_BUSINESSMAINCATEGORY', 'businessmaincategory');
define('FS_BUSINESSCUSTOM', 'businesscustom');
define('FS_MAIN', 'main'); // default
define('FS_CHANGECONTACT', 'chcon');
define('FS_CHANGEORGANISATION', 'chorg');
define('FS_CHANGEPASSWORD', 'chpwd');
define('FS_PAGEHEADING', 'pghead');
define('FS_PAGEMAINCONTENT', 'pgmain');
define('FS_PAGSIDECONTENT', 'pgside');
define('FS_PAGEFOOTER', 'pgfooter');
define('FS_PAGEOPTIONS', 'pgoptions');
define('FS_OPENHOURS', 'hours');
define('FS_GALLERYGROUP', 'galgrp');
define('FS_GALLERYGROUPADD', 'galgrpnew');
define('FS_GALLERYITEMS', 'galitm');
define('FS_ARTICLES', 'arts');
define('FS_ARTICLEADD', 'artadd');
define('FS_ARTICLEEDIT', 'artedit');
define('FS_NEWSLETTERS', 'nlgrp');
define('FS_NEWSLETTERADD', 'nlgrpnew');
define('FS_NEWSLETTERITEMS', 'nlitm');
define('FS_GUESTBOOK', 'gb');
define('FS_GUESTBOOKENTRY', 'gbcmts');
define('FS_FILEITEM', 'fi');
define('FS_FILEITEMUPLOAD', 'fiu');

// form ids
//define('FI_CHANGECONTACTNAME', 'changecontactname');


function ReadGetValue($key, $default = false) {
  return isset($_GET[$key]) ? $_GET[$key] : $default;
}

function ReadPostValue($key, $default = false) {
  return isset($_POST[$key]) ? $_POST[$key] : $default;
}

function ReadSetting($key, $default = 0) {
  return isset($_GET[$key]) ? $_GET[$key] : (isset($_POST[$key]) ? $_POST[$key] : $default);
}

function DebugShowVar($var) {
  var_dump($var);
}

/*function ShowButton($name, $value, $title, $handler, $newwindow = false) {
  if ($newwindow) {
    $event = "javascript:window.open('{$handler}', '_blank');";
  } else {
    $event = "javascript:window.open('{$handler}', '_self');";
  }
  return "<input class='actionbutton' type='button' name='{$name}' value='{$value}' title='{$title}' onclick=\"{$event}\" />";
}*/

function IsBlank($value) {
  return (!$value || strtolower($value) == 'na');
}

function ArrayToString($value) {
  if (is_array($value)) {
    $ret = implode("\n", $value);
  } else {
    $ret = $value;
  }
  return $ret;
}

function StringToArray($value) {
  if (is_array($value)) {
    $ret = $value;
  } else {
    $ret = explode("\n", $value);
  }
  return $ret;
}

function GetGet($name, $default = false) {
  return (isset($_GET[$name])) ? $_GET[$name] : $default;
}

function GetPost($name, $default = false) {
  return (isset($_POST[$name])) ? $_POST[$name] : $default;
}

function __autoload($name) {
  $classfile = "class.{$name}.php";
  if (file_exists($classfile)) {
    include $classfile;
  } else {
    $classfile = "worker.{$name}.php";
    if (file_exists($classfile)) {
      include $classfile;
    } else {
      $classfile = "class.table.{$name}.php";
      if (file_exists($classfile)) {
        include $classfile;
      } else {     
        $classfile = "../../scripts/class.table.{$name}.php";
        if (file_exists($classfile)) {
          include $classfile;
        } else {     
          echo
            "<p class='error'>Class {$name} not found</p>\n" .
            "<p>Current Directory: " . getcwd() . "</p>\n";
          throw new Exception("Unable to load {$name}.");
        }
      }
    }
  }
}

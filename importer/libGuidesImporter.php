<?php
namespace SubjectsPlus\Control;
include_once('..\lib\SubjectsPlus\Control\Guide.php');
include_once('..\lib\SubjectsPlus\Control\Querier.php');
include_once('..\control\includes\config.php');
include_once('..\control\includes\functions.php');


if($lg = simplexml_load_file('./libguides2_export.xml')) {
  foreach ($lg->guides->guide as $gc) {
    setPost($gc); //sets _POST fields with appropriate values from XML
    $newgui = new Guide("", "post"); //create new Guide object with _POST values
    //$newgui->insertRecord(); //insert guide into subject
    $subid = fetchSubjectID(getShortform($gc->name)); //retrieve subject_id of guide that was just inserted


    foreach ($gc->pages->page as $page) {
      setTab($page, $subid);
    }
    break;
  }
} else {
  echo "failed to load file";
}

/*
* getShortform() takes a string and returns a checksum
*   this is a temporary mesaure to get a unique shortform
*   for any guides to be import and will likely be removed
*   or changed later on
*/
function getShortform($str) {
  $newstr = crc32($str);
  if(ord($newstr) == 45){
    $newstr = substr($newstr, 1);
  }
  return $newstr;
}

/*
* getStaffId() returns the staff id of the user
*   associated with an email address($email)
*/
function fetchStaffId($email) {
  $db = new Querier;
  $sql = "SELECT * FROM staff WHERE email = '{$email}'";
  //$sql = "SELECT * FROM subject";
  $result = $db->query($sql);
  if ($result) {
    return $result[0]['staff_id'];
  }
  else {
    return NULL;
  }
}

/*
* fetchSubjectID() returns the subject_id of the 'subject'
*   table entry that matches the provided shortform
*/
function fetchSubjectID($shortform) {
  /*
    code that uses "SELECT `subject_id` FROM `subject` WHERE `shortform` = 'exguide'"
    to get the subject_id of an entry
    Possibly uses Querier
  */
  $db = new Querier;
  $sql = "SELECT * FROM subject WHERE shortform = {$shortform}";
  //$sql = "SELECT * FROM subject";
  $result = $db->query($sql);
  if ($result) {
    return $result[0]['subject_id'];
  }
  else {
    return $result;
  }
}

/*
* setPost() prepares _POST fields with the approriate
*   values from the libguides xml
*/
function setPost($gc) {
  $_POST["subject_id"]=NULL;
  $_POST["subject"]=$gc->name;
  $_POST["shortform"]=getShortform($gc->name);
  $_POST["description"]=$gc->description;
  foreach ($gc->tags->tag as $ti => $tc){
    $_POST["keywords"][$ti] = $tc->name;
  }
  $_POST["redirect_url"]=$gc->redirect;
  $_POST["active"]='1';
  $_POST["type"]='Subject';
  $_POST['extra']=Array('maincol'=>'');
  $_POST['header']='default';
  $_POST['staff_id']=Array(fetchStaffId($gc->owner->email));
  $_POST['parent_id']=[];  //same
  //$_POST['discipline_id'];
}

/*
* setTab() takes a single xml page object from
*   the libguides2_export, extracts the appropriate
*   values and either updates an existing tab or
*   creates a new one
*/
function setTab($page, $subid) {

  $tab_label = $page->name;
  $tab_index = ($page->position - 1);
  if($page->hidden == 1){
    $tab_visibility = 0;
  }
  else{
    $tab_visibility = 1;
  }


  $db = new Querier;
  if ($tab_index == 0) {
    $sql = "UPDATE tab SET label='{$tab_label}', visibility={$tab_visibility} WHERE subject_id={$subid} AND tab_index={$tab_index}";
  }
  else {
    $sql = "INSERT INTO tab (subject_id, label, tab_index, visibility)
            VALUES ('{$subid}', '{$tab_label}', '{$tab_index}', '{$tab_visibility}')";
  }
  $result = $db->exec($sql);
  return $result;
}

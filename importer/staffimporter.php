<?php
  namespace SubjectsPlus\Control;
  use SubjectsPlus\Control\Staff;
  use SubjectsPlus\Control\Querier;
  include_once('..\lib\SubjectsPlus\Control\Staff.php');
  include_once('..\lib\SubjectsPlus\Control\Querier.php');
  include_once('..\control\includes\config.php');
  include_once('..\control\includes\functions.php');


  $lg = simplexml_load_file('./libguides2_export.xml');

  $libaccounts = $lg->accounts->account->count();
  for ($i=0;$i<$libaccounts;$i++){
    $_POST["staff_id"] = NULL;
    $_POST["lname"] = $lg->accounts->account[$i]->last_name;
    $_POST["fname"] = $lg->accounts->account[$i]->first_name;
    $_POST["title"] = NULL;
    $_POST["tel"] = NULL;
    $_POST["department_id"] = array("1");
    $_POST["staff_sort"] = "0";
    $_POST["email"] = $lg->accounts->account[$i]->email;
    $_POST["user_type_id"] = "1";
    $_POST["password"] = $lg->accounts->account[$i]->last_name . '1234!A';
    $_POST["ptags"] = NULL;
    $_POST["active"] = "1";
    $_POST["bio"] = NULL;
    $_POST["message"] = NULL;
    $_POST["position_number"] = NULL;
    $_POST["job_classification"] = NULL;
    $_POST["room_number"] = NULL;
    $_POST["supervisor_id"] = "0";
    $_POST["emergency_contact_name"] = NULL;
    $_POST["emergency_contact_relation"] = NULL;
    $_POST["emergency_contact_phone"] = NULL;
    $_POST["street_address"] = NULL;
    $_POST["city"] = NULL;
    $_POST["state"] = NULL;
    $_POST["zip"] = NULL;
    $_POST["home_phone"] = NULL;
    $_POST["cell_phone"] = $lg->accounts->account[$i]->phone;
    $_POST["fax"] = NULL;
    $_POST["intercom"] = NULL;
    $_POST["lat_long"] = NULL;
    $_POST["debug"] = NULL;
    $_POST["social_media"] = NULL;
    $_POST["extra"] = NULL;

    $user = new Staff("", "post");
    $user->insertRecord();
  }

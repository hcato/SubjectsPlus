<?php
class lgImporter {

  var $libguides;
  var $db;

  function __construct($fexport){
    if (file_exists($fexport)){
      $this->libguides = simplexml_load_file($fexport);
    }
    else {
      echo "construct failed";
    }
    $this->connectDB();
  }

  function connectDB(){
    $this->db = new mysqli("127.0.0.1", "lbcc_sp", "constantine", "lbcc_sp");
    if ($this->db->connect_errno) {
      die("Connection failed: " . $this->db->connect_error);
    };
  }

  function accountsImport(){
    //TODO:
      //determine the fate of the extra fields from export.xml
      //review necessary values that do not appear in xml(password, department_id, etc.)
    $libaccounts = $this->libguides->accounts->account->count();
    for ($i=0;$i<$libaccounts;$i++){
      //echo $libguides->accounts->account[$i]->id;			//toss
      $email = $this->libguides->accounts->account[$i]->email;		//_email
      $fname = $this->libguides->accounts->account[$i]->first_name;	//_fname
      $lname = $this->libguides->accounts->account[$i]->last_name;	//_lname
      //echo $this->libguides->accounts->account[$i]->nickname;	//toss
      //echo $this->libguides->accounts->account[$i]->signature;	//toss
      //echo $this->libguides->accounts->account[$i]->image;		//TBD
      $address = $this->libguides->accounts->account[$i]->address;	//_street_address
      $phone = $this->libguides->accounts->account[$i]->phone;		//_home_phone*
      //echo $this->libguides->accounts->account[$i]->skype;		//toss*
      //echo $this->libguides->accounts->account[$i]->website;	//TBD
      //echo $this->libguides->accounts->account[$i]->created;	//toss
      //echo $this->libguides->accounts->account[$i]->updated;	//toss
      $this->addStaff($fname, $lname, $email, $phone, $address);
    }
  }

  function customerImport(){
    //TODO: function may be unnecessary
    //echo $this->libguides->customer->id;
    echo $this->libguides->customer->type;
    echo $this->libguides->customer->name;
    echo $this->libguides->customer->url;
    echo $this->libguides->customer->city;
    echo $this->libguides->customer->state;
    echo $this->libguides->customer->country;
    echo $this->libguides->customer->time_zone;
    echo $this->libguides->customer->created;
    echo $this->libguides->customer->updated;
  }

  function siteImport(){
    //TODO: function may be unnecessary
    //echo $this->libguides->site->id;
    echo $this->libguides->site->type;
    echo $this->libguides->site->name;
    echo $this->libguides->site->domain;
    echo $this->libguides->site->admin;
    echo $this->libguides->site->created;
    echo $this->libguides->site->updated;
  }

  function subjectsImport(){
    //TODO: function may be unnecessary
    $subjects = $this->libguides->subjects->subject->count();
    for ($i=0;$i<$subjects;$i++){
      echo $this->libguides->subjects->subject[$i]->id, PHP_EOL;
      echo $this->libguides->subjects->subject[$i]->name, PHP_EOL;
      echo $this->libguides->subjects->subject[$i]->url, PHP_EOL;
    }
  }

  function tagsImport(){
    //TODO: function probably unnecessary
    $tags = $this->libguides->tags->tag->count();
    for ($i=0;$i<$tags;$i++){
      //echo $this->libguides->tags->tag[$i]->id;
      echo $this->libguides->tags->tag[$i]->name;
    }
  }

  function guidesImport(){
    $guides = $this->libguides->guides->guide->count();
    for ($i=0;$i<$guides;$i++){
      //echo $this->libguides->guides->guide[$i]->id;
      $type = $this->libguides->guides->guide[$i]->type;
      $name = $this->libguides->guides->guide[$i]->name;
      $desc =  $this->libguides->guides->guide[$i]->description;
      $url = $this->libguides->guides->guide[$i]->url;
      $o_id = $this->libguides->guides->guide[$i]->owner->id;
      $o_email = $this->libguides->guides->guide[$i]->owner->email;
      $o_fname = $this->libguides->guides->guide[$i]->owner->first_name;
      $o_lname = $this->libguides->guides->guide[$i]->owner->last_name;
      $o_img = $this->libguides->guides->guide[$i]->owner->image;
      $group = $this->libguides->guides->guide[$i]->group;
      $redirect = $this->libguides->guides->guide[$i]->redirect;
      $status = $this->libguides->guides->guide[$i]->status;
      $password = $this->libguides->guides->guide[$i]->password;
      //$created = $this->libguides->guides->guide[$i]->created;
      //$updated = $this->libguides->guides->guide[$i]->updated;
      //$published = $this->libguides->guides->guide[$i]->published;
    }
  }

  function addStaff($fname, $lname, $email, $phone, $address){
    if (!$this->entryExists('staff', 'email', $email)){
      $sql = "INSERT INTO staff (lname, fname, email, tel, street_address, user_type_id, department_id)
      VALUES ('$lname', '$fname', '$email', '$phone', '$address', '1', '1')";
      if ($this->db->query($sql) == TRUE) {
        echo "New record created successfully <br>";
      } else {
        echo "Error: NO INSERTION <br>" . $this->db->error;
      };
    };
  }

  function entryExists($table, $col, $target){
    $sql = "SELECT $col FROM $table WHERE $col = '$target'";
    $result = $this->db->query($sql);
    if ($result->num_rows > 0) {
      return True;
    }
    else{
      return False;
    }
  }
}

<?php

include('class-database.php');

class FormValidator{

  public $form;
  private $valid;
  public $errors;
  private $postUrl;
  private $db;

  public function __construct($rawFormData){
    $contents = explode("&", urldecode($rawFormData));
    foreach($contents as $content){
      $keyVals = explode("=", $content);
      $key = $keyVals[0];
      $val = $keyVals[1];
      $this->form[$key] = $val;
    }
    $this->db = new DatabaseConnection();
    $this->errors = array();
    //echo "Created new form instance\n";
  }

  public function validate(){
    /*
    Rules:

    Valid form submissions cannot have:

    - A missing form id
    - Firstname that is the same as the lastname
    - Links (http / https) within the message
    - Invalid email address
    */

    if(!isset($this->form["formid"])){
      $this->writeError("Form id not found");
      return false;
    }

    if(isset($this->form['firstname']) && isset($this->form['lastname'])){
      if($this->form['firstname'] == $this->form['lastname']){
        $this->writeError("First name is same as last name");
        return false;
      }
    }
    if(isset($this->form['message'])){
      if(strpos($this->form['message'],"http") != false){
        $this->writeError("Comments contain links");
        return false;
      }
    }

    if(isset($this->form['email'])){
      if(!filter_var($this->form['email'], FILTER_VALIDATE_EMAIL)){
        $this->writeError("Invalid email address");
        return false;
      }
    }

    /* All validations passed */
    return true;

  }

  private function writeError($error){
    array_push($this->errors, $error);
  }

  private function getPostUrl(){
    $rawPostUrlRow = $this->db->get_result("select url from forms where id = '" . $this->form["formid"] . "'");
    $this->postUrl = $rawPostUrlRow["url"];
  }

  protected function log($isSpam){
    /*
    O: Not spam
    1: Is spam
    */
    $firstname = $this->form["firstname"];
    $lastname = $this->form["lastname"];
    $email = $this->form["email"];
    $query = "insert into formSubmissions(firstname,lastname,email,spam,time";
    $queryValues = " values('$firstname', '$lastname', '$email',$isSpam,now()";
    if(count($this->errors) > 0){
      $this->errors = implode(" ", $this->errors);
      $query .= ",errors)" . $queryValues . ",'$this->errors')";
    }
    else{
      $query .= ")" . $queryValues . ")";
    }
    $this->db->execute($query);
  }

  public function transmit(){
    if($this->validate()){
      $this->send();
    }
    else{
      $this->deny();
    }
  }


  private function send(){
    $this->log(0);
    //$successUrl = get_home_url() . "/form-success/";
    header("Location:http://localhost:8888/mobilehealth/form-success/");
    $this->getPostUrl();
    $this->form = array_slice($this->form,0,-1);

    $request_contents = http_build_query($this->form);
    $stream_options = array( //define http post request options
      'http' => array(
        'method' => "POST",
        'header' => "Content-type: application/x-www-form-encoded",
        'content' => $request_contents
      )
    );

    file_get_contents($this->postUrl,false,stream_context_create($stream_options));
  }

  private function deny(){
    //$failureUrl = get_home_url() . "/form-error/";
    $this->log(1);
    header("Location:http://localhost:8888/mobilehealth/form-error/");
  }

}

 ?>

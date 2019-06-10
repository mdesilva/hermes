<?php

class DatabaseConnection{

  private $connection;

  public function __construct(){
    $this->connection = mysqli_connect("127.0.0.1:8889", "root", "root", "Mobilehealth_current"); //connect to our database
  }

  public function get_result($statement){
    $query = mysqli_query($this->connection,$statement);
    $results = mysqli_fetch_assoc($query);
    return $results;
  }

  public function execute($statement){
    mysqli_query($this->connection,$statement);
  }
}

 ?>

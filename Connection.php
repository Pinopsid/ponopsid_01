<?php

  class Connection {

    var $host="localhost";
    var $dbname="db2";
    var $user="root";
    var $pass="";
 
    var $cnn;
    var $error;

    function Connect(){
      $this->cnn = mysqli_connect($this->host,$this->user,$this->pass,$this->dbname);
      if (!$this->cnn) {
         $this->error = mysqli_connect_error();
      }
	  mysqli_set_charset($this->cnn,'utf8');
    }
  }

?>
<?php 

class DB {
  private $host = 'localhost';
  private $user = 'root';
  private $dbname = 'desafio_splitpay';

  public function connect() {
    $conn_str = "mysql:host=$this->host;dbname=$this->dbname";
    $conn = new PDO($conn_str, $this->user);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $conn;
  }
}
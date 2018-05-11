<?php
class conOCI{
  private $conn;
  private $lastError='';
  private $resActive;

  public function fetch(){
    if(empty($this->resActive)){
      return false;
    }
    $row = oci_fetch_array($this->resActive, OCI_ASSOC+OCI_RETURN_NULLS);
    return $row;
  }

  public function __construct(){
    // $this->conn = $this->connettiOracle();
    // return $this->conn;
  }

public function connettiOracle($user = 'archdb', $pass = 'ARCHIVIO', $host = '//192.168.50.250:1521/xe'){
    $conn = oci_connect($user, $pass, $host);
    return $conn;
  }


  public function getError(){
    return $this->lastError;
  }




  public function query( $que ){
    $stid = oci_parse($this->conn, $que);
    if( $stid != false ){
      if (!oci_execute($stid)) {
        $e = oci_error($stid);
        $this->lastError=$e;
        $this->resActive='';
        return false;
      }else  {
        $this->resActive=$stid;
        return $stid ;
      }
    } else {
      $e = oci_error($stid);
      $this->resActive='';
      $this->lastError=$e;
      return false;
    }
  }

}



?>

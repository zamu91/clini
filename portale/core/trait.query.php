<?php

trait sql{
private $dataPrepare=[];
private $lastError;
private $resActive;
//inclusione e creazione oggetto DB
public function connetti(){
    include_once $_SERVER["DOCUMENT_ROOT"].'/portale/config/conOCI.php';
    $this->conn=new conOCI();
}



public function fetch(){
        if(empty($this->resActive)){
            return false;
        }
        $row = oci_fetch_array($this->resActive, OCI_ASSOC+OCI_RETURN_NULLS);
        return $row;
}



public function query( $que ){
  if(empty($this->conn)){
      $this->connetti();
  }
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




public function adValPreare($col,$val){
  $this->dataPrepare[$col]=$val;
}

public function insertPrepare($tabName,$data){
  $con=$this->getConn();
  if(empty($data)){
    $data=$this->dataPrepare;
  }
  $sql=" INSERT INTO $tabName ( ";
  $col="";
  foreach ($data as $col => $value) {
    if(!empty($col)){
      $col.=",";
      $val=",";
    }
    $col.="$col";
    $val.=" :$col";
  }
  $sql.=$col.") VALUE (".$val.")";

  $compiled = oci_parse($con, $sql);

  foreach ($data as $col => $value) {
    oci_bind_by_name($compiled, ':'.$col, $value);
  }
  oci_execute($compiled);
}

}



 ?>

<?php

trait sql{
  private $dataPrepare=[];
  private $lastError;
  private $resActive;


  private function logQuery($mess){
    if($this->isDebug()){
      $this->setJsonMess("query",$mess);
    }
  }


  private function getConn(){
    if(empty($this->conn)){
      $this->logQuery("Connessione vuota, da istanziare");
      $this->connetti();
    }
    return $this->conn;
  }

  //inclusione e creazione oggetto DB
  public function connetti(){
    $sep=$this->dSep();
    include_once __DIR__.$sep.'..'.$sep."config".$sep."conOCI.php";
    $oc=new conOCI();
    $this->conn=$oc->connettiOracle();
    $this->setJsonMess("query","Connesso a OCI");
  }



  public function fetch(){
    if(empty($this->resActive)){
      return false;
    }
    $row = oci_fetch_array($this->resActive, OCI_ASSOC+OCI_RETURN_NULLS);
    return $row;
  }



  public function query( $que ){
    $conn=$this->getConn();


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
    $sql=" insert INTO $tabName ( ";
    $val="";
    $col="";
    foreach ($data as $colonna => $value) {
      if(!empty($col)){
        $col.=",";
        $val=",";
      }
      $col.="$colonna";
      $val.=" :$colonna";
    }
    $sql.=$col.") VALUES (".$val.")";
    $this->logQuery($sql);
    $compiled = oci_parse($con, $sql);
    foreach ($data as $col => $value) {
      oci_bind_by_name($compiled, ':'.$col, $value);
    }
    oci_execute($compiled);
  }
}



?>

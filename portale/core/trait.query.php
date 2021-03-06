<?php

trait sql{
  private $dataPrepare=[];
  private $dbError;
  private $resActive;
  private $bufQuery;
  private $stmtPrepare;



  private function getIdNext($col,$tabella){
    $this->query("SELECT max({$col}) as ID from {$tabella}  ");
    $row=$row=$this->fetch();
    $id=$row['ID'];
    if(empty($id)){
      $id=1;
    }else{
      $id++;
    }
    return $id;
  }



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
    $this->logQuery("Connesso a OCI");
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
      if (!oci_execute($stid,OCI_NO_AUTO_COMMIT)) {
        $e = oci_error($stid);
        $this->setDbError($e." -- ".$que);
        $this->resActive='';
        return false;
      }else  {
        $this->resActive=$stid;
        return $stid ;
      }
    } else {
      $e = oci_error($stid);
      $this->resActive='';
      $this->setDbError($e);
      return false;
    }
  }

  public function queryPrepare($query){
    $conn=$this->getConn();
    $this->bufQuery=$query;
    $stmt = oci_parse($conn, $query);
    $this->stmtPrepare=$stmt;
  }

  private function setDbError($error){
    $this->dbErro=$error;
  }

  public function getDbError(){
    return $this->dbError;
  }

  public function commit(){
    $conn=$this->getConn();
    if(!oci_commit($conn)) {
      $e = oci_error($conn);
      $error=$e['message'];
      $this->logQuery($error);
      $this->setDbError($error);
      return false;
    }else{
      return false;
    }
  }

  public function getOciError(){
    $e = oci_error($conn);  // For oci_parse errors pass the connection handle
    echo htmlentities($e['message']);
  }

  public function rollback(){
    $conn=$this->getConn();
    oci_rollback($conn);

  }

  public function queryBind($id,$val){
    $stmt=$this->stmtPrepare;
    if(empty($stmt)){
    //  echo "errroe bind dataset vuoto";
  //    return false;

    }
    oci_bind_by_name($this->stmtPrepare, ":".$id, $val); // ,-1,SQLT_CHR);
  }

  public function executeQuery(){
    $this->executePrepare();
  }


  public function queryNumRows(){
    return oci_num_rows($this->resActive);
  }

  public function executePrepare(){
    oci_execute($this->stmtPrepare, OCI_NO_AUTO_COMMIT);
    $this->resActive=$this->stmtPrepare;
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
        $val.=",";
      }
      $col.="$colonna";
      $val.=" :$colonna";
    }
    $sql.=$col.") VALUES (".$val.")";
    $this->logQuery($sql);
    $compiled = oci_parse($con, $sql);
    foreach ($data as $col => $value) {
      oci_bind_by_name($compiled, ':'.$col, $data[$col]);
    }
    oci_execute($compiled);
  }

  public function formOcDate($date){
    $str="TO_DATE($date, 'YYYY-MM-DD')";
    return $str;
  }

  public function formOcDateEu($date){
    $str="TO_DATE($date, 'DD/MM/YYYY')";
    return $str;
  }



} //fine classe



?>

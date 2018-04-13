<?php

trait prenotazione{


  public function salvaPrenotazione(){
    $this->startTransaction();
    $data=$this->post('data');
    $data['IDPRENOTAZIONE']=$this->getNextId('IDPRENOTAZIONE','XDM_AMBULATORIO_PRENOTAZIONE');
    $this->insertPrepare('XDM_AMBULATORIO_PRENOTAZIONE',$data);
    $idOccupato=$data['IDOCCUPATO'];
    $this->segnaOccupato($idOccupato);
    $this->commit();
    $this->setJsonMess("ok",true);
    $this->halt();

  }

  private function segnaOccupato($idOccupato){
    $this->queryPrepare("UPDATE XDM_AMBULATORIO_CONTRATTO_OCCUPATO SET STATO=1 WHERE IDOCCUPATO=:id ");
    $this->queryBind("id",$idOccupato);
    $this->executeQuery();
  }

  public function getClinicaPerData(){
    $data=$this->post('data');
    $que = "SELECT  IDAMBULATORIO
    FROM XDM_AMBULATORIO AS AMB INNER JOIN XDM_AMBULATORIO_CONTRATTO_OCCUPATO AS OCCU
    ON AMB.IDAMBULATORIO=OCCU.IDAMBULATORIO AND OCCUPATO=0
    WHERE DATA=:data  ";
    $this->queryPrepare($que);
    $this->queryBind("data", $data);
    $this->executeQuery();
    $this->procCercaClinica();
  }


  public function getDataPerClinica(){
    $data=$this->post('data');
    $que = "SELECT  IDAMBULATORIO
    FROM XDM_AMBULATORIO AS AMB INNER JOIN XDM_AMBULATORIO_CONTRATTO_OCCUPATO AS OCCU
    ON AMB.IDAMBULATORIO=OCCU.IDAMBULATORIO AND OCCUPATO=0
    WHERE DATA=:data ";
    $this->queryPrepare($que);
    $this->queryBind("data", $data);
    $this->executeQuery();
    $this->procCercaClinica();
  }

  private function procCercaClinica(){
    $i=0;
    $res=[];
    while($row=$this->fetch()){
      $res[$i]=$row;
    }
    $this->setJsonMess("ambulatorio",$res);
    $this->halt();

  }

}


?>

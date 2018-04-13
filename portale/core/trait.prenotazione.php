<?php

trait prenotazione{


  public function salvaPrenotazione(){
    $this->startTransaction();
    $data=$this->post('data');
    //$data['IDPRENOTAZIONE']=$this->getNextId('IDPRENOTAZIONE','XDM_AMBULATORIO_PRENOTAZIONE');
    $dataIns=$this->formOcDate(':data');
    $str="INSERT INTO XDM_PRENOTAZIONE
    (IDPRENOTAZIONE,IDCONTRATTO,ORAINIZIO,ORAFINE,TEMPO)VALUES
    (:id,:idContratto,:oraInizio,:oraFine,:tempo)  ";

    $this->queryPrepare($str);
    $this->queryBind("id",$this->getIdNext("IDPRENOTAZIONE","XDM_PRENOTAZIONE"));
    $this->queryBind("idContratto",$data['IDCONTRATTO']);
    $this->queryBind("oraInizio",$data['ORAINIZIO']);
    $this->queryBind("oraFine",$data['ORAFINE']);
    $this->queryBind("tempo",$data['TEMPO']);
    $this->prepareExecute();
    //TODO: da segnare questo?
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
    $this->procCercaData();
  }

  private function procCercaData(){
    $i=0;

    while($row=$this->fetch()){
      $i++;
      ?>
      <div class="containerClinca">
        <h2><?php echo $row['NOME'];?></h2>
        <button onclick="prenota"><?php echo $row['IDAMBULATORIO'] ?></button
        </div>
        <?php
      }
      if($i==0){
        ?>Nessuna Data valida per la clinica selezionata<?php
      }

    }


    public function getDataPerClinica(){
      $id=$this->post('clinica');
      $que = "SELECT  XDM_AMBULATORIO.IDAMBULATORIO,XDM_AMBULATORIO.NOME,XDM_PRENOTAZIONE.DATA
      FROM XDM_AMBULATORIO
         JOIN XDM_AMBULATORIO_CONTRATTO
          ON XDM_AMBULATORIO.IDAMBULATORIO=XDM_AMBULATORIO_CONTRATTO.IDAMBULATORIO
         JOIN XDM_PRENOTAZIONE ON XDM_PRENOTAZIONE.IDCONTRATTO=XDM_AMBULATORIO_CONTRATTO.IDCONTRATTO
         AND XDM_PRENOTAZIONE.STATO=0
      WHERE XDM_AMBULATORIO.IDAMBULATORIO=:id GROUP BY IDAMBULATORIO,NOME,DATA ";

      echo $que;


      $this->queryPrepare($que);
      $this->queryBind("id", $id);
      $this->executeQuery();
      echo "eseguito";
      $this->procCercaClinica();
      echo "fine";
    }

    private function procCercaClinica(){
      $row=$this->fetch();
      if(!$row){
        ?>
        <h2>Nessuna Clinica disponibile per la data cercata</h2>
        <?php
        return 0;
      }
      ?>
      <div class="containerClinca">
        <h2><?php echo $row['NOME'];?></h2>
        <button onclick="prenota"><?php echo $row['IDAMBULATORIO'] ?></button
        </div>
        <?php
      }


    }//end trait


    ?>

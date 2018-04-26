<?php

trait prenotazione{


  public function salvaPrenotazione(){
    $this->startTransaction();
    $data=$this->post('data');
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
    $que = "SELECT DISTINCT XDM_AMBULATORIO.IDAMBULATORIO,XDM_AMBULATORIO.NOME,XDM_PRENOTAZIONE.DATA
    FROM XDM_AMBULATORIO
    JOIN XDM_AMBULATORIO_CONTRATTO
    ON XDM_AMBULATORIO.IDAMBULATORIO=XDM_AMBULATORIO_CONTRATTO.IDAMBULATORIO
    JOIN XDM_PRENOTAZIONE ON XDM_PRENOTAZIONE.IDCONTRATTO=XDM_AMBULATORIO_CONTRATTO.IDCONTRATTO
    AND XDM_PRENOTAZIONE.STATO=0
    WHERE XDM_PRENOTAZIONE.DATA=:data  ";
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
        ?>Nessuna clinica valida per la data selezionata<?php
      }

    }


    public function getDataPerClinica(){
      $prov=$this->post('clinica');
      $que = "SELECT DISTINCT XDM_AMBULATORIO.IDAMBULATORIO,XDM_AMBULATORIO.NOME,
      XDM_PRENOTAZIONE.DATA,XDM_AMBULATORIO.IDPRENOTAZIONE
      FROM XDM_AMBULATORIO
      JOIN XDM_AMBULATORIO_CONTRATTO
      ON XDM_AMBULATORIO.IDAMBULATORIO=XDM_AMBULATORIO_CONTRATTO.IDAMBULATORIO
      JOIN XDM_PRENOTAZIONE ON XDM_PRENOTAZIONE.IDCONTRATTO=XDM_AMBULATORIO_CONTRATTO.IDCONTRATTO
      AND XDM_PRENOTAZIONE.STATO=0
      WHERE XDM_AMBULATORIO.PROVINCIA=:prov  ";


      $this->queryPrepare($que);
      $this->queryBind("prov", $prov);
      $this->executeQuery();
      $this->procCercaClinica();
    }

    private function procCercaClinica(){
      $i=0;
      while($row=$this->fetch()){
        $i++;

        ?>
        <div class="containerClinca">
          <h2><?php echo $row['NOME']." ".$row['DATA'];?></h2>
          <button class="button is_primary" onclick="prenota('<?php $row['IDPRENOTAZIONE']; ?>');"><?php echo $row['IDAMBULATORIO'] ?></button
          </div>
          <?php
        }

        if($i==0){
          ?>
          <h2>Nessuna Clinica disponibile</h2>
          <?php
          return 0;
        }
      }


    }//end trait


    ?>

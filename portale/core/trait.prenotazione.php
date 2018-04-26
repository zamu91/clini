<?php

trait prenotazione{


  private function getPrimaDisp(){
    $idPrenotazione=$this->post('idPrenotazione');

    $str="SELECT DATA,IDAMBULATORIO,DATA,VERSO,ID_CONTRATTO FROM XDM_PRENOTAZIONE
    INNER JOIN  XDM_AMBULATORIO_CONTRATTO ON
    XDM_AMBULATORIO_CONTRATTO.IDPRENOTAZIONE=XDM_PRENOTAZIONE.IDPRENOTAZIONE
    WHERE XDM_PRENOTAZIONE.IDPRENOTAZIONE=:id ";

    $this->queryPrepare($str);
    $this->queryBind("id","$idPrenotazione");
    $this->prepareExecute();
    $row=$this->fetch();

    $data=$row['DATA'];
    $verso=$row['VERSO'];
    $idCont=$row['IDCONTRATTO'];
    if($verso=='0'){
      $order=" order by IDPRENOTAZIONE desc ";
    }else{
      $order="";
    }


    $str="SELECT IDPRENOTAZIONE,ORAINIZIO,ORAFINE,TEMPO,DATA FROM XDM_PRENOTAZIONE
    WHERE XDM_PRENOTAZIONE.ID_CONTRATTO=:idCont and DATA=:data AND STATO=0 $order ";
    $this->queryPrepare($str);
    $this->queryBind("idCont",$idCont);
    $this->queryBind("data",$data);
    $this->prepareExecute();
    $row=$this->fetch();
    return $row;
  }

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

  private function segnaOccupato($idPrenotazione){
    $this->queryPrepare("UPDATE XDM_PRENOTAZIONE SET STATO=1 WHERE IDPRENOTAZIONE=:id ");
    $this->queryBind("id",$idPrenotazione);
    $this->prepareExecute();
  }

  public function getClinicaPerData(){
    $data=$this->post('data');
    $que = "SELECT DISTINCT XDM_AMBULATORIO.IDAMBULATORIO,XDM_AMBULATORIO.NOME,
    XDM_PRENOTAZIONE.DATA,VERSO,INDIRIZZO,PROVINCIA,COMUNE,IDCONTRATTO,XDM_AMBULATORIO_CONTRATTO.IDCONTRATTO
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
      XDM_PRENOTAZIONE.DATA,VERSO,INDIRIZZO,PROVINCIA,COMUNE,XDM_AMBULATORIO_CONTRATTO.IDCONTRATTO
      FROM XDM_AMBULATORIO
      JOIN XDM_AMBULATORIO_CONTRATTO
      ON XDM_AMBULATORIO.IDAMBULATORIO=XDM_AMBULATORIO_CONTRATTO.IDAMBULATORIO
      JOIN XDM_PRENOTAZIONE ON XDM_PRENOTAZIONE.IDCONTRATTO=XDM_AMBULATORIO_CONTRATTO.IDCONTRATTO
      AND XDM_PRENOTAZIONE.STATO=0
      WHERE XDM_AMBULATORIO.PROVINCIA=:prov order by DATA  ";


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
        <div class="containerClinica">
          <h2><?php echo $row['NOME']." - ".$row['INDIRIZZO']." , ".$row['PROVINCIA']." ".$row['COMUNE']." IN DATA :".$row['DATA'];  ?></h2>
          <button class="button is-primary"
          onclick="scegliPrenotazione('<?php echo $row['IDCONTRATTO'];?>');">PRENOTA
          </button
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

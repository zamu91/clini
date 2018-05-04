<?php

trait prenotazione{

  private $idPrenotazioneWork; //var di lavorazione dell'idPrenotazione

  private function getPrimaDisp(){
    $idCont=$this->post('idContratto');
    $str="SELECT VERSO FROM XDM_AMBULATORIO_CONTRATTO
    WHERE IDCONTRATTO=:id ";
    $this->queryPrepare($str);
    $this->queryBind("id","$idCont");
    $this->executeQuery();
    $row=$this->fetch();
    $data=$this->post('data');
    $verso=$row['VERSO'];

    if($verso=='0'){
      $order=" order by IDPRENOTAZIONE desc ";
    }else{
      $order="";
    }


    $str="SELECT IDPRENOTAZIONE,ORAINIZIO,ORAFINE,TEMPO,DATA FROM XDM_PRENOTAZIONE
    WHERE XDM_PRENOTAZIONE.IDCONTRATTO=:idCont and DATA=:data AND STATO=0 $order ";
    $this->queryPrepare($str);
    $this->queryBind("idCont",$idCont);
    $this->queryBind("data",$data);
    $this->executeQuery();
    $row=$this->fetch();
    if($this->queryNumRows()==0){
      return false;
    }

    $this->idPrenotazioneWork=$row['IDPRENOTAZIONE'];
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
    $this->executeQuery();
    //TODO: da segnare questo?
    $idOccupato=$data['IDOCCUPATO'];
    $this->segnaOccupato($idOccupato);
    $this->commit();
    $this->setJsonMess("ok",true);
    $this->halt();

  }

  private function segnaOccupato($doc){
    $this->queryPrepare("UPDATE XDM_PRENOTAZIONE SET STATO=1,DOCNUMBER=:doc WHERE IDPRENOTAZIONE=:id ");
    $this->queryBind("id",$this->idPrenotazioneWork);
    $this->queryBind("doc",$doc);

    $this->prepareExecute();
  }

  public function getClinicaPerData(){
    $data=$this->post('data');
    $cercaData=$this->formOcDateEu(':data');
    $que = "SELECT DISTINCT XDM_AMBULATORIO.IDAMBULATORIO,XDM_AMBULATORIO.NOME,
    XDM_PRENOTAZIONE.DATA,VERSO,INDIRIZZO,PROVINCIA,COMUNE,XDM_AMBULATORIO_CONTRATTO.IDCONTRATTO,
    XDM_AMBULATORIO_CONTRATTO.IDCONTRATTO,
    TO_CHAR(XDM_PRENOTAZIONE.DATA,'DD/MM/YYYY') as DATAFORM
    FROM XDM_AMBULATORIO
    JOIN XDM_AMBULATORIO_CONTRATTO
    ON XDM_AMBULATORIO.IDAMBULATORIO=XDM_AMBULATORIO_CONTRATTO.IDAMBULATORIO
    JOIN XDM_PRENOTAZIONE ON XDM_PRENOTAZIONE.IDCONTRATTO=XDM_AMBULATORIO_CONTRATTO.IDCONTRATTO
    AND XDM_PRENOTAZIONE.STATO=0
    WHERE XDM_PRENOTAZIONE.DATA=$cercaData  ";
    $this->queryPrepare($que);
    $this->queryBind("data", $data);
    $this->executeQuery();
    $this->procCercaClinica();
  }


  public function getDataPerClinica(){
    $prov=$this->post('clinica');
    $que = "SELECT DISTINCT XDM_AMBULATORIO.IDAMBULATORIO,XDM_AMBULATORIO.NOME,
    XDM_PRENOTAZIONE.DATA,VERSO,INDIRIZZO,PROVINCIA,COMUNE,
    XDM_AMBULATORIO_CONTRATTO.IDCONTRATTO,TO_CHAR(XDM_PRENOTAZIONE.DATA,'DD/MM/YYYY') as DATAFORM
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
      if($i==1){
        ?>
        <div class="box column is-12" style="margin-top:15px;">
          <h3>Appuntamenti Disponibili</h3>
        </div>
        <div style="clear:both;"></div>
        <?php
      }
      ?>

      <div class="containerClinica box">
        <div class="content">
          <p><strong>DATA <?php echo $row['DATAFORM']; ?></strong></p>
          <p><?php echo $row['NOME']." - ".$row['INDIRIZZO']." , ".$row['PROVINCIA']." ".$row['COMUNE']; ?>
          </p>
          <button class="button is-primary"
          onclick="scegliPrenotazione('<?php echo $row['IDCONTRATTO'];?>',
          '<?php echo $row['DATA']; ?>');">PRENOTA
        </button>
      </div>
    </div>
    <?php
  } //end ciclo fetch

  if($i==0){
    ?>
    <h2>Nessuna Clinica disponibile</h2>
    <?php
  }
  die;
}


}//end trait


?>

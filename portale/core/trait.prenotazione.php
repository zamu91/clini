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
    $cercaData=$this->formOcDateEu(':data');

    $verso=$row['VERSO'];

    if($verso=='0'){
      $order=" order by P.IDPRENOTAZIONE desc ";
    }else{
      $order="";
    }

    $str="SELECT P.IDPRENOTAZIONE, P.ORAINIZIO, P.ORAFINE, P.TEMPO, P.DATA, AC.IDAMBULATORIO
    FROM XDM_PRENOTAZIONE P
    INNER JOIN XDM_AMBULATORIO_CONTRATTO AC ON P.IDCONTRATTO = AC.IDCONTRATTO
    WHERE P.IDCONTRATTO=:idCont and P.DATA=$cercaData AND P.STATO=0 $order ";
    $this->queryPrepare($str);
    $this->queryBind("idCont",$idCont);
    $this->queryBind("data",$data);
    $this->executeQuery();
    $row=$this->fetch();
    if(empty($row['IDPRENOTAZIONE'])){
      $this->setJsonMess("mess","Nessuna prenotazione disponibile per la giornata, scegli un altro giorno");
      $this->setJsonMess("res",false);
      $this->halt();
      return false;
    }
    $this->idPrenotazioneWork=$row['IDPRENOTAZIONE'];
    //  $this->setJsonMess("debugIdPreno",  $this->idPrenotazioneWork);
    return $row;
  }
  //
  // public function salvaPrenotazione(){
  //   $this->startTransaction();
  //   $data=$this->post('data');
  //   $dataIns=$this->formOcDate(':data');
  //   $str="INSERT INTO XDM_PRENOTAZIONE
  //   (IDPRENOTAZIONE,IDCONTRATTO,ORAINIZIO,ORAFINE,TEMPO)VALUES
  //   (:id,:idContratto,:oraInizio,:oraFine,:tempo)  ";
  //
  //   $this->queryPrepare($str);
  //   $this->queryBind("id",$this->getIdNext("IDPRENOTAZIONE","XDM_PRENOTAZIONE"));
  //   $this->queryBind("idContratto",$data['IDCONTRATTO']);
  //   $this->queryBind("oraInizio",$data['ORAINIZIO']);
  //   $this->queryBind("oraFine",$data['ORAFINE']);
  //   $this->queryBind("tempo",$data['TEMPO']);
  //   $this->executeQuery();
  //   $idOccupato=$data['IDOCCUPATO'];
  //   $this->segnaOccupato($idOccupato);
  //   $this->commit();
  //   $this->setJsonMess("ok",true);
  //   $this->halt();
  // }

  private function segnaOccupato($doc){
    //$this->setJsonMess("debugOccupato",  $doc);
    $str="UPDATE XDM_PRENOTAZIONE SET STATO=1,DOCNUMBER=:dnumber WHERE IDPRENOTAZIONE=:pren ";
    $this->queryPrepare($str);
    $this->queryBind("pren",$this->idPrenotazioneWork);
    $this->queryBind("dnumber",$doc);
    $this->executeQuery();
    $this->commit(); //fine della cosa faccio qua commit

  }

  public function getClinicaPerData(){
    $data=$this->post('data');
    $cercaData=$this->formOcDateEu(':data');
    // $que = "SELECT DISTINCT XDM_AMBULATORIO.IDAMBULATORIO,XDM_AMBULATORIO.NOME,
    // XDM_PRENOTAZIONE.DATA,VERSO,INDIRIZZO,PROVINCIA,COMUNE,XDM_AMBULATORIO_CONTRATTO.IDCONTRATTO,
    // XDM_AMBULATORIO_CONTRATTO.IDCONTRATTO,
    // TO_CHAR(XDM_PRENOTAZIONE.DATA,'DD/MM/YYYY') as DATAFORM
    // FROM XDM_AMBULATORIO
    // JOIN XDM_AMBULATORIO_CONTRATTO
    // ON XDM_AMBULATORIO.IDAMBULATORIO=XDM_AMBULATORIO_CONTRATTO.IDAMBULATORIO
    // JOIN XDM_PRENOTAZIONE ON XDM_PRENOTAZIONE.IDCONTRATTO=XDM_AMBULATORIO_CONTRATTO.IDCONTRATTO
    // AND XDM_PRENOTAZIONE.STATO=0
    // WHERE XDM_PRENOTAZIONE.DATA=$cercaData and  XDM_PRENOTAZIONE.DATA>SYSDATE order by DATA  ";

    $que = "SELECT DISTINCT AM.IDAMBULATORIO
    , AM.NOME
    , PR.DATA
    , AMC.VERSO
    , INITCAP(AM.INDIRIZZO) INDIRIZZO
    , INITCAP(AM.PROVINCIA) PROVINCIA
    , INITCAP(AM.COMUNE) COMUNE
    , AMC.IDCONTRATTO
    , TO_CHAR(PR.DATA,'DD/MM/YYYY') as DATAFORM
    FROM XDM_AMBULATORIO AM
    JOIN XDM_AMBULATORIO_CONTRATTO AMC ON AM.IDAMBULATORIO=AMC.IDAMBULATORIO
    JOIN XDM_PRENOTAZIONE PR ON PR.IDCONTRATTO=AMC.IDCONTRATTO AND PR.STATO=0
    WHERE PR.DATA=$cercaData AND PR.DATA>SYSDATE
    ORDER BY PROVINCIA ASC, COMUNE ASC, NOME ASC";
    $this->queryPrepare($que);
    $this->queryBind("data", $data);
    $this->executeQuery();
    $this->procCercaClinica();
  }


  public function getOptDataRicerca(){
    $que = "SELECT TO_CHAR(DATA,'DD/MM/YYYY') as DATAFORM  from ( SELECT PR.DATA
  FROM  XDM_PRENOTAZIONE PR
  WHERE PR.DATA>SYSDATE AND PR.STATO=0 group by data
  ORDER BY PR.DATA) ";
    $this->query($que);

    $html="";
    while($row=$this->fetch()){
      $html.="<option value='{$row['DATAFORM']}'>{$row['DATAFORM']}</option>";
    }
    echo $html;
    die;

  }



  public function getDataPerClinica(){
    $prov=$this->post('clinica');
    // $que = "SELECT DISTINCT XDM_AMBULATORIO.IDAMBULATORIO,XDM_AMBULATORIO.NOME,
    // XDM_PRENOTAZIONE.DATA,VERSO,INDIRIZZO,PROVINCIA,COMUNE,
    // XDM_AMBULATORIO_CONTRATTO.IDCONTRATTO,TO_CHAR(XDM_PRENOTAZIONE.DATA,'DD/MM/YYYY') as DATAFORM
    // FROM XDM_AMBULATORIO
    // JOIN XDM_AMBULATORIO_CONTRATTO
    // ON XDM_AMBULATORIO.IDAMBULATORIO=XDM_AMBULATORIO_CONTRATTO.IDAMBULATORIO
    // JOIN XDM_PRENOTAZIONE ON XDM_PRENOTAZIONE.IDCONTRATTO=XDM_AMBULATORIO_CONTRATTO.IDCONTRATTO
    // AND XDM_PRENOTAZIONE.STATO=0
    // WHERE XDM_AMBULATORIO.PROVINCIA=:prov and XDM_PRENOTAZIONE.DATA>SYSDATE  order by DATA  ";

    $que = "SELECT DISTINCT AM.IDAMBULATORIO
    , AM.NOME
    , PR.DATA
    , AMC.VERSO
    , INITCAP(AM.INDIRIZZO) INDIRIZZO
    , INITCAP(AM.PROVINCIA) PROVINCIA
    , INITCAP(AM.COMUNE) COMUNE
    , AMC.IDCONTRATTO
    , TO_CHAR(PR.DATA,'DD/MM/YYYY') as DATAFORM
    FROM XDM_AMBULATORIO AM
    JOIN XDM_AMBULATORIO_CONTRATTO AMC ON AM.IDAMBULATORIO=AMC.IDAMBULATORIO
    JOIN XDM_PRENOTAZIONE PR ON PR.IDCONTRATTO=AMC.IDCONTRATTO AND PR.STATO=0
    WHERE AM.PROVINCIA=UPPER(:prov) AND PR.DATA>SYSDATE
    ORDER BY PR.DATA ASC";

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
          <p><?php echo $row['NOME']." - ".$row['INDIRIZZO']." , ".$row['COMUNE']." (".$row['PROVINCIA'].")"; ?>
          </p>
          <button class="button is-primary"
          onclick="scegliPrenotazione('<?php echo $row['IDCONTRATTO'];?>',
          '<?php echo $row['DATAFORM']; ?>');">PRENOTA
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

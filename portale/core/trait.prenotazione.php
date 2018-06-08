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
    //$data=$this->post('data');

    $data=substr($this->post('data'),0,(stripos( $this->post('data'), ' ' )));

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

  //funzione che dato l'idprenotazione mi ritorna il record dei compi che mi servono
  public function getRowPrenotazione($idPrenotazione){
    $str="SELECT P.IDPRENOTAZIONE, P.ORAINIZIO, P.ORAFINE, P.TEMPO, P.DATA, AC.IDAMBULATORIO
          FROM XDM_PRENOTAZIONE P
          INNER JOIN XDM_AMBULATORIO_CONTRATTO AC ON P.IDCONTRATTO = AC.IDCONTRATTO
          WHERE IDPRENOTAZIONE=:id";
    $this->queryPrepare($str);
    $this->queryBind("id",$idPrenotazione);
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


  // funzione test per ottenere la prima data profilo
  public function primaDataLibera(){
    $idCont=$this->post('idContratto');
    $data=$this->post('data');
    $solo_data=substr($this->post('data'),0,(stripos( $this->post('data'), ' ' )));

    $str="SELECT VERSO FROM XDM_AMBULATORIO_CONTRATTO
    WHERE IDCONTRATTO=:id ";
    $this->queryPrepare($str);
    $this->queryBind("id",$idCont);
    $this->executeQuery();
    $row=$this->fetch();

    $verso=$row['VERSO'];

    // trovo il primo idprenotazione libero
    $str="SELECT fn_get_idprenotazione (:idcontratto,:data,:verso) as IDPRENOTAZIONE
          FROM dual";

    $this->queryPrepare($str);
    $this->queryBind("idcontratto",$idCont);
    $this->queryBind("data",$data);
    $this->queryBind("verso",$verso);
    $this->executeQuery();
    $row=$this->fetch();

    //$this->setJsonMess("verso", $verso);
    $this->setJsonMess("idcontratto", $idCont);
    $this->setJsonMess("idprenotazione", $row['IDPRENOTAZIONE']);



    if($row['IDPRENOTAZIONE']>0){
      // dato l'id prenotazione prendo la data e ora dello slot libero
      $str="SELECT * from XDM_PRENOTAZIONE
            WHERE IDPRENOTAZIONE=:idprenotazione";

      $this->queryPrepare($str);
      $this->queryBind("idprenotazione",$row['IDPRENOTAZIONE']);
      $this->executeQuery();
      $row=$this->fetch();

      $this->setJsonMess("res", true);
      $this->setJsonMess("data_p", $solo_data);
      $this->setJsonMess("ora_inizio", $row['ORAINIZIO']);
      $this->setJsonMess("ora_fine", $row['ORAFINE']);
    }
    else{
      $this->setJsonMess("res", false);
    }

    $this->halt();
  }


  // funzione per liberare una prenotazione dallo stato di -1
  public function liberaPrenotazione(){
    $idPrenotazione=$this->post('idPre');
    $str="UPDATE XDM_PRENOTAZIONE
          SET STATO=0
          WHERE IDPRENOTAZIONE=:id";
    $this->queryPrepare($str);
    $this->queryBind("id",$idPrenotazione);
    $this->executeQuery();
    $this->commit();
    $this->setJsonMess("res", true);
    $this->halt();
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
    //$cercaData=$this->formOcDateEu(':data');
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

    /*$que = "SELECT DISTINCT AM.IDAMBULATORIO
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
    ORDER BY PROVINCIA ASC, COMUNE ASC, NOME ASC";*/

    $que="SELECT DISTINCT AM.IDAMBULATORIO
    , AM.NOME
    , PR.DATA
    , AMC.VERSO
    , INITCAP(AM.INDIRIZZO) INDIRIZZO
    , INITCAP(AM.PROVINCIA) PROVINCIA
    , INITCAP(AM.COMUNE) COMUNE
    , AMC.IDCONTRATTO
    , TO_CHAR(PR.DATA,'DD/MM/YYYY')||' '||substr(amc.orainizio,1,5)||'-'||substr(amc.orafine,1,5) as DATAFORM
    FROM XDM_AMBULATORIO AM
    JOIN XDM_AMBULATORIO_CONTRATTO AMC ON AM.IDAMBULATORIO=AMC.IDAMBULATORIO
    JOIN XDM_PRENOTAZIONE PR ON PR.IDCONTRATTO=AMC.IDCONTRATTO AND PR.STATO=0
    WHERE PR.DATA>SYSDATE AND TO_CHAR(PR.DATA,'DD/MM/YYYY')= :data
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

    /*$que = "SELECT DISTINCT AM.IDAMBULATORIO
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
    ORDER BY PR.DATA ASC";*/

    $que ="SELECT DISTINCT AM.IDAMBULATORIO
    , AM.NOME
    , TO_CHAR(PR.DATA,'DD/MM/YYYY')||' '||substr(amc.orainizio,1,5)||'-'||substr(amc.orafine,1,5) DATA
    , AMC.VERSO
    , INITCAP(AM.INDIRIZZO) INDIRIZZO
    , INITCAP(AM.PROVINCIA) PROVINCIA
    , INITCAP(AM.COMUNE) COMUNE
    , AMC.IDCONTRATTO
    , TO_CHAR(PR.DATA,'DD/MM/YYYY')||' '||substr(amc.orainizio,1,5)||'-'||substr(amc.orafine,1,5) as DATAFORM
    FROM XDM_AMBULATORIO AM
    JOIN XDM_AMBULATORIO_CONTRATTO AMC ON AM.IDAMBULATORIO=AMC.IDAMBULATORIO
    JOIN XDM_PRENOTAZIONE PR ON PR.IDCONTRATTO=AMC.IDCONTRATTO AND PR.STATO=0
    WHERE AM.PROVINCIA=UPPER(:prov) AND PR.DATA>SYSDATE
    ORDER BY DATAFORM";


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

      // calcolo campo data fino a che il giro non sarà completo
      $data_temp=substr($row['DATAFORM'],0,(stripos( $row['DATAFORM'], ' ' )));

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

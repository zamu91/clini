<?php

trait contratto{

  private $durataMin=20;


  private $idContrattoWork; //riferimento id su quale associare i blocchi temporali
  private $giorni; //array configurazione giorni
  private $dataWork;

  private $varWork; //data caricata dal POST nel bootstrap

  //varibili di servizio per il processo di inserimento
  private $dataInizio;
  private $dataFine;
  private $oraInizio;
  private $oraFine;

  private $dataFineSTR;
  private $dataInizioSTR;




  private $idPrenotazione; //indice impostato manuale dal ciclo

  private function setIdContratto($id){
    $this->idContrattoWork=$id;
  }

  private function logCont($mess){
    if($this->isDebug()==false){return 0;}
    $this->setJsonMess("logContratto",$mess);
  }

  private function getIdContratto(){
    return $this->idContrattoWork;
  }


  private function errorInputCnt($error,$mess){
    $this->setJsonMess($error,$mess);
    $this->setJsonMess('mess',$mess);

    $this->halt();
  }

  //controllo se l'input inserito è valido
  private function controlInputContratto(){
    $dataInizio=$this->dataInizioSTR;
    $dataFine=$this->dataFineSTR;
    $oraInizio=$this->oraInizio;
    $oraFine=$this->oraFine;

    if($dataInizio<date('Y-m-d')){
      $this->errorInputCnt("data","Attenzione, data inizio nel passato");
    }

    //caso errore date non valide
    if($dataInizio>=$dataFine){
      $this->errorInputCnt("data","Attenzione, data inizio avanti dalla data fine");
    }

    if($oraInizio>=$oraFine){
      $this->errorInputCnt("ora","Attenzione, ora inizio avanti dall'ora fine");
    }

    $durataMin=$this->durataMin;
    if($this->getVCont("TEMPO")<$durataMin){
      $this->errorInputCnt("durata","durata troppo breve, il minimo è $durataMin ");
    }
    $tempo=($this->getVCont("TEMPO"));
    if(!is_numeric($tempo)){
      $this->errorInputCnt("tempo","Formato invalido per il tempo");
    }

  }


  private function getJoinConflict(){
    $adQuery="";
    for($i=1;$i<7;$i++){
      if($this->ifDayWork($i)){
        $tab="GIORNO$i";
        $adQuery.=" INNER JOIN XDM_CONTRATTO_GIORNO  $tab
        ON $tab.IDCONTRATTO=XDM_AMBULATORIO_CONTRATTO.IDCONTRATTO
        ";
      }
    }
    return $adQuery;
  }

  //controllo se ci sono conflitti con altre
  private function checkConflict(){

    $iniz=$this->formOcDate(':dataIniz');
    $fine=$this->formOcDate(':dataFine');

    $adJoin=$this->getJoinConflict();
    $str="SELECT XDM_AMBULATORIO_CONTRATTO.IDCONTRATTO  FROM XDM_AMBULATORIO_CONTRATTO
    $adJoin
    where IDAMBULATORIO=:idAmb and DATAINIZIOCONTRATTO>=$iniz
    and DATAFINECONTRATTO<=$fine and ORAINIZIO>=:oraIniz and ORAFINE<:oraFine
    ";
    $this->queryPrepare($str);
    $this->queryBind('dataIniz',$this->dataInizioSTR);
    $this->queryBind('dataFine',$this->dataFineSTR);
    $this->queryBind('oraIniz',$this->getVCont('ORAINIZIO'));
    $this->queryBind('oraFine',$this->getVCont('ORAFINE'));
    $this->queryBind('idAmb',$this->getVCont('IDAMBULATORIO'));

    $this->executePrepare();
    if($this->queryNumRows()>0){
      $row=$this->fetch();
      $this->setJsonMess("conflitto",$row);
      $this->setJsonMess("mess","Conflitto nel contratto trovato ".$row['IDCONTRATTO']);
      $this->halt();
    }else{
      return true;
    }


  }


  private function insContrattoDb(){
    $this->logCont("Inizio inserimento contratto");
    $iniz=$this->formOcDate(':dataIniz');
    $fine=$this->formOcDate(':dataFine');
    $str="INSERT INTO XDM_AMBULATORIO_CONTRATTO
    (IDCONTRATTO,IDAMBULATORIO, TEMPO,VERSO,DATAINIZIOCONTRATTO,DATAFINECONTRATTO,ORAINIZIO,ORAFINE)
    VALUES(:id,:idAmbulatorio,:tempo,:verso,$iniz,$fine,:oraIniz,:oraFine) ";
    $this->queryPrepare($str);

    $this->logCont("Iniz  e start inserimento contratto");

    $idContrattoNew=$this->getIdNext("IDCONTRATTO","XDM_AMBULATORIO_CONTRATTO");
    $this->queryBind('id',$idContrattoNew);
    $this->queryBind('tempo',$this->getVCont('TEMPO'));
    $this->queryBind('idAmbulatorio',$this->getVCont('IDAMBULATORIO'));
    $this->queryBind('verso',$this->getVCont('VERSO'));
    $this->queryBind('dataIniz',$this->dataInizioSTR);
    $this->queryBind('dataFine',$this->dataFineSTR);
    $this->queryBind('oraIniz',$this->getVCont('ORAINIZIO'));
    $this->queryBind('oraFine',$this->getVCont('ORAFINE'));
    $this->executePrepare();
    $this->logCont("Salvataggio contratto");
    $this->setIdContratto($idContrattoNew);
    $this->insGiorniDb();
  }

  public function insContratto(){
    $this->inizVarContratto();
    $data=$this->post("data");

    $this->controlInputContratto();
    $this->checkConflict();

    $this->insContrattoDb();
    $this->logCont("Iniz variabile e start inserimento spazio");
    $this->occupaSpazioPrenotazione();
    $this->commit();
    $this->halt();
  }

  private function getVCont($var){
    if(!isset($this->varWork[$var])){
      $this->logCont("Variabile $var non trovata");
      return false;
    }
    return $this->varWork[$var];
  }


  //controllo se il giorni è da contare nel contratto o no
  private function ifDayWork($day){
    $giorni=$this->giorni;
    if($giorni[$day]=='1'){
      return true;
    }else{
      return false;
    }
  }

  //inserisco i giorni in oracle come log per capire i giorni della prenotazione
  private function insGiorniDb(){
    $giorni=$this->giorni;
    $i=1;
    $idContratto=$this->getIdContratto();
    foreach ($giorni as $giorno => $attivo) {
      if($attivo=='1'){
        $this->queryPrepare("INSERT INTO XDM_CONTRATTO_GIORNO
          (IDCONTRATTO,GIORNO)
          VALUES(:idcont,:giorno) ");
          $this->queryBind('idcont',$idContratto);
          $this->queryBind('giorno',$giorno);
          $this->executePrepare();
        }
        $i++;
      }
    }

    //setup giorni della settimana
    private function inizVarContratto(){
      $this->varWork=$this->post('data');
      $giorni=$this->post('giorni');
      $giorni[0]=0;
      $this->giorni=$giorni;
      $this->setTime();
      $this->setDate();
      $this->setIdPrenotazione();
    }

    private function setIdPrenotazione(){
      $id=$this->getIdNext("IDPRENOTAZIONE","XDM_PRENOTAZIONE");
      $this->idPrenotazione=$id;
    }


    private function getIncPrenotazione(){
      $this->idPrenotazione++;
      return $this->idPrenotazione;
    }

    public function setTime(){
      $oraInizio=$this->getVCont("ORAINIZIO");
      $oraFine=$this->getVCont("ORAFINE");
      $oraInizio=strtotime($oraInizio);
      $oraFine=strtotime($oraFine);
      $this->oraInizio=$oraInizio;
      $this->oraFine=$oraFine;
    }

    private function formatData($data){
      if(strlen($data)==10){
        return $data[6].$data[7].$data[8].$data[9]."-".$data[3].$data[4]."-".$data[0].$data[1];
      }else{
        $this->errorInputCnt("format data","Errore formatazzione data");
      }
    }

    public function setDate(){
      $dataInizio=$this->getVCont("DATAINIZIOCONTRATTO");
      $dataFine=$this->getVCont("DATAFINECONTRATTO");
      $dataInizio=$this->formatData($dataInizio);
      $dataFine=$this->formatData($dataFine);

      $this->dataInizioSTR=$dataInizio; //$this->formatData($dataInizio);
      $this->dataFineSTR=$dataFine; //$this->formatData($dataFine);

      $dataInizio=strtotime($dataInizio);
      $dataFine=strtotime($dataFine);
      $this->dataInizio=$dataInizio;
      $this->dataFine=$dataFine;
    }


    //scrivo il blocco sul db
    private function occupaSpazioSingolo($data,$newOra,$fineOra){
      $dataIns=$this->formOcDate(':data');

      //TODO: Da storicizzare il prepare solo al primo insert per rendere più veloce tutto
      $str="INSERT INTO XDM_PRENOTAZIONE
      (IDPRENOTAZIONE,IDCONTRATTO,ORAINIZIO,ORAFINE,TEMPO,DATA,STATO)VALUES
      (:id,:idContratto,:oraInizio,:oraFine,:tempo,$dataIns,0)  ";
      $this->queryPrepare($str);
      $this->queryBind("id",$this->getIncPrenotazione());
      $this->queryBind("idContratto",$this->getIdContratto());
      $this->queryBind("oraInizio",date('H:i',$newOra));
      $this->queryBind("oraFine",date('H:i',$fineOra));
      $this->queryBind("tempo",$this->getVCont('TEMPO'));
      $this->queryBind("data",date('Y-m-d',$data) );
      $this->executePrepare();
    }

    // inserisco la data
    private function procDataContratto($data){
      $giorno = date('w', $data);
      if(!$this->ifDayWork($giorno)){
        return false; //giorno da saltare
      }

      //procedo con il calcolo dei blocchi temporali
      $oraInizio=$this->oraInizio;
      $oraFine=$this->oraFine;
      $newOra=$oraInizio;
      $durata=$this->getVCont("TEMPO");
      $i=0;
      while($newOra<$oraFine){
        $i++;
        $fineOra=strtotime('+'.$durata.' minutes',$newOra);
        $this->occupaSpazioSingolo($data,$newOra,$fineOra);
        $newOra=strtotime('+'.$durata.' minutes',$newOra);
      }

    }


    public function occupaSpazioPrenotazione(){
      $dataInizio=$this->dataInizio;
      $dataFine=$this->dataFine;
      $newData=strtotime($dataInizio);
      $i=0;

      while($newData<=$dataFine){ //ciclo i giorni da data inizio e data fine
        $i++;
        $this->procDataContratto($newData);
        $newData=strtotime('+ '.$i.' days',($dataInizio));
      }
      $this->setJsonMess("ok",true);
      $this->logCont("Fine esecuzione occupa");
    }


    public function getTable(){

    }

    public function getContrattiInseriti(){
      $que = "SELECT AM.IDAMBULATORIO, AM.NOME, AM.PROVINCIA, AM.COMUNE, AM.INDIRIZZO,
      TO_CHAR(AMC.DATAINIZIOCONTRATTO, 'DD/MM/YYYY') DATAINIZIOCONTRATTO, TO_CHAR(AMC.DATAFINECONTRATTO, 'DD/MM/YYYY') DATAFINECONTRATTO,
      ORAINIZIO, ORAFINE
      FROM XDM_AMBULATORIO AM
      INNER JOIN XDM_AMBULATORIO_CONTRATTO AMC ON AM.IDAMBULATORIO = AMC.IDAMBULATORIO";
      $this->queryPrepare($que);
      $this->executePrepare();

      ?>
      <table class="table">
        <thead>
          <tr>
            <td>NOME AMBULATORIO</td>
            <td>PROVINCIA</td>
            <td>COMUNE</td>
            <td>INDIRIZZO</td>
            <td>DATAINIZIOCONTRATTO</td>
            <td>DATAFINECONTRATTO</td>
            <td>ORAINIZIO</td>
            <td>ORAFINE</td>
          </tr>
        </thead>
        <tbody>
          <?php while( $row=$this->fetch() ){ ?>
            <tr>
              <td><?php echo $row["NOME"]; ?></td>
              <td><?php echo $row["PROVINCIA"]; ?></td>
              <td><?php echo $row["COMUNE"]; ?></td>
              <td><?php echo $row["INDIRIZZO"]; ?></td>
              <td><?php echo $row["DATAINIZIOCONTRATTO"]; ?></td>
              <td><?php echo $row["DATAFINECONTRATTO"]; ?></td>
              <td><?php echo $row["ORAINIZIO"]; ?></td>
              <td><?php echo $row["ORAFINE"]; ?></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
      <?php
      die();
    }

  }//end classe



  ?>

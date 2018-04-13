<?php

trait contratto{

  private $idContrattoWork; //riferimento id su quale associare i blocchi temporali
  private $giorni; //array configurazione giorni
  private $dataWork;

  private $varWork; //data caricata dal POST nel bootstrap

  //varibili di servizio per il processo di inserimento
  private $dataInizio;
  private $dataFine;
  private $oraInizio;
  private $oraFine;


  private $idPrenotazione; //indice impostato manuale dal ciclo

  private function setIdContratto($id){
    $this->idContrattoWork=$id;
  }

  private function logCont($mess){
    $this->setJsonMess("logContratto",$mess);
  }

  private function getIdContratto(){
    return $this->idContrattoWork;
  }



  private function checkConflict(){
    return true;
    //TODO: da sistemare, controllo sul db se ci sono casi di sovraposizione

  }


  public function insContratto(){
    //TODO: Controllo se i dati inseriti non sono in conflitto con altre prenotazioni
    $this->checkConflict();


    $this->varWork=$this->post('data');
    $data=$this->post("data");
    if($data['TEMPO']<10){
      $this->error("Durata inferiore del previsto, controllare");
    }
    $this->logCont("inserimento contratto");

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
    $this->queryBind('dataIniz',$this->getVCont('DATAINIZIOCONTRATTO'));
    $this->queryBind('dataFine',$this->getVCont('DATAFINECONTRATTO'));
    $this->queryBind('oraIniz',$this->getVCont('ORAINIZIO'));
    $this->queryBind('oraFine',$this->getVCont('ORAFINE'));
    $this->executePrepare();
    $this->logCont("Salvataggio contratto");


    $this->logCont("Iniz variabile e start inserimento spazio");
    $this->setIdContratto($idContrattoNew);
    $this->occupaSpazioPrenotazione();
    $this->commit();
    $this->halt();
  }

  private function getVCont($var){
    if(empty($this->varWork[$var])){
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
    return 0; //TODO: da creare la tabella
    $giorni=$this->giorni;
    $i=1;
    $idContratto=$this->getIdContratto();
    foreach ($giorni as $giorno => $attivo) {
      if($attivo=='1'){
        $this->queryPrepare("INSERT INTO XDM_AMBULATORIO_CONTRATTO_GIORNO
          (IDCONTRATTO,GIORNO,NGIORNO)
          VALUES(:idcont,:giorno,:nGiorno) ");
          $this->queryBind('idcont',$idContratto);
          $this->queryBind('giorno',$giorno);
          $this->queryBind('nGiorno',$i);
          $this->executePrepare();
        }
        $i++;
      }
    }

    //setup giorni della settimana
    private function inizVar(){
      $giorni=$this->post('giorni');
      $giorni['Sunday']=0;
      $this->giorni=$giorni;
      $this->insGiorniDb();
      $this->setTime();
      $this->setDate();
      $this->setIdPrenotazione();
    }

    private function setIdPrenotazione(){
      $id=$this->getNexId();
      $this->idPrenotazione=$id;
    }


    private function getIncPrenotazione(){
      $this->idPrenotazione++;
      return $this->idPrenotazione;
    }

    public function setTime(){
      $oraInizio=$this->getVCont("ORANIZIO");
      $oraFine=$this->getVCont("ORAFINE");
      $oraInizio=strtotime($oraInizio);
      $oraFine=strtotime($oraFine);
      $this->oraInizio=$oraInizio;
      $this->oraFine=$oraFine;
    }

    public function setDate(){
      $dataInizio=$this->getValC("dataInizio");
      $dataFine=$this->getValC("dataFine");
      $dataInzio=strtotime($dataInizio);
      $dataFine=strtotime($dataFine);
      $this->dataInizio=$dataInizio;
      $this->dataFine=$dataFine;
    }


    //scrivo il blocco sul db
    private function occupaSpazioSingolo($data,$newOra){
      $durata=$this->getValC("TEMPO");
      $dataIns=$this->formOcDate(':data');

      $data['ORAINIZIO']=$newOra;
      $data['ORAFINE']=$newOra;
      $data['TEMPO']=$durata;
      $data['IDPRENOTAZIONE']=$this->getIncPrenotazione();
      //$data['IDAMBULATORIO']=$this->getValC("idAmbulatorio");
      $data['IDCONTRATTO']=$this->getIdContratto();

      $str="INSERT INTO XDM_PRENOTAZIONE
      (IDPRENOTAZIONE,IDCONTRATTO,ORAINIZIO,ORAFINE,TEMPO,DATA)VALUES
      (:id,:idContratto,:oraInizio,:oraFine,:tempo,$dataIns)  ";

      $this->queryPrepare($str);
      $this->queryBind("id",$this->getIdNext("IDPRENOTAZIONE","XDM_PRENOTAZIONE"));
      $this->queryBind("idContratto",$data['IDCONTRATTO']);
      $this->queryBind("oraInizio",$data['ORAINIZIO']);
      $this->queryBind("oraFine",$data['ORAFINE']);
      $this->queryBind("tempo",$data['TEMPO']);
      $this->prepareExecute();


    }

    // inserisco la data
    private function procDataContratto($data){
      $giorno = date('D', strtotime($data));
      if(!$this->ifDayWork($giorno)){
        return false; //giorno da saltare
      }
      //procedo con il calcolo dei blocchi temporali
      $oraInizio=$this->oraInizio;
      $oraFine=$this->oraFine;
      $newOra=$oraInizio;
      $durata=$this->getValC("TEMPO");
      while($newOra<=$oraFine){
        $this->occupaSpazioSingolo($data,$newOra,$durata);
        $newData=strtotime('+'.$durata.' minutes',$newOra);
      }
    }


    public function occupaSpazioPrenotazione(){
      $this->inizVar();
      $dataInizio=$this->dataInizio;
      $dataFine=$this->dataFine;
      $newData=strtotime($dataInizio);
      $i=0;
      while($newData<=$dataFine){ //ciclo i giorni da data inizio e data fine
        $i++;
        $this->procDataContratto($newData);
        $newData=strtotime('+ '.$i.' days',strtotime($dataInizio));
      }
      $this->logCont("Fine esecuzione occupa");
    }


  }//end classe



  ?>

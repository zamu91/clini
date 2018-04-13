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
    if($this->isDebug()==false){return 0;}
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
    $this->setIdContratto($idContrattoNew);
    $this->logCont("Iniz variabile e start inserimento spazio");
    $this->occupaSpazioPrenotazione();
    $this->commit();
    $this->halt();
  }

  private function getVCont($var){
    if(empty($this->varWork[$var])){
      $this->logCont("Variabile $var non trovata");
      print_r($this->varWork);
      return false;
    }
    return $this->varWork[$var];
  }


  //controllo se il giorni è da contare nel contratto o no
  private function ifDayWork($day){
    $giorni=$this->giorni;
    echo " cerco il giorno --> $day ";
    print_r($giorni);
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
    private function inizVar(){
      $giorni=$this->post('giorni');
      $giorni[0]=0;
      $this->giorni=$giorni;
      $this->insGiorniDb();
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

    public function setDate(){
      $dataInizio=$this->getVCont("DATAINIZIOCONTRATTO");
      $dataFine=$this->getVCont("DATAFINECONTRATTO");
      $dataInzio=strtotime($dataInizio);
      $dataFine=strtotime($dataFine);
      $this->dataInizio=$dataInizio;
      $this->dataFine=$dataFine;
    }


    //scrivo il blocco sul db
    private function occupaSpazioSingolo($data,$newOra){
      $dataIns=$this->formOcDate(':data');
      $str="INSERT INTO XDM_PRENOTAZIONE
      (IDPRENOTAZIONE,IDCONTRATTO,ORAINIZIO,ORAFINE,TEMPO,DATA)VALUES
      (:id,:idContratto,:oraInizio,:oraFine,:tempo,$dataIns)  ";
      echo $str;
      $this->queryPrepare($str);
      $this->queryBind("id",$this->getIncPrenotazione());
      $this->queryBind("idContratto",$this->getIdContratto());
      $this->queryBind("oraInizio",$newOra);
      $this->queryBind("oraFine",$newOra);
      $this->queryBind("tempo",$this->getVCont('TEMPO'));
      $this->prepareExecute();


    }

    // inserisco la data
    private function procDataContratto($data){
      $giorno = date('w', strtotime($data));
      if(!$this->ifDayWork($giorno)){
        echo "salto il gionro";
        return false; //giorno da saltare
      }

      echo "start inizio";

      //procedo con il calcolo dei blocchi temporali
      $oraInizio=$this->oraInizio;
      $oraFine=$this->oraFine;
      $newOra=$oraInizio;
      $durata=$this->getVCont("TEMPO");
      $i=0;
      while($newOra<=$oraFine){
        echo "volta --->  $i - $newOra ";
        $i++;
        $this->occupaSpazioSingolo($data,$newOra,$durata);
        $newOra=strtotime('+'.$durata.' minutes',$newOra);
      }
      echo "fatto";
      die;

    }


    public function occupaSpazioPrenotazione(){
      $this->inizVar();
      $dataInizio=$this->dataInizio;
      $dataFine=$this->dataFine;
      $newData=strtotime($dataInizio);
      $i=0;

      while($newData<=$dataFine){ //ciclo i giorni da data inizio e data fine
        $i++;
        echo "processo data $i";
        $this->procDataContratto($newData);
        $newData=strtotime('+ '.$i.' days',strtotime($dataInizio));
      }
      $this->logCont("Fine esecuzione occupa");
    }


  }//end classe



  ?>

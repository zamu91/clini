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

    $data=$this->post("data");
    if($data['TEMPO']<10){
      $this->error("Durata inferiore del previsto, controllare");
    }
    $this->logCont("inserimento contratto");
    $idContrattoNew=$this->getIdNext("IDCONTRATTO","XDM_AMBULATORIO_CONTRATTO");
    $data['IDCONTRATTO']=$idContrattoNew;
    $data['DATAINIZIOCONTRATTO']=$this->formOcDate($data['DATAINIZIOCONTRATTO']);
    $data['DATAFINECONTRATTO']=$this->formOcDate($data['DATAFINECONTRATTO']);

    print_r($data);
    $this->insertPrepare('XDM_AMBULATORIO_CONTRATTO',$data);
    $this->logCont("Iniz variabile e start inserimento spazio");
    $this->setIdContratto($idContrattoNew);
    $this->occupaSpazioPrenotazione();
    $this->commit();
    $this->halt();
  }

  private function getVCont($var){
    if(empty($var)){
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
        $this->queryPrepare("INSERT INTO XDM_AMBULATORIO_CONTRATTO_GIORNO
          (IDCONTRATTO,GIORNO,NGIORNO)
          VALUES(:idcont,:giorno,:nGiorno) ");
          $this->queryBind('idcont',$idContratto);
          $this->queryBind('giorno',$giorno);
          $this->queryBind('nGiorno',$i);
          $this->execute();
        }
        $i++;
      }

    }

    //setup giorni della settimana
    private function inizVar(){
      $this->varWork=$this->post('data');
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
      $data['DATA']=$data;
      $data['ORAINIZIO']=$newOra;
      $data['TEMPO']=$durata;
      $data['IDPRENOTAZIONE']=$this->getIncPrenotazione();
      //$data['IDAMBULATORIO']=$this->getValC("idAmbulatorio");
      $data['IDCONTRATTO']=$this->getIdContratto();
      $this->insertPrepare("AMBULARIO_CONTRATTO_PRENOTAZIONE",$data);
      $this->execute();
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

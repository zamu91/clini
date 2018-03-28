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

  private function getLastIdContratto(){
    $str=" SELECT TOP 1 idContratto as id from ambulatorio_contratto  order by idContratto desc ";
    $this->query($str);
    $row=$this->fetch();
    $this->setIdContratto($row['id']);
  }

  private function checkConflict(){
    return true;
    //TODO: da sistemare, controllo sul db se ci sono casi di sovraposizione

  }

  public function insContratto(){
    //TODO: Controllo se i dati inseriti non sono in conflitto con altre prenotazioni
    $this->checkConflict();

    $data=$this->post("data");

    $data['idPatrocinatore']=$this->getIdArxivar();
    if($data['durata']<10){
      $this->error("Durata inferiore del previsto, controllare");
    }
    $this->logCont("inserimento contratto");
    $this->insPrepare('ambulatorio_contratto',$data);
    $this->getLastIdContratto();
    $this->logCont("Iniz variabile e start inserimento spazio");
    $this->varWork=$data;
    $this->occupaSpazioPrenotazione();
    $this->commit();

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

  //setup giorni della settimana
  private function inizVar(){
    $giorni=$this->post('giorni');
    $giorni['Sunday']=0;
    $this->giorni=$giorni;
    $this->setTime();
    $this->setDate();
    $this->setIdPrenotazione();
  }

  private function setIdPrenotazione(){
    $this->query(" SELECT TOP 1 idPrenotazione as id from ambulatorio_contratto_prenotazione order by idPrenotazione desc");
    $row=$this->fetch();
    $id=$row['id'];
    if(empty($id)){
      $id=0;
    }
    $this->idPrenotazione=$id;
  }


  private function getIncPrenotazione(){
    $this->idPrenotazione++;
    return $this->idPrenotazione;
  }

  public function setTime(){
    $oraInizio=$this->getVCont("oraInizio");
    $oraFine=$this->getVCont("oraFine");
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
    $durata=$this->getValC("durata");
    $data['data']=$data;
    $data['inizio']=$newOra;
    $data['durata']=$durata;
    $data['idPrenotazione']=$this->getIncPrenotazione();
    $data['idAmbulatorio']=$this->getValC("idAmbulatorio");
    $data['idContratto']=$this->getIdContratto();
    $this->insertPrepare("AMBULARIO_CONTRATTO_PRENOTAZIONE",$data);
  }


  private function procDataContratto($data){
    $giorno = date('D', strtotime($data));
    if(!$this->ifDayWork($giorno)){
      return false; //giorno da saltare
    }
    //procedo con il calcolo dei blocchi temporali
    $oraInizio=$this->oraInizio;
    $oraFine=$this->oraFine;
    $newOra=$oraInizio;
    $durata=$this->getValC("durata");
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

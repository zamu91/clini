<?php
//
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
$dir=__DIR__.DIRECTORY_SEPARATOR;
include_once $dir."trait.arxivar.php";
include_once $dir."trait.login.php";
include_once $dir."trait.contratto.php";
include_once $dir."trait.clinica.php";
include_once $dir."trait.query.php";
include_once $dir."trait.prenotazione.php";
include_once $dir."trait.navigazione.php";



class chiamate{
  use login,contratto,clinica,prenotazione,arxivar,sql, navigazione;
  private $conn; //connessione oracle
  private $jsonMess;
  private $debug=false;
  private $arxEnvPath;
  private $envPath;

  public function __construct(){
    $path=$_SERVER["DOCUMENT_ROOT"].$this->dSep()."Arxivar".$this->dSep();
    $this->envPath = $_SERVER["DOCUMENT_ROOT"].$this->dSep()."clini".$this->dSep()."portale".$this->dSep();
    $this->arxEnvPath = $path;
    require($path."ARX_Login.php");
    require($path."ARX_Dati.php");
    require($path."ARX_Search.php");
    require($path."ARX_Documenti.php");
    require($path."ARX_Workflow.php");
  }

  public function debug(){
    $this->debug=true;
  }

  public function dSep($forceSystem = false){
    if(!$forceSystem){
      return DIRECTORY_SEPARATOR;
    }else{
      switch ($forceSystem) {
        case "W":
        case "win":
          return "\\";
          break;

        case "L":
        case "linux":
          return "/";
          break;

        default:
          return "/";
          break;
      }
    }
  }

  private function debugHtml($mess){
    if(!$this->isDebug()){
      return false;
    }
    echo "<p>$mess</p>";
  }

  public function getEnvPath(){
    return $this->envPath;
  }

  public function halt(){
    if( !empty($this->jsonMess) ){
      echo json_encode($this->jsonMess);
    }
    die;
  }

  public function isDebug(){
    return $this->debug;
  }

  public function setJsonMess($var,$mess,$dupli=1){
    if(($dupli=='1')&&(!empty($this->jsonMess[$var]) ) ) {
      $this->jsonMess[$var].=" ".$mess;
    }else{
      $this->jsonMess[$var]=$mess;
    }
  }

  public function post($nome, $verbose = true){
    if(empty($_POST[$nome])){
      if($this->isDebug()){
        if($verbose){$this->setJsonMess('warn',"$nome non trovato");}
      }
      return false;
    }else{
      return $_POST[$nome];
    }
  }



  public function launcher(){
    $azione=$this->post('azione');
    switch($azione){
      case 'loginPatrocinatore':
      $this->loginArxivar();
      break;

      case "loginImpersonatePatrocinatore":
      $this->loginArxivarImpersonate();
      break;

      case 'loginSessionActive':
      $this->controlloARXLogin();
      break;

      case "controlloTokenARXLogin":
      $this->controlloTokenARXLogin();
      break;

      case 'salvaClinica':
      $this->salvaClinica();
      break;

      case 'getOptionClinica':
      $this->optClinica();
      break;

      case 'getOptDataRicerca':
      $this->getOptDataRicerca();
      break;


      case 'optClinicaProvincia':
      $this->optClinicaProvincia();
      break;


      case 'insContratto':
      $this->insContratto();
      break;

      case 'naviga':
      $this->naviga();
      break;

      case 'listaProfili':
      $this->listaProfili();
      break;

      case 'dettaglioProfilo':
      $this->dettaglioProfilo();
      break;

      case "getTaskworkFromDocnumber":
      $this->getTaskworkFromDocnumber();
      break;

      case "scriviDatiProfilo":
      $this->scriviDatiProfilo();
      break;

      case 'getClinicaPerData':
      $this->getClinicaPerData();
      break;

      case 'getDataPerClinica':
      $this->getDataPerClinica();
      break;

      case "scriviDocumentiProfilo":
      $this->scriviDocumentiProfilo();
      break;

      case "listaDocumenti":
      $this->listaDocumenti();
      break;

      case "getContrattiInseriti":
      $this->getContrattiInseriti();
      break;

      case 'getAmbulatoriInseriti':
      $this->getAmbulatoriInseriti();
      break;

      case 'primaDataLibera':
      $this->primaDataLibera();
      break;

      case 'liberaPrenotazione':
      $this->liberaPrenotazione();
      break;

    } //end swtich
    $this->halt();

  }

}

$chiamate=new chiamate();
if(empty($noChiamte)){
  $chiamate->launcher();
}

?>

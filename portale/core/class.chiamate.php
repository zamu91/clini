<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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
    $this->debug();
    $path=$_SERVER["DOCUMENT_ROOT"].$this->dSep()."Arxivar".$this->dSep();
    $this->envPath = $_SERVER["DOCUMENT_ROOT"].$this->dSep()."clini".$this->dSep()."portale".$this->dSep();
    $this->arxEnvPath = $path;
    require($path."ARX_Login.php");
    require($path."ARX_Dati.php");
    require($path."ARX_Search.php");
    require($path."ARX_Documenti.php");
  }


  public function debug(){
    $this->debug=true;
  }

  public function dSep(){
    return DIRECTORY_SEPARATOR;
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
    echo json_encode($this->jsonMess);
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
    // $this->jsonMess["ciao"] = "balalalalla";
  }

  public function post($nome, $verbose = true){
    if(empty($_POST[$nome])){
      if($verbose){$this->setJsonMess('warn',"$nome non trovato");}
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

      case 'loginSessionActive':
        $this->controlloARXLogin();
        break;

      case "controlloTokenARXLogin":
        $this->controlloTokenARXLogin();
        break;

      case 'salvaClinica':
      $this->salvaClinica();
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

      case "scriviDatiProfilo":
        $this->scriviDatiProfilo();
        break;
    } //end swtich
    $this->halt();

  }

}

$chiamate=new chiamate();
$chiamate->launcher();

?>

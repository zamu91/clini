<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "start inc lib ";
$dir=__DIR__.DIRECTORY_SEPARATOR;

echo "esempio ".$dir."trait.arxivar.php";
die;
include_once $dir."trait.arxivar.php";

include_once $dir."trait.login.php";
include_once $dir."trait.contratto.php";
include_once $dir."trait.clinica.php";
include_once $dir."trait.query.php";
include_once $dir."trait.prenotazione.php";


echo "fine inch ";

class chiamate{
  use login,contratto,clinica,prenotazione,arxiva,sql;
  private $conn; //connessione oracle
  private $jsonMess;
  private $debug;

  public function debug(){
    $this->debug=true;

  }

  public function dSep(){
    return DIRECTORY_SEPARATOR;
  }

  public function __construct(){
    $this->debug();
    $path=$_SERVER["DOCUMENT_ROOW"].$this->dSep()."Arxivar".$this->dSep();
    require($path."ARX_Login.php");
    require($path."ARX_Dati.php");
    require($path."ARX_Search.php");
    require($path."ARX_Documenti.php");
  }


  public function halt(){
    echo json_encode($this->jsonMess);
    die;
  }

  public function setJsonMess($var,$mess,$dupli=1){
    if($dupli=='1'){
      $this->jsonMess[$var].=$mess;
    }else{
      $this->jsonMess[$var]=$mess;
    }
  }


  public function post($nome){
    if(empty($_POST[$nome])){
      $this->setJsonMess('warn',"$nome non trovato");
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

    }

  }





}



$chiamate=new chiamate();
$chiamate->launcher();






?>

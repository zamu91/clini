<?php
$dir=__DIR__."/";
include_once $dir."trait.arxivar.php";
include_once $dir."trait.login.php";
include_once $dir."trait.contratto.php";
include_once $dir."trait.clinica.php";
include_once $dir."trait.query.php";
include_once $dir."trait.prenotazione.php";



class chiamate{
  use login,contratto,clinica,prenotazione,arxiva,sql;
    private $conn; //connessione oracle
    private $jsonMess;


    public function __construct(){
        require($_SERVER["DOCUMENT_ROOT"]."/Arxivar/ARX_Login.php");
        require($_SERVER["DOCUMENT_ROOT"]."/Arxivar/ARX_Dati.php");
        require($_SERVER["DOCUMENT_ROOT"]."/Arxivar/ARX_Search.php");
        require($_SERVER["DOCUMENT_ROOT"]."/Arxivar/ARX_Documenti.php");
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






 ?>

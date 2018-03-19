<?php


trait arxivar{

  public $baseUrl = "http://localhost:81/";
  private $loginResult;
  private $isLoginArxivar;

  private $logError;


  public function getLoginResult(){
    if(empty($this->loginResult)){
      return false;
    }else{
      return $this->loginResult;
    }
  }

  public function isLoginArxivar(){
    return $this->isLoginArxivar;
  }

  public function getLoginError(){
    return $this->logError;
  }


  private function arxLog($mess){
    $this->setJsonMess("Arx_mess",$mess);
  }


  public function loginArxivar(){
    $baseUrl=$this->baseUrl;
    $ARX_Login = new ARX_Login\ARX_Login($baseUrl."ARX_Login.asmx?WSDL");
    $userName = $this->post("username");
    $password = $this->post("password");
    $softwareName = "PHP Gestione cliniche";
    $this->loginResult = $ARX_Login->Login($userName, $password, $softwareName);
    $this->arxLog(' WCF chiamate');
    if( $this->loginResult->LoggedIn ){
      $this->arxLog(' Login eseguito con successo');
      $this->sessionid = $loginResult->SessionId;
      $this->isLogin=true;
      $ARX_Login->LogOut($sessionid); //rilascio la sessione per nuovi login
      $this->registerSessionLogin();
      return true;
    }else{
      $this->arxLog(' Login fallito ');
      $this->arLog($this->loginResult->ArxLogOnErrorTypeString);
      $this->logError=$this->loginResult->ArxLogOnErrorTypeString;
      return false;
    }
  }

}



?>

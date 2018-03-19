<?php

trait login {

  private $loginToken;

  public $username;
  public $password;



  private function getUsername(){
    if(empty($this->username)){
      $this->username=$this->post('username');
    }
    return $this->username;
  }



  private function getPassword(){
    if(empty($this->password)){
      $this->password=$this->post('password');
    }
    return $this->password;
  }


  private function loginLog($mess){
    $this->setJsonMess("loginMess",$mess);
  }

  private function checkExistSession(){
    $userName = $this->post("username");
    $password = $this->post("password");
    $que = "SELECT USERNAME, PASSWORD, ARXSESSION, TO_CHAR(SCADENZA, 'YYYY-MM-DD HH24:MI:SS') AS SCADENZA
    FROM XDM_WEBSERVICE_SESSION
    WHERE USERNAME = '$userName' AND PASSWORD = '$password' ";
    $res = $this->query($que);
    $row=$this->fetch();
    return $row;
  }

  public function registerSessionLogin(){
    $this->loginLog("Check login oracle");
    $loginResult=$this->getLoginResult();
    $app = $loginResult->ExpiratedTime;
    $expirationTime = substr($app, 0, 10).' '.substr($app, 11, 8);
    $row=$this->checkExistSession();

    $session=$loginResult->SessionId;


    if( !empty($row["USERNAME"]) ){
      $this->loginLog("sessione trovata, aggiorno");
      $que = "UPDATE XDM_WEBSERVICE_SESSION SET ARXSESSION = '$session',
      SCADENZA = TO_DATE('$expirationTime', 'YYYY-MM-DD HH24:MI:SS') ";
      $res = $this->query($que);
      $this->setJsonMess('sessionMess','aggiornamento Sessione');
    } else {
      $username=$this->getUsername();
      $password=$this->getPassword();
      $this->loginLog("nuova sessione, registro");
      $que = "INSERT INTO XDM_WEBSERVICE_SESSION (USERNAME, PASSWORD, ARXSESSION, SCADENZA)
      VALUES ('$username', '$password', '$session', TO_DATE('$expirationTime', 'YYYY-MM-DD HH24:MI:SS')) ";
      $this->query($que);
      $this->setJsonMess('sessionMess','registrazione Sessione');

    }
    $this->setJsonMess("token",$session);
    $this->setJsonMess("login",true);
    $this->loginToken=true;
  }

  public function controlloARXLogin(){
    $token = $this->post("token");
    $que = "SELECT USERNAME, PASSWORD, ARXSESSION, TO_CHAR(SCADENZA, 'YYYY-MM-DD HH24:MI:SS') AS SCADENZA
    FROM XDM_WEBSERVICE_SESSION
    WHERE ARXSESSION = '$token' AND SYSDATE <= SCADENZA";
    $this->query($que);
    $row =$this->fetch();
    if(!empty($row['username'])){
      $this->loginToken=true;
      return true;

    }else{
      return false;
    }
  }

  public function isLogin(){
    return $this->isLoginToken;
  }

}//fine classe


?>

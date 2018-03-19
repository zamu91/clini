<?php

trait login {

  private $loginToken;

  private function checkExistSession(){
    $userName = $this->post("username");
    $password = $this->post("password");
    $que = "SELECT USERNAME, PASSWORD, ARXSESSION, TO_CHAR(SCADENZA, 'YYYY-MM-DD HH24:MI:SS') AS SCADENZA
    FROM XDM_WEBSERVICE_SESSION
    WHERE USERNAME = '$userName' AND PASSWORD = '$password' ";
    $res = $this->query($que);
    $row=$this->fetchQuery();
    return $row;
  }

  public function registerSessionLogin(){
    $loginResult=$this->getLoginResult();
    $app = $loginResult->ExpiratedTime;
    $expirationTime = substr($app, 0, 10).' '.substr($app, 11, 8);
    $row=$this->checkExistSession();
    if( !empty($row["USERNAME"]) ){
      $que = "UPDATE XDM_WEBSERVICE_SESSION SET ARXSESSION = '$loginResult->SessionId',
      SCADENZA = TO_DATE('$expirationTime', 'YYYY-MM-DD HH24:MI:SS') ";
      $this->setJsonMess('sessionMess','aggiornamento Sessione');
    } else {
      $que = "INSERT INTO XDM_WEBSERVICE_SESSION (USERNAME, PASSWORD, ARXSESSION, SCADENZA)
      VALUES ('$userName', '$password', '$loginResult->SessionId', TO_DATE('$expirationTime', 'YYYY-MM-DD HH24:MI:SS')) ";
      $this->setJsonMess('sessionMess','registrazione Sessione');
    }
    $res = $this->query($que);
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

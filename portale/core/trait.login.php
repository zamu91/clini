<?php

trait login {

  private $loginToken;

  public $username;
  public $password;

  private $idArxivar;

  private function getIdArxivar(){
    if(empty($this->idArxivar)){
      return false;
    }
    return $this->idArxivar;
  }

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

  private function setIdArxivar(){
    $username=$this->getUsername();
    $this->queryPrepare(" SELECT dm_utenti.UTENTE from dm_utenti where description= :us ");
    $this->queryBind("us",$username);
    $this->executeQuery();
    $row=$this->fetch();
    $this->idArxivar=$row['UTENTE'];
  }


  private function checkExistSession(){
    $userName = $this->getUsername();
    $password = $this->getPassword();
      $que = "SELECT  ARXSESSION,ses.USERNAME, TO_CHAR(SCADENZA, 'YYYY-MM-DD HH24:MI:SS') AS SCADENZA
    FROM XDM_WEBSERVICE_SESSION ses
    WHERE ses.USERNAME = :ut AND ses.PASSWORD = :credenz ";
    // $this->debugHtml($userName);
    // $this->debugHtml($password);
    $this->queryPrepare($que);
    $this->queryBind("ut", $userName);
    $this->queryBind("credenz", $password);
    $this->executeQuery();

    $row=$this->fetch();
    $this->setIdArxivar();
    return $row;
  }

  private function checkExistSessionFromToken(){
    $token = $this->post("token", false);
    $que = "SELECT ARXSESSION, ses.USERNAME, TO_CHAR(SCADENZA, 'YYYY-MM-DD HH24:MI:SS') AS SCADENZA, ses.AOO, ses.GRUPPO
    FROM XDM_WEBSERVICE_SESSION ses
    WHERE ses.ARXSESSION = :tok ";

    $this->queryPrepare($que);
    $this->queryBind("tok", $token);
    $this->executeQuery();

    $row = $this->fetch();
    $this->setIdArxivar();
    return $row;
  }

  public function registerSessionLogin($aoo, $gruppo){
    $this->loginLog("Check login oracle");
    $loginResult=$this->getLoginResult();
    $session=$loginResult->SessionId;
    $app = $loginResult->ExpiratedTime;
    $expirationTime = substr($app, 0, 10).' '.substr($app, 11, 8);
    $username=$this->getUsername();
    $password=$this->getPassword();
    $row=$this->checkExistSession();

    $this->arxDebug($aoo);
    $aoo = intval($aoo);
    $this->arxDebug($gruppo);

    if( !empty($row["USERNAME"]) ){
      $this->loginLog("sessione trovata, aggiorno");
      $que = "UPDATE XDM_WEBSERVICE_SESSION SET ARXSESSION = :sess,
      SCADENZA = TO_DATE(:expi, 'YYYY-MM-DD HH24:MI:SS'), AOO = :aoo, GRUPPO = :gru
      WHERE USERNAME = :us AND PASSWORD = : ps ";

      $this->queryPrepare($que);
      $this->queryBind("us", $username);
      $this->queryBind("pa", $password);
      $this->queryBind('sess',$session);
      $this->queryBind('expi',$expirationTime);
      $this->queryBind("aoo", $aoo);
      $this->queryBind("gru", $gruppo);
      $this->executePrepare();

      $this->commit();
      $this->setJsonMess('sessionMess','aggiornamento Sessione');
    } else {
      $this->loginLog("nuova sessione, registro");
      $que = "INSERT INTO XDM_WEBSERVICE_SESSION (USERNAME, PASSWORD, ARXSESSION, SCADENZA, AOO, GRUPPO)
      VALUES (:us, :pa, :sess, TO_DATE(:expi, 'YYYY-MM-DD HH24:MI:SS'), :aoo, :gru) ";

      $this->queryPrepare($que);
      $this->queryBind("us", $username);
      $this->queryBind("pa", $password);
      $this->queryBind("sess", $session);
      $this->queryBind("expi", $expirationTime);
      $this->queryBind("aoo", $aoo);
      $this->queryBind("gru", $gruppo);
      $this->executePrepare();

      $this->commit();
      $this->setJsonMess('sessionMess','registrazione Sessione');

    }
    $this->setJsonMess("token",$session);
    $this->setJsonMess("login", true);

    $this->loginToken=true;
  }

  public function controlloARXLogin(){
    $token = $this->post("token", false);
    $que = "SELECT USERNAME, PASSWORD, ARXSESSION, TO_CHAR(SCADENZA, 'YYYY-MM-DD HH24:MI:SS') AS SCADENZA
    FROM XDM_WEBSERVICE_SESSION
    WHERE ARXSESSION = '$token' AND SYSDATE <= SCADENZA";

    $this->queryPrepare("SELECT USERNAME, PASSWORD, ARXSESSION, TO_CHAR(SCADENZA, 'YYYY-MM-DD HH24:MI:SS') AS SCADENZA
    FROM XDM_WEBSERVICE_SESSION
    WHERE ARXSESSION = :tok AND SYSDATE <= SCADENZA");
    $this->queryBind("tok", $token);
    $this->executePrepare();
    //$this->query($que);
    $this->commit();


    $row =$this->fetch();
    if(!empty($row['username'])){
      $this->loginToken=true;
      return true;
    }else{
      return false;
    }
  }

  public function controlloTokenARXLogin(){
    $token = $this->post("token");
    $this->queryPrepare("SELECT USERNAME, PASSWORD, ARXSESSION, TO_CHAR(SCADENZA, 'YYYY-MM-DD HH24:MI:SS') AS SCADENZA
    FROM XDM_WEBSERVICE_SESSION
    WHERE ARXSESSION = :tok AND SYSDATE <= SCADENZA");
    $this->queryBind("tok", $token);
    $this->executePrepare();
    $this->commit();


    $row =$this->fetch();
    if(!empty($row['username'])){
      $this->loginToken=true;
      $this->setJsonMess("res", true);
      $this->setJsonMess("validToken", true);
    }else{
      $this->loginToken=false;
      $this->setJsonMess("res", true);
      $this->setJsonMess("validToken", false);
    }
  }

  public function isLogin(){
    return $this->isLoginToken;
  }

}//fine classe


?>

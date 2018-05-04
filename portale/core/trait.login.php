<?php

trait login {

  private $loginToken;

  public $username;
  public $password;
  public $partiva;

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

  private function getPartiva(){
    if(empty($this->partiva)){
      $this->partiva=$this->post('code');
    }
    return $this->partiva;
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


  private function checkExistSession( $imp = 0 ){
    if( $imp == 1 ){
      $partiva = $this->getPartiva();
      $que = "SELECT ARXSESSION, ses.USERNAME, TO_CHAR(SCADENZA, 'YYYY-MM-DD HH24:MI:SS') AS SCADENZA
      FROM XDM_WEBSERVICE_SESSION ses WHERE ses.USERNAME = :partiva ";
      $this->queryPrepare($que);
      $this->queryBind("partiva", $partiva);
    } else {
      $userName = $this->getUsername();
      $password = $this->getPassword();
      $que = "SELECT ARXSESSION,ses.USERNAME, TO_CHAR(SCADENZA, 'YYYY-MM-DD HH24:MI:SS') AS SCADENZA
      FROM XDM_WEBSERVICE_SESSION ses
      WHERE ses.USERNAME = :ut AND ses.PASSWORD = :credenz ";
      // $this->debugHtml($userName);
      // $this->debugHtml($password);
      $this->queryPrepare($que);
      $this->queryBind("ut", $userName);
      $this->queryBind("credenz", $password);
    }
    $this->executeQuery();

    $row=$this->fetch();
    $this->setIdArxivar();
    return $row;
  }

  private function checkExistSessionFromToken(){
    $token = $this->post("token", false);
    $que = "SELECT ARXSESSION, ses.USERNAME, ses.PASSWORD, TO_CHAR(SCADENZA, 'YYYY-MM-DD HH24:MI:SS') AS SCADENZA, ses.AOO, ses.INSDOC, ses.IMPERSONATE, ses.PARTIVA
    FROM XDM_WEBSERVICE_SESSION ses
    WHERE ses.ARXSESSION = :tok ";

    $this->queryPrepare($que);
    $this->queryBind("tok", $token);
    $this->executeQuery();

    $row = $this->fetch();
    $this->setIdArxivar();
    return $row;
  }

  public function registerSessionLogin($aoo, $insDoc, $imp = 0){
    $this->loginLog("Check login oracle");
    $loginResult = $this->getLoginResult();
    $session = $loginResult->SessionId;
    $app = $loginResult->ExpiratedTime;
    $expirationTime = substr($app, 0, 10).' '.substr($app, 11, 8);
    $aoo = intval($aoo);
    $row = $this->checkExistSession($imp);
    if($imp == 1){$basepath = dirname($_SERVER['DOCUMENT_ROOT']);
      /* Login tramite impersonate */
      $username=$this->getUsername();
      $partiva = $this->getPartiva();
      if( !empty($row["USERNAME"]) ){
        $this->loginLog("sessione trovata, aggiorno");
        $que = "UPDATE XDM_WEBSERVICE_SESSION SET ARXSESSION = :sess, SCADENZA = TO_DATE(:expi, 'YYYY-MM-DD HH24:MI:SS'), AOO = :aoo,
        IMPERSONATE = :imp, INSDOC = :insdoc
        WHERE PARTIVA = :partiva ";
      } else {
        $this->loginLog("nuova sessione, registro");
        $que = "INSERT INTO XDM_WEBSERVICE_SESSION (IDSESSIONE, USERNAME, PASSWORD, ARXSESSION, SCADENZA, AOO, INSDOC, IMPERSONATE, PARTIVA)
        VALUES ( XDM_WEBSERVICE_SESSION_SEQ1.NEXTVAL , :us, '', :sess, TO_DATE(:expi, 'YYYY-MM-DD HH24:MI:SS'), :aoo, :insdoc, :imp, :partiva) ";
      }
      $this->queryPrepare($que);
      $this->queryBind("us", $username);
      $this->queryBind('sess',$session);
      $this->queryBind('expi',$expirationTime);
      $this->queryBind("aoo", $aoo);
      $this->queryBind("insdoc", $insDoc);
      $this->queryBind('imp',$imp);
      $this->queryBind('partiva',$partiva);
      $this->executePrepare();

      $this->commit();

    } else {
      /* Login canonico */
      $username=$this->getUsername();
      $password=$this->getPassword();

      if( !empty($row["USERNAME"]) ){
        $this->loginLog("sessione trovata, aggiorno");
        $que = "UPDATE XDM_WEBSERVICE_SESSION SET ARXSESSION = :sess,
        SCADENZA = TO_DATE(:expi, 'YYYY-MM-DD HH24:MI:SS'), AOO = :aoo, INSDOC = :insdoc, IMPERSONATE = :imp
        WHERE USERNAME = :us AND PASSWORD = :pa ";
      } else {
        $this->loginLog("nuova sessione, registro");
        $que = "INSERT INTO XDM_WEBSERVICE_SESSION (IDSESSIONE, USERNAME, PASSWORD, ARXSESSION, SCADENZA, AOO, INSDOC, IMPERSONATE, PARTIVA)
        VALUES ( XDM_WEBSERVICE_SESSION_SEQ1.NEXTVAL ,:us, :pa, :sess, TO_DATE(:expi, 'YYYY-MM-DD HH24:MI:SS'), :aoo, :insdoc, :imp, '') ";
      }
      $this->queryPrepare($que);
      $this->queryBind("us", $username);
      $this->queryBind("pa", $password);
      $this->queryBind('sess',$session);
      $this->queryBind('expi',$expirationTime);
      $this->queryBind("aoo", $aoo);
      $this->queryBind("insdoc", $insDoc);
      $this->queryBind('imp',$imp);
      $this->executePrepare();

      $this->commit();
    }


    $this->setJsonMess("token",$session);
    $this->setJsonMess("login", true);

    $this->loginToken=true;
    $this->halt();
  }

  public function controlloARXLogin(){
    $token = $this->post("token", false);
    $this->queryPrepare("SELECT USERNAME, PASSWORD, ARXSESSION, TO_CHAR(SCADENZA, 'YYYY-MM-DD HH24:MI:SS') AS SCADENZA, IMPERSONATE, PARTIVA
    FROM XDM_WEBSERVICE_SESSION
    WHERE ARXSESSION = :tok AND SYSDATE <= SCADENZA");
    $this->queryBind("tok", $token);
    $this->executePrepare();
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
    $this->queryPrepare("SELECT USERNAME, PASSWORD, ARXSESSION, TO_CHAR(SCADENZA, 'YYYY-MM-DD HH24:MI:SS') AS SCADENZA, IMPERSONATE, PARTIVA
    FROM XDM_WEBSERVICE_SESSION
    WHERE ARXSESSION = :tok AND SYSDATE <= SCADENZA");
    $this->queryBind("tok", $token);
    $this->executePrepare();
    $this->commit();

    $row =$this->fetch();
    if(!empty($row['ARXSESSION'])){
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

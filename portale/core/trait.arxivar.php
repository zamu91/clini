<?php

trait arxivar{

  public $baseUrl = "http://localhost:81/";
  public $adminUser = "Admin";
  public $adminPass = "123";
  public $maskid = "ec6009ba90064374a09956b7b7a61328";
  public $softwareName = "PHP Gestione cliniche";
  private $loginResult;
  private $isLoginArxivar;

  private $logError;

  public function getFieldListMascheraRicherca(){
    ob_start();
    $this->loginArxivarServizio();
    $ARX_Search = new ARX_Search\ARX_Search($this->baseUrl."ARX_Search.asmx?WSDL");
    $sessionid = $this->loginResult->SessionId;

    $search = $ARX_Search->Dm_Profile_Search_Get_New_Instance_By_TipiDocumentoCodice($sessionid, "GEST.POS");
    // $this->arxDebug($search);

    // TODO: Differenziazione delle tipologie di campo
    foreach ($search->Aggiuntivi->Field_Abstract as $agg) { ?>
      <div class="fieldBox">
        <label><?php echo $agg->Label; ?></label><br>
        <input type="text" data-name="<?php echo $agg->Nome ?>" />
      </div>
    <?php }

    $this->logoutArxivar();
    return ob_get_clean();
  }

  public function getFieldFromMaschera(){
    ob_start();
    $this->loginArxivarServizio();
    $ARX_Dati  = new ARX_Dati\ARX_Dati($this->baseUrl."ARX_Dati.asmx?WSDL");
    $sessionid = $this->loginResult->SessionId;
    try
    {
      // funzione per recuperare tutte le maschere
      //echo "dati di maschera<br />";
      $masks = $ARX_Dati->Dm_MaskGetData($sessionid);
      // $this->arxDebug($masks);

      // funzione per utilizzare una maschera con uno specifico ID
      //echo "utilizzo la maschera specifica<br />";
      $profileMv = $ARX_Dati->Dm_Profile_Insert_MV_GetNewInstance_From_DmMaskId($sessionid, $this->maskid);
      // echo "<pre>";
      // var_dump($profileMv);
      // echo "</pre><hr/>";
      $mask_data = $ARX_Dati->Dm_MaskDetail_GetData_By_DmMaskId($sessionid, $this->maskid);
      // echo "<pre>";
      // var_dump($mask_data);
      // echo "</pre><hr/>";


      // DM profile default
      //var_dump($profileMv->DmProfileDefault);

      // DmMaskDetails contiene l'elenco dei campi previsti nella maschera
      //echo "elenco campi della maschera specifica<br />";
      $details = $profileMv->DmMaskDetails->Dm_MaskDetail;

      //echo "dati DmProfileDEfault<br />";
      $profile = $profileMv->DmProfileDefault->Dm_Profile_Insert_Base;
      // @var $profile \ARX_Dati\Dm_Profile_Insert_Base //
      //echo "<pre>";
      //var_dump($details);
      //echo "</pre>";

      // ******* DOCUMENT TYPE ********
      $docType=$profile->DocumentType;
      // ******* Tipo2 ********
      $Tipo2=$profile->Tipo2;
      // ******* Tipo3 ********
      $Tipo3=$profile->Tipo3;
      // ******* Aoo ********
      $Aoo=$profile->Aoo;

      // array con i tipi di documento
      $id_td=array($docType,$Tipo2,$Tipo3);

      $lista_campi_specifici= $ARX_Dati->Dm_CampiSpecifici_GetData($sessionid,$Aoo,$id_td,false,"");

      //echo "<pre>";
      //var_dump($lista_campi_specifici);
      //var_dump($profile);
      //echo "</pre>";

      ?>
      <form id="formMaschera" action="#" onsubmit="function(){ return false; }" method="post">
        <fieldset>
          <legend>Maschera</legend>
          <?php
          foreach ($details as $field) {

            switch ($field->FIELD_KIND) {
              case ARX_Dati\Dm_MaskDetail_FieldKind::From:
              // mittente
              echo "mittente<br />";
              break;
              case ARX_Dati\Dm_MaskDetail_FieldKind::To:
              // destinatario
              echo "destinatario<br />";
              break;
              case ARX_Dati\Dm_MaskDetail_FieldKind::Oggetto:
              // Oggetto
              ?>
              <label for="oggetto">Oggetto</label>
              <input type="text" name="oggetto" id="oggetto" value=""><br>
              <?php
              break;
              case ARX_Dati\Dm_MaskDetail_FieldKind::Aggiuntivo:

              // Aggiuntivo
              // campo aggiuntivo, identifiato da $field->FIELD_ID
              $aggiuntivo = $ARX_Dati->Dm_CampiSpecifici_GetValues($sessionid,$field->FIELD_ID,$profile,null);//, combovalue, null);
              //$dati_aggiuntivi=$ARX_Dati->Dm_CampiSpecifici_GetData($sessionid,$field->FIELD_ID,$profile,null);//, combovalue, null);

              // mi prendo la label associata al campo
              $label='';
              $alias='';
              $required=false;

              // mi prendo gli attributi associati ai campi
              foreach($lista_campi_specifici->Dm_CampiSpecifici as $campo_spec){
                if($campo_spec->NOMECAMPO==$aggiuntivo->NomeCampo){
                  if($aggiuntivo->Classe=='Textbox'){
                    $n_char=$campo_spec->CARATTERI;
                    $row_count=$campo_spec->SHOWROWCOUNT;
                  }
                  $label=$campo_spec->ETICHETTA;
                  $alias=$campo_spec->SDKALIASNAME;
                  $required=$campo_spec->OBBLIGATORIO;
                }
              }

              ?>
              <label for="<?php echo $aggiuntivo->NomeCampo; ?>"><?php echo $label; ?><?php if($required) echo "*";?></label>
              <?php
              // controllo la tipologia di campo aggiuntivo
              $tipo=$aggiuntivo->Classe;
              // ****** TEXTBOX *****
              if($tipo=='Textbox'){

                // controllare alias per vedere se è un campo time
                if(strpos($alias,'(HH:MM)')>0){
                  // campo time
                  ?>
                  <input type="text" name="<?php echo $aggiuntivo->NomeCampo; ?>" class="time" id="<?php echo $aggiuntivo->NomeCampo; ?>" value="" maxlength="5" <?php if($required) echo "required";?>><br />
                  <?php
                }
                elseif($row_count>1){
                  // textarea
                  ?>
                  <textarea name="<?php echo $aggiuntivo->NomeCampo; ?>" id="<?php echo $aggiuntivo->NomeCampo; ?>" maxlength="<?php echo $n_char; ?>" <?php if($required) echo "required";?>></textarea>
                  <br /><?php
                }
                else{
                  ?>
                  <input type="text" name="<?php echo $aggiuntivo->NomeCampo; ?>" id="<?php echo $aggiuntivo->NomeCampo; ?>" maxlength="<?php echo $n_char; ?>" value="" <?php if($required) echo "required";?>><br />
                  <?php
                }
              }
              // ****** COMBOBOX *****
              elseif($tipo=='Combobox'){
                // devo processare la lista delle scelte che viene ritornata in xml
                // creo un nuovo oggetto Dom Document
                $xmlDoc = new DOMDocument();
                // mi salvo in temp la porzione di xml che è presente nella propietà arxDataSource dell'oggetto campo aggiuntivo
                $temp=$aggiuntivo->arxDataSource;
                $xml=simplexml_load_string($temp) or die("Error: Cannot create object");
                // $this->arxDebug($xml);
                // $this->arxDebug($xml->TABLE[0]->ELEMENTO);

                // $pos_start=stripos($temp,'<ricerca>');
                // $pos_end=stripos($aggiuntivo->arxDataSource,'</newdataset>');
                // // estraggo la porzione di codice che mi interessa
                // $lista_xml=substr($aggiuntivo->arxDataSource,$pos_start,$pos_end);
                // // ci aggiungo dei tag che faranno da wrapper per la lista
                // $lista_xml='<lista>'.trim($lista_xml).'</lista>';
                // // disabilito la gestione degli errori altrimenti avrei dei warning in quanto i tag <ricerca></ricerca> non sono standard HTML
                // libxml_use_internal_errors(true);
                // // tiro su la porzione di html
                // $xmlDoc->LoadHTML($lista_xml);
                // $xmlDoc->loadXML($aggiuntivo->arxDataSource);
                // $myXmlCollection= new XMLListCollection($aggiuntivo->arxDataSource);
                // riabilito la gestione degli errori
                // echo "<pre>";
                // var_dump($myXmlCollection);
                // $search = $ARX_Search->Dm_Profile_Search_Get_New_Instance_By_TipiDocumentoCodice($sessionid, "GEST.POS");echo "</pre>";

                // libxml_use_internal_errors(false);

                // prendo tutti gli elementi con tag 'ricerca'
                // $rows = $xmlDoc->getElementsByTagName('ricerca');
                if( $aggiuntivo->NomeCampo != "COMBO19_1"){
                  ?>
                  <select id="<?php echo $aggiuntivo->NomeCampo; ?>" name="<?php echo $aggiuntivo->NomeCampo; ?>" <?php if($required) echo "required";?>>
                    <?php
                    // ciclo sui tag ricerca per creare i valori option del campo select html
                    foreach ($xml as $row) { ?>
                      <option value="<?php echo $row->ELEMENTO; ?>"><?php echo $row->ELEMENTO; ?></option>
                    <?php } ?>
                  </select><br />
                  <?php
                } else {
                  ?>
                  <select id="<?php echo $aggiuntivo->NomeCampo; ?>" name="<?php echo $aggiuntivo->NomeCampo; ?>" <?php if($required) echo "required";?>>
                    <?php
                    // ciclo sui tag ricerca per creare i valori option del campo select html
                    foreach ($xml as $row) { ?>
                      <option value="<?php echo $row->UTENTE; ?>"><?php echo $row->NOMINATIVO; ?></option>
                    <?php } ?>
                  </select><br />
                  <?php
                }
              }
              // ****** CHECKBOX *****
              elseif($tipo=='Checkbox'){
                ?>
                <input type="checkbox" id="<?php echo $aggiuntivo->NomeCampo; ?>" name="<?php echo $aggiuntivo->NomeCampo; ?>" <?php if($required) echo "required";?> /><br />
                <?php
              }
              break;
              case ARX_Dati\Dm_MaskDetail_FieldKind::DataDoc:
              // DatfieldsetaDoc
              ?>
              <label for="DataDoc">DataDoc</label>
              <input type="date" name="DataDoc" id="DataDoc" value="" placeholder="YYYY-MM-DD"><br>
              <?php
              break;
              /*case ARX_Dati\Dm_MaskDetail_FieldKind::File:
              echo "File<br />";
              // campo aggiuntivo, identifiato da $field->FIELD_ID
              break;*/
              // ....
            }

            //var_dump($field->Get_Property_Value_By_Name($field->FIELD_ID));
          }
          ?>
          <input type="button" onclick="scriviDatiProfilo();" value="carica" />
          <input type="reset" value="reset" />
        </fieldset>
      </form>
      <div id="requestResult"></div>
      <?php

    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }


    $this->logoutArxivar();
    return ob_get_clean();
  }

  public function dettaglioProfilo(){
    $docnumber = $this->post("docnumber", false);
    if( !empty($docnumber) ){
      // TODO: parte del dettaglio
    } else {
      // parte dell'inserimento
      echo $this->getFieldFromMaschera();
    }
  }

  public function listaProfili(){
    $this->loginArxivarServizio();
    $ARX_Dati = new ARX_Dati\ARX_Dati($this->baseUrl."ARX_Dati.asmx?WSDL");
    $ARX_Search = new ARX_Search\ARX_Search($this->baseUrl."ARX_Search.asmx?WSDL");
    $ARX_Documenti = new ARX_Documenti\ARX_Documenti($this->baseUrl."ARX_Documenti.asmx?WSDL");
    // $ARX_Workflow = new ARX_Workflow\ARX_Workflow($this->baseUrl."ARX_Workflow.asmx?WSDL");
    $sessionid = $this->loginResult->SessionId;

    /* TODO: Parametrizzo il campo da cercare (proprietario del profilo) in modo da agevolare la ricerca successivamente */
    /* TODO: Sarà necessario inserire la ricerca dell'utente e dell'AOO partendo dal login. */
    $comboUtente = "1\\3aMestre"; // aoo\utente

    // esecuzione ricerca
    // GEST.POS classe della ricerca.
    $search = $ARX_Search->Dm_Profile_Search_Get_New_Instance_By_TipiDocumentoCodice($sessionid, "GEST.POS");
    //  select per quali campi
    $select = $ARX_Search->Dm_Profile_Select_Get_New_Instance_By_TipiDocumentoCodice($sessionid, "GEST.POS");
    $this->arxDebug($select);
    // esempio di ricerca per campo standard "NUMERO"
    /*
    $search->Numero->Operatore = ARX_Search\Dm_Base_Search_Operatore_String::Uguale;
    $search->Numero->Valore = "123456";
    */
    // esempio di ricerca per campo aggiuntivo

    foreach ($search->Aggiuntivi->Field_Abstract as $agg) {
      /* @var $agg \ARX_Search\Field_String */
      if ($agg->Nome == "COMBO19_1") {
        $agg->Operatore = ARX_Search\Dm_Base_Search_Operatore_String::Uguale;
        $agg->Valore = $comboUtente;
      }

      // per ricercare per id esterno (alias per SDK) si può verificare $agg->ExternalId
    }

    $select->DOCNUMBER->Selected = true;
    $select->DOCNAME->Selected = true;
    $select->STATO->Selected = true;
    $result = $ARX_Search->Dm_Profile_GetData($sessionid, $select, $search);
    $ds = simplexml_load_string($result);
    // $this->arxDebug($ds);

    foreach ($ds->Ricerca as $row) {
      echo $row->DOCNUMBER." - ".$row->DOCNAME."<hr/>";
    }

    $docnumber = $ds->Ricerca[0]->DOCNUMBER;
    $this->arxDebug($docnumber);

    //// estrazione profilo
    //$profile = $ARX_Dati->Dm_Profile_GetData_By_DocNumber($sessionid, $docnumber);
    //var_dump($profile);
    //
    //// estrazione campo aggiuntivo
    //$valore = "";
    //foreach ($profile->Aggiuntivi->Aggiuntivo_Base as $agg) {
    //    /* @var $agg \ARX_Dati\Aggiuntivo_String */
    //    if ($agg->Nome == "TESTO1_1") $valore = $agg->Valore;
    //}
    //echo "TESTO1_1: ".$valore;
    //
    //// estrazione documento
    //$file = $ARX_Documenti->Dm_Profile_GetDocument($sessionid, $docnumber);
    //file_put_contents("/tmp/".$file->FileName, $file->File);

    // cerco il task attivo per questo documento
    $search = $ARX_Search->Dm_TaskWork_Search_GetNewInstance($sessionid);
    $search->Dm_Profile->DOCNUMBER->Operatore = \ARX_Search\Dm_Base_Search_Operatore_Numerico::Uguale;
    $search->Dm_Profile->DOCNUMBER->Valore = $docnumber;
    $search->STATO->Operatore = \ARX_Search\Dm_Base_Search_Operatore_Numerico::Uguale;
    $search->STATO->Valore = 1;
    $search->TIPOTASK->Operatore = \ARX_Search\Dm_Base_Search_Operatore_Numerico::Uguale;
    $search->TIPOTASK->Valore = 1;
    $search->NOMETASK->Operatore = \ARX_Search\Dm_Base_Search_Operatore_Numerico::Uguale;
    $search->NOMETASK->Valore = "99 - Inserimento Documentazione";
    $select = $ARX_Search->Dm_TaskWork_Select_GetNewInstance($sessionid);
    $select->ID->Selected = TRUE;
    $result = $ARX_Search->Dm_TaskWork_GetData($sessionid, $select, $search);
    $ds = simplexml_load_string($result);
    $this->arxDebug($ds);

    $this->logoutArxivar();
  }

  public function scriviDatiProfilo(){

    $this->loginArxivarServizio();
    $ARX_Dati = new ARX_Dati\ARX_Dati($this->baseUrl."ARX_Dati.asmx?WSDL");


    $sessionid = $this->loginResult->SessionId;

    try
    {
      // funzione per recuperare tutte le maschere
      //echo "dati di maschera<br />";
      // $masks = $ARX_Dati->Dm_MaskGetData($sessionid);

      // funzione per utilizzare una maschera con uno specifico ID
      //echo "utilizzo la maschera specifica<br />";
      $profileMv = $ARX_Dati->Dm_Profile_Insert_MV_GetNewInstance_From_DmMaskId($sessionid, $this->maskid);

      // DM profile default
      //var_dump($profileMv->DmProfileDefault);

      // DmMaskDetails contiene l'elenco dei campi previsti nella maschera
      //echo "elenco campi della maschera specifica<br />";
      $details = $profileMv->DmMaskDetails->Dm_MaskDetail;

      //echo "dati DmProfileDEfault<br />";
      $profile = $profileMv->DmProfileDefault->Dm_Profile_Insert_Base;
      // @var $profile \ARX_Dati\Dm_Profile_Insert_Base //
      // echo "<pre>";
      // var_dump($profile);
      // echo "</pre><hr/>";
      // die;


      foreach ($details as $field) {

        switch ($field->FIELD_KIND) {
          case ARX_Dati\Dm_MaskDetail_FieldKind::From:
          // mittente
          echo "mittente<br />";
          break;
          case ARX_Dati\Dm_MaskDetail_FieldKind::To:
          // destinatario
          echo "destinatario<br />";
          break;
          case ARX_Dati\Dm_MaskDetail_FieldKind::Oggetto:
          // Oggetto
          $profile->DocName = $_POST['oggetto'];

          break;
          case ARX_Dati\Dm_MaskDetail_FieldKind::Aggiuntivo:
          // Aggiuntivo

          // compilo i campi aggiuntivi
          foreach ($profile->Aggiuntivi->Aggiuntivo_Base as $agg) {
            if(array_key_exists($agg->Nome,$_POST)){
              $agg->Valore=$_POST[$agg->Nome];
            }
          }

          break;

          case ARX_Dati\Dm_MaskDetail_FieldKind::DataDoc:
          // DataDoc - FORMATO - YYYY-MM-DD
          // TODO controllo formato data
          $profile->DataDoc = $_POST['DataDoc'];

          break;
          /*case ARX_Dati\Dm_MaskDetail_FieldKind::File:
          echo "File<br />";
          // campo aggiuntivo, identifiato da $field->FIELD_ID
          break;*/
          // ....
        }

        //var_dump($field->Get_Property_Value_By_Name($field->FIELD_ID));
      }
      /*echo "<pre>";
      var_dump($profile->Aggiuntivi->Aggiuntivo_Base);
      echo "</pre>";*/




      // devo convertire Dm_Profile_Insert_Base in Dm_Profile_Insert_For_Mask
      $profileForMask = new \ARX_Dati\Dm_Profile_Insert_For_Mask();

      /*echo "m_Profile_Insert_For_Mask<br />";
      echo "<pre>";
      var_dump($profileForMask);
      echo "</pre>";*/

      $props = get_object_vars($profile);
      foreach ($props as $key => $value) {
        $profileForMask->$key = $value;
      }

      $profileForMask->DmMaskId = $profileMv->DmMask->ID;
      $profileForMask->Reason = \ARX_Dati\Dm_Mask_Type::Archiviazione;
      $profileForMask->DataFile = date("c");

      // eseguo l'archiviazione
      $result = $ARX_Dati->Dm_Profile_Insert_For_Mask($sessionid, $profileForMask);

      // verifico il risultato
      if ($result->EXCEPTION == \ARX_Dati\Security_Exception::Nothing) {
        echo "Importazione completata con system ID: ".$result->PROFILE->DOCNUMBER."<hr/>";
      } else {
        echo "Errore in fase di importazione: ".$result->EXCEPTION."; ".$result->MESSAGE."<hr/>";
      }


    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }


    $this->logoutArxivar();

  }

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
    if(!$this->isDebug()){
      return 0;
    }
    $this->setJsonMess("arx_log",$mess);
  }

  private function arxDebug($something, $stop = false){
    if( $this->isDebug()){
      echo "<pre>";
      var_dump($something);
      echo "</pre>";

      if($stop){
        $this->logoutArxivar();
        die();
      }
    }
  }

  private function loginArxivarServizio(){
    $ARX_Login = new ARX_Login\ARX_Login($this->baseUrl."ARX_Login.asmx?WSDL");
    $this->loginResult = $ARX_Login->Login($this->adminUser, $this->adminPass, $this->softwareName);
    if ($this->loginResult->ArxLogOnErrorType != Arx_Login\ArxLogOnErrorType::None){
      $this->arxLog("Logon Failed: ".$this->loginResult->ArxLogOnErrorType);
      die();
    }
  }

  public function loginArxivar(){
    $ARX_Login = new ARX_Login\ARX_Login($this->baseUrl."ARX_Login.asmx?WSDL");
    $userName = $this->getUsername();
    $password = $this->getPassword();
    $this->loginResult = $ARX_Login->Login($userName, $password, $this->softwareName);
    $this->arxLog('WCF chiamate');
    if( $this->loginResult->LoggedIn ){
      $this->arxLog('Login eseguito con successo');
      $this->sessionid = $this->loginResult->SessionId;
      $this->isLogin=true;

      // echo 'Siamo prima del getinfo';
      // var_dump($this->sessionid);
      // $userC = $ARX_Login->GetInfoUserConnected($this->sessionid);
      // var_dump($userC);
      //
      // $ARX_Dati = new ARX_Dati\ARX_Dati($this->baseUrl."ARX_Dati.asmx?WSDL");
      // $userB = $ARX_Dati->Dm_Gruppi_GetData_By_Utente($this->sessionid, 52);
      // // $userB = $userC->ToUtentiBase();
      // var_dump($userB);
      // // die;


      $ARX_Login->LogOut($this->sessionid); //rilascio la sessione per nuovi login
      $this->arxLog('Logout e registrazione');
      $this->registerSessionLogin();
      return true;
    }else{
      $this->arxLog(' Login fallito ');
      $this->arxLog($this->loginResult->ArxLogOnErrorTypeString);
      $this->logError=$this->loginResult->ArxLogOnErrorTypeString;
      return false;
    }
  }

  private function logoutArxivar(){
    // esecuzione logout
    $ARX_Login = new ARX_Login\ARX_Login($this->baseUrl."ARX_Login.asmx?WSDL");
    $ARX_Login->LogOut($this->loginResult->SessionId);
  }

  public function impersonateLogin(){

  }

}
// end class
?>

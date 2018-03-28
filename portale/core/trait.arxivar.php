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

    public function getFieldFromMaschera(){
        $ARX_Login = new ARX_Login\ARX_Login($baseUrl."ARX_Login.asmx?WSDL");
        $ARX_Dati  = new ARX_Dati\ARX_Dati($baseUrl."ARX_Dati.asmx?WSDL");


        $loginResult = $ARX_Login->Login($this->adminUser, $this->adminPass, $this->softwareName);
        if ($loginResult->ArxLogOnErrorType != Arx_Login\ArxLogOnErrorType::None){
            $this->arxLog("Logon Failed: ".$loginResult->ArxLogOnErrorType);
            die();
        }

        $sessionid = $loginResult->SessionId;
        try {
            // funzione per recuperare tutte le maschere
            //echo "dati di maschera<br />";
            $masks = $ARX_Dati->Dm_MaskGetData($sessionid);
            $this->debug($mask);

            // funzione per utilizzare una maschera con uno specifico ID
            //echo "utilizzo la maschera specifica<br />";
            $profileMv = $ARX_Dati->Dm_Profile_Insert_MV_GetNewInstance_From_DmMaskId($sessionid, $this->maskid);
            $mask_data = $ARX_Dati->Dm_MaskDetail_GetData_By_DmMaskId($sessionid, $this->maskid);

            // DM profile default
            //var_dump($profileMv->DmProfileDefault);

            // DmMaskDetails contiene l'elenco dei campi previsti nella maschera
            //echo "elenco campi della maschera specifica<br />";
            $details = $profileMv->DmMaskDetails->Dm_MaskDetail;

            //echo "dati DmProfileDEfault<br />";
            $profile = $profileMv->DmProfileDefault->Dm_Profile_Insert_Base;
            // @var $profile \ARX_Dati\Dm_Profile_Insert_Base //

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
            $this->printFieldFromMaschera($details, $lista_campi_specifici);

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }


        // esecuzione logout
        $ARX_Login->LogOut($sessionid);
    }

    public function printFieldFromMaschera($details, $lista_campi_specifici){
      // funzione generica per il caricamento delle maschere Arxivar
        ?>
        <form action="scrivi_dati.php" method="post">
            <fieldset>
                <legend>Maschera</legend>
                <?php
                foreach ($details as $field) {
                    $this->convertFieldFromMaschera($field, $lista_campi_specifici);
                }
                ?>
                <input type="submit" value="carica" />
                <input type="reset" value="reset" />
            </fieldset>
        </form>
        <?php
    }

    private function convertFieldFromMaschera($field, $lista_campi_specifici){
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
            switch ($tipo) {
                case "Textbox":
                    // controllare alias per vedere se è un campo time
                    if(strpos($alias,'(HH:MM)')>0){ ?>
                        <input type="text" name="<?php echo $aggiuntivo->NomeCampo; ?>"
                        class="time" id="<?php echo $aggiuntivo->NomeCampo; ?>" value="" maxlength="5"
                        <?php if($required) echo "required";?>><br />
                    <?php } elseif($row_count>1){ ?>
                        <textarea name="<?php echo $aggiuntivo->NomeCampo; ?>" id="<?php echo $aggiuntivo->NomeCampo; ?>"
                            maxlength="<?php echo $n_char; ?>" <?php if($required) echo "required";?>></textarea>
                        <br />
                    <?php } else{ ?>
                        <input type="text" name="<?php echo $aggiuntivo->NomeCampo; ?>" id="<?php echo $aggiuntivo->NomeCampo; ?>"
                        maxlength="<?php echo $n_char; ?>" value="" <?php if($required) echo "required";?>><br />
                    <?php }
                    break;

                    case "Combobox":
                    // devo processare la lista delle scelte che viene ritornata in xml
                    // creo un nuovo oggetto Dom Document
                    $xmlDoc = new DOMDocument();
                    // mi salvo in temp la porzione di xml che è presente nella propietà arxDataSource dell'oggetto campo aggiuntivo
                    $temp="".$aggiuntivo->arxDataSource;
                    $xml=simplexml_load_string($temp) or die("Error: Cannot create object");
                    // debug($xml);
                    debug($xml->TABLE[0]->ELEMENTO);

                    $pos_start=stripos($temp,'<ricerca>');
                    $pos_end=stripos($aggiuntivo->arxDataSource,'</newdataset>');
                    // estraggo la porzione di codice che mi interessa
                    $lista_xml=substr($aggiuntivo->arxDataSource,$pos_start,$pos_end);
                    // ci aggiungo dei tag che faranno da wrapper per la lista
                    $lista_xml='<lista>'.trim($lista_xml).'</lista>';
                    // disabilito la gestione degli errori altrimenti avrei dei warning in quanto i tag <ricerca></ricerca> non sono standard HTML
                    libxml_use_internal_errors(true);
                    // tiro su la porzione di html
                    $xmlDoc->LoadHTML($lista_xml);
                    $xmlDoc->loadXML($aggiuntivo->arxDataSource);
                    // $myXmlCollection= new XMLListCollection($aggiuntivo->arxDataSource);
                    // riabilito la gestione degli errori
                    // echo "<pre>";
                    // var_dump($myXmlCollection);
                    // echo "</pre>";

                    libxml_use_internal_errors(false);

                    // prendo tutti gli elementi con tag 'ricerca'
                    $rows = $xmlDoc->getElementsByTagName('ricerca');
                    ?>
                    <select id="<?php echo $aggiuntivo->NomeCampo; ?>" name="<?php echo $aggiuntivo->NomeCampo; ?>" <?php if($required) echo "required";?>>
                        <?php
                        // ciclo sui tag ricerca per creare i valori option del campo select html
                        foreach ($rows as $row) {
                            ?>
                            <option value="<?php echo $row->nodeValue; ?>"><?php echo $row->nodeValue; ?></option>
                            <?php
                        }
                        ?>
                    </select><br />
                    <?php
                    break;

                    case "Combobox":
                    ?>
                    <input type="checkbox" id="<?php echo $aggiuntivo->NomeCampo; ?>" name="<?php echo $aggiuntivo->NomeCampo; ?>" <?php if($required) echo "required";?> /><br />
                    <?php
                    break;
                }
                break;
                case ARX_Dati\Dm_MaskDetail_FieldKind::DataDoc:
                    ?>
                    <label for="DataDoc">DataDoc</label>
                    <input type="date" name="DataDoc" id="DataDoc" value="" placeholder="YYYY-MM-DD"><br>
                    <?php
                    break;
            }
        }

        public function scriviDatiProfilo(){

          // classi necessarie
          $ARX_Login = new ARX_Login\ARX_Login($baseUrl."ARX_Login.asmx?WSDL");
          $ARX_Dati = new ARX_Dati\ARX_Dati($baseUrl."ARX_Dati.asmx?WSDL");

          $loginResult = $ARX_Login->Login($userName, $password, $softwareName);

          if ($loginResult->ArxLogOnErrorType != Arx_Login\ArxLogOnErrorType::None)
              die("Logon Failed: ".$loginResult->ArxLogOnErrorType);

          $sessionid = $loginResult->SessionId;

          try
          {
              // funzione per recuperare tutte le maschere
              //echo "dati di maschera<br />";
              $masks = $ARX_Dati->Dm_MaskGetData($sessionid);

              // funzione per utilizzare una maschera con uno specifico ID
              //echo "utilizzo la maschera specifica<br />";
              $profileMv = $ARX_Dati->Dm_Profile_Insert_MV_GetNewInstance_From_DmMaskId($sessionid, $maskid);

              // DM profile default
              //var_dump($profileMv->DmProfileDefault);

              // DmMaskDetails contiene l'elenco dei campi previsti nella maschera
              //echo "elenco campi della maschera specifica<br />";
              $details = $profileMv->DmMaskDetails->Dm_MaskDetail;

              //echo "dati DmProfileDEfault<br />";
              $profile = $profileMv->DmProfileDefault->Dm_Profile_Insert_Base;
              // @var $profile \ARX_Dati\Dm_Profile_Insert_Base //
              echo "<pre>";
              var_dump($profile);
              echo "</pre><hr/>";
              die;


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
                          $profile->DataDoc = $_POST['DataDoc'];;

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


          // esecuzione logout
          $ARX_Login->LogOut($sessionid);

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

        private function arxDebug($something){
            if( $this->isDebug()){
                echo "<pre>";
                var_dump($something);
                echo "</pre>";
            }
        }

        private function loginArxivarServizio(){

        }

        public function loginArxivar(){
            $baseUrl=$this->baseUrl;
            $ARX_Login = new ARX_Login\ARX_Login($baseUrl."ARX_Login.asmx?WSDL");
            $userName = $this->getUsername();
            $password = $this->getPassword();
            $this->loginResult = $ARX_Login->Login($userName, $password, $this->softwareName);
            $this->arxLog('WCF chiamate');
            if( $this->loginResult->LoggedIn ){
                $this->arxLog('Login eseguito con successo');
                $this->sessionid = $this->loginResult->SessionId;
                $this->isLogin=true;
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

        public function impersonateLogin(){

        }

    }
    // end class
    ?>

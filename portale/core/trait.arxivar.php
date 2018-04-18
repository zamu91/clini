<?php

trait arxivar{

  public $baseUrl = "http://localhost:81/";
  public $adminUser = "Admin";
  public $adminPass = "123";
  public $maskid = "ec6009ba90064374a09956b7b7a61328";
  public $softwareName = "PHP Gestione cliniche";
  public $softwareNameSecret ="035518E483DE4436";
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
    foreach ($search->Aggiuntivi->Field_Abstract as $agg) {
      if( $agg->Nome != 'COMBO19_1') {?>
        <div class="fieldBox">
          <label><?php echo $agg->Label; ?></label><br>
          <input type="text" data-name="<?php echo $agg->Nome ?>" />
        </div>
      <?php }
    }

    $this->logoutArxivar();
    return ob_get_clean();
  }

  public function getFieldFromMascheraUpload(){
    ob_start();

    $this->loginArxivarServizio();
    $sessionid = $this->loginResult->SessionId;

    $ARX_Dati  = new ARX_Dati\ARX_Dati($this->baseUrl."ARX_Dati.asmx?WSDL");
    $profileMv = $ARX_Dati->Dm_Profile_Insert_MV_GetNewInstance_From_DmMaskId($sessionid, $this->maskid);
    $profile = $profileMv->DmProfileDefault->Dm_Profile_Insert_Base;

    /*
    * COMBO15_297 -- Tipo valutazione
    * TESTO10_297 -- Cognome
    * TESTO13_297 -- Nome
    */

    $tipo_valutazione = $ARX_Dati->Dm_CampiSpecifici_GetValues($sessionid, "COMBO15_297",$profile,null);
    $cognome = $ARX_Dati->Dm_CampiSpecifici_GetValues($sessionid, "TESTO10_297",$profile,null);
    $nome = $ARX_Dati->Dm_CampiSpecifici_GetValues($sessionid, "TESTO13_297",$profile,null);

    ?>
      <span class="btn btn-success fileinput-button">
          <i class="glyphicon glyphicon-plus"></i>
          <span>Seleziona i file...</span>
          <!-- The file input field used as target for the file upload widget -->
          <input id="fileupload" type="file" name="files[]" multiple>
      </span>
      <br>
      <br>
      <!-- The global progress bar -->
      <div id="progress" class="progress">
          <div class="progress-bar progress-bar-success"></div>
      </div>
      <!-- The container for the uploaded files -->
      <div id="files" class="files"></div>
    <?php

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
          <!-- <input type="button" onclick="scriviDatiProfilo();" value="carica" />
          <input type="reset" value="reset" /> -->
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
      echo $this->getFieldFromMascheraUpload();
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
    $sessionid = $this->loginResult->SessionId;

    $ses = $this->checkExistSessionFromToken();
    $comboUtente = "{$ses["AOO"]}\\{$ses["USERNAME"]}";

    $search = $ARX_Search->Dm_Profile_Search_Get_New_Instance_By_TipiDocumentoCodice($sessionid, "GEST.POS");
    $select = $ARX_Search->Dm_Profile_Select_Get_New_Instance_By_TipiDocumentoCodice($sessionid, "GEST.POS");
    // $this->arxDebug($select);

    $tipoValutazione = $this->post("tipoValutazione", false);
    $cognome = $this->post("cognome", false);
    $nome = $this->post("nome", false);
    $deceduto = $this->post("deceduto", false);
    $telefono = $this->post("telefono", false);
    $mail = $this->post("mail", false);

    foreach ($search->Aggiuntivi->Field_Abstract as $agg) {
      /* @var $agg \ARX_Search\Field_String */
      if ($agg->Nome == "COMBO19_1") {
        $agg->Operatore = ARX_Search\Dm_Base_Search_Operatore_String::Uguale;
        $agg->Valore = $comboUtente;
      }
      if($agg->Nome == "COMBO15_297" && !empty($tipoValutazione) ){
        $agg->Operatore = ARX_Search\Dm_Base_Search_Operatore_String::Uguale;
        $agg->Valore = $tipoValutazione;
      }
      if($agg->Nome == "TESTO10_297" && !empty($cognome) ){
        $agg->Operatore = ARX_Search\Dm_Base_Search_Operatore_String::Uguale;
        $agg->Valore = $cognome;
      }
      if($agg->Nome == "TESTO13_297" && !empty($nome) ){
        $agg->Operatore = ARX_Search\Dm_Base_Search_Operatore_String::Uguale;
        $agg->Valore = $nome;
      }
      if($agg->Nome == "CHECK17_1" && !empty($deceduto) ){
        $agg->Operatore = ARX_Search\Dm_Base_Search_Operatore_String::Uguale;
        $agg->Valore = $deceduto;
      }
      if($agg->Nome == "TESTO14_297" && !empty($telefono) ){
        $agg->Operatore = ARX_Search\Dm_Base_Search_Operatore_String::Uguale;
        $agg->Valore = $telefono;
      }
      if($agg->Nome == "TESTO12_297" && !empty($mail) ){
        $agg->Operatore = ARX_Search\Dm_Base_Search_Operatore_String::Uguale;
        $agg->Valore = $mail;
      }
    }


    $select->DOCNUMBER->Selected = true;
    $select->DATADOC->Selected = true;
    $select->STATO->Selected = true;
    foreach ($search->Aggiuntivi->Field_Abstract as $ix => $agg) {
      if ($agg->Nome == "COMBO19_1") {
        $search->Aggiuntivi->Field_Abstract[$ix]->Selected = true;
      }
      if($agg->Nome == "COMBO15_297" && !empty($tipoValutazione) ){
        $search->Aggiuntivi->Field_Abstract[$ix]->Selected = true;
      }
      if($agg->Nome == "TESTO10_297" && !empty($cognome) ){
        $search->Aggiuntivi->Field_Abstract[$ix]->Selected = true;
      }
      if($agg->Nome == "TESTO13_297" && !empty($nome) ){
        $search->Aggiuntivi->Field_Abstract[$ix]->Selected = true;
      }
      if($agg->Nome == "CHECK17_1" && !empty($deceduto) ){
        $search->Aggiuntivi->Field_Abstract[$ix]->Selected = true;
      }
      if($agg->Nome == "TESTO14_297" && !empty($telefono) ){
        $search->Aggiuntivi->Field_Abstract[$ix]->Selected = true;
      }
      if($agg->Nome == "TESTO12_297" && !empty($mail) ){
        $search->Aggiuntivi->Field_Abstract[$ix]->Selected = true;
      }
    }


    $result = $ARX_Search->Dm_Profile_GetData($sessionid, $select, $search);
    $ds = simplexml_load_string($result);
    // $this->arxDebug($ds);

    ?>
    <table class="fullWidthTable clickable">
      <thead>
        <tr>
          <th>DOCNUMBER</th>
          <th>STATO</th>
          <th>DATA DOCUMENTO</th>
        </tr>
      </thead>
      <tbody>
        <?php
        foreach ($ds->Ricerca as $row) { ?>
          <tr data-task="<?php echo $row->DOCNUMBER; ?>" onclick="dettagliTaskProfilo(this);">
            <td><?php echo $row->DOCNUMBER; ?></td>
            <td><?php echo $row->STATO; ?></td>
            <td><?php echo $row->DATADOC; ?></td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
    <?php


    // $this->arxDebug($ds->Ricerca[0]);


    // $this->arxDebug($docnumber);

    //// estrazione profilo
    //$profile = $ARX_Dati->Dm_Profile_GetData_By_DocNumber($sessionid, $docnumber);
    //var_dump($profile);
    //
    //// estrazione campo aggiuntivo
    //$valore = "";
    //foreach ($profile->Aggiuntivi->Aggiuntivo_Base as $agg) {
    //    /* @var $agg \ARX_Dati\Aggiuntivo_String */
    //    if ($agg->Nome == "TESTOutilizzare il codice seguente (dove xmlData è il risultato1_1") $valore = $agg->Valore;
    //}
    //echo "TESTO1_1: ".$valore;
    //
    //// estrazione documento
    // $file = $ARX_Documenti->Dm_Profile_GetDocument($sessionid, $docnumber);
    // file_put_contents("/tm#f5f5f5p/".$file->FileName, $file->File);

    $this->logoutArxivar();
  }

  public function getTaskworkFromDocnumber(){
    $this->loginArxivarServizio();

    $sessionid = $this->loginResult->SessionId;
    $docnumber = $this->post("docnumber", false);
    $ARX_Search = new ARX_Search\ARX_Search($this->baseUrl."ARX_Search.asmx?WSDL");
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
    // $this->arxDebug($ds);
    $taskwork = (string)$ds->Ricerca->ID;
    $this->logoutArxivar();
    $this->setJsonMess("res", true);
    $this->setJsonMess("taskwork", $taskwork);
    $this->halt();
  }

  public function listaDocumenti(){
    // estrazione documento
    // $file = $ARX_Documenti->Dm_Profile_GetDocument($sessionid, $docnumber);
    // file_put_contents("docs/".$file->FileName, $file->File);


    $this->loginArxivarServizio();
    $sessionid = $this->loginResult->SessionId;
    $ARX_Dati = new ARX_Dati\ARX_Dati($this->baseUrl."ARX_Dati.asmx?WSDL");
    $ARX_Search = new ARX_Search\ARX_Search($this->baseUrl."ARX_Search.asmx?WSDL");

    /* TODO: Parametrizzo il campo da cercare (proprietario del profilo) in modo da agevolare la ricerca successivamente */
    /* TODO: Sarà necessario inserire la ricerca dell'utente e dell'AOO partendo dal login. */
    $docnumber = $this->post("docnumber", false);

    // esecuzione ricerca
    // GEST.POS classe della ricerca.
    $search = $ARX_Search->Dm_Profile_Search_Get_New_Instance_By_TipiDocumentoCodice($sessionid, "GEST.DOCMED");
    //  select per quali campi
    $select = $ARX_Search->Dm_Profile_Select_Get_New_Instance_By_TipiDocumentoCodice($sessionid, "GEST.DOCMED");
    // $this->arxDebug($select->Aggiuntivi);
    // esempio di ricerca per campo standard "NUMERO"
    /*
    $search->Numero->Operatore = ARX_Search\Dm_Base_Search_Operatore_String::Uguale;
    $search->Numero->Valore = "123456";
    */
    // esempio di ricerca per campo aggiuntivo

    foreach ($search->Aggiuntivi->Field_Abstract as $agg) {
      /* @var $agg \ARX_Search\Field_String */
      if ($agg->Nome == "NUMERIC16_299") {
        $agg->Operatore = ARX_Search\Dm_Base_Search_Operatore_String::Uguale;
        $agg->Valore = $docnumber;
      }

      // per ricercare per id esterno (alias per SDK) si può verificare $agg->ExternalId
    }

    $select->DOCNUMBER->Selected = true;
    $select->DOCNAME->Selected = true;
    $select->DATADOC->Selected = true;
    $select->CREATION_DATE->Selected = true;
    $select->FILESIZE->Selected = true;
    $select->STATO->Selected = true;
    $result = $ARX_Search->Dm_Profile_GetData($sessionid, $select, $search);
    $ds = simplexml_load_string($result);
    // $this->arxDebug($ds);

    ?>
    <table class="fullWidthTable clickable">
      <thead>
        <tr>
          <th>DOCNUMBER</th>
          <th>NOME DOCUMENTO</th>
          <th>DATA DOCUMENTO</th>
          <th>DIMENSIONE DOCUMENTO</th>
          <th>STATO</th>
        </tr>
      </thead>
      <tbody>
        <?php
        foreach ($ds->Ricerca as $row) { ?>
          <tr data-doc="<?php echo $row->DOCNUMBER; ?>" >
            <td><?php echo $row->DOCNUMBER; ?></td>
            <td><?php echo $row->DOCNAME; ?></td>
            <td><?php echo $row->DATADOC; ?></td>
            <td><?php echo $row->FILESIZE; ?></td>
            <td><?php echo $row->STATO; ?></td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
    <?php
    $this->halt();
  }

  public function scriviDatiProfilo(){

    $this->loginArxivarServizio();
    $ARX_Dati = new ARX_Dati\ARX_Dati($this->baseUrl."ARX_Dati.asmx?WSDL");
    $sessionid = $this->loginResult->SessionId;

    try {
      // $masks = $ARX_Dati->Dm_MaskGetData($sessionid);

      // funzione per utilizzare una maschera con uno specifico ID
      //echo "utilizzo la maschera specifica<br />";
      $profileMv = $ARX_Dati->Dm_Profile_Insert_MV_GetNewInstance_From_DmMaskId($sessionid, $this->maskid);

      // DmMaskDetails contiene l'elenco dei campi previsti nella maschera
      //echo "elenco campi della maschera specifica<br />";
      $details = $profileMv->DmMaskDetails->Dm_MaskDetail;

      //echo "dati DmProfileDEfault<br />";
      $profile = $profileMv->DmProfileDefault->Dm_Profile_Insert_Base;
      // @var $profile \ARX_Dati\Dm_Profile_Insert_Base //


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

  public function scriviDocumentiProfilo(){

    $this->loginArxivarServizio();
    $ARX_Dati = new ARX_Dati\ARX_Dati($this->baseUrl."ARX_Dati.asmx?WSDL");
    $ARX_Workflow = new ARX_Workflow\ARX_Workflow($this->baseUrl."ARX_Workflow.asmx?WSDL");
    // $ARX_Documenti = new ARX_Documenti\ARX_Documenti($this->baseUrl."ARX_Documenti.asmx?WSDL");

    $sessionid = $this->loginResult->SessionId;
    $idTaskWork = $this->post("taskwork", false);
    $files = $this->post("files", false);
    // $basepath = dirname($_SERVER['DOCUMENT_ROOT']).$this->dSep("W")."arx_portale{$this->dSep("W")}clini{$this->dSep("W")}portale{$this->dSep("W")}uploads{$this->dSep("W")}";
    $basepath = dirname($_SERVER['DOCUMENT_ROOT']);


    // cerco l'operazione di inserimento documento per questo task
    $dmTaskDocs = $ARX_Workflow->Dm_TaskDoc_GetData_By_DmTaskworkId($sessionid, $idTaskWork);
    $dmTaskDoc = $dmTaskDocs->Dm_TaskDoc;
    /* @var $dmTaskDoc \ARX_Workflow\Dm_TaskDoc */
    $idTaskDoc = $dmTaskDoc->ID;

    // si può anche controllare se l'operazione è già stata eseguita con la variabile $dmTaskDoc->OP_ESEGUITA

    // // preparo il profilo per l'operazione
    // $profileInsertMv = $ARX_Workflow->Dm_Profile_Insert_MV_Get_New_Instance_By_DmTaskDocId($sessionid, $idTaskWork, $idTaskDoc, ARX_Workflow\Dm_TaskDoc_ProfileMode::Normale);
    // $profileBase = $profileInsertMv->DmProfileDefault->Dm_Profile_Insert_Base;
    // // qui va valorizzato il profilo da inserire...
    // $profileBase->InOut = ARX_Workflow\DmProfileInOut::Interno;
    // $profileBase->Stato = "VALIDO";
    // $profileBase->Aoo = "01";
    // $profileBase->DocName = "Prova importazione da TASK";
    // $profileBase->ProtocolloInterno = "123456+++";
    // $filepath = "c:\test.txt";
    //     $arxFile = new ARX_Dati\Arx_File();
    //     $arxFile->CreationDate = date("c", filectime($filepath));
    //     $arxFile->FileName = basename($filepath);
    //     $arxFile->File = file_get_contents($filepath);
    // $profileBase->File = $arxFile;
    // $dmProfileResult = $ARX_Workflow->Dm_Profile_Insert($sessionid, $idTaskWork, $idTaskDoc, $profileBase, ARX_Workflow\Dm_TaskDoc_ProfileMode::Normale);


    // preparo il profilo per l'operazione
    $profileInsertMv = $ARX_Workflow->Dm_Profile_Insert_MV_Get_New_Instance_By_DmTaskDocId($sessionid, $idTaskWork, $idTaskDoc, ARX_Workflow\Dm_TaskDoc_ProfileMode::Normale);
    $profileBase = $profileInsertMv->DmProfileDefault->Dm_Profile_Insert_Base;

    // qui va valorizzato il profilo da inserire...
    $profileBase->InOut = ARX_Workflow\DmProfileInOut::Interno;
    $profileBase->Stato = "VALIDO";
    $profileBase->Aoo = "1";
    $profileBase->ProtocolloInterno = "123456";

    $this->arxDebug($files);
    foreach ($files as $key => $value) {
      $profileBase->DocName = basename($value);
      // $filepath = $basepath.$profileBase->DocName;
      $filepath = $basepath.trim($value, ".");
      $arxFile = new ARX_Dati\Arx_File();
      $arxFile->CreationDate = date("c", filectime($filepath));
      $arxFile->FileName = basename($filepath);
      $arxFile->File = file_get_contents($filepath);
      $profileBase->File = $arxFile;
      // $ARX_Documenti->Dm_AllegatiDoc_Insert_Document($sessionid, $idTaskWork, $arxFile);
      // $dmProfileResult = $ARX_Workflow->Dm_Profile_Insert($sessionid, $idTaskWork, $idTaskDoc, $profileBase, ARX_Workflow\Dm_TaskDoc_ProfileMode::Normale);
      $dmProfileResult = $ARX_Workflow->Dm_Profile_Insert($sessionid, $idTaskWork, $idTaskDoc, $profileBase, ARX_Workflow\Dm_TaskDoc_ProfileMode::Normale, $arxFile);
      $this->arxDebug($dmProfileResult);
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


  public function loginArxivarNext(){
    /* ARXivarNext */
    $ARX_Login = new ARX_Login\ARX_Login($this->baseUrl."ARX_Login.asmx?WSDL");
    // esecuzione login
    $logonRequest = new \ARX_Login\ArxLogonRequest();
    $logonRequest->Username = $this->getUsername();
    $logonRequest->Password = $this->getPassword();
    // specificare qui Client ID e Client Secret configurati nel portale Authentication
    $logonRequest->ClientId = $this->softwareName;
    $logonRequest->ClientSecret = $this->softwareNameSecret;
    $logonRequest->EnablePushEvents = FALSE;
    $this->loginResult = $ARX_Login->Login($logonRequest);
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

      $userC = $ARX_Login->GetInfoUserConnected($this->sessionid);
      $aoo = $userC->AOO;
      $ARX_Dati = new ARX_Dati\ARX_Dati($this->baseUrl."ARX_Dati.asmx?WSDL");
      $userB = $ARX_Dati->Dm_Gruppi_GetData_By_Utente($this->sessionid, $userC->UTENTE);
      $gruppo = $userB->Dm_Gruppi->GRUPPO;

      $this->logoutArxivar(); //rilascio la sessione per nuovi login
      $this->arxLog('Logout e registrazione');
      $this->registerSessionLogin($aoo, $gruppo);
      return true;
    }else{
      $this->arxLog(' Login fallito ');
      $this->arxLog($this->loginResult->ArxLogOnErrorTypeString);
      $this->logError=$this->loginResult->ArxLogOnErrorTypeString;
      return false;
    }
  }

  public function loginArxivarImpersonate(){
    $this->loginArxivarServizio();
    $sessionid = $this->loginResult->SessionId;
    $code = $this->post("code", false);

    $str="SELECT * FROM DM_RUBRICA R INNER JOIN DM_UTENTI U ON R.CONTATTI = U.DESCRIPTION
    WHERE R.PARTIVA = :partiva ";
    $this->queryPrepare($str);
    $this->queryBind("partiva", $code);
    $this->executeQuery();
    $row = $this->fetch();

    $ARX_Login = new ARX_Login\ARX_Login($this->baseUrl."ARX_Login.asmx?WSDL");
    $impersonate = $ARX_Login->Impersonate_By_UserName($sessionid, $row["DESCRIPTION"]);
    $this->arxDebug($impersonate);
    $userI = $ARX_Login->GetInfoUserImpersonated($sessionid);
    $aoo = $userI->AOO;
    $userIA = $ARX_Dati->Dm_Gruppi_GetData_By_Utente($this->sessionid, $userI->UTENTE);
    $gruppo = $userB->Dm_Gruppi->GRUPPO;
    $ARX_Login->DeImpersonate($sessionid);
    $this->logoutArxivar();

    // $this->registerSessionLogin($aoo, $gruppo, true);
    return true;


    // $select->DM_RUBRICA_SYSTEM_ID->Selected = true;
    // $agg->Operatore = ARX_Search\Dm_Base_Search_Operatore_String::Uguale;
    // $agg->Valore = $docnumber;
    // $select->Dm_Rubrica->PARTIVA.SetFilter(Dm_Base_Search_Operatore_String.Uguale, "PARTITA IVA");
    //
    //                 //eseguo la search
    //                 using (Arx_DataSource dmRubricaAzienda = _manager.ARX_SEARCH.Dm_Contatti_GetData(searchRubrica, selectRubrica))
    //                 {
    //                     //ho trovato il contatto in rubrica
    //                     if (dmRubricaAzienda.TableCount == 1 && dmRubricaAzienda.RowsCount(0) > 0)
    //                     {
    //                         //int systemid contatto
    //                         //CI SONO RUBRICHE CON QUESTA PARTITA IVA
    //                     }
    //                 }
    //
    //             }
    // $result = $ARX_Search->Dm_Contatti_GetData($sessionid, $select, $search);
    // $this->arxDebug($result);

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

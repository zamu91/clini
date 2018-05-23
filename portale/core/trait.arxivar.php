<?php
trait arxivar{
  public $baseUrl = "http://192.168.50.250:81/";
  public $adminUser = "Admin";
  public $adminPass = "123";
  public $maskid = array("d2c91aab4a35489489675dd9fa831087", "1361f4e97b394cc39aa29fcbb2936a47", "0a326382877e4cb8a7369f2f43082815", "0c6d4ba528d34adcabda6705de8965f8","d34fde6fae8441ccaebca26fd9b8653c");
  public $maskDefaultTipoVal = array("RCA", "RCT", "PP", "L210","CTP");
  public $softwareName = "PHP Gestione cliniche";
  public $softwareNameSecret ="035518E483DE4436";
  private $loginResult;
  private $isLoginArxivar;
  private $arxVer = 5;
  private $gruppoPatrocinatori = 27;
  private $logError;

  public function getFieldListMascheraRicherca(){
    //ob_start();

    try{
      $this->loginArxivarServizio();
      $ARX_Search = new ARX_Search\ARX_Search($this->baseUrl."ARX_Search.asmx?WSDL");
      $sessionid = $this->loginResult->SessionId;
      $search = $ARX_Search->Dm_Profile_Search_Get_New_Instance_By_TipiDocumentoCodice($sessionid, "GEST.POS");
      $campidaesporre=array("COMBO15_297","TESTO10_297","TESTO13_297","CHECK17_1","TESTO14_297","TESTO12_297");
      foreach ($search->Aggiuntivi->Field_Abstract as $agg) {
        if(in_array($agg->Nome,$campidaesporre)) {?>
          <div class="fieldBox">
            <label><?php echo $agg->Label; ?></label><br>
            <input type="text" id="<?php echo $agg->Nome ?>" name="<?php echo $agg->Nome ?>" class="input" data-name="<?php echo $agg->Nome ?>" />
          </div>
        <?php  }
      }
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
      die;
    }
    $this->logoutArxivar();
    //  return ob_get_clean();
  }

  public function getFieldFromMascheraUpload(){
    ob_start();

    $this->loginArxivarServizio();
    $sessionid = $this->loginResult->SessionId;
    $maskix = $this->post("maskix", false);

    $ARX_Dati  = new ARX_Dati\ARX_Dati($this->baseUrl."ARX_Dati.asmx?WSDL");
    $profileMv = $ARX_Dati->Dm_Profile_Insert_MV_GetNewInstance_From_DmMaskId($sessionid, $this->maskid[$maskix]);
    $profile = $profileMv->DmProfileDefault->Dm_Profile_Insert_Base;

    $tipo_valutazione = $ARX_Dati->Dm_CampiSpecifici_GetValues($sessionid, "COMBO15_297",$profile,null);
    $cognome = $ARX_Dati->Dm_CampiSpecifici_GetValues($sessionid, "TESTO10_297",$profile,null);
    $nome = $ARX_Dati->Dm_CampiSpecifici_GetValues($sessionid, "TESTO13_297",$profile,null);

    ?>
    <span class="btn btn-primary fileinput-button">
      <i class="glyphicon glyphicon-plus"></i>
      <span>Seleziona i file...</span>
      <input id="fileupload" type="file" name="files[]" multiple>
    </span>
    <br>
    <br>
    <div id="progress" class="progress">
      <div class="progress-bar progress-bar-success"></div>
    </div>
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
    $maskix = $this->post("maskix", false);

    $ses = $this->checkExistSessionFromToken();
    $abiFile = ( $ses["IMPERSONATE"] == '1') ? false : true;

    try
    {
      $masks = $ARX_Dati->Dm_MaskGetData($sessionid);
      $profileMv = $ARX_Dati->Dm_Profile_Insert_MV_GetNewInstance_From_DmMaskId($sessionid, $this->maskid[$maskix]);
      $mask_data = $ARX_Dati->Dm_MaskDetail_GetData_By_DmMaskId($sessionid, $this->maskid[$maskix]);
      $details = $profileMv->DmMaskDetails->Dm_MaskDetail;
      $profile = $profileMv->DmProfileDefault->Dm_Profile_Insert_Base;
      $docType=$profile->DocumentType;
      $Tipo2=$profile->Tipo2;
      $Tipo3=$profile->Tipo3;
      $Aoo=$profile->Aoo;
      $id_td=array($docType,$Tipo2,$Tipo3);
      $lista_campi_specifici= $ARX_Dati->Dm_CampiSpecifici_GetData($sessionid,$Aoo,$id_td,false,"");
      ?>
      <form id="formMaschera" action="#" onsubmit="function(){ return false; }" method="post">
        <fieldset>
          <legend></legend>
          <?php
          foreach ($details as $field) {

            switch ($field->FIELD_KIND) {
              case ARX_Dati\Dm_MaskDetail_FieldKind::From:
              echo "mittente<br />";
              break;

              case ARX_Dati\Dm_MaskDetail_FieldKind::To:
              echo "destinatario<br />";
              break;

              case ARX_Dati\Dm_MaskDetail_FieldKind::Oggetto:
              ?>
              <label for="oggetto">Oggetto</label>
              <input type="text" name="oggetto" id="oggetto" value=""><br>
              <?php
              break;

              case ARX_Dati\Dm_MaskDetail_FieldKind::Aggiuntivo:
              $aggiuntivo = $ARX_Dati->Dm_CampiSpecifici_GetValues($sessionid,$field->FIELD_ID,$profile,null);//, combovalue, null);
              $label='';
              $alias='';
              $required=false;

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

              // $primaDisp =

              ?>
              <?php if( $aggiuntivo->NomeCampo != "TESTO22_2" ){ ?>
                <label for="<?php echo $aggiuntivo->NomeCampo; ?>"><?php echo $label; ?><?php if($required) echo "*";?></label>
              <?php } ?>
              <?php
              $tipo=$aggiuntivo->Classe;
              if($tipo=='Textbox'){
                if(strpos($alias,'(HH:MM)')>0){
                  ?>
                  <input type="text" name="<?php echo $aggiuntivo->NomeCampo; ?>" class="input time" id="<?php echo $aggiuntivo->NomeCampo; ?>" value="" maxlength="5" <?php if($required) echo "required";?>><br />
                  <?php
                }elseif($row_count>1){
                  ?>
                  <textarea name="<?php echo $aggiuntivo->NomeCampo; ?>" class="textarea" id="<?php echo $aggiuntivo->NomeCampo; ?>" maxlength="<?php echo $n_char; ?>" <?php if($required) echo "required";?>></textarea>
                  <br /><?php
                }else{
                  if( $aggiuntivo->NomeCampo != "TESTO22_2" ){ ?>
                    <input type="text" name="<?php echo $aggiuntivo->NomeCampo; ?>" class="input" id="<?php echo $aggiuntivo->NomeCampo; ?>" maxlength="<?php echo $n_char; ?>" value="" <?php if($required) echo "required";?>><br />
                  <?php }
                }
              } elseif($tipo=='Combobox'){
                $xmlDoc = new DOMDocument();
                $temp=$aggiuntivo->arxDataSource;
                $xml=simplexml_load_string($temp) or die("Error: Cannot create object");
                if( $aggiuntivo->NomeCampo == "COMBO19_1"){
                  ?>
                  <br>
                  <select id="<?php echo $aggiuntivo->NomeCampo; ?>" disabled name="<?php echo $aggiuntivo->NomeCampo; ?>" class="select" <?php if($required) echo "required";?>>
                    <?php
                    // ciclo sui tag ricerca per creare i valori option del campo select html
                    foreach ($xml as $row) { ?>
                      <?php  $selected = ( $ses["AOO"]."\\".$ses["USERNAME"] == $row->UTENTE) ? "selected" : ""; ?>
                      <option value="<?php echo $row->UTENTE; ?>" <?php echo $selected; ?> ><?php echo $row->NOMINATIVO; ?></option>
                    <?php } ?>
                  </select><br />
                  <?php
                } elseif ($aggiuntivo->NomeCampo == "COMBO20_2")  {
                  ?>
                  <br>
                  <select id="<?php echo $aggiuntivo->NomeCampo; ?>" name="<?php echo $aggiuntivo->NomeCampo; ?>" disabled class="select" <?php if($required) echo "required";?>>
                    <?php
                    // ciclo sui tag ricerca per creare i valori option del campo select html
                    $disp = $this->getPrimaDisp();
                    foreach ($xml as $row) { ?>
                      <?php  $selected = ( $disp["IDAMBULATORIO"] == $row->IDAMBULATORIO ) ? "selected" : ""; ?>
                      <option value="<?php echo $row->IDAMBULATORIO; ?>" <?php echo $selected; ?> ><?php echo $row->AMBULATORIO; ?></option>
                    <?php } ?>
                  </select><br />
                  <?php
                } else {
                  ?>
                  <br>
                  <select id="<?php echo $aggiuntivo->NomeCampo; ?>" disabled name="<?php echo $aggiuntivo->NomeCampo; ?>" class="select" <?php if($required) echo "required";?>>
                    <?php
                    // ciclo sui tag ricerca per creare i valori option del campo select html
                    foreach ($xml as $row) { ?>
                      <?php $selected = ($this->maskDefaultTipoVal[$maskix] == $row->ELEMENTO) ? "selected" : ""; ?>
                      <option value="<?php echo $row->ELEMENTO; ?>" <?php echo $selected; ?> ><?php echo $row->ELEMENTO; ?></option>
                    <?php } ?>
                  </select><br />
                  <?php
                }
              } elseif($tipo=='Checkbox'){
                ?>
                <input type="checkbox" id="<?php echo $aggiuntivo->NomeCampo; ?>" name="<?php echo $aggiuntivo->NomeCampo; ?>" <?php if($required) echo "required";?> /><br />
                <?php
              } elseif( $tipo == 'Databox' ){
                $defVal = "";
                $datapost = $this->post("data", false);
                if($aggiuntivo->NomeCampo == "DATA21_2" && !empty($datapost)) {
                  // $app = explode("/", $_POST["data"]);
                  // $defVal = $app[2]."-".$app[1]."-".$app[0];
                  $defVal = $datapost;
                }
                ?>
                <input type="text" disabled id="<?php echo $aggiuntivo->NomeCampo; ?>" name="<?php echo $aggiuntivo->NomeCampo; ?>" class="input" <?php if($required) echo "required";?> value="<?php echo $defVal; ?>" /><br />
                <?php
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
          ?>
        </fieldset>
      </form>

      <?php if($abiFile) { ?>
        <br><br>
        <input type="hidden" id="maskix" value="<?php echo $maskix; ?>" />
        <span class="btn btn-primary fileinput-button">
          <i class="glyphicon glyphicon-plus"></i>
          <span>Seleziona i file...</span>
          <input id="fileupload" type="file" name="files[]" multiple>
        </span>
        <br>
        <br>
        <div id="progress" class="progress">
          <div class="progress-bar progress-bar-success"></div>
        </div>
        <div id="files" class="files"></div>
      <?php } ?>

      <div id="requestResult"></div>
      <?php

    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }

    $this->logoutArxivar();
    echo ob_get_clean();
    die();
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
    // $this->arxDebug($_POST);

    $tipoValutazione = $this->post("tipoValutazione", false);
    $cognome = $this->post("cognome", false);
    $nome = $this->post("nome", false);
    $deceduto = $this->post("deceduto", false);
    $telefono = $this->post("telefono", false);
    $mail = $this->post("mail", false);

    foreach ($search->Aggiuntivi->Field_Abstract as $agg) {
      if($agg->Nome == "COMBO15_297" && !empty($tipoValutazione) ){
        $agg->Operatore = ARX_Search\Dm_Base_Search_Operatore_String::Contiene;
        $agg->Valore = $tipoValutazione;
      }
      if($agg->Nome == "TESTO10_297" && !empty($cognome) ){
        $agg->Operatore = ARX_Search\Dm_Base_Search_Operatore_String::Contiene;
        $agg->Valore = $cognome;
      }
      if($agg->Nome == "TESTO13_297" && !empty($nome) ){
        $agg->Operatore = ARX_Search\Dm_Base_Search_Operatore_String::Contiene;
        $agg->Valore = $nome;
      }
      if($agg->Nome == "CHECK17_1" && !empty($deceduto) ){
        $agg->Operatore = ARX_Search\Dm_Base_Search_Operatore_String::Contiene;
        $agg->Valore = $deceduto;
      }
      if($agg->Nome == "TESTO14_297" && !empty($telefono) ){
        $agg->Operatore = ARX_Search\Dm_Base_Search_Operatore_String::Contiene;
        $agg->Valore = $telefono;
      }
      if($agg->Nome == "TESTO12_297" && !empty($mail) ){
        $agg->Operatore = ARX_Search\Dm_Base_Search_Operatore_String::Contiene;
        $agg->Valore = $mail;
      }
    }
    $campidaesporre=array("COMBO15_297","TESTO10_297","TESTO13_297","CHECK17_1","TESTO14_297","TESTO12_297");
    $campidaesporreTitoli=array("Tipo Valutazione","Cognome","Nome","Deceduto","Telefono di contatto","Mail di Contatto");
    $select->DOCNUMBER->Selected = true;
    $select->DATADOC->Selected = true;
    $select->STATO->Selected = true;
    foreach ($select->Aggiuntivi->Aggiuntivo_Selected as $agg) {
      if (in_array($agg->Nome,$campidaesporre))
      {
        $agg->Selected = TRUE;
      }
    }

    $result = $ARX_Search->Dm_Profile_GetData($sessionid, $select, $search);
    $ds = simplexml_load_string($result);
    ?>
    <table id="tabProfili" class="table is-fullwidth is-hoverable clickable">
      <thead>
        <tr>
          <th>DOCNUMBER</th>
          <th>STATO</th>
          <th>DATA DOCUMENTO</th>
          <?php
          foreach ($campidaesporreTitoli as $item) { ?>
            <th><?php echo $item; ?></th>
          <?php } ?>
        </tr>
      </thead>
      <tbody>
        <?php
        foreach ($ds->Ricerca as $row) { ?>
          <tr data-task="<?php echo $row->DOCNUMBER; ?>" onclick="dettagliTaskProfilo(this);">
            <td><?php echo $row->DOCNUMBER; ?></td>
            <td><?php echo $row->STATO; ?></td>
            <td><?php echo $row->DATADOC; ?></td>
            <?php
            foreach ($campidaesporre as $item) { ?>
              <td><?php echo $row->$item; ?></td>
            <?php } ?>
          </tr>
        <?php } ?>
      </tbody>
    </table>
    <?php

    $this->logoutArxivar();
    die();
  }

  public function getTaskworkFromDocnumber(){
    $this->loginArxivarServizio();

    ini_set('xdebug.var_display_max_depth', -1);
    ini_set('xdebug.var_display_max_children', -1);
    ini_set('xdebug.var_display_max_data', -1);

    $sessionid = $this->loginResult->SessionId;
    $docnumber = $this->post("docnumber", false);
    $ARX_Search = new ARX_Search\ARX_Search($this->baseUrl."ARX_Search.asmx?WSDL");
    $searchTask = $ARX_Search->Dm_TaskWork_Search_GetNewInstance($sessionid);
    $searchTask->Dm_Profile->DOCNUMBER->Operatore = \ARX_Search\Dm_Base_Search_Operatore_Numerico::Uguale;
    $searchTask->Dm_Profile->DOCNUMBER->Valore = $docnumber;
    $searchTask->STATO->Operatore = \ARX_Search\Dm_Base_Search_Operatore_Numerico::Uguale;
    $searchTask->STATO->Valore = 1;
    $searchTask->TIPOTASK->Operatore = \ARX_Search\Dm_Base_Search_Operatore_Numerico::Uguale;
    $searchTask->TIPOTASK->Valore = 1;
    $searchTask->NOMETASK->Operatore = \ARX_Search\Dm_Base_Search_Operatore_Numerico::Uguale;
    $searchTask->NOMETASK->Valore = "99 - Inserimento Documentazione";
    $selectTask = $ARX_Search->Dm_TaskWork_Select_GetNewInstance($sessionid);
    $selectTask->ID->Selected = TRUE;
    $result = $ARX_Search->Dm_TaskWork_GetData($sessionid, $selectTask, $searchTask);
    $this->logoutArxivar();
    // var_dump($result);
    // die;
    $ds = simplexml_load_string($result);
    var_dump($ds);
    $taskwork = (string)$ds->Ricerca->ID;
    var_dump($taskwork);
    $this->setJsonMess("res", true);
    $this->setJsonMess("taskwork", $taskwork);
    $this->halt();
  }

  public function listaDocumenti(){
    $this->loginArxivarServizio();
    $sessionid = $this->loginResult->SessionId;
    $ARX_Dati = new ARX_Dati\ARX_Dati($this->baseUrl."ARX_Dati.asmx?WSDL");
    $ARX_Search = new ARX_Search\ARX_Search($this->baseUrl."ARX_Search.asmx?WSDL");

    $docnumber = $this->post("docnumber", false);
    $searchDocmed = $ARX_Search->Dm_Profile_Search_Get_New_Instance_By_TipiDocumentoCodice($sessionid, "GEST.DOCMED");
    $selectDocmed = $ARX_Search->Dm_Profile_Select_Get_New_Instance_By_TipiDocumentoCodice($sessionid, "GEST.DOCMED");
    foreach ($searchDocmed->Aggiuntivi->Field_Abstract as $agg) {
      if ($agg->Nome == "NUMERIC16_299") {
        $agg->Operatore = ARX_Search\Dm_Base_Search_Operatore_String::Uguale;
        $agg->Valore = $docnumber;
      }
    }
    $selectDocmed->DOCNUMBER->Selected = true;
    $selectDocmed->DOCNAME->Selected = true;
    $selectDocmed->DATADOC->Selected = true;
    $selectDocmed->CREATION_DATE->Selected = true;
    $selectDocmed->FILESIZE->Selected = true;
    $selectDocmed->STATO->Selected = true;
    $result = $ARX_Search->Dm_Profile_GetData($sessionid, $selectDocmed, $searchDocmed);
    $ds = simplexml_load_string($result);

    ?>
    <table id="tableFileDoc" class="table is-fullwidth is-hoverable clickable">
      <thead>
        <tr>
          <th>DOCNUMBER</th>
          <th>OGGETTO</th>
          <th>DATA DOCUMENTO</th>
        </tr>
      </thead>
      <tbody>
        <?php
        foreach ($ds->Ricerca as $row) { ?>
          <tr data-doc="<?php echo $row->DOCNUMBER; ?>" onclick="selezionaDocumento(this);" >
            <td><?php echo $row->DOCNUMBER; ?></td>
            <td><?php echo $row->DOCNAME; ?></td>
            <td><?php echo $row->DATADOC; ?></td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
    <?php
    $this->halt();
  }

  public function scriviDatiProfilo(){
    $this->loginArxivarServizio();
    try {

      $ses = $this->checkExistSessionFromToken();
      if( $ses["IMPERSONATE"] == '0' ){
        $this->logoutArxivar();
        $this->loginArxivarServizio( $ses["USERNAME"], $ses["PASSWORD"] );
        $sessionid = $this->loginResult->SessionId;
      } else {
        $sessionid = $this->loginResult->SessionId;
        // $str="SELECT * FROM DM_RUBRICA R INNER JOIN DM_UTENTI U ON R.CONTATTI = U.DESCRIPTION
        // WHERE R.PARTIVA = :partiva ";
        // $this->queryPrepare($str);
        // $this->queryBind("partiva", $ses["PARTIVA"]);
        // $this->executeQuery();
        // $row = $this->fetch();

        $ARX_Login = new ARX_Login\ARX_Login($this->baseUrl."ARX_Login.asmx?WSDL");
        $impersonate = $ARX_Login->Impersonate_By_UserName($sessionid, $ses["USERNAME"]);
        // var_dump($impersonate);
      }



      $ARX_Dati = new ARX_Dati\ARX_Dati($this->baseUrl."ARX_Dati.asmx?WSDL");
      $maskix = $this->post("maskix", false);
      $files = $this->post("files", false);


      $profileMv = $ARX_Dati->Dm_Profile_Insert_MV_GetNewInstance_From_DmMaskId($sessionid, $this->maskid[$maskix]);
      $details = $profileMv->DmMaskDetails->Dm_MaskDetail;
      $profile = $profileMv->DmProfileDefault->Dm_Profile_Insert_Base;
      $primaDisp = $this->getPrimaDisp();
      // $this->arxDebug($primaDisp);
      foreach ($details as $field) {

        switch ($field->FIELD_KIND) {
          case ARX_Dati\Dm_MaskDetail_FieldKind::From:
          echo "mittente<br />";
          break;

          case ARX_Dati\Dm_MaskDetail_FieldKind::To:
          echo "destinatario<br />";
          break;

          case ARX_Dati\Dm_MaskDetail_FieldKind::Oggetto:
          $profile->DocName = $_POST['oggetto'];
          break;

          case ARX_Dati\Dm_MaskDetail_FieldKind::Aggiuntivo:
          foreach ($profile->Aggiuntivi->Aggiuntivo_Base as $agg) {
            if(array_key_exists($agg->Nome,$_POST)){
              $agg->Valore=$_POST[$agg->Nome];
              /* FIXME: Al momento la data e l'ora della prenotazione viene inserita sul campo mail (TESTO12_297) e su telefono (TESTO14_297)
              * Questa sezione deve funzionare solamente con l'impersonate in quanto se è un utente normale è lecito che venga modificata in fase di inserimento.
              * DATA = DATA21_2    ORA = TESTO22_2   LUOGO = COMBO20_2    PATROCINATORE = COMBO29_1
              */
              /* Siamo nel caso di utente o in impersonate o patrocinatore al quale è associato il gruppo patrocinatori che ha il permesso di INSDOC
              * Valorizzare con l'utente di inserimento il campo patrocinatore. */
              // if( $ses["IMPERSONATE"] == 1 || $ses["INSDOC"] == 1){
                if( $agg->Nome == 'DATA21_2' ) {
                  $agg->Valore = $primaDisp["DATA"];
                }
                if( $agg->Nome == 'TESTO22_2' ){
                  $agg->Valore = $primaDisp["ORAINIZIO"];
                }
                if( $agg->Nome == 'COMBO20_2' ){
                  $agg->Valore = $primaDisp["IDAMBULATORIO"];
                }
                if( $agg->Nome == 'COMBO29_1' ){
                  $agg->Valore = $ses["AOO"]."\\".$ses["USERNAME"];
                }
              // }
            }
          }
          break;

          case ARX_Dati\Dm_MaskDetail_FieldKind::DataDoc:
          $profile->DataDoc = $_POST['DataDoc'];

          break;
        }
      }

      $profileForMask = new \ARX_Dati\Dm_Profile_Insert_For_Mask();
      $props = get_object_vars($profile);
      foreach ($props as $key => $value) {
        $profileForMask->$key = $value;
      }

      $profileForMask->DmMaskId = $profileMv->DmMask->ID;
      $profileForMask->Reason = \ARX_Dati\Dm_Mask_Type::Archiviazione;
      $profileForMask->DataFile = date("c");

      // $basepath = dirname($_SERVER['DOCUMENT_ROOT']);
      $basepath = "";
      $attach = array();
      if( !empty($files)){
        foreach ($files as $chiave => $valore) {
          $filepath = $basepath.trim($valore, ".");
          $arxFile = new ARX_Dati\Arx_File();
          $arxFile->CreationDate = date("c", filectime($filepath));
          $arxFile->FileName = basename($filepath);
          $arxFile->File = file_get_contents($filepath);
          $arxFile->DeleteFolderOnDisposeIfEmpty = FALSE;
          array_push($attach, $arxFile);
        }
      }

      $profileForMask->Attachments = $attach;

      if( !$primaDisp ){
        $this->setJsonMess("res", false);
        $this->setJsonMess("errorMessage", "Sembra che la tua prenotazione sia già stata presa da qualche altro cliente. Ritenta.");
      } else {
        $result = $ARX_Dati->Dm_Profile_Insert_For_Mask($sessionid, $profileForMask);
        if ($result->EXCEPTION == \ARX_Dati\Security_Exception::Nothing) {
          $this->setJsonMess("res", true);
          $this->setJsonMess("docnumber", $result->PROFILE->DOCNUMBER);
          // echo "Importazione completata con system ID: ".$result->PROFILE->DOCNUMBER."<hr/>";
          // if( $ses["IMPERSONATE"] == '1' ){
          /* Occupazione prenotazione */
          $this->segnaOccupato($result->PROFILE->DOCNUMBER);
          // }
        } else {
          $this->setJsonMess("res", false);
          $this->setJsonMess("errorMessage", $result->MESSAGE);
          // echo "Errore in fase di importazione: ".$result->EXCEPTION."; ".$result->MESSAGE."<hr/>";
        }
      }
    } catch (Exception $e) {
      $this->setJsonMess("res", false);
      $this->setJsonMess("errorMessage", $e->getMessage());
    }

    if( $ses["IMPERSONATE"] == '1' ){ $ARX_Login->DeImpersonate($sessionid); }
    $this->logoutArxivar();
    $this->halt();
  }

  public function scriviDocumentiProfilo(){
    $this->loginArxivarServizio();
    try{
      $ARX_Dati = new ARX_Dati\ARX_Dati($this->baseUrl."ARX_Dati.asmx?WSDL");
      $ARX_Workflow = new ARX_Workflow\ARX_Workflow($this->baseUrl."ARX_Workflow.asmx?WSDL");
      $sessionid = $this->loginResult->SessionId;
      $idTaskWork = $this->post("taskwork", false);
      $files = $this->post("files", false);
      // $basepath = dirname($_SERVER['DOCUMENT_ROOT']);
      $basepath = "";
      $dmTaskDocs = $ARX_Workflow->Dm_TaskDoc_GetData_By_DmTaskworkId($sessionid, $idTaskWork);
      $dmTaskDoc = $dmTaskDocs->Dm_TaskDoc;
      $idTaskDoc = $dmTaskDoc->ID;
      $profileInsertMv = $ARX_Workflow->Dm_Profile_Insert_MV_Get_New_Instance_By_DmTaskDocId($sessionid, $idTaskWork, $idTaskDoc, ARX_Workflow\Dm_TaskDoc_ProfileMode::Normale);
      $profileBase = $profileInsertMv->DmProfileDefault->Dm_Profile_Insert_Base;
      $profileBase->InOut = ARX_Workflow\DmProfileInOut::Interno;
      $profileBase->Stato = "VALIDO";
      $profileBase->Aoo = "1";
      $profileBase->ProtocolloInterno = "123456";
      foreach ($files as $chiave => $valore) {
        $profile = new ARX_Workflow\Dm_Profile_ForInsert();
        foreach (get_object_vars($profileBase) as $key => $value) {
          $profile->$key = $value;
        }
        $profile->DocName = basename($valore);
        $filepath = $basepath.trim($valore, ".");
        $arxFile = new ARX_Dati\Arx_File();
        $arxFile->CreationDate = date("c", filectime($filepath));
        $arxFile->FileName = basename($filepath);
        $arxFile->File = file_get_contents($filepath);
        $arxFile->DeleteFolderOnDisposeIfEmpty = FALSE;
        $profile->File = $arxFile;
        $profile->DataFile = date("c", filectime($filepath));
        $dmProfileResult = $ARX_Workflow->Dm_Profile_Insert($sessionid, $idTaskWork, $idTaskDoc, $profile, ARX_Workflow\Dm_TaskDoc_ProfileMode::Normale);
      }
      $this->setJsonMess("res", true);
    } catch (Exception $e) {
      $this->setJsonMess("res", false);
      $this->setJsonMess("errorMessage", $e->getMessage());
    }
    $this->logoutArxivar();
    $this->halt();
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

  private function loginArxivarServizio($username = "", $password = ""){
    if( $this->arxVer == 5){
      $ARX_Login = new ARX_Login\ARX_Login($this->baseUrl."ARX_Login.asmx?WSDL");
      $this->loginResult = $ARX_Login->Login($this->adminUser, $this->adminPass, $this->softwareName);
      if ($this->loginResult->ArxLogOnErrorType != Arx_Login\ArxLogOnErrorType::None){
        $this->arxLog("Logon Failed: ".$this->loginResult->ArxLogOnErrorType);
        die();
      }
    }else {
      /* ARXivarNext */
      $ARX_Login = new ARX_Login\ARX_Login($this->baseUrl."ARX_Login.asmx?WSDL");
      // esecuzione login
      $logonRequest = new \ARX_Login\ArxLogonRequest();
      if( !empty($username) ){ $logonRequest->Username = $username; }
      else { $logonRequest->Username = $this->adminUser; }

      if( !empty($password) ){ $logonRequest->Password = $password; }
      else { $logonRequest->Password = $this->adminPass; }
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
    if( $this->arxVer == 5 ){
      $ARX_Login = new ARX_Login\ARX_Login($this->baseUrl."ARX_Login.asmx?WSDL");
      $userName = $this->getUsername();
      $password = $this->getPassword();
      $this->loginResult = $ARX_Login->Login($userName, $password, $this->softwareName);
    } else {
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
    $this->arxLog('WCF chiamate');
    if( $this->loginResult->LoggedIn ){
      $this->arxLog('Login eseguito con successo');
      $this->sessionid = $this->loginResult->SessionId;
      $this->isLogin=true;

      $userC = $ARX_Login->GetInfoUserConnected($this->sessionid);
      $aoo = $userC->AOO;
      $ARX_Dati = new ARX_Dati\ARX_Dati($this->baseUrl."ARX_Dati.asmx?WSDL");
      $userB = $ARX_Dati->Dm_Gruppi_GetData_By_Utente($this->sessionid, $userC->UTENTE);
      $gruppi = $userB->Dm_Gruppi;
      $insDoc = 0;
      if(is_array($gruppi)){
        foreach ($gruppi as $key => $value) {
          if( $value->GRUPPO == $this->gruppoPatrocinatori ){ $insDoc = 1; }
        }
      } else {
        if( $gruppi->GRUPPO == $this->gruppoPatrocinatori ){ $insDoc = 1; }
      }

      $this->logoutArxivar(); //rilascio la sessione per nuovi login
      $this->arxLog('Logout e registrazione');
      $this->registerSessionLogin($aoo, $insDoc, 0);
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
    $this->partiva = $this->post("code", false);
    $code = $this->getPartiva();

    $str="SELECT * FROM DM_RUBRICA R INNER JOIN DM_UTENTI U ON R.CONTATTI = U.DESCRIPTION
    WHERE R.PARTIVA = :partiva ";
    $this->queryPrepare($str);
    $this->queryBind("partiva", $code);
    $this->executeQuery();
    $row = $this->fetch();

    $ARX_Login = new ARX_Login\ARX_Login($this->baseUrl."ARX_Login.asmx?WSDL");
    $impersonate = $ARX_Login->Impersonate_By_UserName($sessionid, $row["DESCRIPTION"]);

    $userI = $ARX_Login->GetInfoUserImpersonated($sessionid);
    // $this->arxDebug($userI);
    $aoo = $userI->AOO;
    $this->username = $userI->DESCRIPTION;
    $ARX_Dati = new ARX_Dati\ARX_Dati($this->baseUrl."ARX_Dati.asmx?WSDL");
    $userIA = $ARX_Dati->Dm_Gruppi_GetData_By_Utente($sessionid, $userI->UTENTE);
    $gruppi = $userIA->Dm_Gruppi;
    $insDoc = 0;
    if(is_array($gruppi)){
      foreach ($gruppi as $key => $value) {
        if( $value->GRUPPO == $this->gruppoPatrocinatori ){ $insDoc = 1; }
      }
    } else {
      if( $gruppi->GRUPPO == $this->gruppoPatrocinatori ){ $insDoc = 1; }
    }

    $ARX_Login->DeImpersonate($sessionid);
    $this->logoutArxivar();
    $this->registerSessionLogin($aoo, $insDoc, 1);
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

<?php
include_once '../template/head.php';
include_once '../admin/template/html.php';
//include_once '../core/class.chiamate.php';
//$chiamate->controlloArxLogin();
?>
<body>
  <script src="js/navigazione.js"></script>
  <script src="js/menu.js?v=1"></script>

  <div class="container">

    <div class="cercaClinica">
      <div class="radioOpzioni">
        <label><input type="radio" name="tipo" value="data" onclick="cercaClinica('data');" > Cerca Clinica per data</label>
        <label><input type="radio" name="tipo" value="posizione" checked onclick="cercaClinica('provincia');">Cerca Clinica per posizione</label>
      </div> <!-- radioOpzioni -->


      <div class="cercaPerData" style="display:none;" >
        <div class="inputDiv">
          <label for="dataCercaClinica">Data</label>
          <input type="edit" class="data input" id="dataCercaClinica">
        </div>
        <button class="button is_primary" onclick="cercaPerProvincia();">Cerca</button>
      </div> <!-- cerca per data -->

      <div class="cercaPerProv">
        <div class="inputDiv">
          <label for="clinica">Clinica</label>
          <select class="input" class="select" id="clinicaCerca">
          </select>
        </div>
        <button class="button is_primary" onclick="cercaPerClinica();">Cerca</button>
      </div>

      <div class="resultClinica">

      </div> <!-- end res Clinica-->


    </div><!-- fine cerca X clinica -->



    <div class="tipoPrenotazione" style="display:none;">

      <div class="columns">
        <?php divElement('<button class="button is-primary" data-maskix="0" onclick="apriProfiloImpersonate(this);">Responsabilità civile auto</button>',"Inserisci Codice patrocinatore","6"); ?>
        <?php divElement('<button class="button is-primary" data-maskix="0" onclick="apriProfiloImpersonate(this);">Responsabilità civile terzi</button>',"Inserisci Codice patrocinatore","6"); ?>
      </div>
      <div class="columns">
        <?php divElement('<button class="button is-primary" data-maskix="0" onclick="apriProfiloImpersonate(this);">Polizza privata infortuni</button>',"Inserisci Codice patrocinatore","6"); ?>
        <?php divElement('<button class="button is-primary" data-maskix="0" onclick="apriProfiloImpersonate(this);">Consulenza tecnica di parte</button>',"Inserisci Codice patrocinatore","6"); ?>
      </div>
    </div> <!-- end tipo prenotazione -->


    <div class="maschera">
    </div>

  </div><!-- end container -->
</body>

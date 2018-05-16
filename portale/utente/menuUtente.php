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
      <div class="radioOpzioni columns">
        <div class="column is-6">
          <button  name="tipo" class="button is-primary" onclick="cercaClinica('data');" >Cerca Clinica per data</button>
        </div>
        <div class="column is-6">
          <button name="tipo"  class="button is-primary" onclick="cercaClinica('provincia');">Cerca Clinica per posizione</button>
        </div>
      </div> <!-- radioOpzioni -->


      <div class="cercaPerData" style="display:none;" >
        <div class="columns">
          <?php divElement('<select  class="select" id="dataCerca">
          </select>',"Cerca per data","12"); ?>
        </div>

        <div class="columns">
          <div class="column is-6">
            <button onclick="cercaPerData();" class="button is-primary">Cerca</button>
          </div>
        </div>
      </div> <!-- cerca per data -->

      <div class="cercaPerProv">
        <div class="columns">
          <?php divElement('<select  class="select" id="clinicaCerca">
          </select>',"Cerca per luogo","12"); ?>
        </div>

        <div class="columns">
          <div class="column is-6">
            <button onclick="cercaPerClinica();" class="button is-primary">Cerca</button>
          </div>
        </div>
      </div> <!-- end cercaPerProv-->

      <div class="resultClinica">

      </div> <!-- end res Clinica-->


    </div><!-- fine cerca X clinica -->



    <div class="tipoPrenotazione" style="display:none;">

      <div class="columns">
        <?php divElement('<button class="button is-primary" data-maskix="0" onclick="apriProfiloImpersonate(this);">Responsabilità civile auto</button>',"","6"); ?>
        <?php divElement('<button class="button is-primary" data-maskix="1" onclick="apriProfiloImpersonate(this);">Responsabilità civile terzi</button>',"","6"); ?>
      </div>
      <div class="columns">
        <?php divElement('<button class="button is-primary" data-maskix="2" onclick="apriProfiloImpersonate(this);">Polizza privata infortuni</button>',"","6"); ?>
        <?php divElement('<button class="button is-primary" data-maskix="3" onclick="apriProfiloImpersonate(this);">Legge 210</button>',"","6"); ?>
      </div>
      <button class="button is-primary" onclick="indietroBottoni();">Indietro</button>
    </div> <!-- end tipo prenotazione -->


    <div class="mascheraContainer">
      <h2 id="mask-title"></h2>
      <div class="maschera">
      </div>
      <button class="is-primary button" onclick="scriviDatiProfiloImpersonate();">SALVA PROFILO</button>
      <button class="is-primary button" onclick="indietroSalva();">INDIETRO</button>

    </div>


  </div><!-- end container -->
  <script>
  $( document ).ready(function() {
    jd={};
    jd.azione='controlloTokenARXLogin';
    doAjax(jd,function(data){
      if(data.validToken){
        loadProvClinica();
        loadDataRic();
      }else{
        goIndexUtente();
      }
    });
  });
</script>


</body>
</html>

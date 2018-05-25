<?php
include_once '../template/head.php';
include_once '../admin/template/html.php';
?>
<body>
  <script src="js/navigazione.js"></script>
  <script src="js/menu.js?v=4"></script>

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
          <?php divElement('<select  class="select input" id="dataCerca">
          </select>',"Cerca per data","12"); ?>

        </div>

        <div class="columns">
          <div class="column is-6">
            <button onclick="cercaPerData();" class="button is-primary">Cerca</button>
          </div>
          <div class="column is-6">
            <button onclick="indietroCerca();" class="button is-primary">Indietro</button>
          </div>

        </div>
      </div> <!-- cerca per data -->

      <div class="cercaPerProv">
        <div class="columns">
          <?php divElement('<select class="select input" id="clinicaCerca">
          </select>',"Cerca per luogo","12"); ?>
        </div>

        <div class="columns">
          <div class="column is-6">
            <button onclick="cercaPerClinica();" class="button is-primary">Cerca</button>
          </div>
          <div class="column is-6">
            <button onclick="indietroCerca();" class="button is-primary">Indietro</button>
          </div>

        </div>
      </div> <!-- end cercaPerProv-->

      <div class="resultClinica">

      </div> <!-- end res Clinica-->


    </div><!-- fine cerca X clinica -->



    <div class="tipoPrenotazione" style="display:none;">

      <div class="columns">
        <?php divElement('<button class="button is-primary" data-maskix="0" onclick="apriProfilo(this);">Responsabilità civile auto</button>',"","6"); ?>
        <?php divElement('<button class="button is-primary" data-maskix="1" onclick="apriProfilo(this);">Responsabilità civile terzi</button>',"","6"); ?>
      </div>
      <div class="columns">
        <?php divElement('<button class="button is-primary" data-maskix="2" onclick="apriProfilo(this);">Polizza privata infortuni</button>',"","6"); ?>
        <?php divElement('<button class="button is-primary" data-maskix="3" onclick="apriProfilo(this);">Legge 210</button>',"","6"); ?>
      </div>
      <div class="columns">
        <?php divElement('<button class="button is-primary" data-maskix="4" onclick="apriProfilo(this);">Consulenza tecnica di parte</button>',"","6"); ?>
      </div>

      <button class="button is-primary" onclick="indietroBottoni();">Indietro</button>
    </div> <!-- end tipo prenotazione -->


    <div class="mascheraContainer">
      <h2 id="mask-title"></h2>
      <div class="maschera">
      </div>

      <div class="cmdIns">
        <button class="is-primary button" onclick="scriviDatiProfilo();">CONFERMA PRENOTAZIONE</button>
        <button class="is-primary button" onclick="indietroSalva();">INDIETRO</button>
      </div>
    </div>

  </div><!-- end container -->

  <script>


  $( document ).ready(function() {
    jd={};
    jd.azione='controlloTokenARXLogin';
    jd.impersonate=0;
    doAjax(jd,function(data){
      if(data.validToken){
        loadProvClinica();
        loadDataRic();
      }else{
        goIndexPatrocinatore();

      }
    });
  });
</script>


</body>
</html>

<?php
include_once '../template/head.php';
include_once '../admin/template/html.php';
//include_once '../core/class.chiamate.php';
//$chiamate->controlloArxLogin();
?>
<body>
  <script src="js/navigazione.js"></script>
  <script src="js/menu.js?v=2"></script>

  <div class="container">

    <div class="cercaClinica">
      <div class="radioOpzioni columns">
        <div class="column is-3">
          <button  name="tipo" class="button is-primary" onclick="cercaClinica('data');" >
            <span class="icon is-medium" style="margin-right:10px">
              <i class="fas fa-search-plus"></i>
            </span>
            Cerca Clinica per data
          </button>
        </div>
        <div class="column is-3">
          <button name="tipo"  class="button is-primary" onclick="cercaClinica('provincia');">
            <span class="icon is-medium" style="margin-right:10px">
              <i class="fas fa-search-plus"></i>
            </span>
            Cerca Clinica per posizione
          </button>
        </div>
      </div> <!-- radioOpzioni -->


      <div class="cercaPerData" style="display:none;" >
        <div class="columns">
          <?php divElement('<select  class="select input" id="dataCerca">
          </select>',"Cerca per data","4"); ?>
        </div>

        <div class="columns">
          <div class="column is-6">
            <button onclick="cercaPerData();" class="button is-primary">
              <span class="icon is-medium" style="margin-right:10px">
                <i class="fas fa-search-plus"></i>
              </span>
              Cerca
            </button>
          </div>
        </div>
      </div> <!-- cerca per data -->

      <div class="cercaPerProv">
        <div class="columns">
          <?php divElement('<select  class="select input" id="clinicaCerca">
          </select>',"Cerca per luogo","4"); ?>
        </div>

        <div class="columns">
          <div class="column is-6">
            <button onclick="cercaPerClinica();" class="button is-primary">
              <span class="icon is-medium" style="margin-right:10px">
                <i class="fas fa-search-plus"></i>
              </span>
              Cerca
            </button>
          </div>
        </div>
      </div> <!-- end cercaPerProv-->

      <div class="resultClinica">

      </div> <!-- end res Clinica-->


    </div><!-- fine cerca X clinica -->



    <div class="tipoPrenotazione" style="display:none;">

      <?php /*
      <div class="buttons">
        <?php divElement('<button class="button is-medium is-primary" data-maskix="0" onclick="apriProfiloImpersonate(this);">Responsabilità civile auto</button>',"","3"); ?>
        <?php divElement('<button class="button is-medium is-primary" data-maskix="1" onclick="apriProfiloImpersonate(this);">Responsabilità civile terzi</button>',"","3"); ?>
        <?php divElement('<button class="button is-medium is-primary" data-maskix="2" onclick="apriProfiloImpersonate(this);">Polizza privata infortuni</button>',"","3"); ?>
        <?php divElement('<button class="button is-medium is-primary" data-maskix="3" onclick="apriProfiloImpersonate(this);">Legge 210</button>',"","3"); ?>
      </div>
      */ ?>
      <div class="scelta-tipologia-btn">
          <div onclick='apriProfiloImpersonate(this);' class="single-t-btn" data-maskix="0">Responsabilità civile auto</div>
          <div onclick='apriProfiloImpersonate(this);' class="single-t-btn" data-maskix="1">Responsabilità civile terzi</div>
          <div onclick='apriProfiloImpersonate(this);' class="single-t-btn" data-maskix="2">Polizza privata infortuni</div>
          <!-- <div onclick='apriProfiloImpersonate(this);' class="single-t-btn" data-maskix="3">Legge 210</div>-->
          <div onclick='apriProfiloImpersonate(this);' class="single-t-btn" data-maskix="0" style="line-height:80px!important; padding:10px">Responsabilità civile Auto + Polizza infortuni</div>
      </div>

      <div class="level">
        <div class="level-left">
          <!-- vuoto -->
        </div>
        <div class="level-right">
          <button class="button is-primary" onclick="indietroBottoni();">
            <span class="icon is-medium" style="margin-right:10px">
              <i class="fas fa-undo-alt"></i>
            </span>
            Indietro
          </button>
        </div>
      </div>

    </div> <!-- end tipo prenotazione -->


    <div class="mascheraContainer">
      <h2 id="mask-title"></h2>
      <div class="maschera">
      </div>
      <div class="insCmd">

          <label class="check_gdpr">
            <input type="checkbox">
            Acconsento al trattamento dei dati <!-- <a href="#">terms and conditions</a> -->
          </label>

        <div class="level">
           <!--<div class="level-left">
            <button class="is-primary button" onclick="scriviDatiProfiloImpersonate();">
              <span class="icon is-medium" style="margin-right:10px">
              <i class="fas fa-check-circle"></i>
            </span>
            CONFERMA PRENOTAZIONE
          </button>
        </div>-->
          <div class="level-left">
            <button class="is-primary button" onclick="primaDataLibera();">
              <span class="icon is-medium" style="margin-right:10px">
              <i class="fas fa-check-circle"></i>
            </span>
            CONFERMA PRENOTAZIONE
          </button>
          </div>
          <div class="level-right">
            <button class="is-primary button" onclick="indietroSalva();">
              <span class="icon is-medium" style="margin-right:10px">
              <i class="fas fa-undo-alt"></i>
            </span>
            INDIETRO
          </button>
          </div>
        </div>
      </div>
    </div>


  </div><!-- end container -->
  <script>
  $( document ).ready(function() {
    jd={};
    jd.azione='controlloTokenARXLogin';
    jd.impersonate=1;
    doAjax(jd,function(data){
      if(data.validToken){
        loadProvClinica();
        setTimeout(function(){
          loadDataRic();
        },2000);

      }else{
        goIndexUtente();
      }
    });
  });
</script>


</body>
</html>

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
        <div class="level">
          <div class="column is-6 level-left">
            <button  name="tipo" class="button is-primary" onclick="cercaClinica('data');" >
              <span class="icon is-medium" style="margin-right:10px">
                <i class="fas fa-search-plus"></i>
              </span>
              Cerca Clinica per data
            </button>
          </div>
          <div class="column is-6 level-right">
            <button name="tipo"  class="button is-primary" onclick="cercaClinica('provincia');">
              <span class="icon is-medium" style="margin-right:10px">
                <i class="fas fa-search-plus"></i>
              </span>
            Cerca Clinica per posizione
          </button>
          </div>
        </div>
      </div> <!-- radioOpzioni -->


      <div class="cercaPerData" style="display:none;" >
        <div class="columns">
          <?php divElement('<select  class="select input" id="dataCerca">
          </select>',"Cerca per data","4"); ?>
        </div>
        <div class="level">
          <div class="level-left">
            <button onclick="cercaPerData();" class="button is-primary btn-prenota-bottom">
              <span class="icon is-medium" style="margin-right:10px">
                <i class="fas fa-search-plus"></i>
              </span>
              Cerca
            </button>
          </div>
          <div class="level-right">
            <button onclick="indietroCerca();" class="button is-primary btn-prenota-bottom">
              <span class="icon is-medium" style="margin-right:10px">
                <i class="fas fa-undo-alt"></i>
              </span>
            Indietro
          </button></button>
          </div>
        </div>
      </div> <!-- cerca per data -->

      <div class="cercaPerProv">
        <div class="columns">
          <?php divElement('<select class="select input" id="clinicaCerca">
          </select>',"Cerca per luogo","4"); ?>
        </div>

          <div class="level">
            <div class="level-left">
              <button onclick="cercaPerClinica();" class="button is-primary btn-prenota-bottom">
                <span class="icon is-medium" style="margin-right:10px">
                  <i class="fas fa-search-plus"></i>
                </span>
                Cerca
              </button>
            </div>
            <div class="level-right">
              <button onclick="indietroCerca();" class="button is-primary btn-prenota-bottom">
                <span class="icon is-medium" style="margin-right:10px">
                  <i class="fas fa-undo-alt"></i>
                </span>
                Indietro
              </button>
            </div>
          </div>
        </div>

      <div class="resultClinica">

      </div> <!-- end res Clinica-->


    </div><!-- fine cerca X clinica -->



    <div class="tipoPrenotazione" style="display:none;">
      <?php /*
      <div class="columns">
        <?php divElement('<button class="button is-large is-primary" data-maskix="0" onclick="apriProfilo(this);">Responsabilità civile auto</button>',"","4"); ?>
        <?php divElement('<button class="button is-large is-primary" data-maskix="1" onclick="apriProfilo(this);">Responsabilità civile terzi</button>',"","4"); ?>
        <?php divElement('<button class="button is-large is-primary" data-maskix="2" onclick="apriProfilo(this);">Polizza privata infortuni</button>',"","4"); ?>
      </div>
      <div class="columns">
        <?php divElement('<button class="button is-large is-primary" data-maskix="3" onclick="apriProfilo(this);">Legge 210</button>',"","4"); ?>
        <?php divElement('<button class="button is-large is-primary" data-maskix="4" onclick="apriProfilo(this);">Consulenza tecnica di parte</button>',"","4"); ?>
      </div>
      */ ?>

      <div class="scelta-tipologia-btn-a">
          <div onclick='apriProfilo(this);' class="single-t-btn" data-maskix="0">Responsabilità civile auto</div>
          <div onclick='apriProfilo(this);' class="single-t-btn" data-maskix="1">Responsabilità civile terzi</div>
          <div onclick='apriProfilo(this);' class="single-t-btn" data-maskix="2">Polizza privata infortuni</div>
          <!--<div onclick='apriProfilo(this);' class="single-t-btn" data-maskix="3">Legge 210</div>-->
          <div onclick='apriProfilo(this);' class="single-t-btn" data-maskix="0" style="line-height:40px!important; padding:65px">Responsabilità civile Auto + Polizza infortuni</div>
          <div onclick='apriProfilo(this);' class="single-t-btn" data-maskix="4">Consulenza tecnica di parte</div>
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

      <div class="cmdIns">

        <label class="check_gdpr">
          <input type="checkbox">
          Acconsento al trattamento dei dati <!-- <a href="#">terms and conditions</a> -->
        </label>

        <div class="level">
          <!--<div class="level-left">
            <button class="is-primary button" onclick="scriviDatiProfilo();">
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
          <!-- FINE -->
          <div class="level-right">
            <button class="is-primary button" onclick="indietroSalva();">
              <span class="icon is-medium" style="margin-right:10px">
                <i class="fas fa-undo-alt"></i>
              </span>INDIETRO
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

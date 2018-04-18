<?php
include_once 'template/head.php';
include_once 'template/header.php';
include_once 'template/html.php';
?>
<body>
  <?php
  getHeader("CONTRATTO","Inserimento di un nuovo contratto per ambulatorio");
  getSubHeader();
  ?>
  <div class="container">
    <div class="columns">
      <?php divElement('  <select class="input " id="idAmbulatorio" type="text"></select>',"Ambulatorio","4"); ?>
      <?php divElement('  <input class="input " placeholder="Data Inizio" id="dataInizio" type="text">',"Data Inizio","4"); ?>
      <?php divElement('  <input class="input " placeholder="Data Fine" id="dataFine" type="text">',"Data Fine","4"); ?>
    </div>




    <div class="columns">
      <?php divElement('  <input class="input " placeholder="Ora Inizio" id="oraInizio" type="text">',"Ora Inizio","3"); ?>
      <?php divElement('  <input class="input " placeholder="Ora Fine" id="oraInizio" type="text">',"Ora Fine","3"); ?>
      <?php divElement('  <input class="input " placeholder="Durata" id="durata" type="text">',"Durata","3"); ?>
      <?php divElement('  <select class="input "  id="verso" ><option value="0">Alto</option><option value="1">Basso</option></select>',"Verso","3"); ?>
    </div>


    <?php getGiorniDiv(); ?>


    <div class="columns">
      <div class="column is-6">
        <button class="button is-primary" onclick="salvaContratto();">Salva Contratto</button>
      </div>

      <div class="column is-6">
        <button class="button is-primary" onclick="tornaMenu();">TORNA A MENÙ</button>
      </div>

    </div> <!-- end tasti -->




  </div> <!-- conteiner -->


  <script src="js/contratto.js"></script>
  
</body>
</html>
<?php

if(1==2){
  ?>


  <div id="insContratto">
    <div class="inputDiv">
      <label for="idAmbulatorio">Ambulatorio</label>
      <select class="dataProc input" id="idAmbulatorio">
        <option value="4">test</option>
      </select>
    </div>

    <div class="inputDiv">
      <label for="dataInzio">Data inizio</label>
      <input class="dataProc input" type="edit" id="dataInizio">
    </div>

    <div class="inputDiv">
      <label for="dataFine">Data Fine</label>
      <input type="edit" class="input" id="dataFine">
    </div>


    <div class="inputDiv">
      <label for="oraInizio">Ora Inizio</label>
      <input type="edit" class="input" id="oraInizio">
    </div>

    <div class="inputDiv">
      <label for="oraFine">Ora Fine</label>
      <input type="edit" class="dataProc input" id="oraFine">
    </div>

    <div class="inputDiv">
      <label for="durata">Durata</label>
      <input type="edit" class="durata input" id="durata">
    </div>



  </div> <!-- giorni contratto -->

  <?php

}


function getGiorni(){
  ?>


  <div class="giorno">
    <label for="lun">Lunedì</label>
    <input type="checkbox" id="lun">
  </div>

  <div class="giorno">
    <label for="mar">Martedì</label>
    <input type="checkbox" id="mar">
  </div>

  <div class="giorno">
    <label for="mer">Mercoledì</label>
    <input type="checkbox" id="mer">
  </div>

  <div class="giorno">
    <label for="gio">Giovedì</label>
    <input type="checkbox" id="giov">
  </div>

  <div class="giorno">
    <label for="ven">Venerdì</label>
    <input type="checkbox" id="ven">
  </div>

  <div class="giorno">
    <label for="sab">Sabato</label>
    <input type="checkbox" id="sab">
  </div>


  <?php


}


function getGiorniDiv(){
  ?>

  <div class="columns">
    <div class="column is-12">
      <div class="card events-card">
        <header class="card-header">
          <p class="card-header-title">
            Giorni del contratto
          </p>
          <a href="#" class="card-header-icon" aria-label="more options">
            <span class="icon">
              <i class="fa fa-angle-down" aria-hidden="true"></i>
            </span>
          </a>
        </header>
        <div class="content">

          <?php getGiorni(); ?>

        </div> <!-- end content -->
      </div>

    </div> <!-- 12 -->
  </div> <!-- end columns giorni -->

  <?php


}

?>

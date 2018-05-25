<?php
include_once 'template/head.php';
include_once 'template/header.php';
include_once 'template/html.php';
?>
<body>
  <?php
  //  getHeader("Ambulatorio","Inserimento di un nuovo ambulatorio");
  //  getSubHeader();
  ?>
  <div class="container">
    <div class="columns">
      <?php divElement('  <input class="input " placeholder="nomeAmbulatorio" id="nomeAmbulatorio" type="text">',"Nome Ambulatorio","6"); ?>
      <?php divElement('  <input class="input " placeholder="provincia" id="provinciaAmbulatorio" type="text">',"Provincia Ambulatorio","6"); ?>

    </div>

    <div class="columns">

      <?php divElement('  <input class="input " placeholder="comune" id="comuneAmbulatorio" type="text">',"Comune Ambulatorio","6"); ?>
      <?php divElement('  <input class="input " placeholder="indirizzo" id="indirizzoAmbulatorio" type="text">',"Indirizzo Ambulatorio","6"); ?>
    </div>

    <div class="level">
      <div class="level-left">
        <button class="button is-primary" onclick="salvaAmbulatorio();">
          <span class="icon is-medium" style="margin-right:10px">
            <i class="fas fa-check-circle"></i>
          </span>
          SALVA AMBULATORIO
        </button>
      </div>
      <div class="level-right">
        <button class="button is-primary" onclick="tornaMenu();">
          <span class="icon is-medium" style="margin-right:10px">
            <i class="fas fa-undo-alt"></i>
          </span>
          TORNA A MENÙ
        </button>
      </div>

    </div>



    <div id="tabellaAmbulatori" class="colimns"></div>

  </div> <!-- end columns -->


  <script>
  $( document ).ready(function() {
    getAmbulatoriInseriti();
  });
  </script>


</body>
</html>

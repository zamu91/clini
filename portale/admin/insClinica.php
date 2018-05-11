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

    <div class="columns">
      <div class="column is-6">
        <button class="button is-primary" onclick="salvaAmbulatorio();">SALVA AMBULATORIO</button>
      </div>
      <div class="column is-6">
        <button class="button is-primary" onclick="tornaMenu();">TORNA A MENÙ</button>
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

?>

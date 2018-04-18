<?php
include_once 'template/head.php';
include_once 'template/header.php';
include_once 'template/html.php';
?>
<body>
  <?php
  getHeader("Ambulatorio","Inserimento di un nuovo ambulatorio");
  getSubHeader();
  ?>
  <section class="container">
    <div class="columns">

      <div class="column is-6">
          <?php divElement('  <input class="input is-large" placeholder="" id="nomeAmbulatorio" type="text">',"Nome Ambulatorio"); ?>
      </div>



    </div> <!-- end sandbox -->


    <div id="insClinica">
      <div class="inputDiv">
        <label for="nomeAmbulatorio">Nome Ambulatorio</label>
        <input class="input" type="edit" id="nomeAmbulatorio">
      </div>

      <div class="inputDiv">
        <div>
          <label for="provinciaAmbulatorio">Provincia</label>
          <input class="input" type="edit" id="provinciaAmbulatorio">
        </div>
      </div>

      <div class="inputDiv">
        <div>
          <label for="comuneAmbulatorio">Comune</label>
          <input class="input" type="edit" id="comuneAmbulatorio">
        </div>
      </div>

      <div class="inputDiv">
        <label for="indirizzoAmbulatorio">indirizzo</label>
        <input class="input" type="edit" id="indirizzoAmbulatorio">
      </div>

    </div><!-- ins clinica -->


    <button class="button" onclick="salvaAmbulatorio();">SALVA AMBULATORIO</button>
  </section> <!-- container -->


</body>
</html>

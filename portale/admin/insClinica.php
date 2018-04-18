<?php
include_once 'template/head.php';
?>
<body>
  <div id="container">
    <div class="clearSpace"></div>

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
  </div> <!-- container -->


</body>
</html>

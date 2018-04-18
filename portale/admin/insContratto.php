<!DOCTYPE html>
<html>
<?php 
include_once 'template/head.php';

 ?>
<body>
  <div id="container">


    <div id="insContratto">
      <div class="inputDiv">
        <label for="idAmbulatorio">Ambulatorio</label>
        <select class="dataProc" id="idAmbulatorio">
          <option value="4">test</option>
        </select>
      </div>

      <div class="inputDiv">
        <label for="dataInzio">Data inizio</label>
        <input class="dataProc" type="edit" id="dataInizio">
      </div>

      <div class="inputDiv">
        <label for="dataFine">Data Fine</label>
        <input type="edit" id="dataFine">
      </div>


      <div class="inputDiv">
        <label for="oraInizio">Ora Inizio</label>
        <input type="edit" class="dataProc" id="oraInizio">
      </div>

      <div class="inputDiv">
        <label for="oraFine">Ora Fine</label>
        <input type="edit" class="dataProc" id="oraFine">
      </div>

      <div class="inputDiv">
        <label for="durata">Durata</label>
        <input type="edit" class="durata" id="durata">
      </div>

      <div class="iputDiv">
        <div class="giornoDiv">
          <label for="lun">Lunedì</label>
          <input type="checkbox" id="lun">
        </div>

        <div class="giornoDiv">
          <label for="mar">Martedì</label>
          <input type="checkbox" id="mar">
        </div>

        <div class="giornoDiv">
          <label for="mer">Mercoledì</label>
          <input type="checkbox" id="mer">
        </div>

        <div class="giornoDiv">
          <label for="gio">Giovedì</label>
          <input type="checkbox" id="giov">
        </div>

        <div class="giornoDiv">
          <label for="ven">Venerdì</label>
          <input type="checkbox" id="ven">
        </div>

        <div class="giornoDiv">
          <label for="sab">Sabato</label>
          <input type="checkbox" id="sab">
        </div>


      </div> <!-- giorni contratto -->
      <button onclick="salvaContratto();">Salva Contratto</button>

    </div> <!-- ins contratto -->



  </div> <!-- container -->
  <script type="text/jscript">
  skipLoginIfTokenIsValid();
</script>
</body>
</html>

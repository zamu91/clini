<!DOCTYPE html>
<html>
<head>
  <link type="text/css" href="css/bulma.css" />
  <link type="text/css" href="css/myStyle.css" />
  <script type="text/javascript" src="js/jquery_3.3.1.js"></script>
  <script type="text/javascript" src="js/login.js"></script>
  <script type="text/javascript" src="js/azioni.js"></script>
</head>
<body>
  <h1>CIAO!</h1>
  <input type="text" id="username" name="an_username" placeholder="Username"/>
  <input type="text" id="password" name="an_password" placeholder="Password" />
  <button onclick="login();">Login</button>
  <hr>
  <input type="text" id="token" name="an_token" placeholder="Token"/>
  <button onclick="controlloLogin();">Controllo login</button>
  <hr>
  <pre id="resultCall"></pre>

  <div id="insClinica">
    <div class="inputDiv">
      <label for="nomeAmbulatorio">Nome Ambulatorio</label>
      <input type="edit" id="nomeAmbulatorio">
    </div>
    <button onclick="salvaAmbulatorio();">TEST AMBULATORIO</button>
  </div>

  <div id="insContratto">
    <div class="inputDiv">
      <label for="idAmbulatorio">Ambulatorio</label>
      <select class="dataProc" id="idAmbulatorio"></select>
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
        <label for="giov">Giovedì</label>
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



    </div>


  </div> <!-- ins contratto -->

</body>

</html>

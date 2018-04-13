<!DOCTYPE html>
<html>
<head>
  <!-- c -->
  <link rel="stylesheet" type="text/css" href="css/bulma.css" />
  <link rel="stylesheet" type="text/css" href="css/myStyle.css" />
  <link rel="stylesheet" type="text/css" href="css/custom.css" />
  <link rel="stylesheet" type="text/css" href="vendor/bootstrap-4.0.0-dist/css/bootstrap.min.css" />
  <link rel="stylesheet" type="text/css" href="vendor/jquery-file-upload-9.21.0/css/jquery.fileupload.css">
  <!-- javascript -->
  <script type="text/javascript" src="vendor/jquery/jquery.js"></script>
  <script type="text/javascript" src="vendor/bootstrap-4.0.0-dist/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="vendor/jquery-ui/jquery.ui.widget.js"></script>
  <script type="text/javascript" src="vendor/jquery-file-upload-9.21.0/js/jquery.iframe-transport.js"></script>
  <script type="text/javascript" src="vendor/jquery-file-upload-9.21.0/js/jquery.fileupload.js"></script>
  <script type="text/javascript" src="js/login.js"></script>
  <script type="text/javascript" src="js/azioni.js"></script>
</head>
<body>
  <div id="container">
    <h1>Login</h1>
    <input type="text" id="username" name="an_username" placeholder="Username"/>
    <input type="text" id="password" name="an_password" placeholder="Password" />
    <button onclick="login();">Login</button>
    <hr>
    <input type="text" id="token" name="an_token" placeholder="Token"/>
    <button onclick="controlloLogin();">Controllo login</button>
    <hr>
    <pre id="resultCall"></pre>

    <div id="insClinica">
      <div class="inputDiv"><div class="clearSpace"></div>
      <div>
        <label for="nomeAmbulatorio">Nome Ambulatorio</label>
        <input type="edit" id="nomeAmbulatorio">
      </div>

      <div class="inputDiv"><div class="clearSpace"></div>
      <div>
        <label for="provinciaAmbulatorio">Provincia</label>
        <input type="edit" id="nomeAmbulatorio">
      </div>

      <div class="inputDiv"><div class="clearSpace"></div>
      <div>
        <label for="comuneAmbulatorio">Comune</label>
        <input type="edit" id="nomeAmbulatorio">
      </div>
    </div>


  </div>

  <button onclick="salvaAmbulatorio();">TEST AMBULATORIO</button>
</div>

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

    <button onclick="salvaContratto();">salva contratto</button>

  </div>
</div> <!-- ins contratto -->

<div class="cercaClinica">
  <div class="cercaPerData">
    <div class="inputDiv">
      <label for="dataCercaClinica">Data</label>
      <input type="edit" class="data" id="data">
    </div>
    <div class="resultClinica">

    </div>
  </div> <!-- cerca per data -->


  <div class="cercaPerData">
    <div class="inputDiv">
      <label for="clinica">Clinica</label>
      <select class="select" id="clinicaCerca">
        <option value='4'>TEST</option>
      </select>
    </div>
    <button onclick="cercaPerClinica();">Cerca Per clinica</button>

    <div class="resultClinica">

    </div>

  </div> <!-- cerca per data -->
</div> <!-- cerca primo spazio in clinica -->


</div> <!-- container -->
<script type="text/jscript">
skipLoginIfTokenIsValid();
</script>
</body>
</html>

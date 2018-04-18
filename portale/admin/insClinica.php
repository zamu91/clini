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
    <div class="clearSpace"></div>

    <div id="insClinica">
      <div class="inputDiv">
        <label for="nomeAmbulatorio">Nome Ambulatorio</label>
        <input type="edit" id="nomeAmbulatorio">
      </div>

      <div class="inputDiv">
        <div>
          <label for="provinciaAmbulatorio">Provincia</label>
          <input type="edit" id="provinciaAmbulatorio">
        </div>
      </div>

      <div class="inputDiv">
        <div>
          <label for="comuneAmbulatorio">Comune</label>
          <input type="edit" id="comuneAmbulatorio">
        </div>
      </div>

      <div class="inputDiv">
        <label for="indirizzoAmbulatorio">indirizzo</label>
        <input type="edit" id="indirizzoAmbulatorio">
      </div>

    </div><!-- ins clinica -->


    <button onclick="salvaAmbulatorio();">SALVA AMBULATORIO</button>
  </div> <!-- container -->


</body>
</html>

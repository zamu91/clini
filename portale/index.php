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
    <div class="columns">
      <div class="column is-one-quarter"></div>
      <div class="column is-half">
        <br>
        <h1 class="title">Login</h1>
        <div class="fieldBox">
          <label>Username</label><br>
          <input type="text" id="username" name="an_username" placeholder="Username"/>
        </div>
        <div class="fieldBox">
          <label>Password</label><br>
          <input type="text" id="password" name="an_password" placeholder="Password" />
        </div>
        <div style="clear:both;"></div>
        <br>
        <button class="button is-primary" onclick="login();">Login</button>
      </div>
      <div class="column is-one-quarter"></div>
    </div>
  </div>
</body>
</html>

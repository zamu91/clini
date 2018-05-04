<?php
include_once '../template/head.php';
include_once '../admin/template/html.php';
?>
<body>
  <script src="js/navigazione.js"></script>
  <div id="container">
    <div class="columns">
      <div class="column is-one-quarter"></div>
      <div class="column is-half">
        <br>
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
  <script>
  var urlAjax="../core/class.chiamate.php";
</script>

</body>
</html>

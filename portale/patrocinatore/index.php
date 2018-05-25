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
        <div class="login-form">
          <div class="fieldBox">
            <label>Username</label><br>
            <input type="text" id="username" name="an_username" placeholder="Username"/>
          </div>
          <div class="fieldBox">
            <label>Password</label><br>
            <input type="password" id="password" name="an_password" placeholder="Password" />
          </div>
          <!-- <div style="clear:both;"></div> -->

          <button class="button is-primary" onclick="login();" style="margin-top:25px">Login</button>
        </div>
      </div>
      <div class="column is-one-quarter"></div>
    </div>
  </div>
  <script>

  $( document ).ready(function() {
    jd={};
    jd.azione='controlloTokenARXLogin';
    doAjax(jd,function(data){
      if(data.validToken){
        window.location.href="dash.php";
      }

    });
  });

</script>


</body>
</html>

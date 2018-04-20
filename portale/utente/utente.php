<?php
include_once '../template/head.php';
include_once '../admin/template/html.php';

?>
<body>
  <script src="js/navigazione.js"></script>
  <div class="container">
    <div class="columns">
      <?php divElement('<input type="text" class="input" id="codPatro">',"Inserisci Codice patrocinatore","12"); ?>
    </div>
    <div class="columns">
      <div class="column is-12">
        <button onclick="accediCercaLibero();" class="button is-primary">Accedi</button>
      </div>

    </div>
  </div>
  <script>
    $( document ).ready(function() {
      if(empty(getToken())){
        return 0;
      }
      jd={};
      jd.azione='controlloTokenARXLogin';
      doAjax(jd,function(data){
        if(data.validToken){
          console.log('Ok login');
        }else{
          goIndexUtente();
        }
      });
    });

  </script>

</body>

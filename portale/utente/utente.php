<?php
include_once '../template/head.php';
include_once '../admin/template/html.php';

?>
<body>
  <script src="js/navigazione.js"></script>
  <div class="container">
    <div class="columns">
      <?php divElement('<input type="text" class="input" id="codPatro">',"Inserisci Codice patrocinatore","4"); ?>
    </div>
    <div class="columns">
      <div class="column is-12">
        <button onclick="accediCercaLibero();" class="button is-primary">Accedi</button>
      </div>

    </div>
  </div>

  <?php
//controllo c'è già login
  //<script>
  //
  // $( document ).ready(function() {
  //   if(getToken()==""){
  //     return 0;
  //   }
  //   jd={};
  //   jd.azione='controlloTokenARXLogin';
  //   doAjax(jd,function(data){
  //     if(data.validToken){
  //       accediMenu();
  //     }else{
  //       setToken('');
  //     }
  //   });
  // });
  //
  // </script>

  ?>

</body>

<?php
include_once '../template/head.php';
include_once '../admin/template/html.php';
//include_once '../core/class.chiamate.php';
//$chiamate->controlloArxLogin();
 ?>
 <body>
   <script src="js/navigazione.js"></script>
   <script src="js/menu.js"></script>

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
</body>

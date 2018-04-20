<?php
include_once '../template/head.php';
include_once '../admin/template/html.php';

 ?>
 <body>
 <div class="container">
   <div class="columns">
     <?php divElement('<input type="text" class="input" id="codPatro">',"Inserisci Codice patrocinatore","12"); ?>
   </div>
   <div class="columns">
     <?php divElement('     <button onclick="accediCercaLibero();" class="button is-primary">Accedi</button>',"","12"); ?>

   </div>
 </div>
</body>

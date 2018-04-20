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
     <?php divElement('<button class="button" onclick="apriProfilo("0");">Responsabilità civile auto</button>',"Inserisci Codice patrocinatore","6"); ?>
     <?php divElement('<button class="button" onclick="apriProfilo("1");">Responsabilità civile terzi</button>',"Inserisci Codice patrocinatore","6"); ?>
   </div>
   <div class="columns">
     <?php divElement('<button class="button" onclick="apriProfilo("2");">Polizza privata infortuni</button>',"Inserisci Codice patrocinatore","6"); ?>
     <?php divElement('<button class="button" onclick="apriProfilo("3");">Consulenza tecnica di parte</button>',"Inserisci Codice patrocinatore","6"); ?>    
   </div>

 </div>
</body>

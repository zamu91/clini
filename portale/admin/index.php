<?php
include_once 'template/header.php';
include_once 'template/head.php';
include_once 'template/html.php';

?>
<body>
  <?php
  getHeader("Amministrazione clinica","Seleziona l'azione da eseguire");
  getSubHeader();
  ?>

  <section class="container">
    <div class="columns features">
        
      <?php divElement('<button class="button" onclick="insClinica();">INSERISCI CLINICA</button>',"Inserisci Clinica","6"); ?>
      <?php divElement('<button class="button" onclick="insContratto();">INSERISCI CONTRATTO</button>',"Inserisci Contratto","6"); ?>

    </div> <!-- end columns feauteres -->
  </section> <!-- end section container -->

</body>
</html>

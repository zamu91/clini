<?php
include_once 'template/header.php';
include_once 'template/head.php';
include_once 'template/html.php';

?>
<body>
  <?php
  // getHeader("Amministrazione clinica","Seleziona l'azione da eseguire");
  // getSubHeader();
  ?>

  <section class="container">
    <div class="columns features">

      <?php divElement('<center><button class="button is-primary" onclick="insClinica();">INSERISCI CLINICA</button></center>',"INSERISCI AMBULATORIO","6"); ?>
      <?php divElement('<center><button class="button is-primary" onclick="insContratto();">INSERISCI CONTRATTO</button></center>',"ISNERISCI CONTRATTO","6"); ?>

    </div> <!-- end columns feauteres -->
  </section> <!-- end section container -->

</body>
</html>

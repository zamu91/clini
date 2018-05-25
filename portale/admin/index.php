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

      <?php divElement('<center><button class="button is-primary" onclick="insClinica();"><span class="icon is-medium" style="margin-right:10px"><i class="fas fa-plus"></i></span> INSERISCI AMBULATORIO</button></center>',"","6"); ?>
      <?php divElement('<center><button class="button is-primary" onclick="insContratto();"><span class="icon is-medium" style="margin-right:10px"><i class="fas fa-plus"></i></span>INSERISCI CONTRATTO</button></center>',"","6"); ?>

    </div> <!-- end columns feauteres -->
  </section> <!-- end section container -->

</body>
</html>

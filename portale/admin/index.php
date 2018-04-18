<?php
include_once 'template/header.php';
include_once 'template/head.php';
?>
<body>
  <?php
  getHeader("Amministrazione clinica","Seleziona l'azione da eseguire");
  ?>

  <section class="container">
    <div class="columns features">
      <div class="column is-4">
        <div class="card is-shady">
          <h2>Procedura di inserimento di una nuova clinica</h2>
          <button class="button" onclick="insClinica();">INSERISCI CLINICA</button>
        </div>
      </div>

      <div class="column is-4">
        <div class="card is-shady">
          <h2>Procedura di inserimento di un nuovo contratto</h2>
          <button class="button" onclick="insContratto();">INSERISCI CONTRATTO</button>
        </div>
      </div>
    </div> <!-- end columns feauteres -->
  </section> <!-- end section container -->

</body>
</html>

<?php
include_once '../template/head.php';
include_once '../admin/template/html.php';
?>
<body>
  <script src="js/navigazione.js"></script>
  <div class="container">
    <button class="button is-primary" data-maskix="0" onclick=" window.location.href='patrocinatore/inserisciPrenotazione.php' ">Inserisci prenotazione</button>
    <button class="button is-primary" data-maskix="0" onclick="apriProfilo(this);">Responsabilità civile auto</button>
    <button class="button is-primary" data-maskix="1" onclick="apriProfilo(this);">Responsabilità civile terzi</button>
    <button class="button is-primary" data-maskix="2" onclick="apriProfilo(this);">Polizza privata infortuni</button>
    <button class="button is-primary" data-maskix="3" onclick="apriProfilo(this);">Consulenza tecnica di parte</button>

    <div class="clearSpace"></div>

    <div id="containerFiltri">
      <div id="containerFieldList">
        <?php echo $this->getFieldListMascheraRicherca(); ?>

        <button class="button is-primary floatRight buttonSearch" onclick="caricaListaProfili();">Ricerca</button>
      </div>
    </div>

    <div class="clearSpace"></div>

    <div id="containerListaProfili" class="listaProfili"></div>

    <div class="clearSpace"></div>

    <div id="containerComandi" class="hidden dashSection" data-task="">
      <button class="button is-primary" onclick="apriProfilo(this, false);">Carica doc.</button>
    </div>
    <div id="containerDocumenti" class="hidden dashSection" data-task=""></div>

    <div id="requestResult"></div>

    <div id="modal-action" class="modal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 id="modal-title" class="modal-title">Modal title</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div id="modal-body" class="modal-body">
            <p>Modal body text goes here.</p>
          </div>
          <div class="modal-footer">
            <button id="modal-salva" type="button" class="btn btn-primary">Salva</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
          </div>
        </div>
      </div>
    </div>

    <div class="clearSpace"></div>



  </div><!-- end container -->


</body>
</html>

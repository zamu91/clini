<?php
/**
* Utilizzato per lo spostamento da una form all'altra.
* TODO: Da valutare se integrare in qualche altro trait.
*/
trait navigazione {

  public function naviga($parpage = ""){
    $page = !empty( $parpage ) ? $parpage : $this->post("page");
    switch ($page) {
      case 'dashboard':
        echo $this->navigaDashboard();
        break;

      case 'profilo':
        echo $this->navigaProfilo();
        break;
    }
  }

  public function navigaDashboard(){
    // $path = $this->getEnvPath().$this->dSep()."template{$this->dSep()}dashboard.php";
    // return file_get_contents ( $path );
    ob_start();
    ?>
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
    <?php
  }

  public function navigaProfilo(){
    $path = $this->getEnvPath().$this->dSep()."template{$this->dSep()}dettaglioProfilo.php";
    return file_get_contents ( $path );
  }

}

?>

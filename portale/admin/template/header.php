<?php
function getHeader($title,$sub){
  ?>
  <section class="hero is-info is-medium is-bold">
    <div class="hero-body">
      <div class="container has-text-centered">
        <h1 class="title">
          <?php echo $title; ?>
        </h1>
        <h2 class="subtitle">
          <?php echo $sub; ?>
        </h2>
      </div>
    </div>
  </section>
  <?php
}

function subHeader(){
  ?>
  <div class="box cta">
    <p class="has-text-centered">
      <span class="tag is-primary">Gestione clinica</span>
      Amministrazione entità clinica
    </p>
  </div>

  <?php

}

?>

<?php

function divElement($element,$label,$col){
  ?>
  <div class="column is-<?php echo $col; ?>">

    <div class="card">
      <header class="card-header">
        <p class="card-header-title">
          <?php echo $label; ?>
        </p>

      </header>
      <div class="card-content">
        <div class="content">
          <div class="control has-icons-left has-icons-right">
            <?php echo $element; ?>
          </div>
        </div>
      </div>
    </div>

  </div>

  <?php


}



?>

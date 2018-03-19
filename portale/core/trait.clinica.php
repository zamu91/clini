<?php

/**
 *
 */
trait clinica
{
  public function salvaClinica(){
    $data=$this->post('data');
    $this->insertPrepare('XDM_ANBULATORIO',$data);
    $this->setJsonMess("Dati inseriti");
  }

  public function aggiornaStato(){


  }



}






 ?>

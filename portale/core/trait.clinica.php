<?php

/**
 *
 */
trait clinica
{
  public function salvaClinica(){
    $data=$this->post('data');
    $this->insertPrepare('XDM_ANBULATORIO',$data);
    $this->setJsonMess("mess","Dati inseriti");
    $this->setJsonMess("ok",true);
  }

  public function aggiornaStato(){


  }



}






 ?>

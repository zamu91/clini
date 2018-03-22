<?php

/**
*
*/
trait clinica
{
  public function salvaClinica(){
    $data=$this->post('data');
    $data['IDAMBULATORIO']='sequence.nexvalue';
    $this->insertPrepare('XDM_AMBULATORIO',$data);
    $this->commit();
    $this->setJsonMess("mess","Dati inseriti");

    $this->setJsonMess("ok",true);
  }

  public function aggiornaStato(){


  }



}






?>

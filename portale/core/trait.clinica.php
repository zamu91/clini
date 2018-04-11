<?php

/**
*
*/
trait clinica
{

  private function getNexIdAmbulatorio(){
    $this->query("SELECT max(IDAMBULATORIO) as ID from XDM_AMBULATORIO  ");
    $row=$row=$this->fetch();
    $id=$row['ID'];
    if(empty($id)){
      $id=1;
    }else{
      $id++;
    }
    return $id;
  }

  public function salvaClinica(){
    $data=$this->post('data');
    $data['IDAMBULATORIO']=$this->getNexIdAmbulatorio();
    print_r($data);
    $this->insertPrepare('XDM_AMBULATORIO',$data);
    $this->commit();
    $this->setJsonMess("mess","Dati inseriti");
    $this->setJsonMess("ok",true);
  }

  public function aggiornaStato(){


  }



}






?>

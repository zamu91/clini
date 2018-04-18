<?php

/**
*
*/
trait clinica
{

  private function optClinica(){
    $this->query("SELECT IDAMBULATORIO,NOME from XDM_AMBULATORIO where STATO=1 ");
    $html="";
    while($row=$this->fetch()){
      $html.="<option value='{$row['IDAMBULATORIO']}'>{$row['NOME']}</option>";
    }
    echo $html;
  }

  private function optClinicaProvincia(){
    $this->query("SELECT DISTINCT PROVINCIA from XDM_AMBULATORIO  ");
    $html="";
    while($row=$this->fetch()){
      $html.="<option value='{$row['PROVINCIA']}'>{$row['PROVINCIA']}</option>";
    }
    echo $html;

  }


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
    $data['STATO']='1';

    $this->insertPrepare('XDM_AMBULATORIO',$data);
    $this->commit();
    $this->setJsonMess("mess","Dati inseriti");
    $this->setJsonMess("ok",true);
  }

  public function aggiornaStato(){

  }


}


?>

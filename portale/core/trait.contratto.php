<?php

trait contratto{

  public function insContratto(){
    $data=$this->post("data");
    $data['idPatrocinatore']=$this->getIdArxivar();

    $this->insPrepare('ambulatorio_contratto',$data);

    



  }

  public function generaContratto(){


  }


  public function occupaSpazioPrenotazione  (){


  }

}



 ?>

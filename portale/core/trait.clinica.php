<?php

/**
*
*/
trait clinica
{

  private function optClinica(){
    $this->query("SELECT IDAMBULATORIO,NOME,INDIRIZZO,COMUNE from XDM_AMBULATORIO where STATO=1 ");
    $html="";
    while($row=$this->fetch()){
      $html.="<option value='{$row['IDAMBULATORIO']}'>{$row['NOME']} - {$row['COMUNE']},{$row['INDIRIZZO']}</option>";
    }
    echo $html;
    die;
  }




  private function optClinicaProvincia(){


    $this->query("SELECT DISTINCT AM.PROVINCIA
    FROM XDM_AMBULATORIO AM
    JOIN XDM_AMBULATORIO_CONTRATTO AMC ON AM.IDAMBULATORIO=AMC.IDAMBULATORIO
    JOIN XDM_PRENOTAZIONE PR ON PR.IDCONTRATTO=AMC.IDCONTRATTO AND PR.STATO=0
    WHERE PR.DATA>SYSDATE
    ORDER BY 1 ASC");

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


  public function getAmbulatoriInseriti(){

    $que = "SELECT IDAMBULATORIO,NOME
    , AM.PROVINCIA
    , AM.COMUNE
    , AM.INDIRIZZO
    FROM XDM_AMBULATORIO AM ";


    $this->queryPrepare($que);
    $this->executePrepare();

    ?>
    <table class="table">
      <thead>
        <tr>
          <td>NOME AMBULATORIO</td>
          <td>PROVINCIA</td>
          <td>COMUNE</td>
          <td>INDIRIZZO</td>
        </tr>
      </thead>
      <tbody>
        <?php while( $row=$this->fetch() ){ ?>
          <tr>
            <td><?php echo $row["NOME"]; ?></td>
            <td><?php echo $row["PROVINCIA"]; ?></td>
            <td><?php echo $row["COMUNE"]; ?></td>
            <td><?php echo $row["INDIRIZZO"]; ?></td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
    <?php
    die();
  }


}
?>

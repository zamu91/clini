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
    $path = $this->getEnvPath().$this->dSep()."template{$this->dSep()}dashboard.php";
    return file_get_contents ( $path );
  }

  public function navigaProfilo(){
    $path = $this->getEnvPath().$this->dSep()."template{$this->dSep()}dettaglioProfilo.php";
    return file_get_contents ( $path );
  }

}

?>

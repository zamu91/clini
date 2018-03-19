<?php


trait html
{

private $bufHtml="";
private $event;
private $rowWork;

private $defClass="table ";


private function adHtml($html){
  $this->bufHtml.=$html;
}

private function bidEvent($event){
  $this->event=$event;
}

private function procEvent(){
  if(empty($this->event)){
    return 0;
  }
  $event=$this->event;
}

private function head(){
  $this->addHtml("<thead>");
  //TODO testata

  $this->addHtml("</thead>");
}

private function body($query){
  while($this->rowWork=$this->fetch()){
    //TODO --> processo eventvo e dichiarazione head


    $this->addHtml("</tr>");

  }

}




public function table($que){
$this->addHtml("<table class='$defClass'>");
$this->head();
$this->body();
$this->addHtml("</table>");



}


}
//end trait


 ?>

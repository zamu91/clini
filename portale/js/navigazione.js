function accediCercaLibero(){
  var codPatro=$('#codPatro').val();
  data={};
  data.azione='controllaImpersonate';
  data.codicePatrocinatore=codPatro;
  doAjax(data);
}

function accediRicerca(){

}

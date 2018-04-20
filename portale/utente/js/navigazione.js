urlAjax="../core/class.chiamate.php";


function accediCercaLibero(){
  var codPatro=$('#codPatro').val();
  data={};
  data.azione='controllaImpersonate';
  data.codicePatrocinatore=codPatro;
  doAjax(data);
}


function loginImpersonate(){

  var code = $('#codPatro').val();
  do
  jqXHR = $.ajax({
    url: "core/class.chiamate.php",
    type: 'POST',
    dataType:'json',
    data: { azione: "loginImpersonatePatrocinatore", code: code},
  }).done(function(data, textStatus){
    if(data.login){
      localStorage.setItem("tok",data.token);
      alert('Fatto');
    } else {
      swal("Errore","Codice patrocinatore non trovato","error");
    }
  });
}



function accediRicerca(){

}

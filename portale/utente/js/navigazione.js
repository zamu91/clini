urlAjax="../core/class.chiamate.php";



function accediCercaLibero(){
  var code = $('#codPatro').val();
  data={ azione: "loginImpersonatePatrocinatore", code: code};
  doAjax(data,function(data, textStatus){
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

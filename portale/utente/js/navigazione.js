urlAjax="../core/class.chiamate.php";



function accediCercaLibero(){
  var code = $('#codPatro').val();
  data={ azione: "loginImpersonatePatrocinatore", code: code};
  doAjax(data,function(data, textStatus){
    if(data.login){
      setToken(data.token);
      accediMenu();

    } else {
      swal("Errore","Codice patrocinatore non trovato","error");
    }
  });

}

function accediMenu(){
  window.location.href="menuUtente.php";
}

function goIndexUtente(){
  window.location.href="utente.php";
}



function indietroSalva(){
  $('.tipoPrenotazione').show('slow');
  $('.mascheraContainer').hide('slow');
  $('.maschera').html("");
}


function indietroBottoni(){
  $('.cercaClinica').show('slow');
  $('.tipoPrenotazione').hide('slow');

}


function accediRicerca(){

}

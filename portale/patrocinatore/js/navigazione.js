

function navigaDashboard(){
  var jd = { azione: "naviga", page: "dashboard" };
  doLoad("#container", jd);
}

function accediInserisciPatrocinatore(){
  window.location.href="inserisciPatrocinatore.php";
}

function goIndexPatrocinatore(){
  window.location.href="index.php";
}

function indietroSalva(){
  $('.tipoPrenotazione').show('slow');
  $('.mascheraContainer').hide('slow');
  $('.maschera').html("");
}

function goDash(){
  window.location.href="dash.php";
}


function indietroBottoni(){
  $('.cercaClinica').show('slow');
  $('.tipoPrenotazione').hide('slow');
}

function accediRicerca(){

}

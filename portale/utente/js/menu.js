var idPrenotazioneSelect=null;
var dataSelect=null;



$( document ).ready(function() {
  jd={};
  jd.azione='controlloTokenARXLogin';
  doAjax(jd,function(data){
    if(data.validToken){
      loadProvClinica();
      datPick('#dataCercaClinica');
    }else{
      goIndexUtente();
    }
  });
});



function cercaPerClinica(){
  j={};
  j.clinica=$('#clinicaCerca').val();
  j.azione='getDataPerClinica';
  doLoad('.resultClinica',j);
}

function cercaPerData(){
  j={};
  j.data=$('#clinicaCerca').val();
  j.azione='getClinicaPerData';
  doLoad('.resultClinica',data);
}


function loadProvClinica(){
  data={};
  data.azione='optClinicaProvincia';
  doLoad('#clinicaCerca',data);
}


function cercaClinica(tipo){

  if(tipo=='data'){
    $('.cercaPerData').show('slow');
    $('.cercaPerProv').hide('slow');
    return 0;
  }
  if(tipo=='provincia'){
    $('.cercaPerData').hide('slow');
    $('.cercaPerProv').show('slow');
    return 0;
  }
}

function cercaPerClinica(){
  j={};
  j.clinica=$('#clinicaCerca').val();
  j.azione='getDataPerClinica';
  doLoad('.resultClinica',j);
}



function cercaPerData(){
  j={};
  j.data=$('#clinicaCerca').val();
  j.azione='getClinicaPerData';
  doLoad('#resultClinica',data);
}


function scegliPrenotazione(id,data){
  idPrenotazioneSelect=id;
  dataSelect=data;
  $('.cercaClinica').hide('slow');
  $('.tipoPrenotazione').show('slow');
}



function apriProfiloImpersonate(sender, newdoc){

  var docnumber = ( typeof newdoc != 'undefined' && !newdoc) ? getDocunumberDashboard() : "";
  var jd = { azione: "dettaglioProfilo", docnumber: docnumber, maskix: $(sender).data("maskix") };
  doLoad(".maschera", jd, function(){
    $('.tipoPrenotazione').hide('slow');
    $('.maschera').show('slow');

    $("#modal-title").html( $(sender).html() );
    $("#modal-action").modal("toggle");
    $('.maschera').append('<button class="is-primary button" '+
    ' onclick="scriviDatiProfiloImpersonate();">SALVA PROFILO</button>');
  });
}




function scriviDatiProfiloImpersonate(){
  var jd = {};
  alert('innizio ');
  jd.azione = "scriviDatiProfilo";
  jd.maskIx = $("#maskIx").val();
  jd.COMBO15_297 = $("#COMBO15_297").val();
  jd.COMBO19_1 = $("#COMBO19_1").val();
  jd.TESTO10_297 = $("#TESTO10_297").val();
  jd.TESTO13_297 = $("#TESTO13_297").val();
  jd.CHECK17_1 = $("#CHECK17_1").val();
  jd.TESTO14_297 = $("#TESTO14_297").val();
  jd.TESTO12_297 = $("#TESTO12_297").val();
  jd.idContratto=idPrenotazioneSelect;
  jd.data=dataSelect;
  var file = [];
  jd.files = file;
  alert('fine data');
  doAjax(jd, function(data){
    if(data.res){
      swal('Prenotaizone','Prenotazione avvenuta con successo','success');
    } else {
      swal("Errore","Salvataggio profilazione fallito.","error");
    }
  }, function(jqXHR, textStatus, errorThrown){
    swal("errore","Errore salvataggio profilazione.","error");
  });
}

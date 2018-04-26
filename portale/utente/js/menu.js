var idAmbulatorioSelect=null;
var dataSelect=null;


$( document ).ready(function() {
  jd={};
  jd.azione='controlloTokenARXLogin';
  doAjax(jd,function(data){
    if(data.validToken){
      loadProvClinica();
    }else{
      goIndexUtente();
    }
  });
});


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
  idAmbulatorioSelect=id;
  dataSelect=data;
  $('#cercaClinica').hide('slow');
  $('#tipoPrenotazione').show('slow');
}

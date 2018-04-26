var idPrenotazioneSelect=null;



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


function scegliPrenotazione(id){
  idPrenotazioneSelect=id;
  $('.cercaClinica').hide('slow');
  $('.tipoPrenotazione').show('slow');
}

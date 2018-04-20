$( document ).ready(function() {
datPick('#dataInizio');
datPick('#dataFine');
timePick('.timep');
getOptionClinica('#idAmbulatorio');
});




function salvaContratto(){
  j={};
  data={};
  data.TEMPO=$('#durata').val();
  data.IDAMBULATORIO=$('#idAmbulatorio').val();
  data.DATAINIZIOCONTRATTO=$('#dataInizio').val();
  data.DATAFINECONTRATTO=$('#dataFine').val();
  data.ORAINIZIO=$('#oraInizio').val()+':00';
  data.ORAFINE=$('#oraFine').val()+':00';;
  data.VERSO=$('#verso').val();
  j.data=data;
  j.giorni=getGiorniContratto();
  j.azione='insContratto';
  doAjax(j,function(mess){
    if(mess.ok){
      swal('Contratto','Contratto salvato con successo','success');
    }else{
      swal('Contratto','Impossibile salvare il contratto '+mess.mess,'error');
    }
  });
}




function getValGiorni(nome){
  if ($('#'+nome).is(':checked')) {
    return 1;
  }else{
    return 0;
  }
}

function getGiorniContratto(){
  giorni={};
  giorni[1]=getValGiorni('lun');
  giorni[2]=getValGiorni('mar');
  giorni[3]=getValGiorni('mer');
  giorni[4]=getValGiorni('gio');
  giorni[5]=getValGiorni('ven');
  giorni[6]=getValGiorni('sab');
  return giorni;
}

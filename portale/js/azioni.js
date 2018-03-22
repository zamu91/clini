
function doAjax(data,done){
  data.token=getToken();
  jqXHR = $.ajax({
    url: "core/class.chiamate.php",
    type: 'POST',
    datatype:'json',
    data
  }).done(function(data, textStatus){
    alert(data);
    console.log(data);
  });
}



function salvaAmbulatorio(){
  data={};
  add={};
  add.nome=$('#nomeAmbulatorio').val();
  data.data=add;
  data.azione="salvaClinica";
  doAjax(data,function(mess){
    alert('Clinica salvata con successo');
  });
}


function salvaContratto(){

  j={};
  data={};
  data.durata=$('#durata').val();
  data.idAmbulatorio=$('#durata').val();
  data.dataInizio=$('#dataInizio').val();
  data.dataFine=$('#dataFine').val();
  data.oraInizio=$('#oraInizio').val();
  data.oraFine=$('#oraFine').val();
  j.data=data;
  j.giorni=getGiorniContratto();
  j.azione='insContratto';
  doAjax(j,function(mess){
    alert('Contratto salvata con successo');
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
  giorni.lun=getValGiorni('lun');
  giorni.mar=getValGiorni('mar');
  giorni.mer=getValGiorni('mer');
  giorni.gio=getValGiorni('gio');
  giorni.ven=getValGiorni('ven');
  giorni.sab=getValGiorni('sab');
  return giorni;

}

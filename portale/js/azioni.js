function apriProfilo(){
  // Apre la pagina della maschera con la possibilità di inserire o modificare il profilo e quindi della pratica.
}

function caricaListaProfili(){
  var jd = { azione: "listaProfili" };
  doAjax(jd, function(data){
    $("#containerListaProfili").html(data);
  });
}

function doAjax(jd, doneFunc, failFunc){
  jd.token=getToken();
  jqXHR = $.ajax({
    url: "core/class.chiamate.php",
    type: 'POST',
    datatype:'json',
    data: jd
  }).done(function(data, textStatus, jqXHR){
    if( isFunction(doneFunc) ) {
      doneFunc(data);
    }else{
      alert(data);
      console.log(data);
    }
  }).fail(function(jqXHR, textStatus, errorThrown){
    if( isFunction(failFunc) ) {
      failFunc(jqXHR, textStatus, errorThrown);
    }else{
      alert(jqXHR);
      console.log(jqXHR);
    }
  });
}

function isFunction(functionToCheck) {
 return functionToCheck && {}.toString.call(functionToCheck) === '[object Function]';
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
  giorni.Monday=getValGiorni('lun');
  giorni.Tuesday=getValGiorni('mar');
  giorni.Wednesday=getValGiorni('mer');
  giorni.Thursday=getValGiorni('gio');
  giorni.Friday=getValGiorni('ven');
  giorni.Saturday=getValGiorni('sab');
  return giorni;

}

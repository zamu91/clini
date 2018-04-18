
function isFunction(functionToCheck) {
  return functionToCheck && {}.toString.call(functionToCheck) === '[object Function]';
}


function doAjax(jd, doneFunc, failFunc){
  jqXHR = $.ajax({
    url: "../core/class.chiamate.php",
    type: 'POST',
    dataType:'json',
    data: jd
  }).done(function(data, textStatus, jqXHR){
    if( isFunction(doneFunc) ) {
      doneFunc(data);
    }else{
      alert(data);
    }
  }).fail(function(jqXHR, textStatus, errorThrown){
    if( isFunction(failFunc) ) {
      failFunc(jqXHR, textStatus, errorThrown);
    }else{
      alert(jqXHR);
    }
  });
}



function doLoad(target, jd, doneFunc, failFunc){
  jd.token=getToken();
  jqXHR = $.ajax({
    url: "../core/class.chiamate.php",
    type: 'POST',
    dataType:'html',
    data: jd
  }).done(function(data, textStatus, jqXHR){
    // console.log(data);
    $(""+target).html(data);
    if( isFunction(doneFunc) ) {
      doneFunc(data);
    }
  }).fail(function(jqXHR, textStatus, errorThrown){
    if( isFunction(failFunc) ) {
      failFunc(jqXHR, textStatus, errorThrown);
    }else{
      alert(jqXHR);
    }
  });
}


function loadClinicheCont(){
  jd={};
  jd.azione='getOptionClinica';
  doLoad('#idAmbulatorio',jd);

}

function tornaMenu(){
  window.location='index.php';

}

function insClinica(){
  window.location = 'insClinica.php';
}


function insContratto(){
  window.location = 'insContratto.php';
}



function getOptionClinica(idElement){
  data={};
  data.azione='getOptionClinica';
  doLoad(idElement,data);
}



function salvaContratto(){
  j={};
  data={};function insClinica(){
    window.location = 'insClinica.php';
  }


  function insContratto(){
    window.location = 'insContratto.php';
  }



  function getOptionClinica(idElement){
    data={};
    data.azione='getOptionClinica';
    doLoad(idElement,data);
  }



  function salvaContratto(){
    j={};
    data={};
    data.TEMPO=$('#durata').val();
    data.IDAMBULATORIO=$('#idAmbulatorio').val();
    data.DATAINIZIOCONTRATTO=$('#dataInizio').val();
    data.DATAFINECONTRATTO=$('#dataFine').val();
    data.ORAINIZIO=$('#oraInizio').val();
    data.ORAFINE=$('#oraFine').val();
    data.VERSO='1';
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
    giorni[1]=getValGiorni('lun');
    giorni[2]=getValGiorni('mar');
    giorni[3]=getValGiorni('mer');
    giorni[4]=getValGiorni('gio');
    giorni[5]=getValGiorni('ven');
    giorni[6]=getValGiorni('sab');
    return giorni;
  }


  function salvaAmbulatorio(){
    data={};
    add={};
    add.NOME=$('#nomeAmbulatorio').val();
    add.PROVINCIA=$('#provinciaAmbulatorio').val();
    add.COMUNE=$('#comuneAmbulatorio').val();
    add.INDIRIZZO=$('#indirizzoAmbulatorio').val();

    data.data=add;
    data.azione="salvaClinica";
    doAjax(data,function(mess){
      alert('Clinica salvata con successo');
    });
  }

  data.TEMPO=$('#durata').val();
  data.IDAMBULATORIO=$('#idAmbulatorio').val();
  data.DATAINIZIOCONTRATTO=$('#dataInizio').val();
  data.DATAFINECONTRATTO=$('#dataFine').val();
  data.ORAINIZIO=$('#oraInizio').val();
  data.ORAFINE=$('#oraFine').val();
  data.VERSO='1';
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
  giorni[1]=getValGiorni('lun');
  giorni[2]=getValGiorni('mar');
  giorni[3]=getValGiorni('mer');
  giorni[4]=getValGiorni('gio');
  giorni[5]=getValGiorni('ven');
  giorni[6]=getValGiorni('sab');
  return giorni;
}


function salvaAmbulatorio(){
  data={};
  add={};
  add.NOME=$('#nomeAmbulatorio').val();
  add.PROVINCIA=$('#provinciaAmbulatorio').val();
  add.COMUNE=$('#comuneAmbulatorio').val();
  add.INDIRIZZO=$('#indirizzoAmbulatorio').val();

  data.data=add;
  data.azione="salvaClinica";
  doAjax(data,function(mess){
    alert('Clinica salvata con successo');
  });
}

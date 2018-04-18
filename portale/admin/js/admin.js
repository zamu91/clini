
function isFunction(functionToCheck) {
  return functionToCheck && {}.toString.call(functionToCheck) === '[object Function]';
}

function datPick(elem){

      $( elem ).datepicker();
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



function insClinica(){
  window.location = 'insClinica.php';
}


function insContratto(){
  window.location = 'insContratto.php';
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
    if(mess.ok){
      swal('Clinica','Clinica salvata con successo','success');
    }else{
      swal('ATTENZIONE','impossibile inserire la clinica','warning');
    }
  });

}

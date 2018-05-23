var idPrenotazioneSelect=null;
var dataSelect=null;





function cercaPerClinica(){
  j={};
  j.clinica=$('#clinicaCerca').val();
  j.azione='getDataPerClinica';
  doLoad('.resultClinica',j);
}

function cercaPerData(){
  j={};
  j.data=$('#dataCerca').val();
  j.azione='getClinicaPerData';
  doLoad('.resultClinica',j);
}


function loadProvClinica(){
  data={};
  data.azione='optClinicaProvincia';
  doLoad('#clinicaCerca',data);
}

function loadDataRic(){
  data={};
  data.azione='getOptDataRicerca';
  setTimeout(function(){
    doLoad('#dataCerca',data);
  },2000);


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




function scegliPrenotazione(id,data){
  idPrenotazioneSelect=id;
  dataSelect=data;
  $('.cercaClinica').hide('slow');
  $('.tipoPrenotazione').show('slow');
}



function apriProfiloImpersonate(sender, newdoc){

  var docnumber = ( typeof newdoc != 'undefined' && !newdoc) ? getDocunumberDashboard() : "";
  var jd = { azione: "dettaglioProfilo",data:dataSelect,idContratto:idPrenotazioneSelect, docnumber: docnumber,
  maskix: $(sender).data("maskix") };
  doLoad(".maschera", jd, function(){
    $('.tipoPrenotazione').hide('slow');

    $('.mascheraContainer').show('slow');
    //$('#DATA21_2').val(dataSelect);
    $("#mask-title").html( $(sender).html() );

  });
}



function validateEmail(email) {
  var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return re.test(email);
}


function scriviDatiProfiloImpersonate(){
  var jd = {};
  if(!validateEmail($('#TESTO12_297').val())){
    swal("warning","Mail non valida, inserirne una correttamente","warning");
    return 0;
  }


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
  $('.insCmd').hide('slow');
  doAjax(jd, function(data){
    if(data.res){
      swal("salvataggio","Profilo inserito con successo","success"); //end swal

      setTimeout(function(){
        localStorage.setItem("tok","");
        goIndexUtente();
      },5000);



    } else {
      swal("Errore","Salvataggio profilazione fallito.","error");
      $('.insCmd').show('slow');
    }
  }, function(jqXHR, textStatus, errorThrown){
    swal("errore","Errore salvataggio profilazione.","error");
    $('.insCmd').show('slow');
  });
}

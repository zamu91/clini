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


function liberaPrenotazione(idPrenotazione){
  var jd = {};
  jd.azione = "liberaPrenotazione";
  jd.idPre=idPrenotazione;
  doAjax(jd,function(data){
    console.log(data);
  });
}

// FUNZIONE TEST per ottenere la prima data libera
function primaDataLibera(){
  if(!validateEmail($('#TESTO12_297').val())){
    swal("warning","Mail non valida, inserirne una correttamente","warning");
    return 0;
  }

  // CONTROLLO GDPR -->
  if(!$('.check_gdpr input').is(':checked')){
    swal("warning","Devi dare il tuo consenso al trattamento dei dati.","warning");
    return 0;
  }

  var jd = {};
  jd.azione = "primaDataLibera";
  jd.maskIx = $("#maskIx").val();
  jd.data=dataSelect;
  jd.idContratto=idPrenotazioneSelect;

  doAjax(jd, function(data){
    if(data.res){
      // (id contratto: "+data.idcontratto+" - id prenotazione: "+data.idprenotazione+")
      swal("La prima data libera è "+data.data_p+" dalle "+data.ora_inizio+" alle "+data.ora_fine,"Vuoi procedere con la prenotazione ?",
        {
        buttons: {
            cancel: "Annulla",
            procedi: true,
            }
          }
        ).then(function (value) {
          switch (value) {

            case "procedi":
              scriviDatiProfiloImpersonate(data.idprenotazione);
              break;

            default:
              liberaPrenotazione(data.idprenotazione);
          }
        });

    } else {
      swal("Attenzione","la prenotazione per questa risorsa è temporaneamente sospesa provare più tardi.",'warning');
    }
  }, function(jqXHR, textStatus, errorThrown){
    swal("ERRORE","Errore richiesta prima data libera","error");
  });
}



function scriviDatiProfiloImpersonate(idPrenotazione){
  var jd = {};
  if(!validateEmail($('#TESTO12_297').val())){
    swal("warning","Mail non valida, inserirne una correttamente","warning");
    return 0;
  }

  // CONTROLLO GDPR -->
  if(!$('.check_gdpr input').is(':checked')){
    swal("warning","Devi dare il tuo consenso al trattamento dei dati.","warning");
    return 0;
  }


  jd.azione = "scriviDatiProfilo";
  jd.maskIx = $("#maskIx").val();
/*
  jd.COMBO15_297 = $("#COMBO15_297").val();
  jd.COMBO19_1 = $("#COMBO19_1").val();
  jd.TESTO10_297 = $("#TESTO10_297").val();
  jd.TESTO13_297 = $("#TESTO13_297").val();
  jd.CHECK17_1 = ($("#CHECK17_1").is(":checked")) ? '1' : '0';//  $("#CHECK17_1").val();
  jd.TESTO14_297 = $("#TESTO14_297").val();
  jd.TESTO12_297 = $("#TESTO12_297").val();
*/

  jd.COMBO15_297 = $("#COMBO15_297").val();
  jd.COMBO19_1 = $("#COMBO19_1").val();
  jd.TESTO10_297 = $("#TESTO10_297").val();
  jd.TESTO13_297 = $("#TESTO13_297").val();
  jd.CHECK17_1 = ($("#CHECK17_1").is(":checked")) ? '1' : '0';//  $("#CHECK17_1").val();
  jd.TESTO14_297 = $("#TESTO14_297").val();
  jd.TESTO12_297 = $("#TESTO12_297").val();
  jd.COMBO20_2= $("#COMBO20_2").val();


  jd.COMBO26_1= $("#COMBO26_1").val();
  jd.TESTO31_1= $("#TESTO31_1").val();

  jd.TESTO25_1= $("#TESTO25_1").val();
  jd.TESTO30_1= $("#TESTO30_1").val();
  jd.TESTO34_1= $("#TESTO34_1").val();
  jd.TESTO29_1= $("#TESTO29_1").val();
  jd.TESTO32_1= $("#TESTO32_1").val();
  jd.TESTO27_1= $("#TESTO27_1").val();
  jd.TESTO28_1= $("#TESTO28_1").val();
  jd.TESTO33_1= $("#TESTO33_1").val();
  jd.oggetto= $("#oggetto").val();
  jd.DATA24_1= $("#DATA24_1").val();
  jd.DATA35_1= $("#DATA35_1").val();

  /*
  jd.DATA21_2= $("#DATA21_2").val();
  

  */


  jd.idContratto=idPrenotazioneSelect;
  jd.data=dataSelect;
  jd.idPrenotazione=idPrenotazione;
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

var urlAjax="../core/class.chiamate.php";

var idPrenotazioneSelect=null;
var dataSelect=null;


function logOutPatro(){
  localStorage.setItem("tok","");
  goIndexPatrocinatore();
}

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



function apriProfilo(sender, newdoc){
  var docnumber = "";
  if( typeof newdoc != 'undefined' && !newdoc){
    docnumber = getDocunumberDashboard();
    if( $("#containerComandi").data("work") == "" ){
      swal("warning","Attenzione processo sembra non essere ancora arrivato al task corretto, si prega di attendere l'elaborazione del processo.","warning");
      return false;
    }
  }
  var jd = { azione: "dettaglioProfilo",data:dataSelect,
  idContratto:idPrenotazioneSelect,
  docnumber: docnumber, maskix: $(sender).data("maskix") };
  doLoad(".maschera", jd, function(){
    $('.tipoPrenotazione').hide('slow');
    $('.mascheraContainer').show('slow');
    //$('#DATA21_2').val(dataSelect);
    $("#mask-title").html( $(sender).html() );
    $("#modal-action").modal("toggle");


    if( $("#fileupload").length ){
      var url = "../core/jquery-file-upload-9.21.0/index.php";
      $('#fileupload').fileupload({
        url: url,
        dataType: 'json',
        done: function (e, data) {
          $.each(data.result.files, function (index, file) {
            if( typeof file.error != 'undefined' && file.error != "" ) {
              swal("errore","Errore nel caricamento di fle."+ file.name+' --> '+file.error,"error" );
              $('.cmdIns').show('slow');
            } else {
              $('<p class="uploadedFile" data-info="'+decodeURIComponent(file.url)+'"/>').text(file.name).appendTo('#files');
              $('.cmdIns').show('slow');
            }
          });
        },
        progressall: function (e, data) {

          if ($('.cmdIns').is(":visible")){
            $('.cmdIns').hide('slow');
          }

          var progress = parseInt(data.loaded / data.total * 100, 10);
          $('#progress .progress-bar').css(
            'width',
            progress + '%'
          );
        },
        fail: function(jqXHR, errorThrown, textStatus){
          swal("errore","Errore nella procedura di caricamento dei file.","errore");
        }
      }).prop('disabled', !$.support.fileInput).parent().addClass($.support.fileInput ? undefined : 'disabled');
    }
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
      swal("La prima data libera è "+data.data_p+" dalle "+data.ora_inizio+" alle "+data.ora_fine,"Vuoi procedere con la prenotazione ? (id prenotazione: "+data.idprenotazione+")",
        {
        buttons: {
            cancel: "Annulla",
            procedi: true,
            }
          }
        ).then(function (value) {
          switch (value) {

            case "procedi":
              scriviDatiProfilo(data.idprenotazione);
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


function scriviDatiProfilo(idPrenotazione){
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
  jd.azione = "scriviDatiProfilo";
  jd.maskIx = $("#maskIx").val();
  jd.data=dataSelect;
  jd.idContratto=idPrenotazioneSelect;

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

  jd.idPrenotazione=idPrenotazione;
  var file = [];
  $("#files").children(".uploadedFile").each(function(){
    file.push($(this).data("info"));
  });
  jd.files = file;
  $('.cmdIns').hide('slow');
  doAjax(jd, function(data){
    if(data.res){
      swal("salvataggio","Profilo inserito con successo","success");

      setTimeout(function(){
        goDash();
      },4000);

    } else {
      swal("Attenzione",data.mess,'warning');
      $('.cmdIns').show('slow');
    }
  }, function(jqXHR, textStatus, errorThrown){
    swal("ERRORE","Errore salvataggio profilazione. Stato"+textStatus+" error: "+errorThrown,"error");
    $('.cmdIns').show('slow');

  });
}

// Sezione IIS
open = function(verb, url, data, target) {
  var form = document.createElement("form");
  form.action = url;
  form.method = verb;
  form.target = target || "_self";
  if (data) {
    for (var key in data) {
      var input = document.createElement("textarea");
      input.name = key;
      input.value = typeof data[key] === "object" ? JSON.stringify(data[key]) : data[key];
      form.appendChild(input);
    }
  }
  form.style.display = 'none';
  document.body.appendChild(form);
  form.submit();
};
// utilizzo:
function open_preview(){
  if($('.documentSelectTask').length){
    docnum = $('.documentSelectTask').attr("data-doc");
    open('POST', '/arx/dwn/', {docnumber:docnum }, '_blank' );
  } else { alert('Seleziona un documento'); }
}

function open_download(){
  if($('.documentSelectTask').length){
    docnum = $('.documentSelectTask').attr("data-doc");
    open('POST', '/arx/dwn/', {docnumber:docnum,download:1 });
  } else { alert('Seleziona un documento'); }
}

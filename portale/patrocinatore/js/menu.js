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
          $('.cmdIns').hide('slow');
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


function scriviDatiProfilo(){
  if(!validateEmail($('#TESTO12_297').val())){
    swal("warning","Mail non valida, inserirne una correttamente","warning");
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
  var file = [];
  $("#files").children(".uploadedFile").each(function(){
    file.push($(this).data("info"));
  });
  jd.files = file;
  console.log(jd);
  doAjax(jd, function(data){
    if(data.res){

      swal("salvataggio","Profilo inserito con successo","success");

      setTimeout(function(){
        goDash();
      },4000);

    } else {
      swal("Attenzione",data.mess,'warning');
    }
  }, function(jqXHR, textStatus, errorThrown){
    swal("ERRORE","Errore salvataggio profilazione.","error")
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
  if($('#tableFileDoc').children('tr:is-selected').length){
    docnum = $('#tableFileDoc').children('tr:is-selected').attr("data-doc");
    open('POST', 'http://192.168.50.250:84/Default.aspx', {docnumber:docnum });
  } else { alert('Seleziona un documento'); }
}
function open_download(){
  if($('#tableFileDoc').children('tr:is-selected').length){
    docnum = $('#tableFileDoc').children('tr:is-selected').attr("data-doc");
    open('POST', 'http://192.168.50.250:84/Default.aspx', {docnumber:docnum,download:1 });
  } else { alert('Seleziona un documento'); }
}

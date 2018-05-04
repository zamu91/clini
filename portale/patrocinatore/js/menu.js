var idPrenotazioneSelect=null;
var dataSelect=null;


$( document ).ready(function() {
  jd={};
  jd.azione='controlloTokenARXLogin';
  doAjax(jd,function(data){
    if(data.validToken){
      loadProvClinica();
      datPick('#dataCerca');
    }else{
      alert('da variare');
      goIndexPatrocinatore();

    }
  });
});



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

  var docnumber = ( typeof newdoc != 'undefined' && !newdoc) ? getDocunumberDashboard() : "";
  var jd = { azione: "dettaglioProfilo",data:dataSelect, docnumber: docnumber, maskix: $(sender).data("maskix") };
  doLoad(".maschera", jd, function(){
    $('.tipoPrenotazione').hide('slow');
    $('.maschera').show('slow');
    //$('#DATA21_2').val(dataSelect);
    $("#modal-title").html( $(sender).html() );
    $("#modal-action").modal("toggle");
    $('.maschera').append('<button class="is-primary button" '+
    ' onclick="scriviDatiProfiloImpersonate();">SALVA PROFILO</button>');

    if( $("#fileupload").length ){
      var url = "../core/jquery-file-upload-9.21.0/index.php";
      $('#fileupload').fileupload({
          url: url,
          dataType: 'json',
          done: function (e, data) {
              $.each(data.result.files, function (index, file) {
                if( typeof file.error != 'undefined' && file.error != "" ) {
                  alert("Errore nel caricamento di fle."+ file.name+' --> '+file.error );
                } else {
                  $('<p class="uploadedFile" data-info="'+decodeURIComponent(file.url)+'"/>').text(file.name).appendTo('#files');
                }
              });
          },
          progressall: function (e, data) {
              var progress = parseInt(data.loaded / data.total * 100, 10);
              $('#progress .progress-bar').css(
                  'width',
                  progress + '%'
              );
          },
          fail: function(jqXHR, errorThrown, textStatus){
            alert("Errore nella procedura di caricamento dei file.");
          }
      }).prop('disabled', !$.support.fileInput).parent().addClass($.support.fileInput ? undefined : 'disabled');
    }
  });
}




function scriviDatiProfilo(){

  var jd = {};
  jd.azione = "scriviDatiProfilo";
  jd.maskIx = $("#maskIx").val();
  jd.COMBO15_297 = $("#COMBO15_297").val();
  jd.COMBO19_1 = $("#COMBO19_1").val();
  jd.TESTO10_297 = $("#TESTO10_297").val();
  jd.TESTO13_297 = $("#TESTO13_297").val();
  jd.CHECK17_1 = $("#CHECK17_1").val();
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
      $("#modal-action").modal("toggle");
      $("#modal-body").html("");
      caricaListaProfili();
    } else {
      alert("Salvataggio profilazione fallito.")
    }
  }, function(jqXHR, textStatus, errorThrown){
    alert("Errore salvataggio profilazione.")
  });
}

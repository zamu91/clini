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
  var jd = { azione: "dettaglioProfilo", docnumber: docnumber, maskix: $(sender).data("maskix") };
  doLoad(".maschera", jd, function(){
    $('.tipoPrenotazione').hide('slow');
    $('.maschera').show('slow');
    alert(dataSelect);
    $('#DATA21_2').val(dataSelect);
    $("#modal-title").html( $(sender).html() );
    $("#modal-action").modal("toggle");
    $('.maschera').append('<button class="is-primary button" '+
    ' onclick="scriviDatiProfiloImpersonate();">SALVA PROFILO</button>');
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

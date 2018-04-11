function apriProfiloOld(){
  // Apre la pagina della maschera con la possibilità di inserire o modificare il profilo e quindi della pratica.
  // TODO: Parametrizzo il profilo da aprire, nel caso stiamo usando un profilo esistente docnumber deve essere valorizzato, altrimenti predispongo la maschera per l'inserimento.
  var docnumber = "";
  var jd = { azione: "naviga", page: "profilo", docnumber: docnumber };
  doAjax(jd, function(data){
    $("#container").html(data);
    caricaProfilo(data.docnumber);
  });
}

function apriProfilo(sender, newdoc){
  // Apre la pagina della maschera con la possibilità di inserire o modificare il profilo e quindi della pratica.
  // TODO: Parametrizzo il profilo da aprire, nel caso stiamo usando un profilo esistente docnumber deve essere valorizzato, altrimenti predispongo la maschera per l'inserimento.
  var docnumber = ( typeof newdoc != 'undefined' && !newdoc) ? getDocunumberDashboard() : "";
  var jd = { azione: "dettaglioProfilo", docnumber: docnumber };
  doLoad("#modal-body", jd, function(){
    $("#modal-title").html( $(sender).html() );
    $("#modal-action").modal("toggle");
    $("#modal-salva").on("click", function(){
      if(docnumber != ""){
        scriviDocumentiProfilo();
      } else {
        scriviDatiProfilo();
      }
    });

    'use strict';
    // Change this to the location of your server-side upload handler:
    var url = "core/jquery-file-upload-9.21.0/index.php";
    $('#fileupload').fileupload({
        url: url,
        dataType: 'json',
        done: function (e, data) {
            $.each(data.result.files, function (index, file) {
              if( typeof file.error != 'undefined' && file.error != "" ) {
                alert("Errore nel caricamento di fle."+ file.name+' --> '+file.error );
              } else {
                $('<p class="uploadedFile" data-info="'+file.url+'"/>').text(file.name).appendTo('#files');
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
  });
}

function caricaListaProfili(){
  var jd = { azione: "listaProfili" };
  doAjax(jd, function(data){
    $("#containerListaProfili").html(data);
  });
}

function caricaProfiloOld(docnumber){
  var jd = { azione: "dettaglioProfilo", docnumber: docnumber };
  doLoad("#containerMascheraProfilazione", jd);
}

function caricaProfilo(docnumber){
  var jd = { azione: "dettaglioProfilo", docnumber: docnumber };
  doLoad("#containerMascheraProfilazione", jd);
}

function dettagliTaskProfilo(target){
  //  containerComandi
  $("#containerComandi").attr("data-task", "");
  $("#containerComandi").attr("data-work", "");
  $("#containerDocumenti").attr("data-task", "");
  $("#containerDocumenti").attr("data-work", "");

  $(target).parent("tbody").children("tr.selected").removeClass("selected");
  $(target).addClass("selected");

  var docnumber = $(target).data("task");

  var jd = { azione: "getTaskworkFromDocnumber", docnumber: docnumber};
  doAjax(jd, function(data){
    if( data.res ){
      $("#containerComandi").attr("data-task", docnumber);
      $("#containerComandi").attr("data-work", data.taskwork);
      $("#containerDocumenti").attr("data-task", docnumber);
      $("#containerDocumenti").attr("data-work", data.taskwork);

      $("#containerComandi").show();
      $("#containerDocumenti").show();

      var jd = { azione: "listaDocumenti", docnumber: docnumber };
      doLoad("#containerDocumenti", jd);
    } else {
      alert("Errore recupero taskwork");
    }
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

function doLoad(target, jd, doneFunc, failFunc){
  jd.token=getToken();
  jqXHR = $.ajax({
    url: "core/class.chiamate.php",
    type: 'POST',
    datatype:'html',
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
      console.log(jqXHR);
    }
  });
}

function getDocunumberDashboard(){
  var task = $("#containerComandi").data("task");
  task = ( typeof task != 'undefined' ) ? task : "";
  return task;
}

function isFunction(functionToCheck) {
  return functionToCheck && {}.toString.call(functionToCheck) === '[object Function]';
}

function navigaDashboard(){
  var jd = { azione: "naviga", page: "dashboard" };
  doAjax(jd, function(data){
    $("#container").html(data);
    // caricaListaProfili();
  });
}

function salvaAmbulatorio(){
  data={};
  add={};
  add.NOME=$('#nomeAmbulatorio').val();
  data.data=add;
  data.azione="salvaClinica";
  doAjax(data,function(mess){
    alert('Clinica salvata con successo');
  });
}


function scriviDatiProfilo(){
  var jd = $("#formMaschera").serialize();
  jd += "&azione=scriviDatiProfilo";
  console.log(jd);
  doAjax(jd, function(mess){
    $("#requestResult").html(mess);
  });
}

function scriviDocumentiProfilo(){
  var jd = {};
  jd.azione = "scriviDocumentiProfilo";
  jd.taskwork = $("#containerComandi").data("work");
  var file = [];
  $("#files").children(".uploadedFile").each(function(){
    file.push($(this).data("info"));
  });
  jd.files = file;
  console.log(jd);
  // return false;
  doAjax(jd, function(mess){
    $("#requestResult").html(mess);
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

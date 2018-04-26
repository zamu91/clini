var urlAjax="core/class.chiamate.php";
var ajaxCall;
var ajaxLoad;



function datPick(elem){
      $( elem ).datepicker({ dateFormat: 'dd/mm/yy' });
}

function apriProfiloOld(){
  var docnumber = "";
  var jd = { azione: "naviga", page: "profilo", docnumber: docnumber };
  doAjax(jd, function(data){
    $("#container").html(data);
    caricaProfilo(data.docnumber);
  });
}

function apriProfilo(sender, newdoc){
  var docnumber = ( typeof newdoc != 'undefined' && !newdoc) ? getDocunumberDashboard() : "";
  var jd = { azione: "dettaglioProfilo", docnumber: docnumber, maskix: $(sender).data("maskix") };
  doLoad("#modal-body", jd, function(){
    $("#modal-title").html( $(sender).html() );
    $("#modal-action").modal("toggle");
    $("#modal-salva").unbind("click");
    $("#modal-salva").on("click", function(){
      if(docnumber != ""){
        scriviDocumentiProfilo();
      } else {
        scriviDatiProfilo();
      }
    });

    if( $("#fileupload").length ){
      var url = "core/jquery-file-upload-9.21.0/index.php";
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

function apriProfiloImpersonate(sender, newdoc){
  var docnumber = ( typeof newdoc != 'undefined' && !newdoc) ? getDocunumberDashboard() : "";
  var jd = { azione: "dettaglioProfilo", docnumber: docnumber, maskix: $(sender).data("maskix") };
  doLoad("#modal-body", jd, function(){
    $("#modal-title").html( $(sender).html() );
    $("#modal-action").modal("toggle");
    $("#modal-salva").unbind("click");
    $("#modal-salva").on("click", function(){
        scriviDatiProfiloImpersonate();
    });
  });
}


function caricaListaProfili(){
  /* Reset dell'area dei taskwork */
  $("#containerComandi").attr("data-task", "");
  $("#containerComandi").attr("data-work", "");
  $("#containerDocumenti").attr("data-task", "");
  $("#containerDocumenti").attr("data-work", "");
  $("#containerComandi").hide();
  $("#containerDocumenti").hide();

  var jd = { azione: "listaProfili", tipoValutazione: $("#COMBO15_297").val(), cognome: $("#TESTO10_297").val(),
  nome: $("#TESTO13_297").val(), deceduto: $("#CHECK17_1").val(), telefono: $("#TESTO14_297").val(),
  mail: $("#TESTO12_297").val() };
  // console.log(jd);
  doLoad("#containerListaProfili", jd);
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

  $(target).parent("tbody").children("tr.is-selected").removeClass("is-selected");
  $(target).addClass("is-selected");

  var docnumber = $(target).data("task");

  var jd = { azione: "getTaskworkFromDocnumber", docnumber: docnumber};
  doAjax(jd, function(data){
    console.log(data);
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
  if( ajaxCall ){ return false; }
  ajaxCall = true;
  ajaxCall = $.ajax({
    url: urlAjax,
    type: 'POST',
    dataType:'json',
    data: jd
  }).done(function(data, textStatus, jqXHR){
    ajaxCall = false;
    if( isFunction(doneFunc) ) {
      doneFunc(data);
    }else{
      alert(data);
    }
  }).fail(function(jqXHR, textStatus, errorThrown){
    ajaxCall = false;
    if( isFunction(failFunc) ) {
      failFunc(jqXHR, textStatus, errorThrown);
    }else{
      alert("Errore nella comunicazione.");
      console.log(jqXHR);
    }
  });
}

function doLoad(target, jd, doneFunc, failFunc){
  jd.token=getToken();
  if(ajaxLoad){ return false; }
  ajaxLoad = true;
  jqXHR = $.ajax({
    url: urlAjax,
    type: 'POST',
    dataType:'html',
    data: jd
  }).done(function(data, textStatus, jqXHR){
    // console.log(data);
    ajaxLoad = false;
    $(""+target).html(data);
    if( isFunction(doneFunc) ) {
      doneFunc(data);
    }
  }).fail(function(jqXHR, textStatus, errorThrown){
    ajaxLoad = false;
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
  doLoad("#container", jd);
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
  doAjax(jd, function(mess){
    if(data.res){
      $("#modal-action").modal("toggle");
      $("#modal-body").html("");
      caricaListaProfili();
    } else {
      alert("Salvataggio documentazione fallito.")
    }
  }, function(jqXHR, textStatus, errorThrown){
    alert("Errore salvataggio documentazione.")
  });
}

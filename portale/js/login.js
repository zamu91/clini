
function login(){
  $("#resultCall").html("");
  var user = $("#username").val();
  var pass = $("#password").val();

  jqXHR = $.ajax({
    url: "core/class.chiamate.php",
    type: 'POST',
    dataType:'json',
    data: { azione: "loginPatrocinatore", username: user, password: pass},
  }).done(function(data, textStatus){
    localStorage.setItem("tok",data.token);
    $("#resultCall").html(data);
    if(data.login){
      navigaDashboard();
    } else {
      alert("Errore login");
    }
  });
}

function loginImpersonate(){
  $("#resultCall").html("");
  var code = $("#codiceIdentificazione").val();

  jqXHR = $.ajax({
    url: "core/class.chiamate.php",
    type: 'POST',
    dataType:'json',
    data: { azione: "loginImpersonatePatrocinatore", code: code},
  }).done(function(data, textStatus){
    localStorage.setItem("tok",data.token);
    $("#resultCall").html(data);
    if(data.login){
      navigaDashboard();
    } else {
      alert("Errore login");
    }
  });
}

function getToken(){
  token=localStorage.getItem("tok");
  return token;
}

function skipLoginIfTokenIsValid(){
  var token = $("#token").val();

  jqXHR = $.ajax({
    url: "core/class.chiamate.php",
    type: 'POST',
    data: { azione: "controlloTokenARXLogin", token: token},
  }).done(function(jqXHR, textStatus){
    if(jqXHR.res && jqXHR.validToken){
      navigaDashboard();
    }
  });
}

function controlloLogin(){
  $("#resultCall").html("");
  var token = $("#token").val();

  jqXHR = $.ajax({
    url: "core/class.chiamate.php",
    type: 'POST',
    data: { azione: "controlloARXLogin", token: token},
  }).done(function(jqXHR, textStatus){
    $("#resultCall").html(jqXHR);
  });
}

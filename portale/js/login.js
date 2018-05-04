
function login(){
  $("#resultCall").html("");
  var user = $("#username").val();
  var pass = $("#password").val();

  jqXHR = $.ajax({
    url: "../core/class.chiamate.php",
    type: 'POST',
    dataType:'json',
    data: { azione: "loginPatrocinatore", username: user, password: pass},
  }).done(function(data, textStatus){
    // $("#resultCall").html(data);
    if(data.login){
      localStorage.setItem("tok",data.token);
      window.location.href="dash.php";

    } else {
      swal("Attenzione","Errore login,controllare username e password","warning");
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

function setToken(tok){
  localStorage.setItem("tok",tok);
}

function getToken(){
  token=localStorage.getItem("tok");
  if (typeof token!="undefined"){
    return token;
  }else{
    return "";
  }
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
  var token = getToken();

  jqXHR = $.ajax({
    url: "core/class.chiamate.php",
    type: 'POST',
    data: { azione: "controlloARXLogin", token: token},
  }).done(function(jqXHR, textStatus){
    $("#resultCall").html(jqXHR);
  });
}

var token="";

function login(){
    $("#resultCall").html("");
    var user = $("#username").val();
    var pass = $("#password").val();

    jqXHR = $.ajax({
        url: "core/class.chiamate.php",
        type: 'POST',
        datatype:'json',
        data: { azione: "loginPatrocinatore", username: user, password: pass},
    }).done(function(data, textStatus){
        token=data.token;
        $("#resultCall").html(data);
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

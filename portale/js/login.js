function login(){
    $("#resultCall").html("");
    var user = $("#username").val();
    var pass = $("#password").val();

    jqXHR = $.ajax({
        url: "core/login.php",
        type: 'POST',
        data: { azione: "CustomARXLogin", username: user, password: pass},
    }).done(function(jqXHR, textStatus){
        $("#resultCall").html(jqXHR);
    });
}

function controlloLogin(){
    $("#resultCall").html("");
    var token = $("#token").val();

    jqXHR = $.ajax({
        url: "core/login.php",
        type: 'POST',
        data: { azione: "controlloARXLogin", token: token},
    }).done(function(jqXHR, textStatus){
        $("#resultCall").html(jqXHR);
    });
}

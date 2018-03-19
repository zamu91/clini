
function ajaxDefault(data,done){
    data.token=getToken();
    jqXHR = $.ajax({
        url: "core/class.chiamate.php",
        type: 'POST',
        datatype:'json',
        data
    }).done(function(data, textStatus){
        alert(data);
        console.log(data);
    });
}



function salvaAmbulatorio(){
  alert('start salva');
  data={};
  add=[];
  add['nome']="test prova";
  data.data=add;
  data.azione="salvaClinica";
  ajaxDefault(data);
}

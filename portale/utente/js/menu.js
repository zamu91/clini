$( document ).ready(function() {
  jd={};
  jd.azione='controlloTokenARXLogin';
  doAjax(jd,function(data){
    if(data.validToken){
      console.log('Ok login');
    }else{
      goIndexUtente();
    }

  });

});

<?php
include_once '../template/head.php';
include_once '../admin/template/html.php';
?>
<body>
  <script src="js/navigazione.js"></script>
  <script src="js/menu.js?v=1"></script>
  <div class="container" id="container">

  </div><!-- end container -->

  <script>
  var urlAjax="../core/class.chiamate.php";
  $( document ).ready(function() {
    jd={};
    jd.azione='controlloTokenARXLogin';
    jd.impersonate=0;
    doAjax(jd,function(data){
      if(data.validToken){
        navigaDashboard();
      }else{
        goIndexPatrocinatore();
      }
    });
  });

</script>



</body>
</html>

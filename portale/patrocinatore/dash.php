<?php
include_once '../template/head.php';
include_once '../admin/template/html.php';
?>
<body>
  <script src="js/navigazione.js"></script>
  <div class="container">

  </div><!-- end container -->

  <script>
  var urlAjax="../core/class.chiamate.php";
  $( document ).ready(function() {
    jd={};
    jd.azione='controlloTokenARXLogin';
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

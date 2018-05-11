<?php
include_once '../template/head.php';
include_once '../admin/template/html.php';
?>
<body>
  <script src="js/navigazione.js"></script>
  <script src="js/menu.js"></script>
  <div class="container" id="container">

  </div><!-- end container -->

  <script>
  // var urlAjax="../core/class.chiamate.php";
  $( document ).ready(function() {
    jd={};
    jd.azione='controlloTokenARXLogin';
    // alert('ready');
    doAjax(jd,function(data){
      // alert('ok sono qua');
      if(data.validToken){
        // alert('prima di naviga');
        navigaDashboard();
      }else{
        goIndexPatrocinatore();
      }
    });
  });
  alert('end ready');

</script>



</body>
</html>

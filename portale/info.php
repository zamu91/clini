<?php
include_once 'config/conOCI.php';
$ora=new conOCI();
echo "oggetto";
$ora->connettiOracle();
echo "connesso";
$res=$ora->query("select * from XDM_WEBSERVICE_SESSION");
echo "queyr";
print_r($res);
 ?>

<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once 'config/conOCI.php';
$ora=new conOCI();
echo "oggetto";
$res=$ora->connettiOracle();
if(!$res){
  echo "NON VA NIENTE!!!<br>";
}else{
  echo "ok sembra connesso";
}

echo "connesso";

$stid = oci_parse($res, "SELECT * from XDM_WEBSERVICE_SESSION ");
print_r($stid);

echo "fine";


 ?>

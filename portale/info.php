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
$res=$ora->query("select * from XDM_WEBSERVICE_SESSION");
echo "queyr";
print_r($res);
 ?>

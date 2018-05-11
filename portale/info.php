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

if( $stid != false ){
  if (!oci_execute($stid,OCI_NO_AUTO_COMMIT)) {
    $e = oci_error($stid);
    echo ($e." -- ".$que);
    return false;
  }else  {
    echo "valido";
  }
} else {
  $e = oci_error($stid);

  echo ($e);
  return false;
}





$row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS);
print_r($row);

echo "fine";


 ?>

<?php
    require($_SERVER["DOCUMENT_ROOT"]."/Arxivar/ARX_Login.php");
    require($_SERVER["DOCUMENT_ROOT"]."/Arxivar/ARX_Dati.php");
    require($_SERVER["DOCUMENT_ROOT"]."/Arxivar/ARX_Search.php");
    require($_SERVER["DOCUMENT_ROOT"]."/Arxivar/ARX_Documenti.php");
    require("../config/conOCI.php");
    // echo 'Ce la facciamo?';
    // if (function_exists($_POST["azione"])){
    //     call_user_function($_POST["azione"], $_POST);
    // }

    if($_POST["azione"] == "CustomARXLogin" ){
        CustomARXLogin($_POST);
    }
    if($_POST["azione"] == "controlloARXLogin" ){
        controlloARXLogin($_POST);
    }


    function CustomARXLogin($opt){
        // classi necessarie
        // echo "Siamo dentrooooo";
        $baseUrl = "http://localhost:81/";
        $ARX_Login = new ARX_Login\ARX_Login($baseUrl."ARX_Login.asmx?WSDL");
        $ARX_Dati  = new ARX_Dati\ARX_Dati($baseUrl."ARX_Dati.asmx?WSDL");

        $userName = $opt["username"];
        $password = $opt["password"];

        $softwareName = "PHP Gestione cliniche";

        // echo $userName.' - '.$password.' - '.$softwareName;
        $loginResult = $ARX_Login->Login($userName, $password, $softwareName);
        var_dump($loginResult);
        echo '<hr>';
        if( $loginResult->LoggedIn ){
            $sessionid = $loginResult->SessionId;
            echo "Login completato con successo, id session $sessionid <hr>";
            $ARX_Login->LogOut($sessionid);

            // cast della data
            $app = $loginResult->ExpiratedTime;
            $expirationTime = substr($app, 0, 10).' '.substr($app, 11, 8);
            echo $expirationTime."<hr>";

            $oci = new conOCI();

            $que = "SELECT USERNAME, PASSWORD, ARXSESSION, TO_CHAR(SCADENZA, 'YYYY-MM-DD HH24:MI:SS') AS SCADENZA FROM XDM_WEBSERVICE_SESSION
            WHERE USERNAME = '$userName' AND PASSWORD = '$password' ";
            $res = $oci->query($que);
            $row = oci_fetch_array($res["dataSet"], OCI_ASSOC+OCI_RETURN_NULLS);
            if( !empty($row["USERNAME"]) ){
                $que = "UPDATE XDM_WEBSERVICE_SESSION SET ARXSESSION = '$loginResult->SessionId',
                SCADENZA = TO_DATE('$expirationTime', 'YYYY-MM-DD HH24:MI:SS') ";
            } else {
                $que = "INSERT INTO XDM_WEBSERVICE_SESSION (USERNAME, PASSWORD, ARXSESSION, SCADENZA)
                VALUES ('$userName', '$password', '$loginResult->SessionId', TO_DATE('$expirationTime', 'YYYY-MM-DD HH24:MI:SS')) ";
            }

            echo $que."<hr>";
            $res = $oci->query($que);
            var_dump($res);
        }else{
            echo $loginResult->ArxLogOnErrorTypeString;
        }
    }

    function controlloARXLogin($opt){
        $token = $opt["token"];
        echo $token."<hr>";
        $oci = new conOCI();
        $que = "SELECT USERNAME, PASSWORD, ARXSESSION, TO_CHAR(SCADENZA, 'YYYY-MM-DD HH24:MI:SS') AS SCADENZA FROM XDM_WEBSERVICE_SESSION
        -- WHERE ARXSESSION = '$token'
        WHERE ARXSESSION = '$token' AND SYSDATE <= SCADENZA";
        echo $que."<hr>";
        $res = $oci->query($que);
        $row = oci_fetch_array($res["dataSet"], OCI_ASSOC+OCI_RETURN_NULLS);
        var_dump($row);
    }




    // $stid = oci_parse($conn, 'SELECT * FROM employees');
    // oci_execute($stid);
    //
    // echo "<table border='1'>\n";
    // while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
    //     echo "<tr>\n";
    //     foreach ($row as $item) {
    //         echo "    <td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>\n";
    //     }
    //     echo "</tr>\n";
    // }
    // echo "</table>\n";
?>

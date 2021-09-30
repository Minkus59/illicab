<?php
session_start();

if (isset($_SESSION['NeuroAdmin'])) {
    $SessionAdmin=$_SESSION['NeuroAdmin'];

    $VerifSessionAdmin=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_compte_Admin WHERE hash=:hash");
    $VerifSessionAdmin->bindParam(':hash', $SessionAdmin, PDO::PARAM_STR);
    $VerifSessionAdmin->execute();

    $NumRowSessionAdmin=$VerifSessionAdmin->rowCount();

    if ((isset($SessionAdmin))&&($NumRowSessionAdmin==1)) {
        $Cnx_Admin=true;
    }
    else {
        $Cnx_Admin=false;
    }
}    
else {
        $Cnx_Admin=false;
}

if (isset($_SESSION['NeuroClient'])) {
    $SessionClient=$_SESSION['NeuroClient'];
    $SessionGroup=$_SESSION['Compte'];

    $VerifSessionClient=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Client WHERE hash=:hash");
    $VerifSessionClient->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
    $VerifSessionClient->execute();

    $NumRowSessionClient=$VerifSessionClient->rowCount();

    if ((isset($SessionClient))&&($NumRowSessionClient==1)) {
        $Cnx_Client=true;
        if ($SessionGroup=="Livreur") {
            $Cnx_Chauffeur=true;
        }
        else {
            $Cnx_Chauffeur=false;
        }
    }
    else {
        $Cnx_Client=false;
        $Cnx_Chauffeur=false;
    }
}   
else {
    $Cnx_Client=false;
    $Cnx_Chauffeur=false;
}
?>
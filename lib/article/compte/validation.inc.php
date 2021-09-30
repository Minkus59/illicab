<section>
<div id="Center">
 
 <?php 
$Erreur=$_GET['erreur'];
$Valid=$_GET['valid'];
$Client=trim($_GET['id']);
$Valided=trim($_GET['Valid']);

if ((isset($Client))&&(!empty($Client))&&(isset($Valided))&&(!empty($Valided))) {

    $VerifClient=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Client WHERE hash=:hash");
    $VerifClient->bindParam(':hash', $Client, PDO::PARAM_STR);
    $VerifClient->execute();
    $NbRowsClient=$VerifClient->rowCount();

    $VerifValid=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Client WHERE activate=:valid AND hash=:hash");
    $VerifValid->bindParam(':valid', $Valided, PDO::PARAM_STR);
    $VerifValid->bindParam(':hash', $Client, PDO::PARAM_STR);  
    $VerifValid->execute();
    $NbRowsValid=$VerifValid->rowCount();
        
    if (strlen($Client)!=32) {
        $Erreur="Le lien de vérification a été modifié, vérifier qu'il correspond a celui reçu dans l'e-mail de confirmation 1!";   
    }

    elseif ($Valided!=1) {
        $Erreur="Le lien de vérification a été modifié, vérifier qu'il correspond a celui reçu dans l'e-mail de confirmation 2!";   
    }

    elseif ($NbRowsClient!=1) {
        $Erreur="Le lien de vérification a été modifié, vérifier qu'il correspond a celui reçu dans l'e-mail de confirmation 3!";
    }

    elseif ($NbRowsValid==1) {
        $Erreur="Votre compte est déjà actif vous pouvez dès à présent vous connecter !<br />";
    }
    else {   
        $InsertValided=$cnx->prepare("UPDATE ".$Prefix."neuro_Client SET activate=1 WHERE hash=:hash");
        $InsertValided->bindParam(':hash', $Client, PDO::PARAM_STR);
        $InsertValided->execute();

        if ((!$VerifValid)||(!$VerifClient)||(!$InsertValided)) {
            $SupprValided=$cnx->prepare("UPDATE ".$Prefix."neuro_Client SET activate=0 WHERE hash=:hash");
            $SupprValided->bindParam(':hash', $Client, PDO::PARAM_STR);
            $SupprValided->execute();

            $Erreur="L'enregistrement des données à échouée, veuillez réessayer ultèrieurement !";
        }
        else {
            $Valid= "Merci d'avoir validé votre compte.<br />";
            $Valid.= "Vous pouvez dès à présent vous connecter !<br />";
        }   
    }
}
else {
    $Erreur="Erreur !";
}
?>
<article>
<?php if (isset($Erreur)) { echo "<font color='#FF0000'>".$Erreur."</font></p>"; } 
if (isset($Valid)) { echo "<font color='#095f07'>".$Valid."</font></p>"; }  ?>

</article>

</div>
</section>
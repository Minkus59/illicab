<section>
<div id="Center">
 
 <?php 
$Erreur=$_GET['erreur'];
$Valid=$_GET['valid'];
$Client=trim($_GET['id']);
$Hash=trim($_GET['hash']);
?>
<article>
<?php if (isset($Erreur)) { echo "<font color='#FF0000'>".$Erreur."</font></p>"; } 
if (isset($Valid)) { echo "<font color='#095f07'>".$Valid."</font></p>"; } 

if ((isset($Client))&&(!empty($Client))&&(isset($Hash))&&(!empty($Hash))) {

    $VerifClient=$cnx->prepare("SELECT (hash_client) FROM ".$Prefix."neuro_secu_mdp WHERE hash_client=:hash_client");
    $VerifClient->bindParam(':hash_client', $Client, PDO::PARAM_STR);
    $VerifClient->execute();
    $NbRowsClient=$VerifClient->rowCount();

    $VerifHash=$cnx->prepare("SELECT (hash) FROM ".$Prefix."neuro_secu_mdp WHERE hash=:hash");
    $VerifHash->bindParam(':hash', $Hash, PDO::PARAM_STR);
    $VerifHash->execute();
    $NbRowsHash=$VerifHash->rowCount();
        
    if (strlen($Client)!=32) {
        echo "Le lien de vérification a été modifié, vérifier qu'il correspond a celui reçu dans l'e-mail de confirmation !";   
    }

    elseif (strlen($Hash)!=32) {
        echo "Le lien de vérification a été modifié, vérifier qu'il correspond a celui reçu dans l'e-mail de confirmation !";   
    }

    elseif ($NbRowsClient!=1) {
        echo "Aucune procédure de changement de mot de passe n'a été demander !<br />";
    }

    elseif ($NbRowsHash!=1) {
        echo "Aucune procédure de changement de mot de passe n'a été demander !<br />";
    }

    else { ?>
        <form id="form_mdp" action="<?php echo $Home; ?>/lib/script/compte/mdp.php" method="POST">

        <input type="hidden" name="client" value="<?php echo $Client; ?>"/>
        <label class="col_1">Nouveau mot de passe :</label>
        <input type="password" name="mdp" required="required"/>
        <br />
        <label class="col_1">Confirmer le mot de passe :</label>
        <input type="password" name="mdp2" required="required"/>
        <br />

        <span class="col_1"></span>
        <input type="submit" name="Valider" value="Valider"/>
        </form><?php 
    }
}
else {
    echo "Erreur !";
}
?>
</article>

</div>
</section>
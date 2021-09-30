<?
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php"); 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");     
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/log.inc.php");   

$Client=trim($_POST['client']);
$Mdp=trim($_POST['mdp']);
$Mdp2=trim($_POST['mdp2']);

if (isset($_POST['Valider'])) {

    if (strlen($Mdp)<=7) { 
        $Erreur="Le mot de passe n'est pas conforme !<br />";
        $Erreur.="Le mot de passe doit contenir au moin 8 caractères !<br />";
        ErreurLog($Erreur);
        header("location:".$Home."/Validation/Mdp/?erreur=".urlencode($Erreur));
    }

    elseif ($Mdp!=$Mdp2) {
        $Erreur="Les mot de passe saisie doivent êtres identique !<br />";
        ErreurLog($Erreur);
        header("location:".$Home."/Validation/Mdp/?erreur=".urlencode($Erreur));
    }

    else {
        $RecupCreated=$cnx->prepare("SELECT (created) FROM ".$Prefix."neuro_Client WHERE hash=:hash_client");
        $RecupCreated->bindParam(':hash_client', $Client, PDO::PARAM_STR);
        ErreurLog($Erreur);
        $RecupCreated->execute();

            $DateCrea=$RecupCreated->fetch(PDO::FETCH_OBJ);
            $Salt=md5($DateCrea->created);
            $Mdp2=md5($Mdp2);
            $MdpCrypt=crypt($Mdp2, $Salt);

        $InsertMdp=$cnx->prepare("UPDATE ".$Prefix."neuro_Client SET mdp=:mdpcrypt WHERE hash=:hash_client");
        $InsertMdp->bindParam(':mdpcrypt', $MdpCrypt, PDO::PARAM_STR);
        $InsertMdp->bindParam(':hash_client', $Client, PDO::PARAM_STR);
        $InsertMdp->execute();

        $DeleteSecu=$cnx->prepare("DELETE FROM ".$Prefix."neuro_secu_mdp WHERE hash_client=:hash_client");
        $DeleteSecu->bindParam(':hash_client', $Client, PDO::PARAM_STR);
        $DeleteSecu->execute();

        $Valid= "Votre mot de passe a bien été validé !<br />";
        $Valid.= "Vous pouvez dès à présent vous connecter !<br />";
        header("location:".$Home."/Mon-compte/?valid=".urlencode($Valid));
    }
}
?>
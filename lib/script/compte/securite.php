<?
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php"); 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");    
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/log.inc.php");    

$Email=filter_input(INPUT_POST,'email', FILTER_SANITIZE_STRIPPED);
$Email=filter_var($Email, FILTER_SANITIZE_EMAIL);
$Hash=md5(uniqid(rand(), true));
$Now=time();

if (isset($_POST['Recevoir'])) {

    $VerifEmail=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Client WHERE email=:email");
    $VerifEmail->bindParam(':email', $Email, PDO::PARAM_STR);
    $VerifEmail->execute();
    $NbRowsEmail=$VerifEmail->rowCount();
    $Data=$VerifEmail->fetch(PDO::FETCH_OBJ);

    $Client=$Data->hash;
  
    $VerifSecu=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_secu_mdp WHERE hash_client=:hash_client");
    $VerifSecu->bindParam(':hash_client', $Client, PDO::PARAM_STR);
    $VerifSecu->execute();
    $NbRowsClient=$VerifSecu->rowCount();
    
  if ($NbRowsClient==1) {    
        $Body="Veuillez cliquer sur le lien suivant pour changer votre mot de passe sur $Home .</p>                        
            <a href='http://www.neuro-soft.fr/Validation/Mdp/?id=$Client&hash=$Hash'>Cliquez ici</a>";

        if (EnvoiNotification($Societe, $Serveur, $Destinataire, "illicab - Procédure de changement de mot de passe", $Body, $Email)==false) {
            $Erreur="L'e-mail n'a pu être envoyé, vérifiez que vous l'avez entré correctement !</p>";
            ErreurLog($Erreur);
            header("location:".$Home."/Securite/?erreur=".urlencode($Erreur));
        } 
        else {
            $InsertHash=$cnx->prepare("UPDATE ".$Prefix."neuro_secu_mdp SET hash=:hash WHERE hash_client=:hash_client");
            $InsertHash->bindParam(':hash', $Hash, PDO::PARAM_STR);
            $InsertHash->bindParam(':hash_client', $Client, PDO::PARAM_STR);
            $InsertHash->execute();

            $Erreur="Une procédure de changement de mot de passe à déjà été demander !<br />";
            $Erreur.="Un E-mail de confirmation vous a été envoyé à l'adresse suivante : ".$Email."<br />"; 
            ErreurLog($Erreur);
            header("location:".$Home."/Securite/?erreur=".urlencode($Erreur));
        }
    }

    elseif (!preg_match("#^[A-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $Email)) { 
        $Erreur="L'adresse e-mail n'est pas conforme !<br />";
        ErreurLog($Erreur);
        header("location:".$Home."/Securite/?erreur=".urlencode($Erreur));
    }
    
    elseif ($NbRowsEmail!=1) {          
        $Erreur="Cette adresse n'existe pas !<br />";
        ErreurLog($Erreur);
        header("location:".$Home."/Securite/?erreur=".urlencode($Erreur));
    }

    else {
        $Body="<font color='#9e2053'><H1>Procédure de changement de mot de passe</H1></font>
        Veuillez cliquer sur le lien suivant pour changer votre mot de passe sur $Home .</p>                        
            <a href='$Home/Validation/Mdp/?id=$Client&hash=$Hash'>Cliquez ici</a>";

        if (EnvoiNotification($Societe, $Serveur, $Destinataire, "illicab - Changement de mot de passe", $Body, $Email)==false) {
            $Erreur="L'e-mail n'a pu être envoyé, vérifiez que vous l'avez entré correctement !</p>";
            ErreurLog($Erreur);
            header("location:".$Home."/Securite/?erreur=".urlencode($Erreur));
        } 
        else {
            $InsertHash=$cnx->prepare("INSERT INTO ".$Prefix."neuro_secu_mdp (hash, hash_client, created) VALUES(:hash, :hash_client, :created)");
            $InsertHash->bindParam(':hash', $Hash, PDO::PARAM_STR);
            $InsertHash->bindParam(':hash_client', $Client, PDO::PARAM_STR);
            $InsertHash->bindParam(':created', $Now, PDO::PARAM_STR);
            $InsertHash->execute();

            $Valid="Un E-mail de confirmation vous a été envoyé à l'adresse suivante : ".$Email."<br />";
            header("location:".$Home."/Securite/?valid=".urlencode($Valid));
             
        }
    }
}
?>


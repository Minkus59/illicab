<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php"); 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");     
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/log.inc.php");   

$Civilite=$_SESSION['civilite']=$_POST['civilite'];
$Email=$_SESSION['email']=FiltreEmail('email');
$Mdp=FiltreMDP('mdp');
$Mdp2=FiltreMDP('mdp2');
$Nom=filter_input(INPUT_POST,'nom', FILTER_SANITIZE_STRIPPED);
$Nom=$_SESSION['nom']=filter_var($Nom, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$Prenom=filter_input(INPUT_POST,'prenom', FILTER_SANITIZE_STRIPPED);
$Prenom=$_SESSION['prenom']=filter_var($Prenom, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$Tel=filter_input(INPUT_POST,'tel', FILTER_SANITIZE_STRIPPED);
$Tel=$_SESSION['tel']=filter_var($Tel, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

$Code = md5(uniqid(rand(), true));
$Client=$Code;
$Created=time();

$Body="Bonjour, Merci d'avoir cr&eacute;&eacute; votre compte client chez ".$Societe.".<BR /><BR />
    Veuillez cliquer sur le lien suivant pour valider votre inscription sur ".$Home.".<BR />                  
    <a href='".$Home."/Validation/?id=".$Client."&Valid=1'>Cliquez ici</a>
    <p>Conseils de s&eacute;curit&eacute; importants :</p>
    <ol>
    <li>Vos informations de compte doivent rester confidentielles.</li>
    <li>Ne les communiquez jamais &agrave; qui que ce soit.</li>
    <li>Changez votre mot de passe r&eacute;guli&egrave;rement.</li>
    <li>Si vous pensez que quelqu'un utilise votre compte ill&eacute;galement, veuillez nous pr&eacute;venir imm&eacute;diatement.</li>
    </ol>";
  
if (($_POST['Valider'])&&($_POST['Valider']=="M'inscrire")) {

    $VerifEmail=$cnx->prepare("SELECT (email) FROM ".$Prefix."neuro_Client WHERE email=:email");
    $VerifEmail->bindParam(':email', $Email, PDO::PARAM_STR);
    $VerifEmail->execute();
    $NbRowsEmail=$VerifEmail->rowCount();
  
   if ($Email[0]===false) {
       $Erreur=$Email[1]; 
       ErreurLog($Erreur);
       header("location:".$Home."/Mon-compte/?erreur=".urlencode($Erreur));
   }
    elseif ($NbRowsEmail>=1) {          
        $Erreur="Cette adresse E-mail existe déjà, veuillez en choisir une autre !<br />";
        ErreurLog($Erreur);
        header("location:".$Home."/Mon-compte/?erreur=".urlencode($Erreur));
    }
   elseif ($Mdp[0]===false) {
       $Erreur=$Mdp[1]; 
       ErreurLog($Erreur);
       header("location:".$Home."/Mon-compte/?erreur=".urlencode($Erreur));
   }
    elseif ($Mdp!=$Mdp2) {
        $Erreur="Les mot de passe saisie doivent êtres identique !<br />";
        ErreurLog($Erreur);
        header("location:".$Home."/Mon-compte/?erreur=".urlencode($Erreur));
    }
    elseif (strlen($Nom)<=2) { 
        $Erreur="Le nom doit etre saisie !<br />";
        ErreurLog($Erreur);
        header("location:".$Home."/Mon-compte/?erreur=".urlencode($Erreur));
    }
    elseif (strlen($Tel)<=5) { 
        $Erreur="Le numéro de téléphone doit etre saisie !<br />";
        ErreurLog($Erreur);
        header("location:".$Home."/Mon-compte/?erreur=".urlencode($Erreur));
    }
    else {
        $InsertUser=$cnx->prepare("INSERT INTO ".$Prefix."neuro_Client (titre, email, nom, prenom, telephone, hash, created) VALUES (:titre, :email, :nom, :prenom, :telephone, :hash, :created)");
        $InsertUser->bindParam(':titre', $Civilite, PDO::PARAM_STR);
        $InsertUser->bindParam(':email', $Email, PDO::PARAM_STR);
        $InsertUser->bindParam(':nom', $Nom, PDO::PARAM_STR);
        $InsertUser->bindParam(':prenom', $Prenom, PDO::PARAM_STR);  
        $InsertUser->bindParam(':telephone', $Tel, PDO::PARAM_STR);
        $InsertUser->bindParam(':hash', $Client, PDO::PARAM_STR);
        $InsertUser->bindParam(':created', $Created, PDO::PARAM_STR);  
        $InsertUser->execute();  

        $RecupCreated=$cnx->prepare("SELECT (created) FROM ".$Prefix."neuro_Client WHERE hash=:hash");
        $RecupCreated->bindParam(':hash', $Client, PDO::PARAM_STR);
        $RecupCreated->execute();

            $DateCrea=$RecupCreated->fetch(PDO::FETCH_OBJ);
            $Salt=md5($DateCrea->created);
            $Mdp2=md5($Mdp2);
            $MdpCrypt=crypt($Mdp2, $Salt);

        $InsertMdp=$cnx->prepare("UPDATE ".$Prefix."neuro_Client SET mdp=:mdpcrypt WHERE hash=:hash");
        $InsertMdp->bindParam(':mdpcrypt', $MdpCrypt, PDO::PARAM_STR);
        $InsertMdp->bindParam(':hash', $Client, PDO::PARAM_STR);
        $InsertMdp->execute();

        if (($InsertUser===false)||($InsertMdp===false)||($RecupCreated===false)||($VerifEmail===false)) {

            $DeleteUser=$cnx->prepare("DELETE FROM ".$Prefix."neuro_Client WHERE hash=:hash");
            $DeleteUser->bindParam(':hash', $Client, PDO::PARAM_STR);
            $DeleteUser->execute();

            $Erreur="L'enregistrement des données à échouée, veuillez réessayer ultèrieurement !<br />";
            ErreurLog($Erreur);
            header("location:".$Home."/Mon-compte/?erreur=".urlencode($Erreur));

        }
        
        else {
            if (EnvoiNotification($Societe, $Serveur, $Destinataire, "illicab - Validation d'inscription", $Body, $Email)==false) {
                $Erreur="L'e-mail n'a pu être envoyé, vérifiez que vous l'avez entré correctement !</p>";
                ErreurLog($Erreur);
                header("location:".$Home."/Mon-compte/?erreur=".urlencode($Erreur));     
            }            
            else {
                $Valid="Bonjour, ".$Nom." ".$Prenom."<br />";
                $Valid.="Merci de vous être inscrit sur ".$Home."<br />";
                $Valid.="Un E-mail de confirmation vous a été envoyé à l'adresse suivante : ".$Email."<br />";
                $Valid.="Veuillez valider votre adresse e-mail avant de vous connecter !<br />";

                unset($_SESSION['civilite']);
                unset($_SESSION['email']);
                unset($_SESSION['nom']);
                unset($_SESSION['prenom']);
                unset($_SESSION['tel']);

                if (isset($_SESSION['panierTrajet'])) {
                  header("location:".$Home."/Panier/Identification//?valid=".urlencode($Valid));
                }
                else {
                  header("location:".$Home."/Mon-compte/?valid=".urlencode($Valid));
                }
            }
        }  
    }  
}
else {
        header("location:".$Home."/");
} 
?>
<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php"); 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");   
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/log.inc.php");   

if (isset($_POST['modifierEmail'])) {

  $Email=filter_input(INPUT_POST,'email', FILTER_SANITIZE_STRIPPED);
  $Email=filter_var($Email, FILTER_SANITIZE_EMAIL);
  $Mdp=filter_input(INPUT_POST,'mdp', FILTER_SANITIZE_STRIPPED);
  $Mdp=filter_var($Mdp, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
  $Mdp2=filter_input(INPUT_POST,'mdp2', FILTER_SANITIZE_STRIPPED);
  $Mdp2=filter_var($Mdp2, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

  $VerifEmail=$cnx->prepare("SELECT (email) FROM ".$Prefix."neuro_Client WHERE email=:email");
  $VerifEmail->bindParam(':email', $Email, PDO::PARAM_STR);
  $VerifEmail->execute();
  $NbRowsEmail=$VerifEmail->rowCount();

    if (!preg_match("#^[A-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $Email)) { 
        $Erreur="L'adresse e-mail n'est pas conforme !<br />";
        ErreurLog($Erreur);
        header("location:".$Home."/Mon-compte/?erreur=".urlencode($Erreur));
    }
    elseif ($NbRowsEmail>=1) {          
        $Erreur="Cette adresse E-mail existe déjà, veuillez en choisir une autre !<br />";
        ErreurLog($Erreur);
        header("location:".$Home."/Mon-compte/?erreur=".urlencode($Erreur));
    }
  else {
        $InsertMail=$cnx->prepare("UPDATE ".$Prefix."neuro_Client SET email=:email WHERE hash=:hash");
        $InsertMail->bindParam(':email', $Email, PDO::PARAM_STR);
        $InsertMail->bindParam(':hash', $SessionClient, PDO::PARAM_STR);  
        $InsertMail->execute();   
    
    if (!$InsertMail) {
        $Erreur="L'enregistrement des données à échouée, veuillez réessayer ultèrieurement !<br />";
        ErreurLog($Erreur);
        header("location:".$Home."/Mon-compte/?erreur=".urlencode($Erreur));
    }
    else {
      $Valid="Enregistrement effectué avec succès !<br />";
      header("location:".$Home."/Mon-compte/?valid=".urlencode($Valid));
    }
  }
}

elseif (isset($_POST['modifiermdp'])) {
  $Email=filter_input(INPUT_POST,'email', FILTER_SANITIZE_STRIPPED);
  $Email=filter_var($Email, FILTER_SANITIZE_EMAIL);
  $Mdp=filter_input(INPUT_POST,'mdp', FILTER_SANITIZE_STRIPPED);
  $Mdp=filter_var($Mdp, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
  $Mdp2=filter_input(INPUT_POST,'mdp2', FILTER_SANITIZE_STRIPPED);
  $Mdp2=filter_var($Mdp2, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if (strlen($Mdp)<=7) { 
        $Erreur="Le mot de passe n'est pas conforme !<br />";
        $Erreur.="Le mot de passe doit contenir au moin 8 caractères ! ".$Mdp."<br />";
        ErreurLog($Erreur);
        header("location:".$Home."/Mon-compte/?erreur=".urlencode($Erreur));
    }
  
    elseif ($Mdp!=$Mdp2) {
        $Erreur="Les mot de passe saisie doivent êtres identique !<br />";
        ErreurLog($Erreur);
        header("location:".$Home."/Mon-compte/?erreur=".urlencode($Erreur));
    }
  else {
        $RecupCreated=$cnx->prepare("SELECT (created) FROM ".$Prefix."neuro_Client WHERE hash=:hash");
        $RecupCreated->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
        $RecupCreated->execute();

            $DateCrea=$RecupCreated->fetch(PDO::FETCH_OBJ);
            $Salt=md5($DateCrea->created);
            $Mdp=md5($Mdp);
            $MdpCrypt=crypt($Mdp, $Salt);

        $InsertMdp=$cnx->prepare("UPDATE ".$Prefix."neuro_Client SET mdp=:mdpcrypt WHERE hash=:hash");
        $InsertMdp->bindParam(':mdpcrypt', $MdpCrypt, PDO::PARAM_STR);
        $InsertMdp->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
        $InsertMdp->execute();
  
      if ($InsertMdp===false) {
        $Erreur="L'enregistrement des données à échouée, veuillez réessayer ultèrieurement !<br />";
        ErreurLog($Erreur);
        header("location:".$Home."/Mon-compte/?erreur=".urlencode($Erreur));
    }
    else {
      $Valid="Enregistrement effectué avec succès !<br />";
      header("location:".$Home."/Mon-compte/?valid=".urlencode($Valid));
    }
  }
}

elseif (isset($_POST['modifier'])) {

   $Civilite=$_POST['civilite'];
   $Nom=filter_input(INPUT_POST,'nom', FILTER_SANITIZE_STRIPPED);
   $Nom=filter_var($Nom, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
   $Prenom=filter_input(INPUT_POST,'prenom', FILTER_SANITIZE_STRIPPED);
   $Prenom=filter_var($Prenom, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
   $Tel=filter_input(INPUT_POST,'tel', FILTER_SANITIZE_STRIPPED);
   $Tel=filter_var($Tel, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

     if (strlen($Nom)<=2) { 
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
       try{
            $InsertUser=$cnx->prepare("UPDATE ".$Prefix."neuro_Client SET titre=:titre, nom=:nom, prenom=:prenom, telephone=:telephone WHERE hash=:hash");
            $InsertUser->bindParam(':titre', $Civilite, PDO::PARAM_STR);
            $InsertUser->bindParam(':nom', $Nom, PDO::PARAM_STR);
            $InsertUser->bindParam(':prenom', $Prenom, PDO::PARAM_STR);
            $InsertUser->bindParam(':telephone', $Tel, PDO::PARAM_STR);
            $InsertUser->bindParam(':hash', $SessionClient, PDO::PARAM_STR);  
            $InsertUser->execute();  

            $Valid="Enregistrement effectué avec succès !<br />";
            header("location:".$Home."/Mon-compte/?valid=".urlencode($Valid));
       }
       catch(Exception $e) {
            $Erreur="L'enregistrement des données à échouée, veuillez réessayer ultèrieurement !<br />";
            ErreurLog($Erreur);
            header("location:".$Home."/Mon-compte/?erreur=".urlencode($Erreur));
       }

     }
}
else {
  header("location:".$Home."/Mon-compte/");
}
?>
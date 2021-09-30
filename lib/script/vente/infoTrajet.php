<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php"); 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");    
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/log.inc.php");    

$RecupParam=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_parametre");
$RecupParam->execute();
$Param=$RecupParam->fetch(PDO::FETCH_OBJ);

if(isset($_POST['Valider'])) {
  $Id=$_GET['id'];
  $Valid1=$_POST['valid1'];
  $Now=time();

  if(!empty($_POST['numero'])) {
    $_SESSION['numero']=$_POST['numero'];
  }
  if(!empty($_POST['jour'])) {
    $_SESSION['jour']=$_POST['jour'];
  }
  if(!empty($_POST['mois'])) {
    $_SESSION['mois']=$_POST['mois'];
  }
  if(!empty($_POST['annee'])) {
    $_SESSION['annee']=$_POST['annee'];
  }
  if(!empty($_POST['heure'])) {
    $_SESSION['heure']=$_POST['heure'];
  }
  if(!empty($_POST['min'])) {
    $_SESSION['min']=$_POST['min'];
  }

  $SelectTrajet=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_vol WHERE hash_trajet=:hash_trajet");
  $SelectTrajet->bindParam(':hash_trajet', $Id, PDO::PARAM_STR);
  $SelectTrajet->execute();
  $Trajet=$SelectTrajet->fetch(PDO::FETCH_OBJ);
  $Count=$SelectTrajet->rowCount();

  //Verif condition et Exactitude des donnee
  if ($Valid1!=true) {
    $Erreur.="Les informations n'ont pas été vérifiées !<BR />";
  }
  if ($_SESSION['jour']=="NULL") {
    $Erreur.="Veuillez selectionner un jour de départ<BR />";
  }
  if ($_SESSION['mois']=="NULL") {
    $Erreur.="Veuillez selectionner un mois de départ<BR />";
  }
  if ($_SESSION['annee']=="NULL") {
    $Erreur.="Veuillez selectionner une année de départ<BR />";
  }
  if ($_SESSION['heure']=="NULL") {
    $Erreur.="Veuillez selectionner l'heure de départ<BR />";
  }
  if ($_SESSION['min']=="NULL") {
    $Erreur.="Veuillez selectionner les minutes de départ<BR />";
  }

  if (isset($Erreur)) {
    header("location:".$Home."/Mon-compte/Mes-trajets/?id=".$Id."&erreur=".urlencode($Erreur));
  }
  else {
    $Date = mktime($_SESSION['heure'], $_SESSION['min'], "0", $_SESSION['mois'], $_SESSION['jour'], $_SESSION['annee']);

    if ($Count==0) {
      // Ajout du panier a la bdd ainsi que les info de contact
      $InsertPanier=$cnx->prepare("INSERT INTO ".$Prefix."neuro_vol (numero, date, hash_trajet, created) VALUES(:numero, :date, :hash_trajet, :created)");
      $InsertPanier->BindParam(':numero', $_SESSION['numero'], PDO::PARAM_STR);
      $InsertPanier->BindParam(':date', $Date, PDO::PARAM_STR);
      $InsertPanier->BindParam(':hash_trajet', $Id, PDO::PARAM_STR);
      $InsertPanier->BindParam(':created', $Now , PDO::PARAM_STR);
      $InsertPanier->execute();

      $Titre="illicab - Nouvelle information de vol";
    }
    else {
      $InsertPanier=$cnx->prepare("UPDATE ".$Prefix."neuro_vol SET numero=:numero, date=:date WHERE hash_trajet=:hash_trajet");
      $InsertPanier->BindParam(':numero', $_SESSION['numero'], PDO::PARAM_STR);
      $InsertPanier->BindParam(':date', $Date, PDO::PARAM_STR);
      $InsertPanier->BindParam(':hash_trajet', $Id, PDO::PARAM_STR);
      $InsertPanier->execute();

      $Titre="illicab - Modification des informations de vol";
    }

    //Envoi d'email Avertissement Commande + Recap client
      $Body="<b>Numéro de trajet : </b>".$Id."<BR /><BR />
      
          <b>Numéro de vol : </b>".$_SESSION['numero']."<BR />
          <b>Date de décollage : </b>".$_SESSION['jour']." / ".$_SESSION['mois']." / ".$_SESSION['annee']."<BR />
          <b>Heure de décollage : </b>".$_SESSION['heure']."h ".$_SESSION['min']."min";


      if (EnvoiNotification($Societe, $Serveur, $Destinataire, $Titre, $Body, $Param->email)==false) {
          $Erreur="L'e-mail n'a pu être envoyé, vérifiez que vous l'avez entré correctement !</p>";
          ErreurLog($Erreur);
          header("location:".$Home."/Mon-compte/Mes-trajets/?erreur=".urlencode($Erreur));
      }
      else {
        unset($_SESSION['numero']);
        unset($_SESSION['jour']);
        unset($_SESSION['mois']);
        unset($_SESSION['annee']);
        unset($_SESSION['heure']);
        unset($_SESSION['min']);

        //Redirection sur une page explicative sur le fonctionnement 
        $Valid="Merci d'avoir enregistrer vos informations de vol<BR />";
        header("location:".$Home."/Mon-compte/Mes-trajets/?valid=".urlencode($Valid));
      }
  }
}
elseif(isset($_POST['Annuler'])) {
    unset($_SESSION['numero']);
    unset($_SESSION['jour']);
    unset($_SESSION['mois']);
    unset($_SESSION['annee']);
    unset($_SESSION['heure']);
    unset($_SESSION['min']);

    header("location:".$Home."/Mon-compte/Mes-trajets/");
}
else {
  header("location:".$Home);
}
?>
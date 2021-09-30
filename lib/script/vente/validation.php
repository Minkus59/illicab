<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php"); 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");    
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/log.inc.php");    

if(isset($_POST['Valider'])) {
  $Valid1=$_POST['valid1'];
  $Valid2=$_POST['valid2'];
  $Nom=FiltreText('nom');
  $Prenom=FiltreText('prenom');
  $Tel=FiltreTel('tel');
  $Email=FiltreEmail('email');
  $Civilite=$_POST['civilite'];
  $DistanceAller=$_POST['DistanceAller'];
  $TempsAller=$_POST['TempsAller'];
  $PrixAller=$_POST['PrixAller'];
  $DistanceRetour=$_POST['DistanceRetour'];
  $TempsRetour=$_POST['TempsRetour'];
  $PrixRetour=$_POST['PrixRetour'];
  $Now=time();

  $Code_trajetAller = md5(uniqid(rand(), true));
  $Hash_trajetAller=substr($Code_trajetAller, 0, 8);
  if (!isset($_SESSION['hash_trajetAller'])) {
    $_SESSION['hash_trajetAller']=$Hash_trajetAller;
  }

  //Recuperation d'envoi
  $RecupEmail=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_parametre");
  $RecupEmail->execute();
  $InfoDesti=$RecupEmail->fetch(PDO::FETCH_OBJ);
  
  //Verif condition et Exactitude des donnee
  if (!isset($_SESSION['panierTrajet'])) {
    $Erreur="La session à expiré, veuillez réessayer !";
    header("location:".$Home."/?erreur=".$Erreur);
  }
  elseif (($Valid1!=true)||($Valid2!=true)) {
    $Erreur="Les conditions d'utilisation n'ont pas été accepté !";
    header("location:".$Home."/Panier/Validation/?erreur=".urlencode($Erreur));
  }
  elseif ($Nom[0]===false) {
    $Erreur=$Nom[1];
    header("location:".$Home."/Panier/Validation/?erreur=".urlencode($Erreur));
  }
  elseif ($Prenom[0]===false) {
    $Erreur=$Prenom[1];
    header("location:".$Home."/Panier/Validation/?erreur=".urlencode($Erreur));
  }
  elseif ($Tel[0]===false) {
    $Erreur=$Tel[1];
    header("location:".$Home."/Panier/Validation/?erreur=".urlencode($Erreur));
  }
  elseif ($Email[0]===false) {
    $Erreur=$Email[1];
    header("location:".$Home."/Panier/Validation/?erreur=".urlencode($Erreur));
  }
  elseif((empty($DistanceAller))||
          (empty($TempsAller))||
          (empty($PrixAller))) {
    $Erreur="Erreur 015 : Une erreur est survenue, veuillez réessayer !";
    header("location:".$Home."/Panier/Validation/?erreur=".urlencode($Erreur));
  }
  else {
    if ($_SESSION['panierTrajet']==1) {

      // Ajout du panier a la bdd ainsi que les info de contact
      $InsertPanier=$cnx->prepare("INSERT INTO ".$Prefix."neuro_Trajet 
      (pro, passager, type, type2, Depart, Arriver, Prix, Distance, Temps, hash_commande, hash_trajet, client, created) 
      VALUES(:pro, :passager, :type, '1', :Depart, :Arriver, :Prix, :Distance, :Temps, :hash_commande, :hash_trajet, :client, :created)");
      $InsertPanier->BindParam(':pro', $_SESSION['panierTrajet'] , PDO::PARAM_STR);
      $InsertPanier->BindParam(':type', $_SESSION['trajet'] , PDO::PARAM_STR);
      $InsertPanier->BindParam(':passager', $_SESSION['passager'] , PDO::PARAM_STR);
      $InsertPanier->BindParam(':Depart', $_SESSION['depart'] , PDO::PARAM_STR);
      $InsertPanier->BindParam(':Arriver', $_SESSION['destination'] , PDO::PARAM_STR);
      $InsertPanier->BindParam(':Prix', $PrixAller , PDO::PARAM_STR);
      $InsertPanier->BindParam(':Distance', $DistanceAller , PDO::PARAM_STR);
      $InsertPanier->BindParam(':Temps', $TempsAller , PDO::PARAM_STR);
      $InsertPanier->BindParam(':hash_commande', $_SESSION['hash_commande'] , PDO::PARAM_STR);
      $InsertPanier->BindParam(':hash_trajet', $_SESSION['hash_trajetAller'] , PDO::PARAM_STR);
      $InsertPanier->BindParam(':client', $SessionClient , PDO::PARAM_STR);
      $InsertPanier->BindParam(':created', $Now , PDO::PARAM_STR);
      $InsertPanier->execute();

      if ($_SESSION['trajet']==2) {
        $Code_trajetRetour = md5(uniqid(rand(), true));
        $Hash_trajetRetour=substr($Code_trajetRetour, 0, 8);
        if (!isset($_SESSION['hash_trajetRetour'])) {
          $_SESSION['hash_trajetRetour']=$Hash_trajetRetour;
        }

        $InsertPanier=$cnx->prepare("INSERT INTO ".$Prefix."neuro_Trajet 
        (pro, passager, type, type2, Depart, Arriver, Prix, Distance, Temps, hash_commande, hash_trajet, client, created) 
        VALUES(:pro, :passager, :type, '2', :Depart, :Arriver, :Prix, :Distance, :Temps, :hash_commande, :hash_trajet, :client, :created)");
        $InsertPanier->BindParam(':pro', $_SESSION['panierTrajet'] , PDO::PARAM_STR);
        $InsertPanier->BindParam(':type', $_SESSION['trajet'] , PDO::PARAM_STR);
        $InsertPanier->BindParam(':passager', $_SESSION['passager'] , PDO::PARAM_STR);
        $InsertPanier->BindParam(':Depart', $_SESSION['destinationRetour'] , PDO::PARAM_STR);
        $InsertPanier->BindParam(':Arriver', $_SESSION['depart'] , PDO::PARAM_STR);
        $InsertPanier->BindParam(':Prix', $PrixRetour , PDO::PARAM_STR);
        $InsertPanier->BindParam(':Distance', $DistanceRetour , PDO::PARAM_STR);
        $InsertPanier->BindParam(':Temps', $TempsRetour , PDO::PARAM_STR);
        $InsertPanier->BindParam(':hash_commande', $_SESSION['hash_commande'] , PDO::PARAM_STR);
        $InsertPanier->BindParam(':hash_trajet', $_SESSION['hash_trajetRetour'] , PDO::PARAM_STR);
        $InsertPanier->BindParam(':client', $SessionClient , PDO::PARAM_STR);
        $InsertPanier->BindParam(':created', $Now , PDO::PARAM_STR);
        $InsertPanier->execute();
      }

      $InsertContact=$cnx->prepare("INSERT INTO ".$Prefix."neuro_Contact 
      (civilite, nom, prenom, tel, email, hash_commande, created) 
      VALUES(:civilite, :nom, :prenom, :tel, :email, :hash_commande, :created)");
      $InsertContact->BindParam(':civilite', $Civilite , PDO::PARAM_STR);
      $InsertContact->BindParam(':nom', $Nom , PDO::PARAM_STR);
      $InsertContact->BindParam(':prenom', $Prenom , PDO::PARAM_STR);
      $InsertContact->BindParam(':tel', $Tel , PDO::PARAM_STR);
      $InsertContact->BindParam(':email', $Email , PDO::PARAM_STR);
      $InsertContact->BindParam(':hash_commande', $_SESSION['hash_commande'] , PDO::PARAM_STR);
      $InsertContact->BindParam(':created', $Now , PDO::PARAM_STR);
      $InsertContact->execute();
    }
    else {
      // Ajout du panier a la bdd ainsi que les info de contact PRO
      $InsertPanier=$cnx->prepare("INSERT INTO ".$Prefix."neuro_Trajet 
      (pro, passager, type, type2, Depart, Arriver, Prix, Distance, Temps, hash_commande, hash_trajet, client, created) 
      VALUES(:pro, :passager, :type, '1', :Depart, :Arriver, :Prix, :Distance, :Temps, :hash_commande, :hash_trajet, :client, :created)");
      $InsertPanier->BindParam(':pro', $_SESSION['panierTrajet'] , PDO::PARAM_STR);
      $InsertPanier->BindParam(':passager', $_SESSION['passagerPro'] , PDO::PARAM_STR);
      $InsertPanier->BindParam(':type', $_SESSION['trajetPro'] , PDO::PARAM_STR);
      $InsertPanier->BindParam(':Depart', $_SESSION['departPro'] , PDO::PARAM_STR);
      $InsertPanier->BindParam(':Arriver', $_SESSION['destinationPro'] , PDO::PARAM_STR);
      $InsertPanier->BindParam(':Prix', $PrixAller , PDO::PARAM_STR);
      $InsertPanier->BindParam(':Distance', $DistanceAller , PDO::PARAM_STR);
      $InsertPanier->BindParam(':Temps', $TempsAller , PDO::PARAM_STR);
      $InsertPanier->BindParam(':hash_commande', $_SESSION['hash_commande'] , PDO::PARAM_STR);
      $InsertPanier->BindParam(':hash_trajet', $_SESSION['hash_trajetAller'] , PDO::PARAM_STR);
      $InsertPanier->BindParam(':client', $SessionClient , PDO::PARAM_STR);
      $InsertPanier->BindParam(':created', $Now , PDO::PARAM_STR);
      $InsertPanier->execute();

      if ($_SESSION['trajetPro']==2) {
        $Code_trajetRetour = md5(uniqid(rand(), true));
        $Hash_trajetRetour=substr($Code_trajetRetour, 0, 8);
        if (!isset($_SESSION['hash_trajetRetour'])) {
          $_SESSION['hash_trajetRetour']=$Hash_trajetRetour;
        }

        $InsertPanier=$cnx->prepare("INSERT INTO ".$Prefix."neuro_Trajet 
        (pro, passager, type, type2, Depart, Arriver, Prix, Distance, Temps, hash_commande, hash_trajet, client, created) 
        VALUES(:pro, :passager, :type, '2', :Depart, :Arriver, :Prix, :Distance, :Temps, :hash_commande, :hash_trajet, :client, :created)");
        $InsertPanier->BindParam(':pro', $_SESSION['panierTrajet'] , PDO::PARAM_STR);
        $InsertPanier->BindParam(':passager', $_SESSION['passagerPro'] , PDO::PARAM_STR);
        $InsertPanier->BindParam(':type', $_SESSION['trajetPro'] , PDO::PARAM_STR);
        $InsertPanier->BindParam(':Depart', $_SESSION['destinationRetourPro'] , PDO::PARAM_STR);
        $InsertPanier->BindParam(':Arriver', $_SESSION['departPro'] , PDO::PARAM_STR);
        $InsertPanier->BindParam(':Prix', $PrixRetour , PDO::PARAM_STR);
        $InsertPanier->BindParam(':Distance', $DistanceRetour , PDO::PARAM_STR);
        $InsertPanier->BindParam(':Temps', $TempsRetour , PDO::PARAM_STR);
        $InsertPanier->BindParam(':hash_commande', $_SESSION['hash_commande'] , PDO::PARAM_STR);
        $InsertPanier->BindParam(':hash_trajet', $_SESSION['hash_trajetRetour'] , PDO::PARAM_STR);
        $InsertPanier->BindParam(':client', $SessionClient , PDO::PARAM_STR);
        $InsertPanier->BindParam(':created', $Now , PDO::PARAM_STR);
        $InsertPanier->execute();
      }

      $InsertContact=$cnx->prepare("INSERT INTO ".$Prefix."neuro_Contact 
      (civilite, nom, prenom, tel, email, hash_commande, created) 
      VALUES(:civilite, :nom, :prenom, :tel, :email, :hash_commande, :created)");
      $InsertContact->BindParam(':civilite', $Civilite , PDO::PARAM_STR);
      $InsertContact->BindParam(':nom', $Nom , PDO::PARAM_STR);
      $InsertContact->BindParam(':prenom', $Prenom , PDO::PARAM_STR);
      $InsertContact->BindParam(':tel', $Tel , PDO::PARAM_STR);
      $InsertContact->BindParam(':email', $Email , PDO::PARAM_STR);
      $InsertContact->BindParam(':hash_commande', $_SESSION['hash_commande'] , PDO::PARAM_STR);
      $InsertContact->BindParam(':created', $Now , PDO::PARAM_STR);
      $InsertContact->execute();
    }

    if ((!$InsertPanier)||(!$InsertContact)) {
      $Erreur="Erreur 016 : Une erreur est survenue, veuillez réessayer !";
      header("location:".$Home."/Panier/Validation/?erreur=".urlencode($Erreur));
    }
    else {
      //Envoi d'email Avertissement Commande + Recap client
      if ($_SESSION['panierTrajet']==1) {
        $Pro="Particulier";

        if ($_SESSION['trajet']==1) {
          $Type="Aller simple";

          $Body="<b>Type de trajet : </b>".$Pro." - ".$Type."<BR />
          <b>Nombre de passager : </b>".$_SESSION['passager']."<BR /><BR />
          <b>Aller</b><BR />
          Départ : ".$_SESSION['depart']."<BR />
          Arriver : ".$_SESSION['destination']."<BR /><BR />
          <b>Information de contact</b><BR />
          ".$Civilite." ".$Nom." ".$Prenom."<BR />
          ".$Tel."<BR />
          ".$Email;
        }
        else {
          $Type="Aller / Retour";

          $Body="<b>Type de trajet : </b>".$Pro." - ".$Type."<BR />
          <b>Nombre de passager : </b>".$_SESSION['passager']."<BR /><BR />
          <b>Aller</b><BR />
          Départ : ".$_SESSION['depart']."<BR />
          Arriver : ".$_SESSION['destination']."<BR /><BR />
          <b>Retour</b><BR />
          Départ : ".$_SESSION['destinationRetour']."<BR />
          Arriver : ".$_SESSION['depart']."<BR /><BR />
          <b>Information de contact</b><BR />
          ".$Civilite." ".$Nom." ".$Prenom."<BR />
          ".$Tel."<BR />
          ".$Email;
        }
      }
      else {
        $Pro="Professionnel";

        if ($_SESSION['trajetPro']==1) {
          $Type="Aller simple";

          $Body="<b>Type de trajet : </b>".$Pro." - ".$Type."<BR />
          <b>Nombre de passager : </b>".$_SESSION['passagerPro']."<BR /><BR />
          <b>Aller</b><BR />
          Départ : ".$_SESSION['departPro']."<BR />
          Arriver : ".$_SESSION['destinationPro']."<BR /><BR />
          <b>Information de contact</b><BR />
          ".$Civilite." ".$Nom." ".$Prenom."<BR />
          ".$Tel."<BR />
          ".$Email;
        }
        else {
          $Type="Aller / Retour";

          $Body="<b>Type de trajet : </b>".$Pro." - ".$Type."<BR />
          <b>Nombre de passager : </b>".$_SESSION['passagerPro']."<BR /><BR />
          <b>Aller</b><BR />
          Départ : ".$_SESSION['departPro']."<BR />
          Arriver : ".$_SESSION['destinationPro']."<BR /><BR />
          <b>Retour</b><BR />
          Départ : ".$_SESSION['destinationRetourPro']."<BR />
          Arriver : ".$_SESSION['departPro']."<BR /><BR />
          <b>Information de contact</b><BR />
          ".$Civilite." ".$Nom." ".$Prenom."<BR />
          ".$Tel."<BR />
          ".$Email;
        }
      }

      if (EnvoiNotification($Societe, $Serveur, $Destinataire, "illicab - Nouvelle demande de trajet", $Body, $InfoDesti->email)==false) {
          $Erreur="L'e-mail n'a pu être envoyé, vérifiez que vous l'avez entré correctement !</p>";
          ErreurLog($Erreur);
          header("location:".$Home."/Panier/Validation/?erreur=".urlencode($Erreur));
      } 
      else {   
        unset($_SESSION['depart']);
        unset($_SESSION['destination']);
        unset($_SESSION['passager']);
        unset($_SESSION['destinationRetour']);
        unset($_SESSION['departPro']);
        unset($_SESSION['destinationPro']);
        unset($_SESSION['passagerPro']);
        unset($_SESSION['trajet']);
        unset($_SESSION['trajetPro']);
        unset($_SESSION['destinationRetourPro']);
        unset($_SESSION['panierTrajet']);
        unset($_SESSION['hash_trajetAller']);
        unset($_SESSION['hash_trajetRetour']);
        unset($_SESSION['hash_commande']);

        //Redirection sur une page explicative sur le fonctionnement 
        $Valid="Votre trajet à été pris en compte<BR />";
        $Valid.="Vous recevrez une confirmation dans les plus bref délais";
        header("location:".$Home."/Mon-compte/Mes-trajets/?valid=".urlencode($Valid));
      }
    }
  }
}
else {
  header("location:".$Home);
}
?>
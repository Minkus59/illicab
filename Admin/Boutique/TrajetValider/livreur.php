<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");  
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/log.inc.php"); 

if ($Cnx_Admin===false) {
  header('location:'.$Home.'/Admin');
}

$Id=$_GET['id'];
$Chauffeur=$_POST['livreur'];
$Vehicule=$_POST['vehicule'];
$Now=time();

$RecupParam=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_parametre");
$RecupParam->execute();
$Param=$RecupParam->fetch(PDO::FETCH_OBJ);

$RecupTrajet=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Trajet WHERE hash_trajet=:hash_trajet");
$RecupTrajet->BindParam(':hash_trajet', $Id, PDO::PARAM_STR);
$RecupTrajet->execute();
$Trajet=$RecupTrajet->fetch(PDO::FETCH_OBJ);

$SelectContact=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Contact WHERE hash_commande=:hash_commande");
$SelectContact->bindParam(':hash_commande', $Trajet->hash_commande, PDO::PARAM_STR);
$SelectContact->execute(); 
$Contact=$SelectContact->fetch(PDO::FETCH_OBJ);

$SelectVehicule=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Vehicule WHERE hash_vehicule=:hash_vehicule");
$SelectVehicule->bindParam(':hash_vehicule', $Vehicule, PDO::PARAM_STR);
$SelectVehicule->execute(); 
$VehiculeSelect=$SelectVehicule->fetch(PDO::FETCH_OBJ);

$SelectNewchauffeur=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Client WHERE hash=:hash");
$SelectNewchauffeur->BindParam(':hash', $Chauffeur, PDO::PARAM_STR);
$SelectNewchauffeur->execute();
$ChauffeurNew=$SelectNewchauffeur->fetch(PDO::FETCH_OBJ);

$RecupChauffeur=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_chauffeur WHERE hash_trajet=:hash_trajet");
$RecupChauffeur->BindParam(':hash_trajet', $Id, PDO::PARAM_STR);
$RecupChauffeur->execute();
$ChauffeurActu=$RecupChauffeur->fetch(PDO::FETCH_OBJ);
$CountChauffeur=$RecupChauffeur->rowCount();

$SelectAncienchauffeur=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Client WHERE hash=:hash");
$SelectAncienchauffeur->BindParam(':hash', $ChauffeurActu->hash_client, PDO::PARAM_STR);
$SelectAncienchauffeur->execute();
$Ancienchauffeur=$SelectAncienchauffeur->fetch(PDO::FETCH_OBJ);

$MontantCommandeValid=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Trajet WHERE hash_commande=:hash_commande");
$MontantCommandeValid->bindParam(':hash_commande', $Trajet->hash_commande, PDO::PARAM_STR);
$MontantCommandeValid->execute(); 
$TotalValid=0;

while ($MontantValid=$MontantCommandeValid->fetch(PDO::FETCH_OBJ)) { 
    $TotalValid+=$MontantValid->Prix;
}

if (isset($_POST['Confirmer'])) {
    if ($Chauffeur=="NULL") {
        $Erreur="Veuillez selectionner un chauffeur !<br />";
        ErreurLog($Erreur);
        header("location:".$Home."/Admin/Boutique/TrajetValider/?erreur=".urlencode($Erreur));
    }
    else {
        $Update=$cnx->prepare("INSERT INTO ".$Prefix."neuro_chauffeur (hash_client, hash_trajet, hash_vehicule, created) VALUES(:hash_client, :hash_trajet, :hash_vehicule, :created)");
        $Update->BindParam(':hash_client', $Chauffeur, PDO::PARAM_STR);
        $Update->BindParam(':hash_trajet', $Id, PDO::PARAM_STR);
        $Update->BindParam(':hash_vehicule', $Vehicule, PDO::PARAM_STR);
        $Update->BindParam(':created', $Now, PDO::PARAM_STR);
        $Update->execute();

        if (!$Update) {
            $Erreur="L'enregistrement des données à échouée, veuillez réessayer ultèrieurement !<br />";
            ErreurLog($Erreur);
            header("location:".$Home."/Admin/Boutique/TrajetValider/?erreur=".urlencode($Erreur));
        }
        else {
            if ($Trajet->type2==1) {
                $Type="Trajet Aller";
            }
            else {
                $Type="Trajet retour";
            }
            if ($Trajet->pro==1) {
                $Pro="Particulier";
            }
            else {
                $Pro="Professionnel";
            }
        //Envoi de la Feuille de route au chauffeur
            //Envoi d'email Avertissement Commande + Recap client

            $Body="Trajet n°: ".$Id."<BR /><BR />
            <b>Type de trajet : </b>".$Pro." - ".$Type."<BR />
            <b>Nombre de passager : </b>".$Trajet->passager."<BR />
            <b>Montant du trajet : </b>".$TotalValid." €<BR /><BR />
            <b>Trajet</b><BR />
            Départ : ".$Trajet->Depart."<BR />
            Arriver : ".$Trajet->Arriver."<BR /><BR />
            <b>Information de contact</b><BR />
            ".$Contact->civilite." ".$Contact->nom." ".$Contact->prenom."<BR />
            ".$Contact->tel."<BR />
            ".$Contact->email."<BR /><BR />
            <b>Véhicule : </b>".$VehiculeSelect->libele;

            if (EnvoiNotification($Societe, $Serveur, $Destinataire, "illicab - Nouvelle feuille de route", $Body, $ChauffeurNew->email)==false) {
                $Erreur="L'e-mail n'a pu être envoyé, Veuillez vérifier l'adresse email du client ! </p>";
                ErreurLog($Erreur);
                header("location:".$Home."/Admin/Boutique/TrajetValider/?erreur=".urlencode($Erreur));
            } 
            else {   
                //Redirection sur une page explicative sur le fonctionnement 
                $Valid="Feuille de route envoyer au chauffeur";
                header("location:".$Home."/Admin/Boutique/TrajetValider/?valid=".urlencode($Valid));
            }
        }
    }
}
elseif(isset($_POST['Modifier'])) {
    if ($Chauffeur=="NULL") {
        $Erreur="Veuillez selectionner un chauffeur !<br />";
        ErreurLog($Erreur);
        header("location:".$Home."/Admin/Boutique/TrajetValider/?erreur=".urlencode($Erreur));
    }
    else {
        //prevenir l'ancien chauffeur de l'annulation du trajet'
        if ($Trajet->type2==1) {
            $Type="Trajet Aller";
        }
        else {
            $Type="Trajet retour";
        }
        if ($Trajet->pro==1) {
            $Pro="Particulier";
        }
        else {
            $Pro="Professionnel";
        }

        $Body="Trajet n°: ".$Id." a été annuler<BR /><BR /><BR /><BR />
        <b>Type de trajet : </b>".$Pro." - ".$Type."<BR />
        <b>Nombre de passager : </b>".$Trajet->passager."<BR />
        <b>Montant du trajet : </b>".$TotalValid." €<BR /><BR />
        <b>Trajet</b><BR />
        Départ : ".$Trajet->Depart."<BR />
        Arriver : ".$Trajet->Arriver."<BR /><BR />
        <b>Information de contact</b><BR />
        ".$Contact->civilite." ".$Contact->nom." ".$Contact->prenom."<BR />
        ".$Contact->tel."<BR />
        ".$Contact->email."<BR /><BR />
        <b>Véhicule : </b>".$VehiculeSelect->libele;

        if (EnvoiNotification($Societe, $Serveur, $Destinataire, "illicab - Annulation de trajet", $Body, $Ancienchauffeur->email)==false) {
            $Erreur="L'e-mail n'a pu être envoyé, Veuillez vérifier l'adresse email de l'ancien chauffeur ! </p>";
            ErreurLog($Erreur);
            header("location:".$Home."/Admin/Boutique/TrajetValider/?erreur=".urlencode($Erreur));
        }
        else {   
            $Update=$cnx->prepare("UPDATE ".$Prefix."neuro_chauffeur SET hash_client=:hash_client, hash_vehicule=:hash_vehicule WHERE hash_trajet=:hash_trajet");
            $Update->BindParam(':hash_trajet', $Id, PDO::PARAM_STR);
            $Update->BindParam(':hash_client', $Chauffeur, PDO::PARAM_STR);
            $Update->BindParam(':hash_vehicule', $Vehicule, PDO::PARAM_STR);
            $Update->execute();

            if (!$Update) {
                $Erreur="L'enregistrement des données à échouée, veuillez réessayer ultèrieurement !<br />";
                ErreurLog($Erreur);
                header("location:".$Home."/Admin/Boutique/TrajetValider/?erreur=".urlencode($Erreur));
            }
            else {
                if ($Trajet->type2==1) {
                    $Type="Trajet Aller";
                }
                else {
                    $Type="Trajet retour";
                }
                if ($Trajet->pro==1) {
                    $Pro="Particulier";
                }
                else {
                    $Pro="Professionnel";
                }
                //Envoi de la Feuille de route au nouveau chauffeur
                //Envoi d'email Avertissement Commande + Recap client

                $Body="Trajet n°: ".$Id."<BR /><BR />
                <b>Type de trajet : </b>".$Pro." - ".$Type."<BR />
                <b>Nombre de passager : </b>".$Trajet->passager."<BR />
                <b>Montant du trajet : </b>".$TotalValid." €<BR /><BR />
                <b>Trajet</b><BR />
                Départ : ".$Trajet->Depart."<BR />
                Arriver : ".$Trajet->Arriver."<BR /><BR />
                <b>Information de contact</b><BR />
                ".$Contact->civilite." ".$Contact->nom." ".$Contact->prenom."<BR />
                ".$Contact->tel."<BR />
                ".$Contact->email."<BR /><BR />
                <b>Véhicule : </b>".$VehiculeSelect->libele;

                if (EnvoiNotification($Societe, $Serveur, $Destinataire, "illicab - Nouvelle feuille de route", $Body, $ChauffeurNew->email)==false) {
                    $Erreur="L'e-mail n'a pu être envoyé, Veuillez vérifier l'adresse email du client ! </p>";
                    ErreurLog($Erreur);
                    header("location:".$Home."/Admin/Boutique/TrajetValider/?erreur=".urlencode($Erreur));
                } 
                else {   
                    $Valid="Feuille de route envoyer au chauffeur";
                    header("location:".$Home."/Admin/Boutique/TrajetValider/?valid=".urlencode($Valid));  
                }
            }
        }
    }
}
else {
    header('location:'.$Home.'/Admin/');
}
?>
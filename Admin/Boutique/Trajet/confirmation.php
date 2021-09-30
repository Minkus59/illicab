<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");  
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/log.inc.php"); 

if ($Cnx_Admin===false) {
  header('location:'.$Home.'/Admin');
}

$Confirmation=$_POST['confirmation'];
$InfoRefu=$_POST['infoRefu'];
$Id=$_GET['id'];
$Now=time();

$jour=$_POST['jour'];
$mois=$_POST['mois'];
$annee=$_POST['annee'];
$heure=$_POST['heure'];
$minute=$_POST['min'];
$Commentaire=$_POST['commentaire'];

$RecupParam=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_parametre");
$RecupParam->execute();
$Param=$RecupParam->fetch(PDO::FETCH_OBJ);

$RecupTrajet=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Trajet WHERE hash_trajet=:hash_trajet");
$RecupTrajet->BindParam(':hash_trajet', $Id, PDO::PARAM_STR);
$RecupTrajet->execute();
$Trajet=$RecupTrajet->fetch(PDO::FETCH_OBJ);

$RecupContact=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Contact WHERE hash_commande=:hash_commande");
$RecupContact->BindParam(':hash_commande', $Trajet->hash_commande, PDO::PARAM_STR);
$RecupContact->execute();
$Contact=$RecupContact->fetch(PDO::FETCH_OBJ);

$RecuptRefu=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_refu WHERE hash_trajet=:hash_trajet");
$RecuptRefu->BindParam(':hash_trajet', $Id, PDO::PARAM_STR);
$RecuptRefu->execute();
$CountRefu=$RecuptRefu->rowCount();

$RecuptPrise=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_prise WHERE hash_trajet=:hash_trajet");
$RecuptPrise->BindParam(':hash_trajet', $Id, PDO::PARAM_STR);
$RecuptPrise->execute();
$CountPrise=$RecuptPrise->rowCount();

if (isset($_POST['Confirmer'])) {
    //Refuser
    if ($Confirmation==3) {
        $Update=$cnx->prepare("UPDATE ".$Prefix."neuro_Trajet SET etat=:confirmation WHERE hash_trajet=:hash_trajet");
        $Update->BindParam(':confirmation', $Confirmation, PDO::PARAM_STR);
        $Update->BindParam(':hash_trajet', $Id, PDO::PARAM_STR);
        $Update->execute();

        if ($CountRefu==0) {
            $InsertRefu=$cnx->prepare("INSERT INTO ".$Prefix."neuro_refu (motif, hash_trajet, created) VALUES(:motif, :hash_trajet, :created)");
            $InsertRefu->BindParam(':motif', $InfoRefu, PDO::PARAM_STR);
            $InsertRefu->BindParam(':hash_trajet', $Id, PDO::PARAM_STR);
            $InsertRefu->BindParam(':created', $Now, PDO::PARAM_STR);
            $InsertRefu->execute();
        }
        else {
            $InsertRefu=$cnx->prepare("UPDATE ".$Prefix."neuro_refu SET motif=:motif WHERE hash_trajet=:hash_trajet");
            $InsertRefu->BindParam(':motif', $InfoRefu, PDO::PARAM_STR);
            $InsertRefu->BindParam(':hash_trajet', $Id, PDO::PARAM_STR);
            $InsertRefu->execute();
        }

        if ((!$Update)||(!$InsertRefu)) {
            $Erreur="L'enregistrement des données à échouée, veuillez réessayer ultèrieurement !<br />";
            ErreurLog($Erreur);
            header("location:".$Home."/Admin/Boutique/Trajet/?erreur=".urlencode($Erreur));
        }
        else {
            $Body="Votre trajet n°: ".$Id." a été refusé par notre équipe<BR /><BR />
                  Toutes les informations concernant votre trajet sont disponible <a href='".$Home."/Mon-compte/Mes-trajets/'>ici</a>";

            if (EnvoiNotification($Societe, $Serveur, $Destinataire, "illicab - Trajet refuser", $Body, $Contact->email)==false) {
                $Erreur="L'e-mail n'a pu être envoyé, Veuillez vérifier l'adresse email du client ! </p>";
                ErreurLog($Erreur);
                header("location:".$Home."/Admin/Boutique/Trajet/?erreur=".urlencode($Erreur));
            }
            else {   
                //Redirection sur une page explicative sur le fonctionnement 
                $Valid="Un email de confirmation à été envoyer au client";
                header("location:".$Home."/Admin/Boutique/Trajet/?valid=".urlencode($Valid)."#".$Id);
            }
        }
    }
    else {
        //Accepter
        if ($jour=="NULL") {
            $Erreur.="Veuillez selectionner un jour de départ<BR />";
        }
        if ($mois=="NULL") {
            $Erreur.="Veuillez selectionner un mois de départ<BR />";
        }
        if ($annee=="NULL") {
            $Erreur.="Veuillez selectionner une année de départ<BR />";
        }
        if ($heure=="NULL") {
            $Erreur.="Veuillez selectionner l'heure de départ<BR />";
        }
        if ($minute=="NULL") {
            $Erreur.="Veuillez selectionner les minutes de départ<BR />";
        }

        if (isset($Erreur)) {
            header("location:".$Home."/Admin/Boutique/Trajet/?erreur=".$Erreur);
        }
        else {
            $Date = mktime($heure, $minute, "0", $mois, $jour, $annee);

            $Update=$cnx->prepare("UPDATE ".$Prefix."neuro_Trajet SET etat=:confirmation WHERE hash_trajet=:hash_trajet");
            $Update->BindParam(':confirmation', $Confirmation, PDO::PARAM_STR);
            $Update->BindParam(':hash_trajet', $Id, PDO::PARAM_STR);
            $Update->execute();

            if ($CountPrise==0) {
                $InsertValid=$cnx->prepare("INSERT INTO ".$Prefix."neuro_prise (date, commentaire, hash_trajet, created) VALUES(:date, :commentaire, :hash_trajet, :created)");
                $InsertValid->BindParam(':date', $Date, PDO::PARAM_STR);
                $InsertValid->BindParam(':commentaire', $Commentaire, PDO::PARAM_STR);
                $InsertValid->BindParam(':hash_trajet', $Id, PDO::PARAM_STR);
                $InsertValid->BindParam(':created', $Now, PDO::PARAM_STR);
                $InsertValid->execute();
            }
            else {
                $InsertValid=$cnx->prepare("UPDATE ".$Prefix."neuro_prise SET date=:date, commentaire=:commentaire WHERE hash_trajet=:hash_trajet");
                $InsertValid->BindParam(':date', $Date, PDO::PARAM_STR);
                $InsertValid->BindParam(':commentaire', $Commentaire, PDO::PARAM_STR);
                $InsertValid->BindParam(':hash_trajet', $Id, PDO::PARAM_STR);
                $InsertValid->execute();
            }

            if ((!$Update)||(!$InsertValid)) {
                $Erreur="L'enregistrement des données à échouée, veuillez réessayer ultèrieurement !<br />";
                ErreurLog($Erreur);
                header("location:".$Home."/Admin/Boutique/Trajet/?erreur=".urlencode($Erreur));
            }
            else {
                if ($Confirmation==$Trajet->etat) {
                    $Body="Des modifications ont été apportées au trajet n°: ".$Id."<BR /><BR />
                    Toutes les informations concernant votre trajet sont disponible <a href='".$Home."/Mon-compte/Mes-trajets/'>ici</a>";

                    if (EnvoiNotification($Societe, $Serveur, $Destinataire, "illicab - Modification du trajet n°: ".$Id, $Body, $Contact->email)==false) {
                        $Erreur="L'e-mail n'a pu être envoyé, Veuillez vérifier l'adresse email du client ! </p>";
                        ErreurLog($Erreur);
                        header("location:".$Home."/Admin/Boutique/Trajet/?erreur=".urlencode($Erreur));
                    } 
                    else {   
                        //Redirection sur une page explicative sur le fonctionnement 
                        $Valid="Un email à été envoyé au client";
                        header("location:".$Home."/Admin/Boutique/Trajet/?valid=".urlencode($Valid)."#".$Id);
                    }
                }
                else {
                    $Body="Votre trajet n°: ".$Id." a été valider par notre équipe<BR /><BR />
                    Toutes les informations concernant votre trajet sont disponible <a href='".$Home."/Mon-compte/Mes-trajets/'>ici</a>";

                    if (EnvoiNotification($Societe, $Serveur, $Destinataire, "illicab - Trajet valider", $Body, $Contact->email)==false) {
                        $Erreur="L'e-mail n'a pu être envoyé, Veuillez vérifier l'adresse email du client ! </p>";
                        ErreurLog($Erreur);
                        header("location:".$Home."/Admin/Boutique/Trajet/?erreur=".urlencode($Erreur));
                    } 
                    else {   
                        //Redirection sur une page explicative sur le fonctionnement 
                        $Valid="Un email de confirmation à été envoyé au client";
                        header("location:".$Home."/Admin/Boutique/Trajet/?valid=".urlencode($Valid)."#".$Id);
                    }
                }
            }
        }
    }
}
else {
    header('location:'.$Home.'/Admin');
}
?>
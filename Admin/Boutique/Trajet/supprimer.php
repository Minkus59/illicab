<?php 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");  
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/log.inc.php"); 

if ($Cnx_Admin===false) {
  header('location:'.$Home.'/Admin');
}

$Erreur=$_GET['erreur'];
$Id=$_GET['id'];

if ((!empty($_GET['id']))&&(isset($_POST['oui']))) {

    $SelectTrajet=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Trajet WHERE id=:id");
    $SelectTrajet->bindParam(':id', $Id, PDO::PARAM_INT);
    $SelectTrajet->execute(); 
    $Trajet=$SelectTrajet->fetch(PDO::FETCH_OBJ);
    $NbTrajet=$SelectTrajet->rowCount();

    if ($NbTrajet==1) {
        $DeleteContact=$cnx->prepare("DELETE FROM  ".$Prefix."neuro_Contact WHERE hash_commande=:hash_commande");
        $DeleteContact->bindParam(':hash_commande', $Trajet->hash_commande, PDO::PARAM_STR);
        $DeleteContact->execute(); 
    }

    $DeleteVol=$cnx->prepare("DELETE FROM  ".$Prefix."neuro_vol WHERE hash_trajet=:hash_trajet");
    $DeleteVol->bindParam(':hash_trajet', $Trajet->hash_trajet, PDO::PARAM_STR);
    $DeleteVol->execute(); 

    $DeleteInfoPrise=$cnx->prepare("DELETE FROM  ".$Prefix."neuro_prise WHERE hash_trajet=:hash_trajet");
    $DeleteInfoPrise->bindParam(':hash_trajet', $Trajet->hash_trajet, PDO::PARAM_STR);
    $DeleteInfoPrise->execute(); 

    $DeleteInfoRefu=$cnx->prepare("DELETE FROM  ".$Prefix."neuro_refu WHERE hash_trajet=:hash_trajet");
    $DeleteInfoRefu->bindParam(':hash_trajet', $Trajet->hash_trajet, PDO::PARAM_STR);
    $DeleteInfoRefu->execute(); 

    $DeleteChauffeur=$cnx->prepare("DELETE FROM  ".$Prefix."neuro_chauffeur WHERE hash_trajet=:hash_trajet");
    $DeleteChauffeur->bindParam(':hash_trajet', $Trajet->hash_trajet, PDO::PARAM_STR);
    $DeleteChauffeur->execute(); 

    $SelectReclam=$cnx->prepare("SELECT FROM  ".$Prefix."Reclamation WHERE trajet=:hash_trajet");
    $SelectReclam->bindParam(':hash_trajet', $Trajet->hash_trajet, PDO::PARAM_STR);
    $SelectReclam->execute(); 
    $Reclam=$SelectReclam->fetch(PDO::FETCH_OBJ);

    $DeleteReclamMessage=$cnx->prepare("DELETE FROM  ".$Prefix."Reclamation_Message WHERE hash=:hash");
    $DeleteReclamMessage->bindParam(':hash', $Reclam->hash, PDO::PARAM_STR);
    $DeleteReclamMessage->execute(); 

    $DeleteReclam=$cnx->prepare("DELETE FROM  ".$Prefix."Reclamation WHERE trajet=:hash_trajet");
    $DeleteReclam->bindParam(':hash_trajet', $Trajet->hash_trajet, PDO::PARAM_STR);
    $DeleteReclam->execute(); 

    $deleteTrajet=$cnx->prepare("DELETE FROM ".$Prefix."neuro_Trajet WHERE id=:id");
    $deleteTrajet->bindParam(':id', $Id, PDO::PARAM_INT);
    $deleteTrajet->execute();

    header('location:'.$Home.'/Admin/Boutique/Trajet/');
}

if ((!empty($_GET['id']))&&(isset($_POST['non']))) {  
    header('location:'.$Home.'/Admin/Boutique/Trajet/');
}
?>  


<!-- ************************************
*** Script réalisé par NeuroSoft Team ***
********* www.neuro-soft.fr *************
**************************************-->

<!doctype html>
<html>
<head>


<title>NeuroSoft Team - Accès PRO</title>
  
<meta name="robots" content="noindex, nofollow">

<meta name="author".content="NeuroSoft Team">
<meta name="publisher".content="Helinckx Michael">
<meta name="reply-to" content="contact@neuro-soft.fr">

<meta name="viewport" content="width=device-width" >                                                            

<link rel="shortcut icon" href="<?php echo $Home; ?>/Admin/lib/img/icone.ico">

<link rel="stylesheet" type="text/css" media="screen AND (max-width: 480px)" href="<?php echo $Home; ?>/Admin/lib/css/misenpatel.css" />
<link rel="stylesheet" type="text/css" media="screen AND (min-width: 480px) AND (max-width: 960px)" href="<?php echo $Home; ?>/Admin/lib/css/misenpatab.css" />
<link rel="stylesheet" type="text/css" media="screen AND (min-width: 960px)" href="<?php echo $Home; ?>/Admin/lib/css/misenpapc.css" >
</head>

<body>
<header>
<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/header.inc.php"); ?>
</header>

<section>
    
<nav>
<div id="MenuGauche">
<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/menu.inc.php"); ?>
</div>
</nav>

<article>
<?php if (isset($Erreur)) { echo "<font color='#FF0000'>".$Erreur."</font><BR />"; }
if (isset($Valid)) { echo "<font color='#009900'>".$Valid."</font><BR />"; } ?>

Etes-vous sur de vouloir supprimer ce trajet ? </p>

<TABLE width="300">
<form action="" method="POST">
<TR><TD align="center"><input name="oui" type="submit" value="OUI"></TD><TD align="center"><input name="non" type="submit" value="NON"/></TD></TR>
</form></TABLE>

</article>
</section>
</div>
</CENTER>
</body>

</html>
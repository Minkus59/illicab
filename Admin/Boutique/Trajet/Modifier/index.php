<?php 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");  
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/log.inc.php"); 

if ($Cnx_Admin===false) {
  header('location:'.$Home.'/Admin');
}

$Erreur=$_GET['erreur'];
$Valid=$_GET['valid'];

$SelectDemande=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Trajet WHERE etat='1' ORDER BY created DESC");
$SelectDemande->bindParam(':client', $SessionClient, PDO::PARAM_STR);
$SelectDemande->execute();

$SelectValid=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Trajet WHERE etat='2' ORDER BY created DESC");
$SelectValid->bindParam(':client', $SessionClient, PDO::PARAM_STR);
$SelectValid->execute();

$SelectRefu=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Trajet WHERE etat='0' ORDER BY created DESC");
$SelectRefu->bindParam(':client', $SessionClient, PDO::PARAM_STR);
$SelectRefu->execute(); 
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


<H1><font color="dodgerblue">Vos demandes de disponibilité</font></H1></p>

<form name="form_<?php echo $Demande->id; ?>" action="<?php echo $Home; ?>/Admin/Boutique/Trajet/confirmation.php?id=<?php echo $Demande->id; ?>" method="POST">
<select name="confirmation" onChange="submitForm<?php echo $Demande->id; ?>()">
<option value="NULL" >-- Confirmation --</option>
<option value="2">Accepter</option>
<option value="0">Refuser</option>
</select>
</BR />

<div id="<?php echo 'Affichage'.$Demande->id; ?>"></div>

</form>

</article>
</section>
</div>
</CENTER>
</body>

</html>
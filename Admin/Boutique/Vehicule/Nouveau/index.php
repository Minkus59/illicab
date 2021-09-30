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
$Id=$_GET['id'];
$Libele=trim($_POST['libele']);
$Hash_vehicule = md5(uniqid(rand(), true));

$Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Vehicule WHERE id=:id");
$Select->bindParam(':id', $Id, PDO::PARAM_STR);
$Select->execute();
$Vehicule=$Select->fetch(PDO::FETCH_OBJ);

$VerifExist=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Vehicule WHERE libele=:libele");
$VerifExist->bindParam(':libele', $Libele, PDO::PARAM_STR);
$VerifExist->execute();
$CountDoublon=$VerifExist->rowCount();
        
//Ajout de nouveaux articles
if (isset($_POST['Ajouter'])) {
    if (strlen($Libele)<2) {
        $Erreur="Le champ libélé doit être saisie !";
    }
    elseif($CountDoublon>0) {
        $Erreur="Ce véhicule existe déja !";
    }
    else {
        $InsertArticle=$cnx->prepare("INSERT INTO ".$Prefix."neuro_Vehicule (libele, hash_vehicule) VALUES (:libele, :hash_vehicule)");
        $InsertArticle->bindParam(':libele', $Libele, PDO::PARAM_STR);
        $InsertArticle->bindParam(':hash_vehicule', $Hash_vehicule, PDO::PARAM_STR);
        $InsertArticle->execute();

        $Valid="Destination ajouté avec succès";
        header("location:".$Home."/Admin/Boutique/Vehicule/?valid=".urlencode($Valid));
    }
}

if (isset($_POST['Modifier'])) {
    if (strlen($Libele)<2) {
        $Erreur="Le champ libélé doit être saisie !";
    }
    else {
        $UpdateArticle=$cnx->prepare("UPDATE ".$Prefix."neuro_Vehicule SET libele=:libele WHERE id=:id");
        $UpdateArticle->bindParam(':id', $Id, PDO::PARAM_STR);
        $UpdateArticle->bindParam(':libele', $Libele, PDO::PARAM_STR);
        $UpdateArticle->execute();

        $Valid="Destination modifié avec succès";
        header("location:".$Home."/Admin/Boutique/Vehicule/?valid=".urlencode($Valid));
    }
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

<H1>Nouveau véhicule</H1>

<div id="Gauche">

<form name="form_ajout" action="" method="POST">

<input type="text" name="libele" value="<?php echo $Vehicule->libele; ?>" required />
<BR /><BR />
<?php
if (isset($Id)) {
echo '<input type="submit" name="Modifier" value="Modifier"/>';
}
else {
echo '<input type="submit" name="Ajouter" value="Ajouter"/>';
}
?>
</form>

</article>
</section>
</div>
</CENTER>

</body>

</html>
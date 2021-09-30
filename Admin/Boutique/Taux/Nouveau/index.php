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

$Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Personne WHERE id=:id");
$Select->bindParam(':id', $Id, PDO::PARAM_STR);
$Select->execute();
$ListeTaux=$Select->fetch(PDO::FETCH_OBJ);
        
//Ajout de nouveaux articles
if (isset($_POST['Ajouter'])) {
    
    $Quantite=trim($_POST['quantite']);
    $Taux=trim($_POST['taux']);
    
    if(!preg_match("#[0-9.]#", $Taux)) {
        $Erreur="Le taux Unitaire n'est pas conforme !<br />";
        $Erreur.="Ce champ ne doit contenir que des chiffres !</p>";
    }
    else {
        $InsertArticle=$cnx->prepare("INSERT INTO ".$Prefix."neuro_Personne (quantite, taux) VALUES (:quantite, :taux)");
        $InsertArticle->bindParam(':quantite', $Quantite, PDO::PARAM_STR);
        $InsertArticle->bindParam(':taux', $Taux, PDO::PARAM_STR);
        $InsertArticle->execute();

        $Valid="Taux ajouté avec succès";
        header("location:".$Home."/Admin/Boutique/Taux/?valid=".urlencode($Valid));
    }
}

if (isset($_POST['Modifier'])) {
    
    $Quantite=trim($_POST['quantite']);
    $Taux=trim($_POST['taux']);
    
    if(!preg_match("#[0-9.]#", $Taux)) {
        $Erreur="Le taux Unitaire n'est pas conforme !<br />";
        $Erreur.="Ce champ ne doit contenir que des chiffres !</p>";
    }
    else {
        $UpdateArticle=$cnx->prepare("UPDATE ".$Prefix."neuro_Personne SET quantite=:quantite, taux=:taux WHERE id=:id");
        $UpdateArticle->bindParam(':id', $Id, PDO::PARAM_STR);
        $UpdateArticle->bindParam(':quantite', $Quantite, PDO::PARAM_STR);
        $UpdateArticle->bindParam(':taux', $Taux, PDO::PARAM_STR);
        $UpdateArticle->execute();

        $Valid="Taux modifié avec succès";
        header("location:".$Home."/Admin/Boutique/Taux/?valid=".urlencode($Valid));
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

<H1>Nouveau taux</H1>

<form name="form_ajout" action="" method="POST">

<input type="text" name="quantite" value="<?php echo $ListeTaux->quantite; ?>" required />
<BR />
<input type="text" name="taux" placeholder="taux*" value="<?php echo $ListeTaux->taux; ?>" required="required"/>
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
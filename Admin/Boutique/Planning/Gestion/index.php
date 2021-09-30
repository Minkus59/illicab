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

if (isset($_POST['CodeSemaine2'])) {
   $_SESSION['CodeSemaine2']=$_POST['CodeSemaine2'];
}

if (isset($_POST['codeClient'])) {
   $_SESSION['codeClient']=$_POST['codeClient'];
}

$SemaineActu=date("W", time());

$SelectClient=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Client WHERE groupe='Livreur'");
$SelectClient->execute();

if ((!isset($_SESSION['CodeSemaine2']))||($_SESSION['CodeSemaine2']=="NULL")) {
   if ((!isset($_SESSION['codeClient']))||($_SESSION['codeClient']=="NULL")) {
        $Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Planning ORDER BY id DESC");
        $Select->execute();
   }
   else {
        $Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Planning WHERE user=:user ORDER BY id DESC");
        $Select->bindParam(':user', $_SESSION['codeClient'], PDO::PARAM_STR);
        $Select->execute();
   }
}
else {
   if ((!isset($_SESSION['codeClient']))||($_SESSION['codeClient']=="NULL")) {
      $Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Planning WHERE semaine=:semaine ORDER BY id DESC");
      $Select->bindParam(':semaine', $_SESSION['CodeSemaine2'], PDO::PARAM_STR);
      $Select->execute();
   }
   else {
        $Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Planning WHERE semaine=:semaine AND user=:user ORDER BY id DESC");
        $Select->bindParam(':semaine', $_SESSION['CodeSemaine2'], PDO::PARAM_STR);
        $Select->bindParam(':user', $_SESSION['codeClient'], PDO::PARAM_STR);
        $Select->execute();
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
<?php if (isset($Erreur)) { echo "<font color='#FF0000'>".$Erreur."</font><BR /><BR />"; }
if (isset($Valid)) { echo "<font color='#009900'>".$Valid."</font><BR /><BR />"; } ?>

<form action="" method="POST">
<select name="CodeSemaine2" onChange="this.form.submit()">
<option value="NULL" <?php if ($_SESSION['CodeSemaine2']=="NULL") { echo "selected"; } ?> >-- Tous --</option>
<option value="<?php echo $SemaineActu; ?>" ><?php echo "Semaine actuel : ".$SemaineActu; ?></option>
<?php 
for($CodeSdebut=1;$CodeSdebut<53;$CodeSdebut++) { ?>
  <option value="<?php echo $CodeSdebut; ?>" <?php if ($_SESSION['CodeSemaine2']==$CodeSdebut) { echo "selected"; } ?> ><?php echo "Semaine ".$CodeSdebut; ?></option>
<?php
}
?>
</select>
</form>

<form name="formClient" action="" method="POST">
<select name="codeClient" required="required" onChange="this.form.submit()">
<option value="NULL" <?php if ($_SESSION['codeClient']=="NULL") { echo "selected"; } ?> >-- Tous --</option>
<?php while($Client=$SelectClient->fetch(PDO::FETCH_OBJ)) { ?>
    <option value="<?php echo $Client->hash; ?>" <?php if ($_SESSION['codeClient']==$Client->hash) { echo "selected"; } ?> ><?php echo $Client->nom." ".$Client->prenom; ?></option>
<?php } ?>
</select>
</form>

<H1>Planning</H1>

<a href="<?php echo $Home; ?>/Admin/Boutique/Planning/Nouveau/">Ajouter un service</a>
<table>
<TR>
<TH>Employer</TH>
<TH>Semaine</TH>
<TH>Jour</TH>
<TH>Début de service</TH>
<TH>Fin de service</TH>
<TH>Action</TH>
</TR>
<?php
while($Liste=$Select->fetch(PDO::FETCH_OBJ)) {
    $SelectLivreur=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Client WHERE hash=:hash");
    $SelectLivreur->bindParam(':hash', $Liste->user, PDO::PARAM_STR);
    $SelectLivreur->execute();
    $Livreur=$SelectLivreur->fetch(PDO::FETCH_OBJ);
    $joursFr = Array(1=>'Lundi', 2=>'Mardi', 3=>'Mercredi', 4=>'Jeudi', 5=>'Vendredi', 6=>'Samedi', 7=>'Dimanche');
    ?>
    <TR bgcolor="<?php echo $Livreur->couleur; ?>">
    <TD><?php echo "<b>".$Livreur->nom." ".$Livreur->prenom."</b>"; ?></TD>
    <TD><?php echo $Liste->semaine; ?></TD>
    <TD><?php echo $joursFr[$Liste->jour]; ?></TD>
    <TD><?php echo $Liste->Hdebut.'h'.$Liste->Mdebut; ?></TD>
    <TD><?php echo $Liste->Hfin.'h'.$Liste->Mfin; ?></TD>
    <TD>
    <?php echo '<a href="'.$Home.'/Admin/Boutique/Planning/Nouveau/?id='.$Liste->id.'"><img src="'.$Home.'/Admin/lib/img/modifier.png"></a>'; ?>
    <?php echo '<a href="'.$Home.'/Admin/Boutique/Planning/Gestion/supprimer.php?id='.$Liste->id.'"><img src="'.$Home.'/Admin/lib/img/supprimer.png"></a>'; ?>
    </TD>
    </TR>
    <?php
}
?>
</table>

</article>
</section>
</div>
</CENTER>
</body>

</html>
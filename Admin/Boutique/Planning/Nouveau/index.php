<?php 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");  
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/log.inc.php"); 

if ($Cnx_Admin===false) {
  header('location:'.$Home.'/Admin');
}

$SemaineActu=date("W", time());

$SelectEmployer=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Client WHERE groupe='Livreur'");
$SelectEmployer->execute();

$Erreur=$_GET['erreur'];
$Valid=$_GET['valid'];
$Id=$_GET['id'];

$Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Planning WHERE id=:id");
$Select->bindParam(':id', $Id, PDO::PARAM_STR);
$Select->execute();
$Liste=$Select->fetch(PDO::FETCH_OBJ);

if (isset($_POST['Ajouter'])) {
    
  $Employer=$_POST['employer'];
  $Semaine=$_POST['semaine'];
  $Jour=$_POST['jour'];
  $Hdebut=$_POST['Hdebut'];
  $Mdebut=$_POST['Mdebut'];
  $Hfin=$_POST['Hfin'];
  $Mfin=$_POST['Mfin'];
    
    if ($Employer=="") {
        $Erreur="Veuillez sélectionner un employer !";
    }
    elseif($Semaine=="") {
        $Erreur="Veuillez sélectionner une semaine !";
    }
    elseif($Jour=="") {
        $Erreur="Veuillez sélectionner un jour !";
    }
    elseif($Hdebut=="") {
        $Erreur="Une difficulté avec l'heure de commencement !";
    }
    elseif($Mdebut=="") {
        $Erreur="Une difficulté avec les minutes de commencement !";
    }
    elseif($Hfin=="") {
        $Erreur="Une difficulté avec l'heure de fin !";
    }
    elseif($Mfin=="") {
        $Erreur="Une difficulté avec les minutes de fin !";
    }
    else {
        $Insert=$cnx->prepare("INSERT INTO ".$Prefix."neuro_Planning (user, semaine, jour, Hdebut, Mdebut, Hfin, Mfin) VALUES (:user, :semaine, :jour, :Hdebut, :Mdebut, :Hfin, :Mfin)");
        $Insert->bindParam(':user', $Employer, PDO::PARAM_STR);
        $Insert->bindParam(':semaine', $Semaine, PDO::PARAM_STR);
        $Insert->bindParam(':jour', $Jour, PDO::PARAM_STR);
        $Insert->bindParam(':Hdebut', $Hdebut, PDO::PARAM_STR);
        $Insert->bindParam(':Mdebut', $Mdebut, PDO::PARAM_STR);
        $Insert->bindParam(':Hfin', $Hfin, PDO::PARAM_STR);
        $Insert->bindParam(':Mfin', $Mfin, PDO::PARAM_STR);
        $Insert->execute();

        $Valid="Planning mise à jour avec succès";
        header("location:".$Home."/Admin/Boutique/Planning/Gestion/?valid=".urlencode($Valid));
    }
}

if (isset($_POST['Modifier'])) {

  $Employer=$_POST['employer'];
  $Semaine=$_POST['semaine'];
  $Jour=$_POST['jour'];
  $Hdebut=$_POST['Hdebut'];
  $Mdebut=$_POST['Mdebut'];
  $Hfin=$_POST['Hfin'];
  $Mfin=$_POST['Mfin'];
    
    if ($Employer=="") {
        $Erreur="Veuillez sélectionner un employer !";
    }
    elseif($Semaine=="") {
        $Erreur="Veuillez sélectionner une semaine !";
    }
    elseif($Jour=="") {
        $Erreur="Veuillez sélectionner un jour !";
    }
    elseif($Hdebut=="") {
        $Erreur="Une difficulté avec l'heure de commencement !";
    }
    elseif($Mdebut=="") {
        $Erreur="Une difficulté avec les minutes de commencement !";
    }
    elseif($Hfin=="") {
        $Erreur="Une difficulté avec l'heure de fin !";
    }
    elseif($Mfin=="") {
        $Erreur="Une difficulté avec les minutes de fin !";
    }
    else {
        $Insert=$cnx->prepare("UPDATE ".$Prefix."neuro_Planning SET user=:user, semaine=:semaine, jour=:jour, Hdebut=:Hdebut, Mdebut=:Mdebut, Hfin=:Hfin, Mfin=:Mfin WHERE id=:id");
        $Insert->bindParam(':id', $Id, PDO::PARAM_STR);
        $Insert->bindParam(':user', $Employer, PDO::PARAM_STR);
        $Insert->bindParam(':semaine', $Semaine, PDO::PARAM_STR);
        $Insert->bindParam(':jour', $Jour, PDO::PARAM_STR);
        $Insert->bindParam(':Hdebut', $Hdebut, PDO::PARAM_STR);
        $Insert->bindParam(':Mdebut', $Mdebut, PDO::PARAM_STR);
        $Insert->bindParam(':Hfin', $Hfin, PDO::PARAM_STR);
        $Insert->bindParam(':Mfin', $Mfin, PDO::PARAM_STR);
        $Insert->execute();

        $Valid="Planning mise à jour avec succès";
        header("location:".$Home."/Admin/Boutique/Planning/Gestion/?valid=".urlencode($Valid));
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

<form action="" method="POST">

<label class="col_1"><b>Qui :</b></label>
<select name="employer">
<option value="">-- Employer --</option>
<?php 
while($User=$SelectEmployer->fetch(PDO::FETCH_OBJ)) { 
      ?><option value="<?php echo $User->hash; ?>" <?php if($User->hash==$Liste->user) { echo "selected"; } ?> ><?php echo $User->nom." ".$User->prenom; ?></option><?php
}
?>
</select>

<BR /><BR />

<label class="col_1"><b>Semaine :</b></label>
<select name="semaine">
<option value="<?php echo $SemaineActu; ?>" ><?php echo "Semaine actuel : ".$SemaineActu; ?></option>
<?php 
for($Sdebut=1;$Sdebut<53;$Sdebut++) { 
  ?><option value="<?php echo $Sdebut; ?>" <?php if($Sdebut==$Liste->semaine) { echo "selected"; } ?> ><?php echo $Sdebut; ?></option><?php
}
?>
</select>

<BR /><BR />

<label class="col_1"><b>Le :</b></label>
<select name="jour">
<option value="1" <?php if($Liste->jour=="1") { echo "selected"; } ?> >Lundi</option>
<option value="2" <?php if($Liste->jour=="2") { echo "selected"; } ?> >Mardi</option>
<option value="3" <?php if($Liste->jour=="3") { echo "selected"; } ?> >Mercredi</option>
<option value="4" <?php if($Liste->jour=="4") { echo "selected"; } ?> >Jeudi</option>
<option value="5" <?php if($Liste->jour=="5") { echo "selected"; } ?> >Vendredi</option>
<option value="6" <?php if($Liste->jour=="6") { echo "selected"; } ?> >Samedi</option>
<option value="7" <?php if($Liste->jour=="7") { echo "selected"; } ?> >Dimanche</option>
</select>


<BR /><BR />

<label class="col_1"><b>De :</b></label>
<select name="Hdebut" class="mini">
<?php 
for($Hdebut=0;$Hdebut<24;$Hdebut++) { 
    $HdebutFormat=trim(money_format('%=0(#2.0n', $Hdebut));
    ?><option value="<?php echo $HdebutFormat; ?>" <?php if($Liste->Hdebut==$HdebutFormat) { echo "selected"; } ?> ><?php echo $HdebutFormat; ?></option><?php
}
?>
</select>

<select name="Mdebut" class="mini">
<option value="00" <?php if($Liste->Mdebut=="00") { echo "selected"; } ?> >00min</option>
<option value="30" <?php if($Liste->Mdebut=="30") { echo "selected"; } ?> >30min</option>
</select>

<BR /><BR />

<label class="col_1"><b>à :</b></label>
<select name="Hfin" class="mini">
<?php 
for($Hfin=0;$Hfin<25;$Hfin++) { 
    $HfinFormat=trim(money_format('%=0(#2.0n', $Hfin));
    ?><option value="<?php echo $HfinFormat; ?>" <?php if($Liste->Hfin==$HfinFormat) { echo "selected"; } ?> ><?php echo $HfinFormat; ?></option><?php
}
?>
</select>

<select name="Mfin" class="mini">
<option value="00" <?php if($Liste->Mfin=="00") { echo "selected"; } ?> >00min</option>
<option value="30" <?php if($Liste->Mfin=="30") { echo "selected"; } ?> >30min</option>
</select>

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
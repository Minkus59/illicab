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

$Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_DestinationPro");
$Select->execute();
    
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

<H1>Liste des destinations</H1></p>

<table>
<tr>
      <th>Id</th>
      <th>Libellé</th>
      <th>Prix</th>
      <th>Action</th>
      </tr>
<?php

while ($Destination=$Select->fetch(PDO::FETCH_OBJ)) {
?>
   <tr>
   <td><?php echo $Destination->id; ?></td>
   <td><?php echo $Destination->libele; ?></td>
   <td><?php echo $Destination->prix; ?></td>
   <td><?php 
   echo '<a href="'.$Home.'/Admin/Boutique/DestinationPro/Nouveau/?id='.$Destination->id.'"><img src="'.$Home.'/Admin/lib/img/modifier.png"></a>';
   echo '<a title="Supprimer" href="'.$Home.'/Admin/Boutique/DestinationPro/supprimer.php?id='.$Destination->id.'"><img src="'.$Home.'/Admin/lib/img/supprimer.png"></a></td></tr>';
} ?>
</table>

</article>
</section>
</div>
</CENTER>
</body>

</html>
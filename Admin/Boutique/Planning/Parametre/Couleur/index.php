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

if (isset($_POST['Modifier'])) {

  $Couleur=$_POST['principal'.$i.''];
    
    if($Couleur=="") {
        $Erreur="Veuillez sélectionner une couleur !";
    }
    else {
        $Insert=$cnx->prepare("UPDATE ".$Prefix."neuro_Client SET couleur=:couleur WHERE id=:id");
        $Insert->bindParam(':id', $Id, PDO::PARAM_STR);
        $Insert->bindParam(':couleur', $Couleur, PDO::PARAM_STR);
        $Insert->execute();

        $Valid="Paramètre mise à jour avec succès";
        header("location:".$Home."/Admin/Boutique/Planning/Parametre/?valid=".urlencode($Valid));
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

<H1>Choisissez une couleur</H1>

<form method="POST" action="">
<table id="TableCouleur">
 <tr><td bgColor="#F78181"><input type="radio" name="principal<?php echo $i; ?>" value="#F78181" required="required"></td><td bgColor="#F79F81"><input type="radio" name="principal<?php echo $i; ?>" value="#F79F81"></td><td bgColor="#F7BE81"><input type="radio" name="principal<?php echo $i; ?>" value="#F7BE81"></td><td bgColor="#F5DA81"><input type="radio" name="principal<?php echo $i; ?>" value="#F5DA81"></td><td bgColor="#F3F781"><input type="radio" name="principal<?php echo $i; ?>" value="#F3F781"><td bgColor="#D8F781"><input type="radio" name="principal<?php echo $i; ?>" value="#D8F781"></td></td><td bgColor="#BEF781"><input type="radio" name="principal<?php echo $i; ?>" value="#BEF781"></td><td bgColor="#9FF781"><input type="radio" name="principal<?php echo $i; ?>" value="#9FF781"></td><td bgColor="#81F781"><input type="radio" name="principal<?php echo $i; ?>" value="#81F781"></td><td bgColor="#81F79F"><input type="radio" name="principal<?php echo $i; ?>" value="#81F79F"></td><td bgColor="#81F7BE"><input type="radio" name="principal<?php echo $i; ?>" value="#81F7BE"></td><td bgColor="#81F7D8"><input type="radio" name="principal<?php echo $i; ?>" value="#81F7D8"></td><td bgColor="#81F7F3"><input type="radio" name="principal<?php echo $i; ?>" value="#81F7F3"></td><td bgColor="#81DAF5"><input type="radio" name="principal<?php echo $i; ?>" value="#81DAF5"></td><td bgColor="#81BEF7"><input type="radio" name="principal<?php echo $i; ?>" value="#81BEF7"></td><td bgColor="#819FF7"><input type="radio" name="principal<?php echo $i; ?>" value="#819FF7"></td><td bgColor="#8181F7"><input type="radio" name="principal<?php echo $i; ?>" value="#8181F7"></td><td bgColor="#9F81F7"><input type="radio" name="principal<?php echo $i; ?>" value="#9F81F7"></td><td bgColor="#BE81F7"><input type="radio" name="principal<?php echo $i; ?>" value="#BE81F7"></td><td bgColor="#DA81F5"><input type="radio" name="principal<?php echo $i; ?>" value="#DA81F5"></td><td bgColor="#F781F3"><input type="radio" name="principal<?php echo $i; ?>" value="#F781F3"></td><td bgColor="#F781D8"><input type="radio" name="principal<?php echo $i; ?>" value="#F781D8"></td><td bgColor="#F781BE"><input type="radio" name="principal<?php echo $i; ?>" value="#F781BE"></td><td bgColor="#F7819F"><input type="radio" name="principal<?php echo $i; ?>" value="#F7819F"></td><td bgColor="#FFFFFF"><input type="radio" name="principal<?php echo $i; ?>" value="#FFFFFF"></td></tr>
 <tr><td bgColor="#FE2E2E"><input type="radio" name="principal<?php echo $i; ?>" value="#FE2E2E"></td><td bgColor="#FE642E"><input type="radio" name="principal<?php echo $i; ?>" value="#FE642E"></td><td bgColor="#FE9A2E"><input type="radio" name="principal<?php echo $i; ?>" value="#FE9A2E"></td><td bgColor="#FACC2E"><input type="radio" name="principal<?php echo $i; ?>" value="#FACC2E"></td><td bgColor="#F7FE2E"><input type="radio" name="principal<?php echo $i; ?>" value="#F7FE2E"><td bgColor="#C8FE2E"><input type="radio" name="principal<?php echo $i; ?>" value="#C8FE2E"></td></td><td bgColor="#9AFE2E"><input type="radio" name="principal<?php echo $i; ?>" value="#9AFE2E"></td><td bgColor="#64FE2E"><input type="radio" name="principal<?php echo $i; ?>" value="#64FE2E"></td><td bgColor="#2EFE2E"><input type="radio" name="principal<?php echo $i; ?>" value="#2EFE2E"></td><td bgColor="#2EFE64"><input type="radio" name="principal<?php echo $i; ?>" value="#2EFE64"></td><td bgColor="#2EFE9A"><input type="radio" name="principal<?php echo $i; ?>" value="#2EFE9A"></td><td bgColor="#2EFEC8"><input type="radio" name="principal<?php echo $i; ?>" value="#2EFEC8"></td><td bgColor="#2EFEF7"><input type="radio" name="principal<?php echo $i; ?>" value="#2EFEF7"></td><td bgColor="#2ECCFA"><input type="radio" name="principal<?php echo $i; ?>" value="#2ECCFA"></td><td bgColor="#2E9AFE"><input type="radio" name="principal<?php echo $i; ?>" value="#2E9AFE"></td><td bgColor="#2E64FE"><input type="radio" name="principal<?php echo $i; ?>" value="#2E64FE"></td><td bgColor="#2E2EFE"><input type="radio" name="principal<?php echo $i; ?>" value="#2E2EFE"></td><td bgColor="#642EFE"><input type="radio" name="principal<?php echo $i; ?>" value="#642EFE"></td><td bgColor="#9A2EFE"><input type="radio" name="principal<?php echo $i; ?>" value="#9A2EFE"></td><td bgColor="#CC2EFA"><input type="radio" name="principal<?php echo $i; ?>" value="#CC2EFA"></td><td bgColor="#FE2EF7"><input type="radio" name="principal<?php echo $i; ?>" value="#FE2EF7"></td><td bgColor="#FE2EC8"><input type="radio" name="principal<?php echo $i; ?>" value="#FE2EC8"></td><td bgColor="#FE2E9A"><input type="radio" name="principal<?php echo $i; ?>" value="#FE2E9A"></td><td bgColor="#FE2E64"><input type="radio" name="principal<?php echo $i; ?>" value="#FE2E64"></td><td bgColor="#A4A4A4"><input type="radio" name="principal<?php echo $i; ?>" value="#A4A4A4"></td></tr>
 <tr><td bgColor="#DF0101"><input type="radio" name="principal<?php echo $i; ?>" value="#DF0101"></td><td bgColor="#DF3A01"><input type="radio" name="principal<?php echo $i; ?>" value="#DF3A01"></td><td bgColor="#DF7401"><input type="radio" name="principal<?php echo $i; ?>" value="#DF7401"></td><td bgColor="#DBA901"><input type="radio" name="principal<?php echo $i; ?>" value="#DBA901"></td><td bgColor="#D7DF01"><input type="radio" name="principal<?php echo $i; ?>" value="#D7DF01"><td bgColor="#A5DF00"><input type="radio" name="principal<?php echo $i; ?>" value="#A5DF00"></td></td><td bgColor="#74DF00"><input type="radio" name="principal<?php echo $i; ?>" value="#74DF00"></td><td bgColor="#3ADF00"><input type="radio" name="principal<?php echo $i; ?>" value="#3ADF00"></td><td bgColor="#01DF01"><input type="radio" name="principal<?php echo $i; ?>" value="#01DF01"></td><td bgColor="#01DF3A"><input type="radio" name="principal<?php echo $i; ?>" value="#01DF3A"></td><td bgColor="#01DF74"><input type="radio" name="principal<?php echo $i; ?>" value="#01DF74"></td><td bgColor="#01DFA5"><input type="radio" name="principal<?php echo $i; ?>" value="#01DFA5"></td><td bgColor="#01DFD7"><input type="radio" name="principal<?php echo $i; ?>" value="#01DFD7"></td><td bgColor="#01A9DB"><input type="radio" name="principal<?php echo $i; ?>" value="#01A9DB"></td><td bgColor="#0174DF"><input type="radio" name="principal<?php echo $i; ?>" value="#0174DF"></td><td bgColor="#013ADF"><input type="radio" name="principal<?php echo $i; ?>" value="#013ADF"></td><td bgColor="#0101DF"><input type="radio" name="principal<?php echo $i; ?>" value="#0101DF"></td><td bgColor="#3A01DF"><input type="radio" name="principal<?php echo $i; ?>" value="#3A01DF"></td><td bgColor="#7401DF"><input type="radio" name="principal<?php echo $i; ?>" value="#7401DF"></td><td bgColor="#A901DB"><input type="radio" name="principal<?php echo $i; ?>" value="#A901DB"></td><td bgColor="#DF01D7"><input type="radio" name="principal<?php echo $i; ?>" value="#DF01D7"></td><td bgColor="#DF01A5"><input type="radio" name="principal<?php echo $i; ?>" value="#DF01A5"></td><td bgColor="#DF0174"><input type="radio" name="principal<?php echo $i; ?>" value="#DF0174"></td><td bgColor="#DF013A"><input type="radio" name="principal<?php echo $i; ?>" value="#DF013A"></td><td bgColor="#6E6E6E"><input type="radio" name="principal<?php echo $i; ?>" value="#6E6E6E"></td></tr>
 <tr><td bgColor="#8A0808"><input type="radio" name="principal<?php echo $i; ?>" value="#8A0808"></td><td bgColor="#8A2908"><input type="radio" name="principal<?php echo $i; ?>" value="#8A2908"></td><td bgColor="#8A4B08"><input type="radio" name="principal<?php echo $i; ?>" value="#8A4B08"></td><td bgColor="#886A08"><input type="radio" name="principal<?php echo $i; ?>" value="#886A08"></td><td bgColor="#868A08"><input type="radio" name="principal<?php echo $i; ?>" value="#868A08"><td bgColor="#688A08"><input type="radio" name="principal<?php echo $i; ?>" value="#688A08"></td></td><td bgColor="#4B8A08"><input type="radio" name="principal<?php echo $i; ?>" value="#4B8A08"></td><td bgColor="#298A08"><input type="radio" name="principal<?php echo $i; ?>" value="#298A08"></td><td bgColor="#088A08"><input type="radio" name="principal<?php echo $i; ?>" value="#088A08"></td><td bgColor="#088A29"><input type="radio" name="principal<?php echo $i; ?>" value="#088A29"></td><td bgColor="#088A4B"><input type="radio" name="principal<?php echo $i; ?>" value="#088A4B"></td><td bgColor="#088A68"><input type="radio" name="principal<?php echo $i; ?>" value="#088A68"></td><td bgColor="#088A85"><input type="radio" name="principal<?php echo $i; ?>" value="#088A85"></td><td bgColor="#086A87"><input type="radio" name="principal<?php echo $i; ?>" value="#086A87"></td><td bgColor="#084B8A"><input type="radio" name="principal<?php echo $i; ?>" value="#084B8A"></td><td bgColor="#08298A"><input type="radio" name="principal<?php echo $i; ?>" value="#08298A"></td><td bgColor="#08088A"><input type="radio" name="principal<?php echo $i; ?>" value="#08088A"></td><td bgColor="#29088A"><input type="radio" name="principal<?php echo $i; ?>" value="#29088A"></td><td bgColor="#4B088A"><input type="radio" name="principal<?php echo $i; ?>" value="#4B088A"></td><td bgColor="#6A0888"><input type="radio" name="principal<?php echo $i; ?>" value="#6A0888"></td><td bgColor="#8A0886"><input type="radio" name="principal<?php echo $i; ?>" value="#8A0886"></td><td bgColor="#8A0868"><input type="radio" name="principal<?php echo $i; ?>" value="#8A0868"></td><td bgColor="#8A084B"><input type="radio" name="principal<?php echo $i; ?>" value="#8A084B"></td><td bgColor="#8A0829"><input type="radio" name="principal<?php echo $i; ?>" value="#8A0829"></td><td bgColor="#424242"><input type="radio" name="principal<?php echo $i; ?>" value="#424242"></td></tr>
 <tr><td bgColor="#3B0B0B"><input type="radio" name="principal<?php echo $i; ?>" value="#3B0B0B"></td><td bgColor="#3B170B"><input type="radio" name="principal<?php echo $i; ?>" value="#3B170B"></td><td bgColor="#3B240B"><input type="radio" name="principal<?php echo $i; ?>" value="#3B240B"></td><td bgColor="#3A2F0B"><input type="radio" name="principal<?php echo $i; ?>" value="#3A2F0B"></td><td bgColor="#393B0B"><input type="radio" name="principal<?php echo $i; ?>" value="#393B0B"><td bgColor="#2E3B0B"><input type="radio" name="principal<?php echo $i; ?>" value="#2E3B0B"></td></td><td bgColor="#243B0B"><input type="radio" name="principal<?php echo $i; ?>" value="#243B0B"></td><td bgColor="#173B0B"><input type="radio" name="principal<?php echo $i; ?>" value="#173B0B"></td><td bgColor="#0B3B0B"><input type="radio" name="principal<?php echo $i; ?>" value="#0B3B0B"></td><td bgColor="#0B3B17"><input type="radio" name="principal<?php echo $i; ?>" value="#0B3B17"></td><td bgColor="#0B3B24"><input type="radio" name="principal<?php echo $i; ?>" value="#0B3B24"></td><td bgColor="#0B3B2E"><input type="radio" name="principal<?php echo $i; ?>" value="#0B3B2E"></td><td bgColor="#0B3B39"><input type="radio" name="principal<?php echo $i; ?>" value="#0B3B39"></td><td bgColor="#0B2F3A"><input type="radio" name="principal<?php echo $i; ?>" value="#0B2F3A"></td><td bgColor="#0B243B"><input type="radio" name="principal<?php echo $i; ?>" value="#0B243B"></td><td bgColor="#0B173B"><input type="radio" name="principal<?php echo $i; ?>" value="#0B173B"></td><td bgColor="#0B0B3B"><input type="radio" name="principal<?php echo $i; ?>" value="#0B0B3B"></td><td bgColor="#170B3B"><input type="radio" name="principal<?php echo $i; ?>" value="#170B3B"></td><td bgColor="#240B3B"><input type="radio" name="principal<?php echo $i; ?>" value="#240B3B"></td><td bgColor="#2F0B3A"><input type="radio" name="principal<?php echo $i; ?>" value="#2F0B3A"></td><td bgColor="#3B0B39"><input type="radio" name="principal<?php echo $i; ?>" value="#3B0B39"></td><td bgColor="#3B0B2E"><input type="radio" name="principal<?php echo $i; ?>" value="#3B0B2E"></td><td bgColor="#3B0B24"><input type="radio" name="principal<?php echo $i; ?>" value="#3B0B24"></td><td bgColor="#3B0B17"><input type="radio" name="principal<?php echo $i; ?>" value="#3B0B17"></td><td bgColor="#000000"><input type="radio" name="principal<?php echo $i; ?>" value="#000000"></td></tr>
</table>

<BR /><BR />

<input type="submit" name="Modifier" value="Modifier"/>
</form>

</article>
</section>
</div>
</CENTER>
</body>

</html>
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

$SelectClient=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Client ORDER BY nom ASC");
$SelectClient->execute();
    
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

<form name="liste" action="<?php echo $Home; ?>/Admin/Boutique/Client/Mailing/Envoyer/" method="POST">
    
 <H1>Mailing</H1>   
     
<table width=900>
<tr>
    <th class="TableRose">
        Titre
    </th>
    <th class="TableRose">
        Nom
    </th>
    <th class="TableRose">
        Prenom
    </th>
    <th class="TableRose">
        Téléphone
    </th>
    <th class="TableRose">
        Email
    </th>
    <th class="TableRose">
        Action
    </th>
</tr>
<?php
while($Client=$SelectClient->fetch(PDO::FETCH_OBJ)) { ?>
    <tr>
        <td class="TableRose">
            <?php echo $Client->titre; ?>
        </td>
        <td class="TableRose">
            <?php echo stripslashes($Client->nom); ?>
        </td>
        <td class="TableRose">
            <?php echo stripslashes($Client->prenom); ?>
        </td>
        <td class="TableRose">
            <?php echo nl2br(stripslashes($Client->telephone)); ?>
        </td>
        <td class="TableRose">
            <?php echo stripslashes($Client->email); ?>
        </td>
        <td class="TableRose">
            <input type="checkbox" name="selection[]" value="<?php echo $Client->email; ?>"/>
        </td>
    </tr>
<?php
}
?>
<tr>
<td></td><td></td><td></td><td></td><td></td>
<td>
Tout cocher : <input type="checkbox" onclick="cocher1()" />
</td></tr>
</table><BR /><BR />
Pour la selection : <input type="submit" class="ButtonRose" name="Envoyer" value="Envoyer un e-mail"/>
</form>

</article>
</section>
</div>
</CENTER>
</body>
<script>
function cocher1() {
   var Form = document.forms['liste'];
   var taille = Form.elements.length;
   var element = null;

   for(i=0; i < taille; i++) {
      element = Form.elements[i];
        if(element.type == "checkbox") {
            if (element.checked == false) {
                element.checked = true;
            }
            else {
                element.checked = false;
            }
        }
   }
}
</script>
</html>
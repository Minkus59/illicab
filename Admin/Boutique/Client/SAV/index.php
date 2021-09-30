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

if (isset($_POST['StatueReclamation'])) {
   $_SESSION['StatueReclamation']=$_POST['StatueReclamation'];
}

if (isset($_POST['StatueClient'])) {
   $_SESSION['StatueClient']=$_POST['StatueClient'];
}

$SelectClient=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Client");
$SelectClient->execute();

if ((!isset($_SESSION['StatueClient']))||($_SESSION['StatueClient']=="NULL")) {
    if ((!isset($_SESSION['StatueReclamation']))||($_SESSION['StatueReclamation']=="NULL")) {
    $Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Reclamation ORDER BY id DESC");
    $Select->execute();
    }
    else {
    $Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Reclamation WHERE statue=:statue ORDER BY id DESC");
    $Select->bindParam(':statue', $_SESSION['StatueReclamation'], PDO::PARAM_STR);
    $Select->execute();
    }
}
else {
    if ((!isset($_SESSION['StatueReclamation']))||($_SESSION['StatueReclamation']=="NULL")) {
    $Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Reclamation WHERE client=:client ORDER BY id DESC");
    $Select->bindParam(':client', $_SESSION['StatueClient'], PDO::PARAM_STR);
    $Select->execute();
    }
    else {
    $Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Reclamation WHERE statue=:statue AND client=:client ORDER BY id DESC");
    $Select->bindParam(':statue', $_SESSION['StatueReclamation'], PDO::PARAM_STR);
    $Select->bindParam(':client', $_SESSION['StatueClient'], PDO::PARAM_STR);
    $Select->execute();
    }  
}
    
?>

<!-- ************************************
*** Script realise par NeuroSoft Team ***
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

<H1>Liste des réclamations</H1></p>

<form name="FormClient" action="" method="POST">
<label class="col_2"  for="StatueClient">Client<font color='#FF0000'>*</font> :</label>
<select name="StatueClient" required="required" onChange="this.form.submit()">
<option value="NULL" <?php if ($_SESSION['StatueClient']=="NULL") { echo "selected"; } ?>>Tous</option>
<?php while($InfoClient=$SelectClient->fetch(PDO::FETCH_OBJ)) { ?>
<option value="<?php echo $InfoClient->compte; ?>" <?php if ($_SESSION['StatueClient']==$InfoClient->compte) { echo "selected"; } ?>><?php echo $InfoClient->nom; ?></option>
<?php } ?>
</select>
</form>

<form name="FormStatue" action="" method="POST">
<label class="col_2"  for="StatueReclamation">Statut<font color='#FF0000'>*</font> :</label>
<select name="StatueReclamation" required="required" onChange="this.form.submit()">
<option value="NULL" <?php if ($_SESSION['StatueReclamation']=="NULL") { echo "selected"; } ?>>Tous</option>
<option value="1" <?php if ($_SESSION['StatueReclamation']=="1") { echo "selected"; } ?>>Actif</option>
<option value="0" <?php if ($_SESSION['StatueReclamation']=="0") { echo "selected"; } ?>>Cloturé</option>
</select>
</form>

<p><HR /></p>

<table>
<tr><th>Client </th><th>N° commande</th><th>Sujet</th><th>Date</th><th>Statue</th><th>Action</th></tr>

<?php
while($Info=$Select->fetch(PDO::FETCH_OBJ)) {
  $Select2=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Client WHERE hash=:hash");
  $Select2->bindParam(':hash', $Info->client, PDO::PARAM_STR);
  $Select2->execute();
  $Info2=$Select2->fetch(PDO::FETCH_OBJ);   
?>
   <tr <?php if ($Info->statue==1) { echo "class='rouge'"; } else { echo "class='vert'"; } ?>>
   <td><?php echo $Info2->nom; ?></td>
      <td><?php echo $Info2->commande; ?></td>
   
   <td class="left"><?php echo "<b>Sujet : ".$Info->sujet."</b></p>"; 
    
    $Hash=$Info->hash;

    $Select6=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Reclamation_Message WHERE hash=:hash");
    $Select6->bindParam(':hash', $Hash, PDO::PARAM_STR);
    $Select6->execute(); 
     
   while ($Info3=$Select6->fetch(PDO::FETCH_OBJ)) {  
        
        if ($Info3->auteur=="Admin") {
            if ($Info3->vu==1) { 
                echo "<img class='NewMess' src='".$Home."/Admin/lib/img/NewMessClient.png' title='Non vu par le client'/>";
            }
            else {
                echo "<img class='AncienMess' src='".$Home."/Admin/lib/img/AncienMess.png' title='Vu par le client'/>";
            } 
            echo "<b>Illicab</b> - ".nl2br($Info3->message)."</p> 
            Le ".date("d-m-Y / G:i:s", $Info3->created);
        }
        else { 

            echo "<b>Le client</b> - ".nl2br($Info3->message)."</p> 
            Le ".date("d-m-Y / G:i:s", $Info3->created);
        }
    ?>
    <p><HR /></p>
    <?php
   }
   ?></td>
   
   <td><?php echo date("d-m-Y / G:i:s", $Info->created); ?></td>
   <td><?php if ($Info->statue==1) { echo "Actif"; } else { echo "Cloturé";} ?></td>
             <td><?php if ($Info->statue==1) { echo '<a title="Répondre" href="'.$Home.'/Admin/Boutique/Client/SAV/Visualisation/?id='.$Info->id.'"><img src="'.$Home.'/Admin/lib/img/repondre.png" alt="Répondre"></a>'; } ?>
             <?php if ($Info->statue==1) { ?>
                 <a title="Cloturer la réclamation" href="<?php echo $Home; ?>/Admin/Boutique/Client/SAV/cloturer.php?id=<?php echo $Info->id; ?>"><img src="<?php echo $Home; ?>/Admin/lib/img/ouvrir.png" alt="Cloturer la réclamation"></a>
             <? } else { ?>
                 <a title="Réouvrir la réclamation" href="<?php echo $Home; ?>/Admin/Boutique/Client/SAV/ouvrir.php?id=<?php echo $Info->id; ?>"><img src="<?php echo $Home; ?>/Admin/lib/img/cloturer.png" alt="Réouvrir la réclamation"></a>
             <?php } ?>
             </td></tr>
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
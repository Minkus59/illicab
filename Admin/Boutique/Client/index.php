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
$Groupe=$_POST['groupe'];

if ((isset($Groupe))&&(isset($_POST['id']))) {

  $Update=$cnx->prepare("UPDATE ".$Prefix."neuro_Client SET groupe=:groupe WHERE id=:id");
  $Update->bindParam(':groupe', $Groupe, PDO::PARAM_STR); 
  $Update->bindParam(':id', $_POST['id'], PDO::PARAM_STR); 
  $Update->execute();
  
  header("location:".$Home."/Admin/Boutique/Client/#ligne".$_POST['id']);
}

//Moteur de recherche
if (isset($_POST['MoteurRecherche'])) {
    if (!empty($_POST['RechercheNom'])) {
        $RechercheNom=trim($_POST['RechercheNom']);
        $Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Client WHERE nom LIKE :nom");
        $Select->execute(array(':nom' => "%".$RechercheNom."%")); 
    }
    elseif (!empty($_POST['RecherchePrenom'])) {
        $RecherchePrenom=trim($_POST['RecherchePrenom']);
        $Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Client WHERE prenom LIKE :prenom");
        $Select->execute(array(':prenom' => "%".$RecherchePrenom."%")); 
    }
    elseif (!empty($_POST['RechercheEmail'])) {
        $RechercheEmail=trim($_POST['RechercheEmail']);
        $Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Client WHERE prenom LIKE :prenom");
        $Select->execute(array(':email' => "%".$RechercheEmail."%")); 
    }
    elseif ($_POST['RechercheGroupe']!="") {
        $RechercheGroupe=trim($_POST['RechercheGroupe']);
        $Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Client WHERE groupe=:groupe");
        $Select->execute(array(':groupe' => $RechercheGroupe)); 
    }
    elseif (!empty($_POST['RechercheTel'])) {
        $RechercheTel=trim($_POST['RechercheTel']);
        $Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Client WHERE telephone=:telephone");
        $Select->execute(array(':telephone' => $RechercheTel)); 
    }
    else {
      $Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Client");
      $Select->execute();
    }
}
else {
  $Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Client");
  $Select->execute();
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

<H1>Liste des clients</H1></p>

<table>
<tr>
      <th>Id</th>
      <th>Titre</th>
      <th>Nom </th>
      <th>Prénom</th>
      <th>Téléphone</th>
      <th>E-mail</th>
      <th>Groupe</th>
      <th>Date de création</th>
      <th>Dernière visite</th>
</tr>

<form name="form_recherche" action="#Article" method="POST">
<TR>
    <TH>

    </TH>
    <TH>
        
    </TH>
    <TH>
        <input class="Moyen" type="text" name="RechercheNom"/>
    </TH>
    <TH>
        <input class="Moyen" type="text" name="RecherchePrenom"/>
    </TH>
    <TH>
        <input class="Moyen" type="text" name="RechercheTel"/>
    </TH>
    <TH>
        <input class="Moyen" type="text" name="RechercheEmail"/>
    </TH>
    <TH>
      <select name="RechercheGroupe">
        <option value="">Tous</option>
        <option value="Particulier">Particulier</option>
        <option value="Livreur">Chauffeur</option>
      </select>
    </TH>
    <TH>
        
    </TH>
    <TH>
        <input type="submit" name="MoteurRecherche" value="Rechercher"/>
    </TH>
</TR>
</form>

<?php
while ($Client=$Select->fetch(PDO::FETCH_OBJ)) {
?>
   <tr id="ligne<?php echo $Client->id; ?>">
   <td><?php echo $Client->id; ?></td> 
   <td><?php echo $Client->titre; ?></td>
   <td><?php echo $Client->nom; ?></td>
   <td><?php echo $Client->prenom; ?></td>
   <td><?php echo $Client->telephone; ?></td>
   <td><?php echo $Client->email; ?></td>
   <td>
   <form name="form_<?php echo $Client->id; ?>" action="" method="POST">
   <input type="hidden" name="id" value="<?php echo $Client->id; ?>">
   <select name="groupe" onChange="this.form.submit()">
   <option value="Particulier" <?php if ($Client->groupe=="Particulier") { echo "selected"; } ?>>Particulier</option>
   <option value="Livreur" <?php if ($Client->groupe=="Livreur") { echo "selected"; } ?>>Chauffeur</option>
   </select>
   </form>
   </td>
   <td><?php echo date("d-m-Y", $Client->created); ?></td>
   <td><?php echo date("d-m-Y", $Client->visited); ?></td>
   </tr><?php
}
?>
</table>

</article>
</section>
</div>
</CENTER>
</body>

</html>
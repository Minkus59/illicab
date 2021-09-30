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

$Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_DestinationPro WHERE id=:id");
$Select->bindParam(':id', $Id, PDO::PARAM_STR);
$Select->execute();
$Destination=$Select->fetch(PDO::FETCH_OBJ);
        
//Ajout de nouveaux articles
if (isset($_POST['Ajouter'])) {
    
    $Libele=trim($_POST['libele']);
    $Prix=trim($_POST['prix']);
    
    if (strlen($Libele)<2) {
        $Erreur="La destination semble incorecte !";
    }
    elseif(!preg_match("#[0-9.]#", $Prix)) {
        $Erreur="Le Prix Unitaire n'est pas conforme !<br />";
        $Erreur.="Ce champ ne doit contenir que des chiffres !</p>";
    }
    else {
        $InsertArticle=$cnx->prepare("INSERT INTO ".$Prefix."neuro_DestinationPro (libele, prix) VALUES (:libele, :prix)");
        $InsertArticle->bindParam(':libele', $Libele, PDO::PARAM_STR);
        $InsertArticle->bindParam(':prix', $Prix, PDO::PARAM_STR);
        $InsertArticle->execute();

        $Valid="Destination ajouté avec succès";
        header("location:".$Home."/Admin/Boutique/DestinationPro/?valid=".urlencode($Valid));
    }
}

if (isset($_POST['Modifier'])) {
    
    $Libele=trim($_POST['libele']);
    $Prix=trim($_POST['prix']);
    
    if (strlen($Libele)<2) {
        $Erreur="La destination semble incorecte !";
    }
    elseif(!preg_match("#[0-9.]#", $Prix)) {
        $Erreur="Le Prix Unitaire n'est pas conforme !<br />";
        $Erreur.="Ce champ ne doit contenir que des chiffres !</p>";
    }
    else {
        $UpdateArticle=$cnx->prepare("UPDATE ".$Prefix."neuro_DestinationPro SET libele=:libele, prix=:prix WHERE id=:id");
        $UpdateArticle->bindParam(':id', $Id, PDO::PARAM_STR);
        $UpdateArticle->bindParam(':libele', $Libele, PDO::PARAM_STR);
        $UpdateArticle->bindParam(':prix', $Prix, PDO::PARAM_STR);
        $UpdateArticle->execute();

        $Valid="Destination modifié avec succès";
        header("location:".$Home."/Admin/Boutique/DestinationPro/?valid=".urlencode($Valid));
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

<H1>Nouvelle destination</H1>

<div id="Gauche">

<form name="form_ajout" action="" method="POST">

<input id="autocomplete" class="controls" type="text" name="libele" value="<?php echo $Destination->libele; ?>" required />
<BR />
<input type="text" name="prix" placeholder="Prix*" value="<?php echo $Destination->prix; ?>" required="required"/>
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

</div>
<div id="Droite">
<div id="map"></div>
</div>

</article>
</section>
</div>
</CENTER>

<script>
var autocomplete;

function initMap() {
  var input = document.getElementById('autocomplete');
  var defaultBounds = new google.maps.LatLngBounds(
     new google.maps.LatLng(47.0, 3.0),
     new google.maps.LatLng(50.85,4.38)
  );

  var options = {
    types: [],
    bounds: defaultBounds
    //componentRestrictions: {country: 'be', country: 'fr'}
  };

  autocomplete = new google.maps.places.Autocomplete(input, options);

  autocomplete.addListener('place_changed', fillInAddress);
}

function fillInAddress() {
  var place = autocomplete.getPlace();
}
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC4LZHkNLz-GmGBad6eu9lKouenesS0FyI&libraries=places&language=fr&signed_in=true&callback=initMap" async defer></script>

</body>

</html>
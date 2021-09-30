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
$Now=time();

//Recuperation des info des la reclamation 
$Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Reclamation WHERE id=:id");
$Select->bindParam(':id', $Id, PDO::PARAM_STR);
$Select->execute();
$Info=$Select->fetch(PDO::FETCH_OBJ);

//Recup des messages
$Select2=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Reclamation_Message WHERE hash=:hash");
$Select2->bindParam(':hash', $Info->hash, PDO::PARAM_STR);
$Select2->execute();

if (isset($_POST['Envoyer'])) {
   $Message=$_POST['message'];

    if ($Message=="") {
       $Erreur="Veuillez saisir un message !";
    }
    else {
         $Insert=$cnx->prepare("INSERT INTO ".$Prefix."neuro_Reclamation_Message (hash, message, auteur, created) VALUES (:hash, :message, 'Admin', :created)");
         $Insert->bindParam(":hash", $Info->hash, PDO::PARAM_STR);
         $Insert->bindParam(":message", $Message, PDO::PARAM_STR);
         $Insert->bindParam(":created", $Now, PDO::PARAM_STR);
         $Insert->execute();

         if (!$Insert) {
             $Erreur="L'enregistrement des données à échouée, veuillez réessayer ultèrieurement !<br />";
         }
         else {
              $Valid="Message envoyé avec succès";
              header("location:".$Home."/Admin/Boutique/Client/SAV/?valid=".urlencode($Valid));
         }
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

<script type="text/javascript" src="<?php echo $Home; ?>/Admin/lib/js/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
  tinymce.init({
    force_br_newlines : true,
    force_p_newlines : false,
    forced_root_block : '', // Needed for 3.x
    relative_urls : false,
    remove_script_host : false,
    min_height : '350',
    selector: '#message',
    language : 'fr_FR',
    plugins: [
      'advlist autolink link image lists charmap print preview hr anchor pagebreak',
      'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking',
      'save table contextmenu directionality paste textcolor'
    ],
    content_css: 'css/content.css',
    toolbar: 'insertfile undo redo | styleselect | fontsizeselect | forecolor backcolor | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
    textcolor_map: [
        "000000", "Black",
        "993300", "Burnt orange",
        "333300", "Dark olive",
        "003300", "Dark green",
        "003366", "Dark azure",
        "000080", "Navy Blue",
        "333399", "Indigo",
        "333333", "Very dark gray",
        "800000", "Maroon",
        "FF6600", "NeuroSoft Team",
        "808000", "Olive",
        "008000", "Green",
        "008080", "Teal",
        "0000FF", "Blue",
        "666699", "Grayish blue",
        "808080", "Gray",
        "FF0000", "Red",
        "FF9900", "Amber",
        "99CC00", "Yellow green",
        "339966", "Sea green",
        "33CCCC", "Turquoise",
        "3366FF", "Royal blue",
        "800080", "Purple",
        "999999", "Medium gray",
        "FF00FF", "Magenta",
        "FFCC00", "Gold",
        "FFFF00", "Yellow",
        "00FF00", "Lime",
        "00FFFF", "Aqua",
        "00CCFF", "Sky blue",
        "993366", "Red violet",
        "FFFFFF", "White",
        "FF99CC", "Pink",
        "FFCC99", "Peach",
        "FFFF99", "Light yellow",
        "CCFFCC", "Pale green",
        "CCFFFF", "Pale cyan",
        "53bfa9", "Illicab"
    ]
});
</script>
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

<p><H1>Réclamations n°: <?php echo $Info->hash; ?></H1></p>

<H2><?php echo "Sujet : ".$Info->sujet; ?></H2>

<?php
while ($Info2=$Select2->fetch(PDO::FETCH_OBJ)) {
    $Select3=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Client WHERE hash=:hash");
    $Select3->bindParam(':hash', $Info2->auteur, PDO::PARAM_STR);
    $Select3->execute();
    $Info3=$Select3->fetch(PDO::FETCH_OBJ); 
  
    if ($Info2->auteur=="Admin") {
        echo "<font color='FF6600'>Illicab</font> - ".nl2br($Info2->message)."</p> 
        Le ".date("d-m-Y / G:i:s", $Info2->created);
    }
    else {
        echo "<font color='FF6600'>".$Info3->nom."</font> - ".nl2br($Info2->message)."</p> 
        Le ".date("d-m-Y / G:i:s", $Info2->created);
    }
?>
<p><HR /></p>
<?php
}

if ($Info->statue==1) {
?>
<a title="Cloturer la réclamation" href="<?php echo $Home; ?>/Admin/Boutique/Client/SAV/cloturer.php?id=<?php echo $Info->id; ?>"><img src="<?php echo $Home; ?>/Admin/lib/img/cloturer.png" alt="Cloturer la réclamation"></a>
<a title="Imprimer" target="popup" onClick="window.open('<?php echo $Home; ?>/Admin/Boutique/Client/SAV/Imprimer/?id=<?php echo $Info->id; ?>','Imprimer','height=842,width=595');" href="#" alt="Imprimer"><img src="<?php echo $Home; ?>/Admin/lib/img/imprimer.png" alt="Imprimer"/></a>

<p><HR /></p>

<p><H1>Repondre</H1></p>

<form name="form_reclamation" action="" method="POST">

<textarea id="message" name="message"></textarea>
</p>

<input type="submit" name="Envoyer" value="Envoyer"/>

</form>
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
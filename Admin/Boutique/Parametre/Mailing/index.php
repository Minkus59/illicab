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

$ParamMailling=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Param_Mailling");    
$ParamMailling->execute(); 
$Param=$ParamMailling->fetch(PDO::FETCH_OBJ);

if (isset($_POST['Enregistrer4'])) {
  $Compteur=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Param_Mailling");
  $Compteur->execute();
  $NbCompte=$Compteur->rowCount();

  if($NbCompte==0) {
      $Preparation1=$cnx->query("CREATE TABLE IF NOT EXISTS ".$Prefix."neuro_Param_Mailling (
      id int(32) unsigned NOT NULL AUTO_INCREMENT,
      mailling longtext NOT NULL,
      PRIMARY KEY (id)
      ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

      $Default="Vous pouvez pré-enregistrer un email dans la section /Parametre/Mailling predefini/";
      $Insert5=$cnx->prepare("INSERT INTO ".$Prefix."neuro_Param_Mailling (mailling) VALUES(:mailling)");
      $Insert5->BindParam(":mailling", $Default, PDO::PARAM_STR);
      $Insert5->execute();
  }

    $Mailing=$_POST['mailling']; 

    if (empty($Mailing)) {
        $Erreur="Un message doit etre saisie !";
    }
    else {
        $UpdateParam=$cnx->prepare("UPDATE ".$Prefix."neuro_Param_Mailling SET mailling=:mailling WHERE id='1'");
        $UpdateParam->bindParam(':mailling', $Mailing, PDO::PARAM_STR);
        $UpdateParam->execute();

        $Valid="Paramètre modifié avec succès";
        header("location:".$Home."/Admin/Boutique/Parametre/Mailing/?valid=".urlencode($Valid));
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

<script type="text/javascript" src="<?php echo $Home; ?>/Admin/lib/js/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
  tinymce.init({
    relative_urls : false,
    remove_script_host : false,
    min_height : '350',
    selector: '#message2',
    language : 'fr_FR',
    plugins: [
      'advlist autolink link image lists charmap print preview hr anchor pagebreak',
      'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking',
      'save table contextmenu directionality paste textcolor'
    ],
    content_css: 'css/content.css',
    toolbar: 'insertfile undo redo | styleselect | fontsizeselect | forecolor backcolor | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image'
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

<div id="Form_Middle5">
<H1 class="TitreOrange">Mailing Type</H1>

<form name="SelectMode" action="" method="POST">
Message envoyé avec le Mailing <font color='#FF0000'>*</font> 

<textarea id="message2" name="mailling" placeholder="Message*" require="required"><?php echo $Param->mailling; ?></textarea>
<BR />
<span class="col_1"></span>
<input type="submit" class="ButtonOrange" name="Enregistrer4" value="Enregistrer"/>
</form>
</div>

</article>
</section>
</div>
</CENTER>
</body>

</html>
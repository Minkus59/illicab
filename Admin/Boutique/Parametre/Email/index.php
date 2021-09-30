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

$RecupEmail=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_parametre");
$RecupEmail->execute();
$Info=$RecupEmail->fetch(PDO::FETCH_OBJ);

if (isset($_POST['Valider'])) {
    
    $Email=FiltreEmail('email');
    
    if($Email[0]===false) {
        $Erreur=$Email[1];
    }
    else {
        $VerifEmail=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_parametre");
        $VerifEmail->execute();
        $Rows=$VerifEmail->rowCount();
        
        if ($Rows==0) {
            $InsertEmail=$cnx->prepare("INSERT INTO ".$Prefix."neuro_parametre (email) VALUES (:email)");
            $InsertEmail->bindParam(':email', $Email, PDO::PARAM_STR);
            $InsertEmail->execute();  
        }
        else {
            $InsertEmail=$cnx->prepare("UPDATE ".$Prefix."neuro_parametre SET email=:email");
            $InsertEmail->bindParam(':email', $Email, PDO::PARAM_STR);
            $InsertEmail->execute();
        }
        
        if (($VerifEmail==false)||($InsertEmail==false)) {
            $Erreur="Une erreur est survenue, veuillez réessayer";
            ErreurLog($Erreur);
        }
        else {
            $Valid="Enregistrement réussie";
            header("location:".$Home."/Admin/Boutique/Parametre/Email/?valid=".$Valid);
        }
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

<H1>Paramètre</H1>

Veuillez saisir l'adresse e-mail pour la réception des copies (réclamations, etc...) </p>

<form name="FormEmail" action="" method="POST">   
    <input name="email" tyme="email" value="<?php echo $Info->email; ?>"/></p>
    
    <input type="submit" name="Valider" value="Valider" />
</form> 

</article>
</section>
</div>
</CENTER>
</body>

</html>
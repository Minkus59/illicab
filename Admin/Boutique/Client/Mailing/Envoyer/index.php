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

$RecupParam=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Param_Mailling");
$RecupParam->execute();
$Param=$RecupParam->fetch(PDO::FETCH_OBJ);      

$Selection=$_POST['selection'];
$Compteur=count($Selection);
$Fin=$Compteur-1;

for($u=0;$u<$Compteur;$u++) {
    if($Compteur>1) {
        if($u==0) {
            $Email.=$Selection[$u];  
        }
        else {
            $Email.=", ".$Selection[$u]; 
        }
    }
    else {
        $Email.=$Selection[$u];
    }
}


if ((isset($_POST['Envoyer']))&&($_POST['Envoyer']=="Envoyer")) { 

    $Retour=FiltreEmail('email');
    if ((isset($_POST['destinataire']))&&(!empty($_POST['destinataire']))) {
        if ((isset($_POST['objet']))&&(!empty($_POST['objet']))) {
            if ((isset($_POST['message']))&&(!empty($_POST['message']))) {               
                if (preg_match("#^[A-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $_POST['retour'])) { 
                    
                    $Destinataire2=$_POST['destinataire'];
                    $Objet=$_POST['objet'];
                    $Message=$_POST['message'];
                    $Retour=$_POST['retour'];
                    
                    $boundary = md5(uniqid(mt_rand()));
                    
                    $Entete = "From: $Societe <\"$Retour\">\n";
                    $Entete .= "Reply-To: $Retour\n";
                    $Entete .= "MIME-Version: 1.0\n";
                    $Entete .= "Content-Type:multipart/mixed; boundary=\"$boundary\"\n";
                    $Entete .= "\n";
                    
                    $message="Ce message est au format MIME.\n";
                    
                    $message.="--$boundary\n";
                    $message.="Content-Type: text/html; charset=iso8859-15\n";  
                    $message.="\n";
                    
                    $message.="<html><head>
                                <title>".$Objet."</title>
                                </head>
                                <body>
                                ".$Message."
                                </body>
                                </html>";
                                
                    $message.="\n\n";   
                    $message.="--$boundary\n";   
                    
                    if (mail($Destinataire2, $Objet, $message, $Entete)===FALSE) {
                        $Erreur = "L'e-mail n'a pu être envoyé, vérifiez que vous l'avez entré correctement !";
                    }
                    else {        
                        $Valid="Votre message a bien été envoyé !";
                        header("location:".$Home."/Admin/Boutique/Client/Mailing/?valid=".urlencode($Valid));
                    }
                }
                else {
                    $Erreur="L'adresse e-mail de retour n'est pas conforme !</p>";
                }  
            }
            else {
                $Erreur="Veuillez entrer un message !";
            }
        }
        else {
            $Erreur="Veuillez entrer un objet de message !";
        }
    } 
    else {
        $Erreur="Veuillez entrer aux moins un destinataire !";
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
    selector: '#message',
    language : 'fr_FR',
    plugins: [
      'advlist autolink link image lists charmap print preview hr anchor pagebreak',
      'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime nonbreaking',
      'save table contextmenu directionality paste textcolor'
    ],
    content_css: 'css/content.css',
    toolbar: 'insertfile undo redo | fontsizeselect | forecolor backcolor | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image'
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

<div id="Form_Middle3">
<H1>Envoyer un e-mail</H1></p>
<form name="form_mail" action="" method="POST" enctype="multipart/form-data">

<input type="text" placeholder="Destinataire :" name="destinataire" require="required" value="<?php echo $Email; ?>"/></p>

<input type="text" placeholder="Objet :" name="objet" require="required"/></p>

<input type="text" placeholder="Adresse de retour" name="retour" value="<?php echo $Destinataire; ?>" require="required"/></p>

<textarea id="message" name="message" placeholder="Message*" require="required">
<?php echo $Param->mailling ?>
</textarea></p>

<input type="submit" class="ButtonRose" name="Envoyer" value="Envoyer"/>
</form>
</div>

</article>
</section>
</div>
</CENTER>
</body>

</html>
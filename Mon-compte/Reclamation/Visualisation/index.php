<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");  
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/log.inc.php");   

if ($Cnx_Client===false) {
  $Erreur="Vous devez être connecté pour accéder à cette page";
  header('location:'.$Home.'/Mon-compte/?erreur='.$Erreur);
}

$Erreur=$_GET['erreur'];
$Valid=$_GET['valid'];
$Id=$_GET['id'];
$Now=time();

//Recuperation d'envoi
$RecupEmail=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_parametre");
$RecupEmail->execute();
$InfoDesti=$RecupEmail->fetch(PDO::FETCH_OBJ);

//Recuperation des info client
$Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Client WHERE hash=:hash");
$Select->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
$Select->execute();
$InfoClient=$Select->fetch(PDO::FETCH_OBJ);

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
       ErreurLog($Erreur);
    }
    else {
         $Insert=$cnx->prepare("INSERT INTO ".$Prefix."neuro_Reclamation_Message (hash, message, auteur, created) VALUES (:hash, :message, :auteur, :created)");
         $Insert->bindParam(":hash", $Info->hash, PDO::PARAM_STR);
         $Insert->bindParam(":message", $Message, PDO::PARAM_STR);
         $Insert->bindParam(":auteur", $SessionClient, PDO::PARAM_STR);
         $Insert->bindParam(":created", $Now, PDO::PARAM_STR);
         $Insert->execute();

         if (!$Insert) {
             $Erreur="L'enregistrement des données a échouée, veuillez réessayer ultèrieurement !<br />";
             ErreurLog($Erreur);
         }
         else {
             $Body="Client : ".$InfoClient->nom." ".$InfoClient->prenom."<BR />
                    Sujet : ".$Info->sujet."<BR />
                    Message : ".nl2br($Message);

            if (EnvoiNotification($Societe, $Serveur, $Destinataire, "Nouvelle réponse sur réclamation", $Body, $InfoDesti->email)==false) {
                $Erreur="L'e-mail n'a pu être envoyé, vérifiez que vous l'avez entré correctement !</p>";
                ErreurLog($Erreur);
            } 
            else {   
              $Valid="Réclamation enregistré avec succès, elle sera traité dans les meilleurs delais";
              header("location:".$Home."/Mon-compte/Reclamation/?valid=".urlencode($Valid));
            }
         }
    }
}

?>
<!DOCTYPE HTML>
<html>

<head>   

<title><?php echo $SOEPage->titre ?></title>
<meta name="category" content="<?php if ($SOEPage->nom=="/") { echo "Accueil"; } else { echo $SOEPage->nom; } ?>" />
<meta name="description" content="<?php echo $SOEPage->description ?>" />
<meta name="robots" content="index, follow"/>
<meta name="author" content="NeuroSoft Team"/>
<meta name="publisher" content="<?php echo $Publisher; ?>"/>
<meta name="reply-to" content="<?php echo $Destinataire; ?>"/>
<meta name="viewport" content="width=device-width, initial-scale=0.8" />

<link rel="shortcut icon" href="<?php echo $Home; ?>/lib/img/icone.ico" >
<link rel="stylesheet" type="text/css" media="screen AND (max-width: 480px)" href="<?php echo $Home; ?>/lib/css/misenpatel.css" />
<link rel="stylesheet" type="text/css" media="screen AND (min-width: 480px) AND (max-width: 960px)" href="<?php echo $Home; ?>/lib/css/misenpatab.css" />
<link rel="stylesheet" type="text/css" media="screen AND (min-width: 960px)" href="<?php echo $Home; ?>/lib/css/misenpapc.css" >

<script type="text/javascript" src="<?php echo $Home; ?>/lib/js/analys.js"></script>
</head>

<body>
<center>
<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/header.inc.php"); ?>

<section>
<div id="Center">
<article>
<?php
if (isset($Erreur)) { echo "<p><font color='#FF0000'>".$Erreur."</font></p>"; }
if (isset($Valid)) { echo "<p><font color='#009900'>".$Valid."</font></p>"; }
?>
<p><H1>Réclamations n°: <?php echo $Info->hash; ?></H1></p>

<H2><?php echo "Sujet : ".$Info->sujet; ?></H2>

<?php
while ($Info2=$Select2->fetch(PDO::FETCH_OBJ)) {
    $Select3=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_compte_Client WHERE compte=:compte");
    $Select3->bindParam(':compte', $Info2->auteur, PDO::PARAM_STR);
    $Select3->execute();
    $Info3=$Select3->fetch(PDO::FETCH_OBJ); 
  
    if ($Info2->auteur=="Admin") {
        echo "<font color='FF6600'>illicab</font> - ".nl2br($Info2->message)."</p> 
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
<p><HR /></p>

<p><H1>Répondre</H1></p>

<form name="form_reclamation" action="" method="POST">

<textarea class="long" rows="15" name="message"></textarea>
</p>

<input type="submit" name="Envoyer" value="Envoyer"/>

</form>
<?php
}
?>
</article>
</div>
</section>


<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>

</center>

</body>

</html>
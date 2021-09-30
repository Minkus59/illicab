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
$Now=time();
$Id=$_GET['id'];

if(isset($Id)) {
    $SelectCommande=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Trajet WHERE client=:client AND hash_trajet=:hash_trajet");
    $SelectCommande->bindParam(':client', $SessionClient, PDO::PARAM_STR);
    $SelectCommande->bindParam(':hash_trajet', $Id, PDO::PARAM_STR);
    $SelectCommande->execute();
}
else {
    $SelectCommande=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Trajet WHERE client=:client");
    $SelectCommande->bindParam(':client', $SessionClient, PDO::PARAM_STR);
    $SelectCommande->execute();
}

$RecupClient=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Client WHERE hash=:hash");
$RecupClient->BindParam(":hash", $SessionClient, PDO::PARAM_STR);
$RecupClient->execute();
$Client=$RecupClient->fetch(PDO::FETCH_OBJ);

$RecupEmail=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_parametre");
$RecupEmail->execute();
$Info=$RecupEmail->fetch(PDO::FETCH_OBJ);

if (isset($_POST['Envoyer'])) {
   $Sujet=$_POST['sujet'];
   $Message=$_POST['message'];
   $CommandeNo=$_POST['commande'];
   $Code1 = md5(uniqid(rand()), false);
   $Hash = substr($Code1, 0, 8);

    if ($Sujet=="") {
       $Erreur="Veuillez saisir un sujet !";
       ErreurLog($Erreur);
    }
    elseif ($Message=="") {
       $Erreur="Veuillez saisir un message !";
       ErreurLog($Erreur);
    }
    else {
         $Insert=$cnx->prepare("INSERT INTO ".$Prefix."neuro_Reclamation (hash, trajet, sujet, client, created) VALUES (:hash, :trajet, :sujet, :client, :created)");
         $Insert->bindParam(":hash", $Hash, PDO::PARAM_STR);
         $Insert->bindParam(":sujet", $Sujet, PDO::PARAM_STR);
         $Insert->bindParam(":trajet", $Id, PDO::PARAM_STR);
         $Insert->bindParam(":client", $SessionClient, PDO::PARAM_STR);
         $Insert->bindParam(":created", $Now, PDO::PARAM_STR);
         $Insert->execute();
         
         $InsertMess=$cnx->prepare("INSERT INTO ".$Prefix."neuro_Reclamation_Message (hash, message, auteur, created) VALUES (:hash, :message, :auteur, :created)");
         $InsertMess->bindParam(":hash", $Hash, PDO::PARAM_STR);
         $InsertMess->bindParam(":message", $Message, PDO::PARAM_STR);
         $InsertMess->bindParam(":auteur", $SessionClient, PDO::PARAM_STR);
         $InsertMess->bindParam(":created", $Now, PDO::PARAM_STR);
         $InsertMess->execute();

         if ((!$Insert)||(!$InsertMess)) {
             $Erreur="L'enregistrement des données a échouée, veuillez réessayer ultèrieurement !<br />";
             ErreurLog($Erreur);
         }
         else {
             $Body="Trajet : ".$Id."<BR />
                    Client : ".$Client->nom." ".$Client->prenom."<BR />
                    Sujet : ".$Sujet."<BR />
                    Message : ".$Message;

            if (EnvoiNotification($Societe, $Serveur, $Destinataire, "Nouvelle réclamation", $Body, $Info->email)==false) {
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
if (isset($Erreur)) { echo "<p><font color='#FF0000'>".$Erreur."</font><br />"; }
if (isset($Valid)) { echo "<p><font color='#009900'>".$Valid."</font><br />"; }
?>

<p><H1>Ajouter une réclamation</H1>

<form name="form_reclamation" action="" method="POST">

N° de commande : <br />
<select name="commande">
<option value="NULL"> -- </option>
<?php while ($Commande=$SelectCommande->fetch(PDO::FETCH_OBJ)) { ?>
<option value="<?php echo $Commande->hash_trajet; ?>" <?php if(isset($Id)) { echo "selected"; } ?> ><?php echo $Commande->hash_trajet; ?></option>
<?php } ?>
</select><br /><br />

Sujet de la réclamation<font color='#FF0000'>*</font>: <br />
<input class="long" name="sujet" type="text" required="required"/>
<br /><br />
Détail de votre réclamation<font color='#FF0000'>*</font> : <br />
<textarea class="long" rows="15" name="message"></textarea>
<br /><br />

<input type="submit" name="Envoyer" value="Envoyer"/>
</form>

</article>
</div>
</section>


<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>

</center>

</body>

</html>
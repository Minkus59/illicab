<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php"); 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");    
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/log.inc.php");    

$RecupParam=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_parametre");
$RecupParam->execute();
$Param=$RecupParam->fetch(PDO::FETCH_OBJ);

$Id=$_GET['id'];

$SelectTrajet=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_vol WHERE hash_trajet=:hash_trajet");
$SelectTrajet->bindParam(':hash_trajet', $Id, PDO::PARAM_STR);
$SelectTrajet->execute();
$Trajet=$SelectTrajet->fetch(PDO::FETCH_OBJ);
$Count=$SelectTrajet->rowCount();

if(isset($_POST['Valider'])) {
  $Valid1=$_POST['valid1'];
  $Now=time();

  if(!empty($_POST['numero'])) {
    $_SESSION['numero']=$_POST['numero'];
  }
  if(!empty($_POST['jour'])) {
    $_SESSION['jour']=$_POST['jour'];
  }
  if(!empty($_POST['mois'])) {
    $_SESSION['mois']=$_POST['mois'];
  }
  if(!empty($_POST['annee'])) {
    $_SESSION['annee']=$_POST['annee'];
  }
  if(!empty($_POST['heure'])) {
    $_SESSION['heure']=$_POST['heure'];
  }
  if(!empty($_POST['min'])) {
    $_SESSION['min']=$_POST['min'];
  }

  //Verif condition et Exactitude des donnee
  if ($Valid1!=true) {
    $Erreur.="Les informations n'ont pas été vérifiées !<BR />";
  }
  if ($_SESSION['jour']=="NULL") {
    $Erreur.="Veuillez selectionner un jour de départ<BR />";
  }
  if ($_SESSION['mois']=="NULL") {
    $Erreur.="Veuillez selectionner un mois de départ<BR />";
  }
  if ($_SESSION['annee']=="NULL") {
    $Erreur.="Veuillez selectionner une année de départ<BR />";
  }
  if ($_SESSION['heure']=="NULL") {
    $Erreur.="Veuillez selectionner l'heure de départ<BR />";
  }
  if ($_SESSION['min']=="NULL") {
    $Erreur.="Veuillez selectionner les minutes de départ<BR />";
  }

  if (isset($Erreur)) {
    header("location:".$Home."/Admin/Boutique/TrajetDemander/?id=".$Id."&erreur=".$Erreur);
  }
  else {
    $Date = mktime($_SESSION['heure'], $_SESSION['min'], "0", $_SESSION['mois'], $_SESSION['jour'], $_SESSION['annee']);

    if ($Count==0) {
      // Ajout du panier a la bdd ainsi que les info de contact
      $InsertPanier=$cnx->prepare("INSERT INTO ".$Prefix."neuro_vol (numero, date, hash_trajet, created) VALUES(:numero, :date, :hash_trajet, :created)");
      $InsertPanier->BindParam(':numero', $_SESSION['numero'], PDO::PARAM_STR);
      $InsertPanier->BindParam(':date', $Date, PDO::PARAM_STR);
      $InsertPanier->BindParam(':hash_trajet', $Id, PDO::PARAM_STR);
      $InsertPanier->BindParam(':created', $Now , PDO::PARAM_STR);
      $InsertPanier->execute();

      $Titre="illicab - Nouvelle information de vol";
    }
    else {
      $InsertPanier=$cnx->prepare("UPDATE ".$Prefix."neuro_vol SET numero=:numero, date=:date WHERE hash_trajet=:hash_trajet");
      $InsertPanier->BindParam(':numero', $_SESSION['numero'], PDO::PARAM_STR);
      $InsertPanier->BindParam(':date', $Date, PDO::PARAM_STR);
      $InsertPanier->BindParam(':hash_trajet', $Id, PDO::PARAM_STR);
      $InsertPanier->execute();

      $Titre="illicab - Modification des informations de vol";
    }

    //Envoi d'email Avertissement Commande + Recap client
      $Body="Numéro de trajet : ".$Id."<BR /><BR />
      
             Numéro de vol : ".$_SESSION['numero']."<BR />
             Date de décollage : ".$_SESSION['jour']." / ".$_SESSION['mois']." / ".$_SESSION['annee']."<BR />
             Heure de décollage : ".$_SESSION['heure']."h ".$_SESSION['min']."min<BR />";

      if (EnvoiNotification($Societe, $Serveur, $Destinataire, $Titre, $Body, $Param->email)==false) {
          $Erreur="L'e-mail n'a pu être envoyé, vérifiez que vous l'avez entré correctement !</p>";
          ErreurLog($Erreur);
          header("location:".$Home."/Admin/Boutique/TrajetDemander/?erreur=".$Erreur);
      }
      else {
        unset($_SESSION['numero']);
        unset($_SESSION['jour']);
        unset($_SESSION['mois']);
        unset($_SESSION['annee']);
        unset($_SESSION['heure']);
        unset($_SESSION['min']);

        //Redirection sur une page explicative sur le fonctionnement 
        $Valid="Merci d'avoir enregistrer vos informations de vol<BR />";
        header("location:".$Home."/Admin/Boutique/TrajetDemander/?valid=".$Valid);
      }
  }
}

if(isset($_POST['Annuler'])) {
    unset($_SESSION['numero']);
    unset($_SESSION['jour']);
    unset($_SESSION['mois']);
    unset($_SESSION['annee']);
    unset($_SESSION['heure']);
    unset($_SESSION['min']);

    header("location:".$Home."/Admin/Boutique/Trajet/");
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
<?php
if (isset($Erreur)) { echo "<font color='#FF0000'>".$Erreur."</font></p>"; } 
if (isset($Valid)) { echo "<font color='#095f07'>".$Valid."</font></p>"; } 
?>

<H1><font color="dodgerblue">Information de vol</font></H1>

<b>Merci de remplir avec exactitude toutes les informations concernant le vol</b><BR /><BR />


<form action="" method="POST">

<H2>Vol</H2>

<span class="col_1" for="numero">Numéro de vol : </span>
<input type="text" name="numero" <?php if(isset($_SESSION['numero'])) { echo 'value="'.$_SESSION['numero'].'"'; } else { echo 'value="'.$Trajet->numero.'"'; } ?>/><BR /><BR />

<span class="col_1" for="jour">Date de décollage : </span>
<select name="jour" class="SelectMini">
    <option value="NULL">--</option>
    <?php for($j=1;$j!=32;$j++) { ?>
        <option value="<?php echo sprintf("%'.02d\n", $j); ?>" <?php if((!empty($Trajet->date))&&(date("d", $Trajet->date)==$j)) { echo "selected"; } ?>><?php echo sprintf("%'.02d\n", $j); ?></option>
   <?php } ?>
</select>
<select name="mois" class="SelectMini">
    <option value="NULL">--</option>
    <?php for($mo=1;$mo!=13;$mo++) { ?>
        <option value="<?php echo sprintf("%'.02d\n", $mo); ?>" <?php if((!empty($Trajet->date))&&(date("m", $Trajet->date)==$mo)) { echo "selected"; } ?>><?php echo sprintf("%'.02d\n", $mo); ?></option>
   <?php } ?>
</select>
<select name="annee" class="SelectMini">
    <option value="NULL">--</option>
    <?php for($a=2016;$a!=2027;$a++) { ?>
        <option value="<?php echo $a; ?>" <?php if((!empty($Trajet->date))&&(date("Y", $Trajet->date)==$a)) { echo "selected"; } ?>><?php echo $a; ?></option>
   <?php } ?>
</select><BR /><BR />

<span class="col_1" for="heure">Heure de décollage : </span>
<select name="heure" class="SelectMini">
    <option value="NULL">--</option>
    <?php for($h=0;$h!=24;$h++) { ?>
        <option value="<?php echo sprintf("%'.02d\n", $h); ?>" <?php if((!empty($Trajet->date))&&(date("H", $Trajet->date)==$h)) { echo "selected"; } ?>><?php echo sprintf("%'.02d\n", $h); ?></option>
   <?php } ?>
</select>
<select name="min" class="SelectMini">
    <option value="NULL">--</option>
    <?php for($m=0;$m!=60;$m++) { ?>
        <option value="<?php echo sprintf("%'.02d\n", $m); ?>" <?php if((!empty($Trajet->date))&&(date("i", $Trajet->date)==$m)) { echo "selected"; } ?>><?php echo sprintf("%'.02d\n", $m); ?></option>
   <?php } ?>
</select><BR /><BR />

<span class="col_1" for="valid1"></span>
<input type="checkbox" name="valid1"/> J'ai vérifié l'exactitude des données ci-dessus</a><font color='#FF0000'>*</font><BR /><BR />

<span class="col_1" for="Valider"></span>
<input type="submit" name="Valider" value="Enregistrer">
<input type="submit" name="Annuler" value="Annuler">
</form>

</article>
</section>
</div>
</CENTER>
</body>

</html>
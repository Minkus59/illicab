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
$Type=$_GET['type'];
$Now=time();

$SelectClient=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Client WHERE groupe!= 'Livreur' ORDER BY nom ASC");
$SelectClient->execute();

if (isset($_POST['Reserver'])) {

    $_SESSION['depart']=$_POST['depart'];
    $_SESSION['destination']=$_POST['destination'];
    $_SESSION['passager']=$_POST['passager'];
    $_SESSION['trajet']=$_POST['trajet'];
    $_SESSION['destinationRetour']=$_POST['destinationRetour'];

    $_SESSION['panierTrajet']=$_POST['trajetType'];
    $_SESSION['client']=$_POST['client'];

    $Type=$_GET['type'];
    $_SESSION['civilite']=$_POST['civilite'];
    $_SESSION['nom']=$_POST['nom'];
    $_SESSION['prenom']=$_POST['prenom'];
    $_SESSION['tel']=trim($_POST['tel']);

    $Code_commande = md5(uniqid(rand(), true));
    $Hash_commande=substr($Code_commande, 0, 8);
    if (!isset($_SESSION['hash_commande'])) {
      $_SESSION['hash_commande']=$Hash_commande;
    }

    $TrajetNoAller = md5(uniqid(rand(), true));
    $TrajetAller=substr($TrajetNoAller, 0, 8);
    if (!isset($_SESSION['hash_trajetAller'])) {
      $_SESSION['hash_trajetAller']=$TrajetAller;
    }

    if ($_SESSION['trajet']==2) {
        $TrajetNoRetour = md5(uniqid(rand(), true));
        $TrajetRetour=substr($TrajetNoRetour, 0, 8);
        if (!isset($_SESSION['hash_trajetRetour'])) {
        $_SESSION['hash_trajetRetour']=$TrajetRetour;
        }
    }

    //Si retour seul
    if ($_SESSION['trajet']==3) {
        if ($_SESSION['panierTrajet']==1) {
            $SelectTauxDesti=$cnx->prepare('SELECT * FROM '.$Prefix.'neuro_Destination WHERE libele=:libele');
            $SelectTauxDesti->BindParam(':libele', $_SESSION['depart'], PDO::PARAM_STR);
            $SelectTauxDesti->execute();
            $TauxDesti=$SelectTauxDesti->fetch(PDO::FETCH_OBJ);
        }
        else {
            $SelectTauxDesti=$cnx->prepare('SELECT * FROM '.$Prefix.'neuro_DestinationPro WHERE libele=:libele');
            $SelectTauxDesti->BindParam(':libele', $_SESSION['depart'], PDO::PARAM_STR);
            $SelectTauxDesti->execute();
            $TauxDesti=$SelectTauxDesti->fetch(PDO::FETCH_OBJ);
        }
    }
    //Si aller simple ou aller retour
    else {
        if ($_SESSION['panierTrajet']==1) {
            $SelectTauxDesti=$cnx->prepare('SELECT * FROM '.$Prefix.'neuro_Destination WHERE libele=:libele');
            $SelectTauxDesti->BindParam(':libele', $_SESSION['destination'], PDO::PARAM_STR);
            $SelectTauxDesti->execute();
            $TauxDesti=$SelectTauxDesti->fetch(PDO::FETCH_OBJ);
        }
        else {
            $SelectTauxDesti=$cnx->prepare('SELECT * FROM '.$Prefix.'neuro_DestinationPro WHERE libele=:libele');
            $SelectTauxDesti->BindParam(':libele', $_SESSION['destination'], PDO::PARAM_STR);
            $SelectTauxDesti->execute();
            $TauxDesti=$SelectTauxDesti->fetch(PDO::FETCH_OBJ);
        }
    } 

    $SelectTauxPers=$cnx->prepare('SELECT * FROM '.$Prefix.'neuro_Personne WHERE quantite=:quantite');
    $SelectTauxPers->BindParam(':quantite', $_SESSION['passager'], PDO::PARAM_STR);
    $SelectTauxPers->execute();
    $TauxPers=$SelectTauxPers->fetch(PDO::FETCH_OBJ);  

    //MODULE CALCUL DE TRAJET
    if ($_SESSION['trajet']==1) {
        $Result=DistanceMatrix($_SESSION['depart'], $_SESSION['destination'], $KeyGoogleAPI, $TauxDesti->prix, $TauxPers->taux, $_SESSION['depart'], $_SESSION['destination']);
    }
    if ($_SESSION['trajet']==2) {
        $Result=DistanceMatrix($_SESSION['depart'], $_SESSION['destination'], $KeyGoogleAPI, $TauxDesti->prix, $TauxPers->taux, $_SESSION['depart'], $_SESSION['destination']);
        $Result2=DistanceMatrix($_SESSION['destinationRetour'], $_SESSION['depart'], $KeyGoogleAPI, $TauxDesti->prix, $TauxPers->taux, $_SESSION['destinationRetour'], $_SESSION['depart']);
    }
    if ($_SESSION['trajet']==3) {
        $Result3=DistanceMatrix($_SESSION['depart'], $_SESSION['destination'], $KeyGoogleAPI, $TauxDesti->prix, $TauxPers->taux, $_SESSION['depart'], $_SESSION['destination']);
    }
}

if (isset($_POST['Annuler'])) {
    unset($_SESSION['trajet']);
    unset($_SESSION['client']);
    unset($_SESSION['depart']);
    unset($_SESSION['destination']);
    unset($_SESSION['passager']);
    unset($_SESSION['destinationRetour']);
    unset($_SESSION['panierTrajet']);
    unset($_SESSION['civilite']);
    unset($_SESSION['nom']);
    unset($_SESSION['prenom']);
    unset($_SESSION['tel']);
    unset($_SESSION['email']);
    unset($_SESSION['hash_trajetAller']);
    unset($_SESSION['hash_trajetRetour']);
    unset($_SESSION['hash_commande']);
}

if (isset($_POST['Retour'])) {
    header("location:".$Home."/Admin/Boutique/Nouveau/");
}

if (isset($_POST['Valider'])) {
  $DistanceAller=$_POST['DistanceAller'];
  $TempsAller=$_POST['TempsAller'];
  $PrixAller=$_POST['PrixAller'];
  $DistanceRetour=$_POST['DistanceRetour'];
  $TempsRetour=$_POST['TempsRetour'];
  $PrixRetour=$_POST['PrixRetour'];

    if ($_SESSION['trajet']==1) {
        // Ajout du panier a la bdd ainsi que les info de contact
        $InsertPanier=$cnx->prepare("INSERT INTO ".$Prefix."neuro_Trajet 
        (pro, passager, type, type2, Depart, Arriver, Prix, Distance, Temps, hash_commande, hash_trajet, client, created) 
        VALUES(:pro, :passager, :type, '1', :Depart, :Arriver, :Prix, :Distance, :Temps, :hash_commande, :hash_trajet, :client, :created)");
        $InsertPanier->BindParam(':pro', $_SESSION['panierTrajet'] , PDO::PARAM_STR);
        $InsertPanier->BindParam(':type', $_SESSION['trajet'] , PDO::PARAM_STR);
        $InsertPanier->BindParam(':passager', $_SESSION['passager'] , PDO::PARAM_STR);
        $InsertPanier->BindParam(':Depart', $_SESSION['depart'] , PDO::PARAM_STR);
        $InsertPanier->BindParam(':Arriver', $_SESSION['destination'] , PDO::PARAM_STR);
        $InsertPanier->BindParam(':Prix', $PrixAller , PDO::PARAM_STR);
        $InsertPanier->BindParam(':Distance', $DistanceAller , PDO::PARAM_STR);
        $InsertPanier->BindParam(':Temps', $TempsAller , PDO::PARAM_STR);
        $InsertPanier->BindParam(':hash_commande', $_SESSION['hash_commande'] , PDO::PARAM_STR);
        $InsertPanier->BindParam(':hash_trajet', $_SESSION['hash_trajetAller'] , PDO::PARAM_STR);
        $InsertPanier->BindParam(':client', $_SESSION['client'] , PDO::PARAM_STR);
        $InsertPanier->BindParam(':created', $Now , PDO::PARAM_STR);
        $InsertPanier->execute();
    }

    if ($_SESSION['trajet']==2) {
        // Ajout du panier a la bdd ainsi que les info de contact
        $InsertPanier=$cnx->prepare("INSERT INTO ".$Prefix."neuro_Trajet 
        (pro, passager, type, type2, Depart, Arriver, Prix, Distance, Temps, hash_commande, hash_trajet, client, created) 
        VALUES(:pro, :passager, :type, '1', :Depart, :Arriver, :Prix, :Distance, :Temps, :hash_commande, :hash_trajet, :client, :created)");
        $InsertPanier->BindParam(':pro', $_SESSION['panierTrajet'] , PDO::PARAM_STR);
        $InsertPanier->BindParam(':type', $_SESSION['trajet'] , PDO::PARAM_STR);
        $InsertPanier->BindParam(':passager', $_SESSION['passager'] , PDO::PARAM_STR);
        $InsertPanier->BindParam(':Depart', $_SESSION['depart'] , PDO::PARAM_STR);
        $InsertPanier->BindParam(':Arriver', $_SESSION['destination'] , PDO::PARAM_STR);
        $InsertPanier->BindParam(':Prix', $PrixAller , PDO::PARAM_STR);
        $InsertPanier->BindParam(':Distance', $DistanceAller , PDO::PARAM_STR);
        $InsertPanier->BindParam(':Temps', $TempsAller , PDO::PARAM_STR);
        $InsertPanier->BindParam(':hash_commande', $_SESSION['hash_commande'] , PDO::PARAM_STR);
        $InsertPanier->BindParam(':hash_trajet', $_SESSION['hash_trajetAller'] , PDO::PARAM_STR);
        $InsertPanier->BindParam(':client', $_SESSION['client'] , PDO::PARAM_STR);
        $InsertPanier->BindParam(':created', $Now , PDO::PARAM_STR);
        $InsertPanier->execute();

        $InsertPanier=$cnx->prepare("INSERT INTO ".$Prefix."neuro_Trajet 
        (pro, passager, type, type2, Depart, Arriver, Prix, Distance, Temps, hash_commande, hash_trajet, client, created) 
        VALUES(:pro, :passager, :type, '2', :Depart, :Arriver, :Prix, :Distance, :Temps, :hash_commande, :hash_trajet, :client, :created)");
        $InsertPanier->BindParam(':pro', $_SESSION['panierTrajet'] , PDO::PARAM_STR);
        $InsertPanier->BindParam(':type', $_SESSION['trajet'] , PDO::PARAM_STR);
        $InsertPanier->BindParam(':passager', $_SESSION['passager'] , PDO::PARAM_STR);
        $InsertPanier->BindParam(':Depart', $_SESSION['destinationRetour'] , PDO::PARAM_STR);
        $InsertPanier->BindParam(':Arriver', $_SESSION['depart'] , PDO::PARAM_STR);
        $InsertPanier->BindParam(':Prix', $PrixRetour , PDO::PARAM_STR);
        $InsertPanier->BindParam(':Distance', $DistanceRetour , PDO::PARAM_STR);
        $InsertPanier->BindParam(':Temps', $TempsRetour , PDO::PARAM_STR);
        $InsertPanier->BindParam(':hash_commande', $_SESSION['hash_commande'] , PDO::PARAM_STR);
        $InsertPanier->BindParam(':hash_trajet', $_SESSION['hash_trajetRetour'] , PDO::PARAM_STR);
        $InsertPanier->BindParam(':client', $_SESSION['client'] , PDO::PARAM_STR);
        $InsertPanier->BindParam(':created', $Now , PDO::PARAM_STR);
        $InsertPanier->execute();
    }

    if ($_SESSION['trajet']==3) {
        $InsertPanier=$cnx->prepare("INSERT INTO ".$Prefix."neuro_Trajet 
        (pro, passager, type, type2, Depart, Arriver, Prix, Distance, Temps, hash_commande, hash_trajet, client, created) 
        VALUES(:pro, :passager, :type, '1', :Depart, :Arriver, :Prix, :Distance, :Temps, :hash_commande, :hash_trajet, :client, :created)");
        $InsertPanier->BindParam(':pro', $_SESSION['panierTrajet'] , PDO::PARAM_STR);
        $InsertPanier->BindParam(':type', $_SESSION['trajet'] , PDO::PARAM_STR);
        $InsertPanier->BindParam(':passager', $_SESSION['passager'] , PDO::PARAM_STR);
        $InsertPanier->BindParam(':Depart', $_SESSION['depart'] , PDO::PARAM_STR);
        $InsertPanier->BindParam(':Arriver', $_SESSION['destination'] , PDO::PARAM_STR);
        $InsertPanier->BindParam(':Prix', $PrixAller , PDO::PARAM_STR);
        $InsertPanier->BindParam(':Distance', $DistanceAller , PDO::PARAM_STR);
        $InsertPanier->BindParam(':Temps', $TempsAller , PDO::PARAM_STR);
        $InsertPanier->BindParam(':hash_commande', $_SESSION['hash_commande'] , PDO::PARAM_STR);
        $InsertPanier->BindParam(':hash_trajet', $_SESSION['hash_trajetAller'] , PDO::PARAM_STR);
        $InsertPanier->BindParam(':client', $_SESSION['client'] , PDO::PARAM_STR);
        $InsertPanier->BindParam(':created', $Now , PDO::PARAM_STR);
        $InsertPanier->execute();
    }

    $SelectClientInfo=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Client WHERE hash=:hash");
    $SelectClientInfo->bindParam(':hash', $_SESSION['client'], PDO::PARAM_STR);
    $SelectClientInfo->execute();
    $ClientInfo=$SelectClientInfo->fetch(PDO::FETCH_OBJ);

    $InsertContact=$cnx->prepare("INSERT INTO ".$Prefix."neuro_Contact 
    (civilite, nom, prenom, tel, email, hash_commande, created) 
    VALUES(:civilite, :nom, :prenom, :tel, :email, :hash_commande, :created)");
    $InsertContact->BindParam(':civilite', $_SESSION['civilite'] , PDO::PARAM_STR);
    $InsertContact->BindParam(':nom', $_SESSION['nom'] , PDO::PARAM_STR);
    $InsertContact->BindParam(':prenom', $_SESSION['prenom'] , PDO::PARAM_STR);
    $InsertContact->BindParam(':tel', $_SESSION['tel'] , PDO::PARAM_STR);
    $InsertContact->BindParam(':email', $ClientInfo->email , PDO::PARAM_STR);
    $InsertContact->BindParam(':hash_commande', $_SESSION['hash_commande'] , PDO::PARAM_STR);
    $InsertContact->BindParam(':created', $Now , PDO::PARAM_STR);
    $InsertContact->execute();

    if ((!$InsertPanier)||(!$InsertContact)) {
      $Erreur="Erreur 016 : Une erreur est survenue, veuillez réessayer !";
    }
    else {
        unset($_SESSION['trajet']);
        unset($_SESSION['client']);
        unset($_SESSION['depart']);
        unset($_SESSION['destination']);
        unset($_SESSION['passager']);
        unset($_SESSION['destinationRetour']);
        unset($_SESSION['panierTrajet']);
        unset($_SESSION['civilite']);
        unset($_SESSION['nom']);
        unset($_SESSION['prenom']);
        unset($_SESSION['tel']);
        unset($_SESSION['hash_trajetAller']);
        unset($_SESSION['hash_trajetRetour']);
        unset($_SESSION['hash_commande']);

        $Valid="Nouveau trajet ajouté avec succès";
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

<?php
if (isset($_POST['Reserver'])) {
    echo '<form method="POST" id="Valider" name="Valider" action="">';

    if ($_SESSION['trajet']==1) {
        echo "<table><tr><td>";
        echo "<H1><span>Trajet</span> aller</H1>";

        echo "Nombre de passager : <b>".$_SESSION['passager']."</b><BR /><BR />";

        echo "De : <b>".$Result[0]."</b><BR />";
        echo "Vers : <b>".$Result[1]."</b><BR /><BR />";

        echo "Distance moyenne : <b>".$Result[2]."</b><BR />";
        echo "Temps de trajet moyen : <b>".$Result[3]."</b><BR /><BR />";
        echo "</td><td>";
        echo number_format($Result[4], 2,".", "")." €";
        echo "</td></tr>";
        ?>
        <input type="hidden" name="DistanceAller" value="<?php echo $Result[2]; ?>"/>
        <input type="hidden" name="TempsAller" value="<?php echo $Result[3]; ?>"/>
        <input type="hidden" name="PrixAller" value="<?php echo $Result[4]; ?>"/>
        <?php
    }

    if ($_SESSION['trajet']==2) {
        echo "<table><tr><td>";
        echo "<H1><span>Trajet</span> aller</H1>";

        echo "Nombre de passager : <b>".$_SESSION['passager']."</b><BR /><BR />";

        echo "De : <b>".$Result[0]."</b><BR />";
        echo "Vers : <b>".$Result[1]."</b><BR /><BR />";

        echo "Distance moyenne : <b>".$Result[2]."</b><BR />";
        echo "Temps de trajet moyen : <b>".$Result[3]."</b><BR /><BR />";
        echo "</td><td>";
        echo number_format($Result[4], 2,".", "")." €";
        echo "</td></tr>";
        ?>
        <input type="hidden" name="DistanceAller" value="<?php echo $Result[2]; ?>"/>
        <input type="hidden" name="TempsAller" value="<?php echo $Result[3]; ?>"/>
        <input type="hidden" name="PrixAller" value="<?php echo $Result[4]; ?>"/>
        <?php
        echo "<tr><td>";
        echo "<H1><span>Trajet</span> retour</H1>";       

        echo "Nombre de passager : <b>".$_SESSION['passager']."</b><BR /><BR />";

        echo "De : <b>".$Result2[0]."</b><BR />";
        echo "Vers <b>: ".$Result2[1]."</b><BR /><BR />";

        echo "Distance moyenne : <b>".$Result2[2]."</b><BR />";
        echo "Temps de trajet moyen : <b>".$Result2[3]."</b><BR /><BR />";
        echo "</td><td>";
        echo number_format($Result2[4], 2,".", "")." €";
        echo "</td></tr>";

        echo "<tr><td>";
        echo "<H1><span>Total</span> (aller / retour)</H1>";
        echo "</td><td>";
        echo number_format($Result[4]+$Result2[4], 2,".", "")." €";
        echo "</td></tr>";
        ?>
        <input type="hidden" name="DistanceRetour" value="<?php echo $Result2[2]; ?>"/>
        <input type="hidden" name="TempsRetour" value="<?php echo $Result2[3]; ?>"/>
        <input type="hidden" name="PrixRetour" value="<?php echo $Result2[4]; ?>"/>
        <?php
    }

    if ($_SESSION['trajet']==3) {
        echo "<table><tr><td>";
        echo "<H1><span>Trajet</span> retour</H1>";

        echo "Nombre de passager : <b>".$_SESSION['passager']."</b><BR /><BR />";

        echo "De : <b>".$Result3[0]."</b><BR />";
        echo "Vers : <b>".$Result3[1]."</b><BR /><BR />";

        echo "Distance moyenne : <b>".$Result3[2]."</b><BR />";
        echo "Temps de trajet moyen : <b>".$Result3[3]."</b><BR /><BR />";
        echo "</td><td>";
        echo number_format($Result3[4], 2,".", "")." €";
        echo "</td></tr>";
        ?>
        <input type="hidden" name="DistanceAller" value="<?php echo $Result3[2]; ?>"/>
        <input type="hidden" name="TempsAller" value="<?php echo $Result3[3]; ?>"/>
        <input type="hidden" name="PrixAller" value="<?php echo $Result3[4]; ?>"/>
        <?php
    } ?>
    <tr><td colspan="2">
    <BR /><BR /><input type="submit" value="Valider" name="Valider"/><input type="submit" value="Annuler" name="Annuler"/><input type="submit" value="Retour" name="Retour"/>
    </td></tr>
    </table>
    </form><?php
}
else {
    ?>
    <H1>Nouvelle destination</H1>

    <form method="POST" id="Reservation" name="Reservation" action="">
        <div id="Groupe">
        Client<BR />
        <select name="client" required="required">
        <option value="">-- Selection --</option>
        <?php while($Client=$SelectClient->fetch(PDO::FETCH_OBJ)) { ?>
            <option value="<?php echo $Client->hash; ?>" <?php if ((isset($_SESSION['client']))&&($_SESSION['client']==$Client->hash)) { echo "selected"; } ?>><?php echo $Client->nom." ".$Client->prenom; ?></option>
        <?php } ?>
        </select>
        </div>

      <h2>Informations de contact</h2>
      <div id="Groupe">
      Civilité<font color='#FF0000'>*</font> :<br />
      <select name="civilite" required>
      <option value="" >--</option>
      <option value="Mr" <?php if ($_SESSION['civilite']=="Mr") { echo "selected"; } ?>>Mr</option>
      <option value="Mme" <?php if ($_SESSION['civilite']=="Mme") { echo "selected"; } ?>>Mme</option>
      <option value="Mlle" <?php if ($_SESSION['civilite']=="Mlle") { echo "selected"; } ?>>Mlle</option>
      </select>
      <br /><br />
      Nom<font color='#FF0000'>*</font> :<BR />
      <input type="text" name="nom" value="<?php echo $_SESSION['nom']; ?>" required/> 
      <br />
      Prénom :<BR />
      <input type="text" name="prenom" value="<?php echo $_SESSION['prenom']; ?>"/>  
      <br />
      Numéro de téléphone<font color='#FF0000'>*</font> :<BR />
      <input type="text" name="tel" value="<?php echo $_SESSION['tel']; ?>" required/>   
      </div><br /><br />

        <h2>Informations de trajet</h2>
        <div id="Groupe">
            Trajet<BR />
            <select class="Module" name="trajetType" required>
            <option value="">--  --</option>
                <option value="1" <?php if ((isset($_SESSION['panierTrajet']))&&($_SESSION['panierTrajet']=="1")) { echo "selected"; } ?>>Particulier</option>
                <option value="2" <?php if ((isset($_SESSION['panierTrajet']))&&($_SESSION['panierTrajet']=="2")) { echo "selected"; } ?>>Professionnel</option>
            </select>
        </div><BR /><BR />

        <div id="Groupe">
            Départ<BR />
            <input id="autocomplete" class="controls" type="text" name="depart" value="<?php if (isset($_SESSION['depart'])) { echo $_SESSION['depart']; } ?>" required/>
        </div>

        <div id="Groupe">
            Destination<BR />
            <input id="autocomplete2" class="controls" type="text" name="destination" value="<?php if (isset($_SESSION['destination'])) { echo $_SESSION['destination']; } ?>" required/>
        </div><BR /><BR />

        <div id="Groupe">
            Nombre de voyageurs<BR />
            <select class="Module" name="passager" required>
                <?php 
                $SelectTaux=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Personne");
                $SelectTaux->execute();

                echo '<option value="">-- --</option>';
                while ($Taux=$SelectTaux->fetch(PDO::FETCH_OBJ)) { ?>
                    <option value="<?php echo $Taux->quantite; ?>" <?php if ((isset($_SESSION['passager']))&&($_SESSION['passager']==$Taux->quantite)) { echo "selected"; } ?> ><?php echo $Taux->quantite; ?></option>
                <?php } ?>
            </select>
        </div>

        <div id="Groupe">
            Type de trajet<BR />
            <select class="Module" name="trajet" id="trajet" required onChange="ChoixRetour()">
            <option value="">--  --</option>
                <option value="1">Aller Simple</option>
                <option value="2">Aller / retour</option>
                <option value="3">Retour Simple</option>
            </select>
        </div><BR /><BR />

        <div id="Retour">
        <h1><span>Trajet</span> retour</h1>

        <div id="Groupe">
            Aéroport<BR />
            <select class="Module" name="destinationRetour">
                <?php 
                $SelectDestinationRetour=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Destination");
                $SelectDestinationRetour->execute();

                echo '<option value="">-- Indiquez une destination --</option>';
                while ($destinationRetour=$SelectDestinationRetour->fetch(PDO::FETCH_OBJ)) { ?>
                    <option value="<?php echo $destinationRetour->libele; ?>" <?php if ((isset($_SESSION['destinationRetour']))&&($_SESSION['destinationRetour']==$destinationRetour->libele)) { echo "selected"; } ?> ><?php echo $destinationRetour->libele; ?></option>
                <?php } ?>
            </select>
        </div><BR />

        </div>

        <input type="submit" value="Calculer" name="Reserver"/>
    </form>
    <?php
}
?>
</article>
</section>
</CENTER>

<script>
var autocomplete;

function initMap() {
  var input = document.getElementById('autocomplete');
  var input2 = document.getElementById('autocomplete2');

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
  autocomplete2 = new google.maps.places.Autocomplete(input2, options);
}


</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAsFH7oT4bv7FEqJ_A6QDVnb53GdFJ0pRM&signed_in=true&libraries=places&callback=initMap" async defer></script>

<script>
function ChoixRetour() {
var select = document.getElementById('trajet');
var valeur = select.options[select.selectedIndex].value;
var divRetour = document.getElementById('Retour');

    if(valeur==2) {
        divRetour.style.height='150px';
    }
    if (valeur==1) {
        divRetour.style.height='0px';
    }
    if (valeur==3) {
        divRetour.style.height='0px';
    }
    if (valeur=="") {
        divRetour.style.height='0px';
    }
}
</script>

<script>
function form_x() {
	var i = document.Reservation.type.selectedIndex,
	val = document.Reservation.type.options[i].value;

	parent.location.href = val;
}
</script>

</body>

</html>
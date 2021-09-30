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

$SelectTrajet=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Trajet ORDER BY hash_trajet ASC");
$SelectTrajet->execute();

$SelectClient=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Client ORDER BY nom ASC");
$SelectClient->execute();

if (isset($_POST['codeTrajet'])) {
   $_SESSION['codeTrajet']=$_POST['codeTrajet'];
}
if (isset($_POST['codeRechercheClient'])) {
   $_SESSION['codeRechercheClient']=$_POST['codeRechercheClient'];
}

if ((!isset($_SESSION['codeTrajet']))||($_SESSION['codeTrajet']=="NULL")) {
   if ((!isset($_SESSION['codeRechercheClient']))||($_SESSION['codeRechercheClient']=="NULL")) {
        $SelectDemande=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Trajet WHERE etat='1' GROUP BY hash_commande ORDER BY created DESC");
        $SelectDemande->execute();

        $SelectValid=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Trajet WHERE etat='2' GROUP BY hash_commande ORDER BY created DESC");
        $SelectValid->execute();

        $SelectRefu=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Trajet WHERE etat='3' GROUP BY hash_commande ORDER BY created DESC");
        $SelectRefu->execute();
   }
   else {
        $SelectDemande=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Trajet WHERE etat='1' AND Client=:Client GROUP BY hash_commande ORDER BY created DESC");
        $SelectDemande->BindParam(':Client', $_SESSION['codeRechercheClient'], PDO::PARAM_STR);
        $SelectDemande->execute();

        $SelectValid=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Trajet WHERE etat='2' AND Client=:Client GROUP BY hash_commande ORDER BY created DESC");
        $SelectValid->BindParam(':Client', $_SESSION['codeRechercheClient'], PDO::PARAM_STR);
        $SelectValid->execute();

        $SelectRefu=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Trajet WHERE etat='3' AND Client=:Client GROUP BY hash_commande ORDER BY created DESC");
        $SelectRefu->BindParam(':Client', $_SESSION['codeRechercheClient'], PDO::PARAM_STR);
        $SelectRefu->execute();
   }
}
else {
   if ((!isset($_SESSION['codeRechercheClient']))||($_SESSION['codeRechercheClient']=="NULL")) {
        $SelectDemande=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Trajet WHERE etat='1' AND hash_trajet=:hash_trajet GROUP BY hash_commande ORDER BY created DESC");
        $SelectDemande->BindParam(':hash_trajet', $_SESSION['codeTrajet'], PDO::PARAM_STR);
        $SelectDemande->execute();

        $SelectValid=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Trajet WHERE etat='2' AND hash_trajet=:hash_trajet GROUP BY hash_commande ORDER BY created DESC");
        $SelectValid->BindParam(':hash_trajet', $_SESSION['codeTrajet'], PDO::PARAM_STR);
        $SelectValid->execute();

        $SelectRefu=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Trajet WHERE etat='3' AND hash_trajet=:hash_trajet GROUP BY hash_commande ORDER BY created DESC");
        $SelectRefu->BindParam(':hash_trajet', $_SESSION['codeTrajet'], PDO::PARAM_STR);
        $SelectRefu->execute();
   }
   else {
        $SelectDemande=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Trajet WHERE etat='1' AND hash_trajet=:hash_trajet AND Client=:Client GROUP BY hash_commande ORDER BY created DESC");
        $SelectDemande->BindParam(':hash_trajet', $_SESSION['codeTrajet'], PDO::PARAM_STR);
        $SelectDemande->BindParam(':Client', $_SESSION['codeRechercheClient'], PDO::PARAM_STR);
        $SelectDemande->execute();

        $SelectValid=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Trajet WHERE etat='2' AND hash_trajet=:hash_trajet AND Client=:Client GROUP BY hash_commande ORDER BY created DESC");
        $SelectValid->BindParam(':hash_trajet', $_SESSION['codeTrajet'], PDO::PARAM_STR);
        $SelectValid->BindParam(':Client', $_SESSION['codeRechercheClient'], PDO::PARAM_STR);
        $SelectValid->execute();

        $SelectRefu=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Trajet WHERE etat='3' AND hash_trajet=:hash_trajet AND Client=:Client GROUP BY hash_commande ORDER BY created DESC");
        $SelectRefu->BindParam(':hash_trajet', $_SESSION['codeTrajet'], PDO::PARAM_STR);
        $SelectRefu->BindParam(':Client', $_SESSION['codeRechercheClient'], PDO::PARAM_STR);
        $SelectRefu->execute();
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
<script>
	function createInstance() {
        var req = null;
		if (window.XMLHttpRequest)
		{
 			req = new XMLHttpRequest();
		} 
		else if (window.ActiveXObject) 
		{
			try {
				req = new ActiveXObject("Msxml2.XMLHTTP");
			} catch (e)
			{
				try {
					req = new ActiveXObject("Microsoft.XMLHTTP");
				} catch (e) 
				{
					alert("XHR not created");
				}
			}
	    }
        return req;
	};
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

<H1><font color="dodgerblue">Rechercher un trajet</font></H1></p>

<form name="formTrajet" action="" method="POST">
<select name="codeTrajet" required="required" onChange="this.form.submit()">
<option value="NULL" <?php if ($_SESSION['codeTrajet']=="NULL") { echo "selected"; } ?> >-- Code trajet --</option>
<?php while($Trajet=$SelectTrajet->fetch(PDO::FETCH_OBJ)) { ?>
    <option value="<?php echo $Trajet->hash_trajet; ?>" <?php if ($_SESSION['codeTrajet']==$Trajet->hash_trajet) { echo "selected"; } ?> ><?php echo $Trajet->hash_trajet; ?></option>
<?php } ?>
</select>
</form>

<form name="formClient" action="" method="POST">
<select name="codeRechercheClient" required="required" onChange="this.form.submit()">
<option value="NULL" <?php if ($_SESSION['codeRechercheClient']=="NULL") { echo "selected"; } ?> >-- Client --</option>
<?php while($Client=$SelectClient->fetch(PDO::FETCH_OBJ)) { ?>
    <option value="<?php echo $Client->hash; ?>" <?php if ($_SESSION['codeRechercheClient']==$Client->hash) { echo "selected"; } ?> ><?php echo $Client->nom." ".$Client->prenom; ?></option>
<?php } ?>
</select>
</form>

<H1><font color="dodgerblue">Vos demandes de disponibilité</font></H1>

<div id="TrajetDemande">
<table>
<?php while ($Demande=$SelectDemande->fetch(PDO::FETCH_OBJ)) { 

$SelectTrajetDemande=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Trajet WHERE hash_commande=:hash_commande AND hash_trajet!=:hash_trajet AND etat='1'");
$SelectTrajetDemande->bindParam(':hash_commande', $Demande->hash_commande, PDO::PARAM_STR);
$SelectTrajetDemande->bindParam(':hash_trajet', $Demande->hash_trajet, PDO::PARAM_STR);
$SelectTrajetDemande->execute(); 
$TrajetDemande=$SelectTrajetDemande->fetch(PDO::FETCH_OBJ);

$SelectContactDemande=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Contact WHERE hash_commande=:hash_commande");
$SelectContactDemande->bindParam(':hash_commande', $Demande->hash_commande, PDO::PARAM_STR);
$SelectContactDemande->execute(); 
$ContactDemande=$SelectContactDemande->fetch(PDO::FETCH_OBJ);

$SelectVolAllerDemande=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_vol WHERE hash_trajet=:hash_trajet");
$SelectVolAllerDemande->bindParam(':hash_trajet', $Demande->hash_trajet, PDO::PARAM_STR);
$SelectVolAllerDemande->execute(); 
$VolAllerDemande=$SelectVolAllerDemande->fetch(PDO::FETCH_OBJ);

$SelectVolRetourDemande=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_vol WHERE hash_trajet=:hash_trajet");
$SelectVolRetourDemande->bindParam(':hash_trajet', $TrajetDemande->hash_trajet, PDO::PARAM_STR);
$SelectVolRetourDemande->execute(); 
$VolRetourDemande=$SelectVolRetourDemande->fetch(PDO::FETCH_OBJ);

$MontantCommandeDemande=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Trajet WHERE hash_commande=:hash_commande");
$MontantCommandeDemande->bindParam(':hash_commande', $Demande->hash_commande, PDO::PARAM_STR);
$MontantCommandeDemande->execute(); 
$TotalDemande=0;

while ($MontantDemande=$MontantCommandeDemande->fetch(PDO::FETCH_OBJ)) { 
    $TotalDemande+=$MontantDemande->Prix;
}
?>

<tr>
<td class="Commande" <?php if (($Demande->type==1)||($TrajetDemande->etat!=1)||($Demande->etat!=1)) { echo 'rowspan="5"'; } else { echo 'rowspan="9"'; } ?>>
    <?php if($Demande->pro==1) { ?><img src="<?php echo $Home; ?>/lib/img/fleche.png" /><?php } else { ?><img src="<?php echo $Home; ?>/lib/img/flechePro.png" /><?php } ?><BR /><BR />
    N&deg; de commande<BR /><?php echo $Demande->hash_commande; ?><BR /><BR />
    <div id="Prix"><?php echo $TotalDemande." €"; ?><BR /><BR />Prix / pers<BR /><?php echo round($TotalDemande/$Demande->passager, 2)." €"; ?></div>
    </td>

<?php if ($Demande->etat==1) { 
    if ($Demande->type2==1) { ?>
        <td id="<?php echo $Demande->hash_trajet; ?>">
        <b>Trajet aller</b><BR />
        <?php echo $Demande->passager." personnes"; ?>
        </td>
    <?php } 
    else { ?>
        <td id="<?php echo $Demande->hash_trajet; ?>">
        <b>Trajet retour</b><BR />
        <?php echo $Demande->passager." personnes"; ?>
        </td>
    <?php } ?>
    <td><b>Num&eacute;ro de trajet</b><br /><?php echo $Demande->hash_trajet; ?></td>
    <td><b>D&eacute;part : </b><?php echo $Demande->Depart; ?><br /><b>Arriver : </b><?php echo $Demande->Arriver; ?></td>
    <td><?php echo $Demande->Distance; ?> (<?php echo $Demande->Temps; ?>)</td>
    </tr>

    <tr>
    <td colspan="3"><b>Information de vol</b><BR /><BR />
    <?php
    if (empty($VolAllerDemande->numero)) {
        echo "N°: de vol : <b>Non renseigné<BR /></b>";
    }
    else {
        echo "N°: de vol : <b>".$VolAllerDemande->numero."</b><BR />";
    }
    if (empty($VolAllerDemande->date)) {
        echo "Date de décollage : <b>Non renseigné</b><BR />";
        echo "Heure de décollage : <b>Non renseigné</b><BR />";
    }
    else {
        echo "Date de décollage : <b>".date("d", $VolAllerDemande->date)." / ".date("m", $VolAllerDemande->date)." / ".date("Y", $VolAllerDemande->date)."</b><BR />";
        echo "Heure de décollage : <b>".date("H", $VolAllerDemande->date)."h ".date("i", $VolAllerDemande->date)."min</b><BR />";
    }
    ?>
    </td>
    <td>
    <?php
    if ((empty($VolAllerDemande->numero))||(empty($VolAllerDemande->date))) {
        ?>
        <a class="renseignement" href="<?php echo $Home; ?>/Admin/Boutique/Trajet/infoTrajet.php?id=<?php echo $Demande->hash_trajet; ?>">Renseigner</a>
    <?php }
    else { ?>
        <a class="renseignement" href="<?php echo $Home; ?>/Admin/Boutique/Trajet/infoTrajet.php?id=<?php echo $Demande->hash_trajet; ?>">Modifier</a>
        <?php
    }
    ?>
    </td>
    </tr>

    <tr>
    <td colspan="4">
    <?php
    if ((empty($VolAllerDemande->numero))||(empty($VolAllerDemande->date))) {
        ?>
        <div id="Alerte1">
            En attente des informations de vol
        </div>
    <?php }
    else { ?>
        <div id="Alerte2">
            En attente de confirmation illicab
        </div>
        <?php
    }
    ?>
    </td>
    </tr>

    <script>
            function submitForm<?php echo $Demande->hash_trajet; ?>(element) { 
                function storing(data) {
                //envoi des element receptionné dans la div
                    var element = document.getElementById("<?php echo 'Affichage'.$Demande->hash_trajet; ?>");
                    element.innerHTML = data;
                } 
                
                var req =  createInstance();
                //récuperation des champs du formulaire
                var confirmation = document.<?php echo "form_".$Demande->hash_trajet; ?>.confirmation.value;
                //création >> nomChamp = nomVariable & nomChamp = nomVariable etc...
                var data = "confirmation=" + confirmation;

                req.onreadystatechange = function() { 
                    if(req.readyState == 4)
                    {
                        if(req.status == 200)
                        {
                            storing(req.responseText);  
                        }   
                        else    
                        {
                            alert("Error: returned status code " + req.status + " " + req.statusText);
                        }   
                    } 
                }; 
                
                req.open("POST", "<?php echo $Home; ?>/Admin/Boutique/Trajet/FormConfirmation.php?id=<?php echo $Demande->hash_trajet; ?>", true);
                req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                //envoi
                req.send(data);  
            }
    </script>

    <tr>
    <td colspan="3">
    <b>Confirmation de trajet</b>

    <form name="form_<?php echo $Demande->hash_trajet; ?>" action="<?php echo $Home; ?>/Admin/Boutique/Trajet/confirmation.php?id=<?php echo $Demande->hash_trajet; ?>" method="POST">
    <select name="confirmation" onChange="submitForm<?php echo $Demande->hash_trajet; ?>()">
    <option value="NULL" >-- Confirmation --</option>
    <option value="2">Accepter</option>
    <option value="3">Refuser</option>
    </select>
    <BR />

    <div id="<?php echo 'Affichage'.$Demande->hash_trajet; ?>"></div>

    </form>
    </td>
    <td>
        <?php echo '<a title="Supprimer" href="'.$Home.'/Admin/Boutique/Trajet/supprimer.php?id='.$Demande->id.'"><img src="'.$Home.'/Admin/lib/img/supprimer.png"></a>'; ?>
    </td>
    </tr>

<?php   
}
if ($TrajetDemande->etat==1) { ?>
    <tr>
    <td><b>Trajet retour</b></td>
    <td><b>Num&eacute;ro de trajet</b><br /><?php echo $TrajetDemande->hash_trajet; ?></td>
    <td><b>D&eacute;part : </b><?php echo $TrajetDemande->Depart; ?><br /><b>Arriver : </b><?php echo $TrajetDemande->Arriver; ?></td>
    <td><?php echo $TrajetDemande->Distance; ?> (<?php echo $TrajetDemande->Temps; ?>)</td>
    </tr>

    <tr>
    <td colspan="3"><b>Information de vol</b><BR /><BR />
    <?php
    if (empty($VolRetourDemande->numero)) {
        echo "N°: de vol : <b>Non renseigné<BR /></b>";
    }
    else {
        echo "N°: de vol : ".$VolRetourDemande->numero."<BR />";
    }
    if (empty($VolRetourDemande->date)) {
        echo "Date de décollage : <b>Non renseigné</b><BR />";
        echo "Heure de décollage : <b>Non renseigné</b><BR />";
    }
    else {
        echo "Date de décollage : <b>".date("d", $VolRetourDemande->date)." / ".date("m", $VolRetourDemande->date)." / ".date("Y", $VolRetourDemande->date)."</b><BR />";
        echo "Heure de décollage : <b>".date("H", $VolRetourDemande->date)."h ".date("i", $VolRetourDemande->date)."min</b><BR />";
    }
    ?>
    </td>
    <td>
    <?php
    if ((empty($VolRetourDemande->numero))||(empty($VolRetourDemande->date))) {
        ?>
        <a class="renseignement" href="<?php echo $Home; ?>/Admin/Boutique/Trajet/infoTrajet.php?id=<?php echo $TrajetDemande->hash_trajet; ?>">Renseigner</a>
    <?php }
    else { ?>
        <a class="renseignement" href="<?php echo $Home; ?>/Admin/Boutique/Trajet/infoTrajet.php?id=<?php echo $TrajetDemande->hash_trajet; ?>">Modifier</a>
        <?php
    }
    ?>
    </td>
    </tr>

    <tr>
    <td colspan="4">
    <?php
    if ((empty($VolRetourDemande->numero))||(empty($VolRetourDemande->date))) {
        ?>
        <div id="Alerte1">
            En attente des informations de vol
        </div>
    <?php }
    else { ?>
        <div id="Alerte2">
            En attente de confirmation illicab
        </div>
        <?php
    }
    ?>
    </td>
    </tr>

    <tr>
    <td colspan="3">
    <script>
        function submitForm<?php echo $TrajetDemande->hash_trajet; ?>(element) { 
            function storing(data) {
            //envoi des element receptionné dans la div
                var element = document.getElementById("<?php echo 'Affichage'.$TrajetDemande->hash_trajet; ?>");
                element.innerHTML = data;
            } 
            
            var req =  createInstance();
            //récuperation des champs du formulaire
            var confirmation = document.<?php echo "form_".$TrajetDemande->hash_trajet; ?>.confirmation.value;
            //création >> nomChamp = nomVariable & nomChamp = nomVariable etc...
            var data = "confirmation=" + confirmation;

            req.onreadystatechange = function() { 
                if(req.readyState == 4) {
                    if(req.status == 200) {
                        storing(req.responseText);  
                    }   
                    else  {
                        alert("Error: returned status code " + req.status + " " + req.statusText);
                    }   
                } 
            }; 
            
            req.open("POST", "<?php echo $Home; ?>/Admin/Boutique/Trajet/FormConfirmation.php?id=<?php echo $TrajetDemande->hash_trajet; ?>", true);
            req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            //envoi
            req.send(data);  
        }
    </script>

    <b>Confirmation de trajet</b>

    <form name="form_<?php echo $TrajetDemande->hash_trajet; ?>" action="<?php echo $Home; ?>/Admin/Boutique/Trajet/confirmation.php?id=<?php echo $TrajetDemande->hash_trajet; ?>" method="POST">
    <select name="confirmation" onChange="submitForm<?php echo $TrajetDemande->hash_trajet; ?>()">
    <option value="NULL" >-- Confirmation --</option>
    <option value="2">Accepter</option>
    <option value="3">Refuser</option>
    </select>
    <BR />

    <div id="<?php echo 'Affichage'.$TrajetDemande->hash_trajet; ?>"></div>

    </form>
    </td>
    <td>
        <?php echo '<a title="Supprimer" href="'.$Home.'/Admin/Boutique/Trajet/supprimer.php?id='.$TrajetDemande->id.'"><img src="'.$Home.'/Admin/lib/img/supprimer.png"></a>'; ?>
    </td>
    </tr>

<?php } ?>
<tr>
<td colspan="4"><b>Information de contact</b><BR /><BR />
<?php 
echo $ContactDemande->civilite." ".$ContactDemande->nom." ".$ContactDemande->prenom."<BR />
     Téléphone : ".$ContactDemande->tel."<BR />
     Email : ".$ContactDemande->email;
 ?>
 </td>
</tr>

<tr>
<th colspan="5"></th>
</tr>
<?php } ?>
</table>
</div>

<p><HR /></p>

<H1><font color="green">Vos trajet validés</font></H1>

<div id="TrajetValid">
<table>
<?php while ($Valide=$SelectValid->fetch(PDO::FETCH_OBJ)) { 

$SelectTrajetValid=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Trajet WHERE hash_commande=:hash_commande AND hash_trajet!=:hash_trajet AND etat='2'");
$SelectTrajetValid->bindParam(':hash_commande', $Valide->hash_commande, PDO::PARAM_STR);
$SelectTrajetValid->bindParam(':hash_trajet', $Valide->hash_trajet, PDO::PARAM_STR);
$SelectTrajetValid->execute(); 
$TrajetValid=$SelectTrajetValid->fetch(PDO::FETCH_OBJ);

$SelectContactValid=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Contact WHERE hash_commande=:hash_commande");
$SelectContactValid->bindParam(':hash_commande', $Valide->hash_commande, PDO::PARAM_STR);
$SelectContactValid->execute(); 
$ContactValid=$SelectContactValid->fetch(PDO::FETCH_OBJ);

$SelectVolAllerValid=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_vol WHERE hash_trajet=:hash_trajet");
$SelectVolAllerValid->bindParam(':hash_trajet', $Valide->hash_trajet, PDO::PARAM_STR);
$SelectVolAllerValid->execute(); 
$VolAllerValid=$SelectVolAllerValid->fetch(PDO::FETCH_OBJ);

$SelectVolRetourValid=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_vol WHERE hash_trajet=:hash_trajet");
$SelectVolRetourValid->bindParam(':hash_trajet', $TrajetValid->hash_trajet, PDO::PARAM_STR);
$SelectVolRetourValid->execute(); 
$VolRetourValid=$SelectVolRetourValid->fetch(PDO::FETCH_OBJ);

$SelectInfoPriseAller=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_prise WHERE hash_trajet=:hash_trajet");
$SelectInfoPriseAller->bindParam(':hash_trajet', $Valide->hash_trajet, PDO::PARAM_STR);
$SelectInfoPriseAller->execute(); 
$InfoPriseAller=$SelectInfoPriseAller->fetch(PDO::FETCH_OBJ);

$SelectInfoPriseRetour=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_prise WHERE hash_trajet=:hash_trajet");
$SelectInfoPriseRetour->bindParam(':hash_trajet', $TrajetValid->hash_trajet, PDO::PARAM_STR);
$SelectInfoPriseRetour->execute(); 
$InfoPriseRetour=$SelectInfoPriseRetour->fetch(PDO::FETCH_OBJ);

$SelectChauffeurAller=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_chauffeur WHERE hash_trajet=:hash_trajet");
$SelectChauffeurAller->bindParam(':hash_trajet', $Valide->hash_trajet, PDO::PARAM_STR);
$SelectChauffeurAller->execute(); 
$ChauffeurAller=$SelectChauffeurAller->fetch(PDO::FETCH_OBJ);

$SelectChauffeurRetour=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_chauffeur WHERE hash_trajet=:hash_trajet");
$SelectChauffeurRetour->bindParam(':hash_trajet', $TrajetValid->hash_trajet, PDO::PARAM_STR);
$SelectChauffeurRetour->execute(); 
$ChauffeurRetour=$SelectChauffeurRetour->fetch(PDO::FETCH_OBJ);

$SelectChauffeurListeAller=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Client WHERE groupe='Livreur'");
$SelectChauffeurListeAller->execute();

$SelectVehiculeListeAller=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Vehicule");
$SelectVehiculeListeAller->execute(); 

$SelectChauffeurListeRetour=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Client WHERE groupe='Livreur'");
$SelectChauffeurListeRetour->execute();

$SelectVehiculeListeRetour=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Vehicule");
$SelectVehiculeListeRetour->execute(); 

$MontantCommandeValid=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Trajet WHERE hash_commande=:hash_commande");
$MontantCommandeValid->bindParam(':hash_commande', $Valide->hash_commande, PDO::PARAM_STR);
$MontantCommandeValid->execute(); 
$TotalValid=0;

while ($MontantValid=$MontantCommandeValid->fetch(PDO::FETCH_OBJ)) { 
    $TotalValid+=$MontantValid->Prix;
}
?>

<tr>
<td class="Commande" <?php if (($Valide->type==1)||($TrajetValid->etat!=2)||($Valide->etat!=2)) { echo 'rowspan="5"'; } else { echo 'rowspan="9"'; } ?>>
    <?php if($Valide->pro==1) { ?><img src="<?php echo $Home; ?>/lib/img/fleche.png" /><?php } else { ?><img src="<?php echo $Home; ?>/lib/img/flechePro.png" /><?php } ?><BR /><BR />
    N&deg; de commande<BR /><?php echo $Valide->hash_commande; ?><BR /><BR />
    <div id="Prix"><?php echo $TotalValid." €"; ?><BR /><BR />Prix / pers<BR /><?php echo round($TotalValid/$Valide->passager, 2)." €"; ?></div>
    </td>

<?php if ($Valide->etat==2) { 
    if ($Valide->type2==1) { ?>
        <td id="<?php echo $Valide->hash_trajet; ?>">
        <b>Trajet aller</b><BR />
        <?php echo $Valide->passager." personnes"; ?>
        </td>
    <?php } 
    else { ?>
        <td id="<?php echo $Valide->hash_trajet; ?>">
        <b>Trajet retour</b><BR />
        <?php echo $Valide->passager." personnes"; ?>
        </td>
    <?php } ?>
    <td><b>Num&eacute;ro de trajet</b><br /><?php echo $Valide->hash_trajet; ?></td>
    <td><b>D&eacute;part : </b><?php echo $Valide->Depart; ?><br /><b>Arriver : </b><?php echo $Valide->Arriver; ?></td>
    <td colspan="2"><?php echo $Valide->Distance; ?> (<?php echo $Valide->Temps; ?>)</td>
    </tr>

    <tr>
    <td colspan="3"><b>Information de vol</b><BR /><BR />
    <?php
    if (empty($VolAllerValid->numero)) {
        echo "N°: de vol : <b>Non renseigné<BR /></b>";
    }
    else {
        echo "N°: de vol : ".$VolAllerValid->numero."<BR />";
    }
    if (empty($VolAllerValid->date)) {
        echo "Date de décollage : <b>Non renseigné</b><BR />";
        echo "Heure de décollage : <b>Non renseigné</b><BR />";
    }
    else {
        echo "Date de décollage : <b>".date("d", $VolAllerValid->date)." / ".date("m", $VolAllerValid->date)." / ".date("Y", $VolAllerValid->date)."</b><BR />";
        echo "Heure de décollage : <b>".date("H", $VolAllerValid->date)."h ".date("i", $VolAllerValid->date)."min</b><BR />";
    }
    ?>
    </td>
    <td colspan="2">
    <?php
    if ((empty($VolAllerValid->numero))||(empty($VolAllerValid->date))) {
        ?>
        <a class="renseignement" href="<?php echo $Home; ?>/Admin/Boutique/Trajet/infoTrajet.php?id=<?php echo $Valide->hash_trajet; ?>">Renseigner</a>
    <?php }
    else { ?>
        <a class="renseignement" href="<?php echo $Home; ?>/Admin/Boutique/Trajet/infoTrajet.php?id=<?php echo $Valide->hash_trajet; ?>">Modifier</a>
        <?php
    }
    ?>
    </td>
    </tr>

    <tr>
        <td colspan="3">
            <b>Information de prise en charge <span>aller</span></b><BR /><BR />

            <?php
            echo "Date de prise en charge : <b>".date("d", $InfoPriseAller->date)." / ".date("m", $InfoPriseAller->date)." / ".date("Y", $InfoPriseAller->date)."</b><BR />";
            echo "Heure de prise en charge : <b>".date("H", $InfoPriseAller->date)."h ".date("i", $InfoPriseAller->date)."min</b>";
            ?>
        </td>
        <td colspan="2">
            <b>Information complémentaire</b><BR /><BR />
            <?php echo $InfoPriseAller->commentaire; ?>
        </td>
    </tr>

    <tr>
        <td colspan="3">
            <script>
                    function submitForm<?php echo $Valide->hash_trajet; ?>(element) { 
                        function storing(data) {
                        //envoi des element receptionné dans la div
                            var element = document.getElementById("<?php echo 'Affichage'.$Valide->hash_trajet; ?>");
                            element.innerHTML = data;
                        } 
                        
                        var req =  createInstance();
                        //récuperation des champs du formulaire
                        var confirmation = document.<?php echo "form_Modif".$Valide->hash_trajet; ?>.confirmation.value;
                        //création >> nomChamp = nomVariable & nomChamp = nomVariable etc...
                        var data = "confirmation=" + confirmation;

                        req.onreadystatechange = function() { 
                            if(req.readyState == 4)
                            {
                                if(req.status == 200)
                                {
                                    storing(req.responseText);  
                                }   
                                else    
                                {
                                    alert("Error: returned status code " + req.status + " " + req.statusText);
                                }   
                            } 
                        }; 
                        
                        req.open("POST", "<?php echo $Home; ?>/Admin/Boutique/Trajet/FormConfirmation.php?id=<?php echo $Valide->hash_trajet; ?>", true);
                        req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                        //envoi
                        req.send(data);  
                    }
            </script>
            <div id="CaseConfirmation">
            <b>Modification de trajet</b><BR />

            <form name="form_Modif<?php echo $Valide->hash_trajet; ?>" action="<?php echo $Home; ?>/Admin/Boutique/Trajet/confirmation.php?id=<?php echo $Valide->hash_trajet; ?>" method="POST">
            <select name="confirmation" onChange="submitForm<?php echo $Valide->hash_trajet; ?>()">
            <option value="NULL" >-- Confirmation --</option>
            <option value="2">Modifier</option>
            <option value="3">Refuser</option>
            </select>
            <BR />

            <div id="<?php echo 'Affichage'.$Valide->hash_trajet; ?>"></div>

            </form>
            </div>
            </td>
            
            <td colspan ="1">
            <b>Choix du chauffeur</b><BR />
            <form name="form_livreur<?php echo $Valide->hash_trajet; ?>" action="<?php echo $Home; ?>/Admin/Boutique/Trajet/livreur.php?id=<?php echo $Valide->hash_trajet; ?>" method="POST">
            <select name="livreur">
            <option value="NULL" >-- Chauffeur --</option>
            <?php while($ChauffeurListeAller=$SelectChauffeurListeAller->fetch(PDO::FETCH_OBJ)) { ?> 
                <option value="<?php echo $ChauffeurListeAller->hash; ?>" <?php if($ChauffeurAller->hash_client==$ChauffeurListeAller->hash) { echo "selected"; } ?>><?php echo $ChauffeurListeAller->nom.' '.$ChauffeurListeAller->prenom; ?></option>  
            <?php } ?>
            </select>
            <BR />
            <select name="vehicule">
            <option value="NULL" >-- Véhicule --</option>
            <?php while($VehiculeListeAller=$SelectVehiculeListeAller->fetch(PDO::FETCH_OBJ)) { ?> 
                <option value="<?php echo $VehiculeListeAller->hash_vehicule; ?>" <?php if($ChauffeurAller->hash_vehicule==$VehiculeListeAller->hash_vehicule) { echo "selected"; } ?>><?php echo $VehiculeListeAller->libele; ?></option>  
            <?php } ?>
            </select>
            <BR /><BR />
            <?php if ($ChauffeurAller->hash_client!=NULL) {
                echo '<input type="submit" name="Modifier" value="Modifier"/>';
            }
            else {
                echo '<input type="submit" name="Confirmer" value="Confirmer"/>';
            } ?>
            </form>
            </div>
        </td>
        <td>
            <?php echo '<a title="Supprimer" href="'.$Home.'/Admin/Boutique/Trajet/supprimer.php?id='.$Valide->id.'"><img src="'.$Home.'/Admin/lib/img/supprimer.png"></a>'; ?>
        </td>
    </tr>

    <?php 
}
if ($TrajetValid->etat==2) { ?>
    <tr>
    <td id="<?php echo $TrajetValid->hash_trajet; ?>"><b>Trajet retour</b></td>
    <td><b>Num&eacute;ro de trajet</b><br /><?php echo $TrajetValid->hash_trajet; ?></td>
    <td><b>D&eacute;part : </b><?php echo $TrajetValid->Depart; ?><br /><b>Arriver : </b><?php echo $TrajetValid->Arriver; ?></td>
    <td colspan="2"><?php echo $TrajetValid->Distance; ?> (<?php echo $TrajetValid->Temps; ?>)</td>
    </tr>

    <tr>
    <td colspan="3"><b>Information de vol</b><BR /><BR />
    <?php
    if (empty($VolRetourValid->numero)) {
        echo "N°: de vol : <b>Non renseigné<BR /></b>";
    }
    else {
        echo "N°: de vol : ".$VolRetourValid->numero."<BR />";
    }
    if (empty($VolRetourValid->date)) {
        echo "Date de décollage : <b>Non renseigné</b><BR />";
        echo "Heure de décollage : <b>Non renseigné</b><BR />";
    }
    else {
        echo "Date de décollage : <b>".date("d", $VolRetourValid->date)." / ".date("m", $VolRetourValid->date)." / ".date("Y", $VolRetourValid->date)."</b><BR />";
        echo "Heure de décollage : <b>".date("H", $VolRetourValid->date)."h ".date("i", $VolRetourValid->date)."min</b><BR />";
    }
    ?>
    </td>
    <td colspan="2">
    <?php
    if ((empty($VolRetourValid->numero))||(empty($VolRetourValid->date))) {
        ?>
        <a class="renseignement" href="<?php echo $Home; ?>/Admin/Boutique/Trajet/infoTrajet.php?id=<?php echo $TrajetValid->hash_trajet; ?>">Renseigner</a>
    <?php }
    else { ?>
        <a class="renseignement" href="<?php echo $Home; ?>/Admin/Boutique/Trajet/infoTrajet.php?id=<?php echo $TrajetValid->hash_trajet; ?>">Modifier</a>
        <?php
    }
    ?>
    </td>
    </tr>

    <tr>
        <td colspan="3">
            <b>Information de prise en charge <span>retour</span></b><BR /><BR />

            <?php
            echo "Date de prise en charge : <b>".date("d", $InfoPriseRetour->date)." / ".date("m", $InfoPriseRetour->date)." / ".date("Y", $InfoPriseRetour->date)."</b><BR />";
            echo "Heure de prise en charge : <b>".date("H", $InfoPriseRetour->date)."h ".date("i", $InfoPriseRetour->date)."min</b>";
            ?>
        </td>
        <td colspan="2">
            <b>Information complémentaire</b><BR /><BR />
            <?php echo $InfoPriseRetour->commentaire; ?>
        </td>
    </tr>
    <tr>
        <td colspan="3">
            <script>
                    function submitForm<?php echo $TrajetValid->hash_trajet; ?>(element) { 
                        function storing(data) {
                        //envoi des element receptionné dans la div
                            var element = document.getElementById("<?php echo 'Affichage'.$TrajetValid->hash_trajet; ?>");
                            element.innerHTML = data;
                        } 
                        
                        var req =  createInstance();
                        //récuperation des champs du formulaire
                        var confirmation = document.<?php echo "form_Modif".$TrajetValid->hash_trajet; ?>.confirmation.value;
                        //création >> nomChamp = nomVariable & nomChamp = nomVariable etc...
                        var data = "confirmation=" + confirmation;

                        req.onreadystatechange = function() { 
                            if(req.readyState == 4)
                            {
                                if(req.status == 200)
                                {
                                    storing(req.responseText);  
                                }   
                                else    
                                {
                                    alert("Error: returned status code " + req.status + " " + req.statusText);
                                }   
                            } 
                        }; 
                        
                        req.open("POST", "<?php echo $Home; ?>/Admin/Boutique/Trajet/FormConfirmation.php?id=<?php echo $TrajetValid->hash_trajet; ?>", true);
                        req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                        //envoi
                        req.send(data);  
                    }
            </script>
            <div id="CaseConfirmation">
            <b>Modification de trajet</b><BR />

            <form name="form_Modif<?php echo $TrajetValid->hash_trajet; ?>" action="<?php echo $Home; ?>/Admin/Boutique/Trajet/confirmation.php?id=<?php echo $TrajetValid->hash_trajet; ?>" method="POST">
            <select name="confirmation" onChange="submitForm<?php echo $TrajetValid->hash_trajet; ?>()">
            <option value="NULL" >-- Confirmation --</option>
            <option value="2">Modifier</option>
            <option value="3">Refuser</option>
            </select>
            <BR />

            <div id="<?php echo 'Affichage'.$TrajetValid->hash_trajet; ?>"></div>

            </form>
            </div>
            </td>

            <td colspan ="1">
            <b>Choix du chauffeur</b><BR />
            <form name="form_livreur<?php echo $TrajetValid->hash_trajet; ?>" action="<?php echo $Home; ?>/Admin/Boutique/Trajet/livreur.php?id=<?php echo $TrajetValid->hash_trajet; ?>" method="POST">
            <select name="livreur">
            <option value="NULL" >-- Chauffeur --</option>
            <?php while($ChauffeurListeRetour=$SelectChauffeurListeRetour->fetch(PDO::FETCH_OBJ)) { ?> 
                <option value="<?php echo $ChauffeurListeRetour->hash; ?>" <?php if($ChauffeurRetour->hash_client==$ChauffeurListeRetour->hash) { echo "selected"; } ?>><?php echo $ChauffeurListeRetour->nom.' '.$ChauffeurListeRetour->prenom; ?></option>  
            <?php } ?>
            </select>
            <BR />
            <select name="vehicule">
            <option value="NULL" >-- Véhicule --</option>
            <?php while($VehiculeListeRetour=$SelectVehiculeListeRetour->fetch(PDO::FETCH_OBJ)) { ?> 
                <option value="<?php echo $VehiculeListeRetour->hash_vehicule; ?>" <?php if($ChauffeurRetour->hash_vehicule==$VehiculeListeRetour->hash_vehicule) { echo "selected"; } ?>><?php echo $VehiculeListeRetour->libele; ?></option>  
            <?php } ?>
            </select>
            <BR /><BR />
            <?php if ($ChauffeurRetour->hash_client!=NULL) {
                echo '<input type="submit" name="Modifier" value="Modifier"/>';
            }
            else {
                echo '<input type="submit" name="Confirmer" value="Confirmer"/>';
            } ?>
            </form>
        </td>
        <td>
            <?php echo '<a title="Supprimer" href="'.$Home.'/Admin/Boutique/Trajet/supprimer.php?id='.$TrajetValid->id.'"><img src="'.$Home.'/Admin/lib/img/supprimer.png"></a>'; ?>
        </td>
    </tr>

<?php } ?>
<tr>
<td colspan="6"><b>Information de contact</b><BR /><BR />
<?php 
echo $ContactValid->civilite." ".$ContactValid->nom." ".$ContactValid->prenom."<BR />
     Téléphone : ".$ContactValid->tel."<BR />
     Email : ".$ContactValid->email;
 ?>
 </td>
</tr>


<tr>
<th colspan="6"></th>
</tr>
<?php } ?>
</table>
</div>

<p><HR /></p>

<H1><font color="crimson">Vos Trajet refuser</font></H1>

<div id="TrajetRefu">
<table>
<?php while ($Refu=$SelectRefu->fetch(PDO::FETCH_OBJ)) { 

$SelectTrajetRefu=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Trajet WHERE hash_commande=:hash_commande AND hash_trajet!=:hash_trajet AND etat='3'");
$SelectTrajetRefu->bindParam(':hash_commande', $Refu->hash_commande, PDO::PARAM_STR);
$SelectTrajetRefu->bindParam(':hash_trajet', $Refu->hash_trajet, PDO::PARAM_STR);
$SelectTrajetRefu->execute(); 
$TrajetRefu=$SelectTrajetRefu->fetch(PDO::FETCH_OBJ);

$SelectContactRefu=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Contact WHERE hash_commande=:hash_commande");
$SelectContactRefu->bindParam(':hash_commande', $Refu->hash_commande, PDO::PARAM_STR);
$SelectContactRefu->execute(); 
$ContactRefu=$SelectContactRefu->fetch(PDO::FETCH_OBJ);

$SelectVolAllerRefu=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_vol WHERE hash_trajet=:hash_trajet");
$SelectVolAllerRefu->bindParam(':hash_trajet', $Refu->hash_trajet, PDO::PARAM_STR);
$SelectVolAllerRefu->execute(); 
$VolAllerRefu=$SelectVolAllerRefu->fetch(PDO::FETCH_OBJ);

$SelectVolRetourRefu=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_vol WHERE hash_trajet=:hash_trajet");
$SelectVolRetourRefu->bindParam(':hash_trajet', $TrajetRefu->hash_trajet, PDO::PARAM_STR);
$SelectVolRetourRefu->execute(); 
$VolRetourRefu=$SelectVolRetourRefu->fetch(PDO::FETCH_OBJ);

$SelectMotifRefuAller=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_refu WHERE hash_trajet=:hash_trajet");
$SelectMotifRefuAller->bindParam(':hash_trajet', $Refu->hash_trajet, PDO::PARAM_STR);
$SelectMotifRefuAller->execute(); 
$MotifRefuAller=$SelectMotifRefuAller->fetch(PDO::FETCH_OBJ);

$SelectMotifRefuRetour=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_refu WHERE hash_trajet=:hash_trajet");
$SelectMotifRefuRetour->bindParam(':hash_trajet', $TrajetRefu->hash_trajet, PDO::PARAM_STR);
$SelectMotifRefuRetour->execute(); 
$MotifRefuRetour=$SelectMotifRefuRetour->fetch(PDO::FETCH_OBJ);

$MontantCommandeRefu=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Trajet WHERE hash_commande=:hash_commande");
$MontantCommandeRefu->bindParam(':hash_commande', $Refu->hash_commande, PDO::PARAM_STR);
$MontantCommandeRefu->execute(); 
$TotalRefu=0;

while ($MontantRefu=$MontantCommandeRefu->fetch(PDO::FETCH_OBJ)) { 
    $TotalRefu+=$MontantRefu->Prix;
}
?>

<tr>
<td class="Commande" <?php if (($Refu->type==1)||($TrajetRefu->etat!=3)||($Refu->etat!=3)) { echo 'rowspan="4"'; } else { echo 'rowspan="7"'; } ?>>
    <?php if($Refu->pro==1) { ?><img src="<?php echo $Home; ?>/lib/img/fleche.png" /><?php } else { ?><img src="<?php echo $Home; ?>/lib/img/flechePro.png" /><?php } ?><BR /><BR />
    N&deg; de commande<BR /><?php echo $Refu->hash_commande; ?><BR /><BR />
    <div id="Prix"><?php echo $TotalRefu." €"; ?><BR /><BR />Prix / pers<BR /><?php echo round($TotalRefu/$Refu->passager, 2)." €"; ?></div>
</td>

<?php if ($Refu->etat==3) { 
    if ($Refu->type2==1) { ?>
        <td id="<?php echo $Refu->hash_trajet; ?>">
        <b>Trajet aller</b><BR />
        <?php echo $Refu->passager." personnes"; ?>
        </td>
    <?php } 
    else { ?>
        <td id="<?php echo $Refu->hash_trajet; ?>">
        <b>Trajet retour</b><BR />
        <?php echo $Refu->passager." personnes"; ?>
        </td>
    <?php } ?>
    <td><b>Num&eacute;ro de trajet</b><br /><?php echo $Refu->hash_trajet; ?></td>
    <td><b>D&eacute;part : </b><?php echo $Refu->Depart; ?><br /><b>Arriver : </b><?php echo $Refu->Arriver; ?></td>
    <td colspan="2"><?php echo $Refu->Distance; ?> (<?php echo $Refu->Temps; ?>)</td>
    </tr>

    <tr>
    <td colspan="3"><b>Information de vol</b><BR /><BR />
    <?php
    if (empty($VolAllerRefu->numero)) {
        echo "N°: de vol : <b>Non renseigné<BR /></b>";
    }
    else {
        echo "N°: de vol : ".$VolAllerRefu->numero."<BR />";
    }
    if (empty($VolAllerRefu->date)) {
        echo "Date de décollage : <b>Non renseigné</b><BR />";
        echo "Heure de décollage : <b>Non renseigné</b><BR />";
    }
    else {
        echo "Date de décollage : <b>".date("d", $VolAllerRefu->date)." / ".date("m", $VolAllerRefu->date)." / ".date("Y", $VolAllerRefu->date)."</b><BR />";
        echo "Heure de décollage : <b>".date("H", $VolAllerRefu->date)."h ".date("i", $VolAllerRefu->date)."min</b><BR />";
    }
    ?>
    </td>
    <td colspan="2"><b>Information complémentaire</b><BR /><BR />

        <?php echo $MotifRefuAller->motif; ?>
    </td>
    </tr>

    <tr>
    <td colspan="4">
        <script>
                function submitForm<?php echo $Refu->hash_trajet; ?>(element) { 
                    function storing(data) {
                    //envoi des element receptionné dans la div
                        var element = document.getElementById("<?php echo 'Affichage'.$Refu->hash_trajet; ?>");
                        element.innerHTML = data;
                    } 
                    
                    var req =  createInstance();
                    //récuperation des champs du formulaire
                    var confirmation = document.<?php echo "form_Refu".$Refu->hash_trajet; ?>.confirmation.value;
                    //création >> nomChamp = nomVariable & nomChamp = nomVariable etc...
                    var data = "confirmation=" + confirmation;

                    req.onreadystatechange = function() { 
                        if(req.readyState == 4)
                        {
                            if(req.status == 200)
                            {
                                storing(req.responseText);  
                            }   
                            else    
                            {
                                alert("Error: returned status code " + req.status + " " + req.statusText);
                            }   
                        } 
                    }; 
                    
                    req.open("POST", "<?php echo $Home; ?>/Admin/Boutique/Trajet/FormConfirmation.php?id=<?php echo $Refu->hash_trajet; ?>", true);
                    req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    //envoi
                    req.send(data);  
                }
        </script>
        <div id="CaseConfirmation">
        <b>Modification de trajet</b><BR />

        <form name="form_Refu<?php echo $Refu->hash_trajet; ?>" action="<?php echo $Home; ?>/Admin/Boutique/Trajet/confirmation.php?id=<?php echo $Refu->hash_trajet; ?>" method="POST">
        <select name="confirmation" onChange="submitForm<?php echo $Refu->hash_trajet; ?>()">
        <option value="NULL" >-- Confirmation --</option>
        <option value="2">Accepter</option>
        </select>
        <BR />

        <div id="<?php echo 'Affichage'.$Refu->hash_trajet; ?>"></div>
        </form>
        </div>
        </td>
        <td>
            <?php echo '<a title="Supprimer" href="'.$Home.'/Admin/Boutique/Trajet/supprimer.php?id='.$Refu->id.'"><img src="'.$Home.'/Admin/lib/img/supprimer.png"></a>'; ?>
        </td>
    </tr>
    <?php 
}
if ($TrajetRefu->etat==3) { ?>
    <tr>
    <td id="<?php echo $TrajetRefu->hash_trajet; ?>"><b>Trajet retour</b></td>
    <td><b>Num&eacute;ro de trajet</b><br /><?php echo $TrajetRefu->hash_trajet; ?></td>
    <td><b>D&eacute;part : </b><?php echo $TrajetRefu->Depart; ?><br /><b>Arriver : </b><?php echo $TrajetRefu->Arriver; ?></td>
    <td colspan="2"><?php echo $TrajetRefu->Distance; ?> (<?php echo $TrajetRefu->Temps; ?>)</td>
    </tr>

    <tr>
    <td colspan="3"><b>Information de vol</b><BR /><BR />
    <?php
    if (empty($VolRetourRefu->numero)) {
        echo "N°: de vol : <b>Non renseigné<BR /></b>";
    }
    else {
        echo "N°: de vol : ".$VolRetourRefu->numero."<BR />";
    }
    if (empty($VolRetourRefu->date)) {
        echo "Date de décollage : <b>Non renseigné</b><BR />";
        echo "Heure de décollage : <b>Non renseigné</b><BR />";
    }
    else {
        echo "Date de décollage : <b>".date("d", $VolRetourRefu->date)." / ".date("m", $VolRetourRefu->date)." / ".date("Y", $VolRetourRefu->date)."</b><BR />";
        echo "Heure de décollage : <b>".date("H", $VolRetourRefu->date)."h ".date("i", $VolRetourRefu->date)."min</b><BR />";
    }
    ?>
    </td>
    <td colspan="2"><b>Information complémentaire</b><BR /><BR />

        <?php echo $MotifRefuRetour->motif; ?>
    </td>
    </tr>

    <tr>
    <td colspan="4">
        <script>
                function submitForm<?php echo $TrajetRefu->id; ?>(element) { 
                    function storing(data) {
                    //envoi des element receptionné dans la div
                        var element = document.getElementById("<?php echo 'Affichage'.$TrajetRefu->id; ?>");
                        element.innerHTML = data;
                    } 
                    
                    var req =  createInstance();
                    //récuperation des champs du formulaire
                    var confirmation = document.<?php echo "form_Refu".$TrajetRefu->id; ?>.confirmation.value;
                    //création >> nomChamp = nomVariable & nomChamp = nomVariable etc...
                    var data = "confirmation=" + confirmation;

                    req.onreadystatechange = function() { 
                        if(req.readyState == 4)
                        {
                            if(req.status == 200)
                            {
                                storing(req.responseText);  
                            }   
                            else    
                            {
                                alert("Error: returned status code " + req.status + " " + req.statusText);
                            }   
                        } 
                    }; 
                    
                    req.open("POST", "<?php echo $Home; ?>/Admin/Boutique/Trajet/FormConfirmation.php?id=<?php echo $TrajetRefu->id; ?>", true);
                    req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    //envoi
                    req.send(data);  
                }
        </script>
        <div id="CaseConfirmation">
        <b>Confirmation de trajet</b><BR />

        <form name="form_Refu<?php echo $TrajetRefu->id; ?>" action="<?php echo $Home; ?>/Admin/Boutique/Trajet/confirmation.php?id=<?php echo $TrajetRefu->id; ?>" method="POST">
        <select name="confirmation" onChange="submitForm<?php echo $TrajetRefu->id; ?>()">
        <option value="NULL" >-- Confirmation --</option>
        <option value="2">Accepter</option>
        </select>
        <BR />

        <div id="<?php echo 'Affichage'.$TrajetRefu->id; ?>"></div>
        </form>
        </div>
        </td>
        <td>
            <?php echo '<a title="Supprimer" href="'.$Home.'/Admin/Boutique/Trajet/supprimer.php?id='.$TrajetRefu->id.'"><img src="'.$Home.'/Admin/lib/img/supprimer.png"></a>'; ?>
        </td>
    </tr>

<?php } ?>
<tr>
<td colspan="5"><b>Information de contact</b><BR /><BR />
<?php 
echo $ContactRefu->civilite." ".$ContactRefu->nom." ".$ContactRefu->prenom."<BR />
     Téléphone : ".$ContactRefu->tel."<BR />
     Email : ".$ContactRefu->email;
 ?>
 </td>
</tr>

<tr>
<th colspan="6"></th>
</tr>
<?php } ?>
</table>
</div>

</article>
</section>
</div>
</CENTER>
</body>

</html>
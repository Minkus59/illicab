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
$Now=time();

$SelectDemande=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Trajet WHERE etat='1' GROUP BY hash_commande ORDER BY created DESC");
$SelectDemande->execute();

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
        <a class="renseignement" href="<?php echo $Home; ?>/Admin/Boutique/TrajetDemander/infoTrajet.php?id=<?php echo $Demande->hash_trajet; ?>">Renseigner</a>
    <?php }
    else { ?>
        <a class="renseignement" href="<?php echo $Home; ?>/Admin/Boutique/TrajetDemander/infoTrajet.php?id=<?php echo $Demande->hash_trajet; ?>">Modifier</a>
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
                
                req.open("POST", "<?php echo $Home; ?>/Admin/Boutique/TrajetDemander/FormConfirmation.php?id=<?php echo $Demande->hash_trajet; ?>", true);
                req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                //envoi
                req.send(data);  
            }
    </script>

    <tr>
    <td colspan="4">
    <b>Confirmation de trajet</b>

    <form name="form_<?php echo $Demande->hash_trajet; ?>" action="<?php echo $Home; ?>/Admin/Boutique/TrajetDemander/confirmation.php?id=<?php echo $Demande->hash_trajet; ?>" method="POST">
    <select name="confirmation" onChange="submitForm<?php echo $Demande->hash_trajet; ?>()">
    <option value="NULL" >-- Confirmation --</option>
    <option value="2">Accepter</option>
    <option value="3">Refuser</option>
    </select>
    <BR />

    <div id="<?php echo 'Affichage'.$Demande->hash_trajet; ?>"></div>

    </form>
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
        <a class="renseignement" href="<?php echo $Home; ?>/Admin/Boutique/TrajetDemander/infoTrajet.php?id=<?php echo $TrajetDemande->hash_trajet; ?>">Renseigner</a>
    <?php }
    else { ?>
        <a class="renseignement" href="<?php echo $Home; ?>/Admin/Boutique/TrajetDemander/infoTrajet.php?id=<?php echo $TrajetDemande->hash_trajet; ?>">Modifier</a>
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
    <td colspan="4">
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
            
            req.open("POST", "<?php echo $Home; ?>/Admin/Boutique/TrajetDemander/FormConfirmation.php?id=<?php echo $TrajetDemande->hash_trajet; ?>", true);
            req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            //envoi
            req.send(data);  
        }
    </script>

    <b>Confirmation de trajet</b>

    <form name="form_<?php echo $TrajetDemande->hash_trajet; ?>" action="<?php echo $Home; ?>/Admin/Boutique/TrajetDemander/confirmation.php?id=<?php echo $TrajetDemande->hash_trajet; ?>" method="POST">
    <select name="confirmation" onChange="submitForm<?php echo $TrajetDemande->hash_trajet; ?>()">
    <option value="NULL" >-- Confirmation --</option>
    <option value="2">Accepter</option>
    <option value="3">Refuser</option>
    </select>
    <BR />

    <div id="<?php echo 'Affichage'.$TrajetDemande->hash_trajet; ?>"></div>

    </form>
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


</article>
</section>
</div>
</CENTER>
</body>

</html>
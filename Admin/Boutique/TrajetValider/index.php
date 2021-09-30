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

$SelectValid=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_prise 
RIGHT JOIN ".$Prefix."neuro_Trajet 
ON ".$Prefix."neuro_prise.hash_trajet = ".$Prefix."neuro_Trajet.hash_trajet 
WHERE ".$Prefix."neuro_Trajet.etat='2' 
AND ".$Prefix."neuro_prise.date>:now ORDER BY ".$Prefix."neuro_prise.date ASC");
$SelectValid->bindParam(':now', $Now, PDO::PARAM_STR);
$SelectValid->execute();

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

<H1><font color="green">Vos trajet validés</font></H1>

<div id="TrajetValid">
<table>
<?php while ($Valide=$SelectValid->fetch(PDO::FETCH_OBJ)) { 

$SelectContactValid=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Contact WHERE hash_commande=:hash_commande");
$SelectContactValid->bindParam(':hash_commande', $Valide->hash_commande, PDO::PARAM_STR);
$SelectContactValid->execute(); 
$ContactValid=$SelectContactValid->fetch(PDO::FETCH_OBJ);

$SelectVolAllerValid=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_vol WHERE hash_trajet=:hash_trajet");
$SelectVolAllerValid->bindParam(':hash_trajet', $Valide->hash_trajet, PDO::PARAM_STR);
$SelectVolAllerValid->execute(); 
$VolAllerValid=$SelectVolAllerValid->fetch(PDO::FETCH_OBJ);

$SelectInfoPriseAller=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_prise WHERE hash_trajet=:hash_trajet");
$SelectInfoPriseAller->bindParam(':hash_trajet', $Valide->hash_trajet, PDO::PARAM_STR);
$SelectInfoPriseAller->execute(); 
$InfoPriseAller=$SelectInfoPriseAller->fetch(PDO::FETCH_OBJ);

$SelectChauffeurAller=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_chauffeur WHERE hash_trajet=:hash_trajet");
$SelectChauffeurAller->bindParam(':hash_trajet', $Valide->hash_trajet, PDO::PARAM_STR);
$SelectChauffeurAller->execute(); 
$ChauffeurAller=$SelectChauffeurAller->fetch(PDO::FETCH_OBJ);

$SelectVehiculeAller=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Vehicule WHERE hash_trajet=:hash_trajet");
$SelectVehiculeAller->bindParam(':hash_trajet', $Valide->hash_trajet, PDO::PARAM_STR);
$SelectVehiculeAller->execute(); 
$VehiculeAller=$SelectVehiculeAller->fetch(PDO::FETCH_OBJ);

$SelectChauffeur=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Client WHERE groupe='Livreur'");
$SelectChauffeur->execute();

$SelectVehicule=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Vehicule");
$SelectVehicule->execute(); 

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
        <a class="renseignement" href="<?php echo $Home; ?>/Admin/Boutique/TrajetValider/infoTrajet.php?id=<?php echo $Valide->hash_trajet; ?>">Renseigner</a>
    <?php }
    else { ?>
        <a class="renseignement" href="<?php echo $Home; ?>/Admin/Boutique/TrajetValider/infoTrajet.php?id=<?php echo $Valide->hash_trajet; ?>">Modifier</a>
        <?php
    }
    ?>
    </td>
    </tr>

    <tr>
        <td colspan="3">
            <b>Information de prise en charge</b><BR /><BR />

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
                        
                        req.open("POST", "<?php echo $Home; ?>/Admin/Boutique/TrajetValider/FormConfirmation.php?id=<?php echo $Valide->hash_trajet; ?>", true);
                        req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                        //envoi
                        req.send(data);  
                    }
            </script>
            <div id="CaseConfirmation">
            <b>Modification de trajet</b><BR />

            <form name="form_Modif<?php echo $Valide->hash_trajet; ?>" action="<?php echo $Home; ?>/Admin/Boutique/TrajetValider/confirmation.php?id=<?php echo $Valide->hash_trajet; ?>" method="POST">
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
            
            <td colspan ="2">
            <b>Choix du chauffeur</b><BR />
            <form name="form_livreur<?php echo $Valide->hash_trajet; ?>" action="<?php echo $Home; ?>/Admin/Boutique/TrajetValider/livreur.php?id=<?php echo $Valide->hash_trajet; ?>" method="POST">
            <select name="livreur">
            <option value="NULL" >-- Chauffeur --</option>
            <?php while($Chauffeur=$SelectChauffeur->fetch(PDO::FETCH_OBJ)) { ?> 
                <option value="<?php echo $Chauffeur->hash; ?>" <?php if($ChauffeurAller->hash_client==$Chauffeur->hash) { echo "selected"; } ?>><?php echo $Chauffeur->nom.' '.$Chauffeur->prenom; ?></option>  
            <?php } ?>
            </select>
            <BR />
            <select name="vehicule">
            <option value="NULL" >-- Véhicule --</option>
            <?php while($Vehicule=$SelectVehicule->fetch(PDO::FETCH_OBJ)) { ?> 
                <option value="<?php echo $Vehicule->hash_vehicule; ?>" <?php if($ChauffeurAller->hash_vehicule==$Vehicule->hash_vehicule) { echo "selected"; } ?>><?php echo $Vehicule->libele; ?></option>  
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
    </tr>

    <?php 
} ?>
<tr>
<td colspan="5"><b>Information de contact</b><BR /><BR />
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


</article>
</section>
</div>
</CENTER>
</body>

</html>
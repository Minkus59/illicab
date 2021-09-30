<section>
<div id="Center">
 <?php 
$Erreur=$_GET['erreur'];
$Valid=$_GET['valid'];
$Now=time();

$SelectValid=$cnx->prepare(
"SELECT * FROM ".$Prefix."neuro_chauffeur
 RIGHT JOIN ".$Prefix."neuro_Trajet
 ON ".$Prefix."neuro_chauffeur.hash_trajet = ".$Prefix."neuro_Trajet.hash_trajet 
 RIGHT JOIN ".$Prefix."neuro_prise
 ON ".$Prefix."neuro_chauffeur.hash_trajet = ".$Prefix."neuro_prise.hash_trajet 
 WHERE ".$Prefix."neuro_chauffeur.hash_client = :hash_client 
 AND ".$Prefix."neuro_Trajet.etat='2'
 AND ".$Prefix."neuro_prise.date>:now ORDER BY ".$Prefix."neuro_prise.date ASC
 ");
 
$SelectValid->bindParam(':hash_client', $SessionClient, PDO::PARAM_STR);
$SelectValid->bindParam(':now', $Now, PDO::PARAM_STR);
$SelectValid->execute();
?>
<article>
<div id="Ariane">
<?php
$Chemin=explode("/", $PageActu);
$CountChemin=count($Chemin);
for($l=1;$l!=$CountChemin;$l++) {
    if($l==1) {
        $LienAriane.='<a href="'.$Home.'/'.$Chemin[1].'">'.$Chemin[1].'</a> > ';
    }
    elseif($l==2) {
        $LienAriane.='<a href="'.$Home.'/'.$PageActu.'">'.$Chemin[$l].'</a>';
    }
}
echo "Vous êtes ici : ".$LienAriane."<BR /><BR />";
?>
</div>

<?php
if (isset($Erreur)) { echo "<font color='#FF0000'>".$Erreur."</font></p>"; } 
if (isset($Valid)) { echo "<font color='#095f07'>".$Valid."</font></p>"; } 
?>

<H1><font color="green">Vos Trajets</font></H1>

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

$SelectChauffeur=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_chauffeur WHERE hash_trajet=:hash_trajet");
$SelectChauffeur->bindParam(':hash_trajet', $Valide->hash_trajet, PDO::PARAM_STR);
$SelectChauffeur->execute(); 
$Chauffeur=$SelectChauffeur->fetch(PDO::FETCH_OBJ);

$SelectVehicule=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Vehicule WHERE hash_vehicule=:hash_vehicule");
$SelectVehicule->bindParam(':hash_vehicule', $Chauffeur->hash_vehicule, PDO::PARAM_STR);
$SelectVehicule->execute(); 
$Vehicule=$SelectVehicule->fetch(PDO::FETCH_OBJ);

$MontantCommandeValid=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Trajet WHERE hash_commande=:hash_commande");
$MontantCommandeValid->bindParam(':hash_commande', $Valide->hash_commande, PDO::PARAM_STR);
$MontantCommandeValid->execute(); 
$TotalValid=0;

while ($MontantValid=$MontantCommandeValid->fetch(PDO::FETCH_OBJ)) { 
    $TotalValid+=$MontantValid->Prix;
}
?>

<tr>
<td class="Commande" <?php if (($Valide->type==1)||($TrajetValid->etat!=2)||($Valide->etat!=2)) { echo 'rowspan="4"'; } else { echo 'rowspan="7"'; } ?>>
    <?php if($Valide->pro==1) { ?><img src="<?php echo $Home; ?>/lib/img/fleche.png" /><?php } else { ?><img src="<?php echo $Home; ?>/lib/img/flechePro.png" /><?php } ?><BR /><BR />
    N&deg; de commande<BR /><?php echo $Valide->hash_commande; ?><BR /><BR />
    <div id="Prix"><?php echo $TotalValid." €"; ?></div>
    </td>

<?php if ($Valide->etat==2) { 
    if ($Valide->type2==1) { ?>
        <td><b>Trajet aller</b></td>
    <?php } 
    else { ?>
        <td><b>Trajet retour</b></td>
    <?php } ?>
    <td><b>Num&eacute;ro de trajet</b><br /><?php echo $Valide->hash_trajet; ?></td>
    <td><b>D&eacute;part : </b><?php echo $Valide->Depart; ?><br /><b>Arriver : </b><?php echo $Valide->Arriver; ?></td>
    <td colspan="2"><?php echo $Valide->Distance; ?> (<?php echo $Valide->Temps; ?>)</td>
    </tr>

    <tr>
    <td colspan="5"><b>Information de vol</b><BR /><BR />
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

    <?php 
} ?>
<tr>
<td colspan="3"><b>Information de contact</b><BR /><BR />
<?php 
echo $ContactValid->civilite." ".$ContactValid->nom." ".$ContactValid->prenom."<BR />
     Téléphone : ".$ContactValid->tel;
 ?>
 </td>
    <td colspan="2">
    <b>Type de véhicule</b><BR /><BR />
    <?php echo $Vehicule->libele; ?>
</td>
</tr>

<tr>
<th colspan="6"></th>
</tr>
<?php } ?>

</table>
</div>

</article>

</div>
</section>
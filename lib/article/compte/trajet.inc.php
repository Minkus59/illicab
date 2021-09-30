<section>
<div id="Center">
 <?php 
$Erreur=$_GET['erreur'];
$Valid=$_GET['valid'];

$SelectDemande=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Trajet WHERE client=:client AND etat='1' GROUP BY hash_commande ORDER BY created DESC");
$SelectDemande->bindParam(':client', $SessionClient, PDO::PARAM_STR);
$SelectDemande->execute();

$SelectValid=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Trajet WHERE client=:client AND etat='2' GROUP BY hash_commande ORDER BY created DESC");
$SelectValid->bindParam(':client', $SessionClient, PDO::PARAM_STR);
$SelectValid->execute();

$SelectRefu=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Trajet WHERE client=:client AND etat='3' GROUP BY hash_commande ORDER BY created DESC");
$SelectRefu->bindParam(':client', $SessionClient, PDO::PARAM_STR);
$SelectRefu->execute();
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

<b>Veuillez renseigner les informations de décollage, afin que notre équipe puisse prévoir votre prise en charge dans les meilleures conditions</b><BR /><BR />

<H1><font color="dodgerblue">Vos demandes de disponibilité</font></H1>

<div id="TrajetDemande">
<table>
<?php while ($Demande=$SelectDemande->fetch(PDO::FETCH_OBJ)) { 

$SelectTrajetDemande=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Trajet WHERE client=:client AND hash_commande=:hash_commande AND hash_trajet!=:hash_trajet AND etat='1'");
$SelectTrajetDemande->bindParam(':client', $SessionClient, PDO::PARAM_STR);
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
<td class="Commande" <?php if (($Demande->type==1)||($TrajetDemande->etat!=1)||($Demande->etat!=1)) { echo 'rowspan="4"'; } else { echo 'rowspan="7"'; } ?>>
    <?php if($Demande->pro==1) { ?><img src="<?php echo $Home; ?>/lib/img/fleche.png" /><?php } else { ?><img src="<?php echo $Home; ?>/lib/img/flechePro.png" /><?php } ?><BR /><BR />
    N&deg; de commande<BR /><?php echo $Demande->hash_commande; ?><BR /><BR />
    <div id="Prix"><?php echo $TotalDemande." €"; ?><BR /><BR />Prix / pers<BR /><?php echo round($TotalDemande/$Demande->passager, 2)." €"; ?></div>
    </td>

<?php if ($Demande->etat==1) { 
    if ($Demande->type2==1) { ?>
        <td>
        <b>Trajet aller</b><BR />
        <?php echo $Demande->passager." personnes"; ?>
        </td>
    <?php } 
    else { ?>
        <td>
        <b>Trajet retour</b><BR />
        <?php echo $Demande->passager." personnes"; ?>
        </td>
    <?php } ?>
    <td><b>Num&eacute;ro de trajet</b><br /><?php echo $Demande->hash_trajet; ?></td>
    <td colspan="2"><b>D&eacute;part : </b><?php echo $Demande->Depart; ?><br /><b>Arriver : </b><?php echo $Demande->Arriver; ?></td>
    <td><?php echo $Demande->Distance; ?> (<?php echo $Demande->Temps; ?>)</td>
    </tr>

    <tr>
    <td colspan="4"><b>Information de vol</b><BR /><BR />
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
        <a class="renseignement" href="<?php echo $Home; ?>/Mon-compte/Trajet/?id=<?php echo $Demande->hash_trajet; ?>">Renseigner</a>
    <?php }
    else { ?>
        <a class="renseignement" href="<?php echo $Home; ?>/Mon-compte/Trajet/?id=<?php echo $Demande->hash_trajet; ?>">Modifier</a>
        <?php
    }
    ?>
    </td>
    </tr>

    <tr>
    <td colspan="5">
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

<?php 
}
if ($TrajetDemande->etat==1) { ?>
    <tr>
    <td><b>Trajet retour</b></td>
    <td><b>Num&eacute;ro de trajet</b><br /><?php echo $TrajetDemande->hash_trajet; ?></td>
    <td colspan="2"><b>D&eacute;part : </b><?php echo $TrajetDemande->Depart; ?><br /><b>Arriver : </b><?php echo $TrajetDemande->Arriver; ?></td>
    <td><?php echo $TrajetDemande->Distance; ?> (<?php echo $TrajetDemande->Temps; ?>)</td>
    </tr>

    <tr>
    <td colspan="4"><b>Information de vol</b><BR /><BR />
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
        <a class="renseignement" href="<?php echo $Home; ?>/Mon-compte/Trajet/?id=<?php echo $TrajetDemande->hash_trajet; ?>">Renseigner</a>
    <?php }
    else { ?>
        <a class="renseignement" href="<?php echo $Home; ?>/Mon-compte/Trajet/?id=<?php echo $TrajetDemande->hash_trajet; ?>">Modifier</a>
    <?php
    }
?>
</div>
</td>
</tr>

<tr>
<td colspan="5">
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

<?php } ?>
<tr>
<td colspan="5"><b>Information de contact</b><BR /><BR />
<?php 
echo $ContactDemande->civilite." ".$ContactDemande->nom." ".$ContactDemande->prenom."<BR />
     Téléphone : ".$ContactDemande->tel."<BR />
     Email : ".$ContactDemande->email;
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

<H1><font color="green">Vos Trajets Valider</font></H1>

<div id="TrajetValid">
<table>
<?php while ($Valide=$SelectValid->fetch(PDO::FETCH_OBJ)) { 

$SelectTrajetValid=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Trajet WHERE client=:client AND hash_commande=:hash_commande AND hash_trajet!=:hash_trajet AND etat='2'");
$SelectTrajetValid->bindParam(':client', $SessionClient, PDO::PARAM_STR);
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
        <td>
        <b>Trajet aller</b><BR />
        <?php echo $Valide->passager." personnes"; ?>
        </td>
    <?php } 
    else { ?>
        <td>
        <b>Trajet retour</b><BR />
        <?php echo $Valide->passager." personnes"; ?>
        </td>
    <?php } ?>
    <td><b>Num&eacute;ro de trajet</b><br /><?php echo $Valide->hash_trajet; ?></td>
    <td><b>D&eacute;part : </b><?php echo $Valide->Depart; ?><br /><b>Arriver : </b><?php echo $Valide->Arriver; ?></td>
    <td><?php echo $Valide->Distance; ?> (<?php echo $Valide->Temps; ?>)</td>
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
    <td>
    <?php
    if ((empty($VolAllerValid->numero))||(empty($VolAllerValid->date))) {
        ?>
        <a class="renseignement" href="<?php echo $Home; ?>/Mon-compte/Trajet/?id=<?php echo $Valide->hash_trajet; ?>">Renseigner</a>
    <?php }
    else { ?>
        <a class="renseignement" href="<?php echo $Home; ?>/Mon-compte/Trajet/?id=<?php echo $Valide->hash_trajet; ?>">Modifier</a>
        <?php
    }
    ?>
    </td>
    </tr>

    <tr>
    <td colspan="3">
        Vous pouvez à tout moment créer une réclamation, elle sera traité dans les meilleurs délais
    </td>

    <td>
        <a class="reclamation" href="<?php echo $Home; ?>/Mon-compte/Reclamation/Nouveau/?id=<?php echo $TrajetValid->hash_trajet; ?>">Réclamation</a>
    </td>
    </tr>

    <tr>
    <td colspan="3">
        <b>Information de prise en charge Aller</b><BR /><BR />
        <?php
            echo "Date de prise en charge : <b>".date("d", $InfoPriseAller->date)." / ".date("m", $InfoPriseAller->date)." / ".date("Y", $InfoPriseAller->date)."</b><BR />";
            echo "Heure de prise en charge : <b>".date("H", $InfoPriseAller->date)."h ".date("i", $InfoPriseAller->date)."min</b>";
        ?>
    </td>
    <td colspan="1">
        <b>Information complémentaire</b><BR /><BR />
       <?php echo $InfoPriseAller->commentaire; ?>
    </td>
    </tr>

    <?php 
}
if ($TrajetValid->etat==2) { ?>
    <tr>
    <td><b>Trajet retour</b></td>
    <td><b>Num&eacute;ro de trajet</b><br /><?php echo $TrajetValid->hash_trajet; ?></td>
    <td><b>D&eacute;part : </b><?php echo $TrajetValid->Depart; ?><br /><b>Arriver : </b><?php echo $TrajetValid->Arriver; ?></td>
    <td><?php echo $TrajetValid->Distance; ?> (<?php echo $TrajetValid->Temps; ?>)</td>
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
    <td>
    <?php
    if ((empty($VolRetourValid->numero))||(empty($VolRetourValid->date))) {
        ?>
        <a class="renseignement" href="<?php echo $Home; ?>/Mon-compte/Trajet/?id=<?php echo $TrajetValid->hash_trajet; ?>">Renseigner</a>
    <?php }
    else { ?>
        <a class="renseignement" href="<?php echo $Home; ?>/Mon-compte/Trajet/?id=<?php echo $TrajetValid->hash_trajet; ?>">Modifier</a>
        <?php
    }
    ?>
    </div>
    </td>
    </tr>

    <tr>
    <td colspan="3">
        Vous pouvez à tout moment créer une réclamation, elle sera traité dans les meilleurs délais
    </td>

    <td>
        <a class="reclamation" href="<?php echo $Home; ?>/Mon-compte/Reclamation/Nouveau/?id=<?php echo $TrajetValid->hash_trajet; ?>">Réclamation</a>
    </td>
    </tr>

    <tr>
    <td colspan="3">
        <b>Information de prise en charge Retour</b><BR /><BR />
        <?php
            echo "Date de prise en charge : <b>".date("d", $InfoPriseRetour->date)." / ".date("m", $InfoPriseRetour->date)." / ".date("Y", $InfoPriseRetour->date)."</b><BR />";
            echo "Heure de prise en charge : <b>".date("H", $InfoPriseRetour->date)."h ".date("i", $InfoPriseRetour->date)."min</b>";
        ?>
    </td>
    <td colspan="1">
        <b>Information complémentaire</b><BR /><BR />
       <?php echo $InfoPriseRetour->commentaire; ?>
    </td>
    </tr>

<?php } ?>
<tr>
<td colspan="4"><b>Information de contact</b><BR /><BR />
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

<H1><font color="crimson">Vos Trajets refuser</font></H1>

<div id="TrajetRefu">
<table>
<?php while ($Refu=$SelectRefu->fetch(PDO::FETCH_OBJ)) { 

$SelectTrajetRefu=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Trajet WHERE client=:client AND hash_commande=:hash_commande AND hash_trajet!=:hash_trajet AND etat='3'");
$SelectTrajetRefu->bindParam(':client', $SessionClient, PDO::PARAM_STR);
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
        <td>
        <b>Trajet aller</b><BR />
        <?php echo $Refu->passager." personnes"; ?>
        </td>
    <?php } 
    else { ?>
        <td>
        <b>Trajet retour</b><BR />
        <?php echo $Refu->passager." personnes"; ?>
        </td>
    <?php } ?>
    <td><b>Num&eacute;ro de trajet</b><br /><?php echo $Refu->hash_trajet; ?></td>
    <td><b>D&eacute;part : </b><?php echo $Refu->Depart; ?><br /><b>Arriver : </b><?php echo $Refu->Arriver; ?></td>
    <td><?php echo $Refu->Distance; ?> (<?php echo $Refu->Temps; ?>)</td>
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
    </td>
    <td colspan="1"><b>Information complémentaire</b><BR /><BR />

        <?php echo $MotifRefuAller->motif; ?>
    </td>
    </tr>

    <tr>
    <td colspan="3">
        Vous pouvez à tout moment créer une réclamation, elle sera traité dans les meilleurs délais
    </td>
    <td>
        <a class="reclamation" href="<?php echo $Home; ?>/Mon-compte/Reclamation/Nouveau/?id=<?php echo $Refu->hash_trajet; ?>">Réclamation</a>
    </td>
    </tr>
    <?php 
}
if ($TrajetRefu->etat==3) { ?>
    <tr>
    <td><b>Trajet retour</b></td>
    <td><b>Num&eacute;ro de trajet</b><br /><?php echo $TrajetRefu->hash_trajet; ?></td>
    <td><b>D&eacute;part : </b><?php echo $TrajetRefu->Depart; ?><br /><b>Arriver : </b><?php echo $TrajetRefu->Arriver; ?></td>
    <td><?php echo $TrajetRefu->Distance; ?> (<?php echo $TrajetRefu->Temps; ?>)</td>
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
    <td colspan="1"><b>Information complémentaire</b><BR />

        <?php echo $MotifRefuRetour->motif; ?>
    </td>
    </tr>

    <tr>
    <td colspan="3">
        Vous pouvez à tout moment créer une réclamation, elle sera traité dans les meilleurs délais
    </td>
    <td>
        <a class="reclamation" href="<?php echo $Home; ?>/Mon-compte/Reclamation/Nouveau/?id=<?php echo $CommandeRefu->hash_trajet; ?>">Réclamation</a>
    </td>
    </tr>

<?php } ?>
<tr>
<td colspan="4"><b>Information de contact</b><BR /><BR />
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

</div>
</section>
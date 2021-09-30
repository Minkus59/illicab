<section>
<div id="Center">
 <?php 
$Erreur=$_GET['erreur'];
$Valid=$_GET['valid'];
$Id=$_GET['id'];
$Mode=$_GET['mode'];

$SelectTrajet=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_vol WHERE hash_trajet=:hash_trajet");
$SelectTrajet->bindParam(':hash_trajet', $Id, PDO::PARAM_STR);
$SelectTrajet->execute();
$Trajet=$SelectTrajet->fetch(PDO::FETCH_OBJ);

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
echo "Vous êtes ici : ".$LienAriane."<BR />";
?>
</div>

<?php
if (isset($Erreur)) { echo "<font color='#FF0000'>".$Erreur."</font></p>"; } 
if (isset($Valid)) { echo "<font color='#095f07'>".$Valid."</font></p>"; } 
?>

<H1><font color="dodgerblue">Information de vol</font></H1>

<b>Merci de remplir avec exactitude toutes les informations concernant le vol</b><BR /><BR />


<form action="<?php echo $Home; ?>/lib/script/vente/infoTrajet.php?id=<?php echo $Id; ?>&mode=<?php echo $Mode; ?>" method="POST">

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

</div>
</section>
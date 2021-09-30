<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");  
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/log.inc.php"); 

$Confirmation=$_POST['confirmation'];
$Id=$_GET['id'];

$RecupTrajet=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Trajet WHERE hash_trajet=:hash_trajet");
$RecupTrajet->BindParam(':hash_trajet', $Id, PDO::PARAM_STR);
$RecupTrajet->execute();
$Trajet=$RecupTrajet->fetch(PDO::FETCH_OBJ);

$RecupPrise=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_prise WHERE hash_trajet=:hash_trajet");
$RecupPrise->BindParam(':hash_trajet', $Id, PDO::PARAM_STR);
$RecupPrise->execute();
$Prise=$RecupPrise->fetch(PDO::FETCH_OBJ);

$RecupRefu=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_refu WHERE hash_trajet=:hash_trajet");
$RecupRefu->BindParam(':hash_trajet', $Id, PDO::PARAM_STR);
$RecupRefu->execute();
$Refu=$RecupRefu->fetch(PDO::FETCH_OBJ);

$RecupVol=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_vol WHERE hash_trajet=:hash_trajet");
$RecupVol->BindParam(':hash_trajet', $Id, PDO::PARAM_STR);
$RecupVol->execute();
$Vol=$RecupVol->fetch(PDO::FETCH_OBJ);
$CountVol=$RecupVol->rowCount();

if ($Confirmation==3) { ?>
    <textarea name="infoRefu" placeholder="Veuillez indiquer le motif du refus" rows="8" cols="60"><?php if(isset($Refu->motif)) { echo $Refu->motif; } ?></textarea>
    <BR /><BR />
    <input type="submit" name="Confirmer" value="Confirmer"/>
<?php 
}

if ($Confirmation==2) { 
    if ($Trajet->type2==1) { 
        echo "<H2>Vol <span>aller</span></H2>";
    }
    else {
        echo "<H2>Vol <span>retour</span></H2>";
    }
    ?>

    <span class="col_1" for="jour">Date de prise en charge : </span>
    <select name="jour" class="SelectMini">
        <option value="NULL">--</option>
        <?php for($j=1;$j!=32;$j++) { ?>
            <option value="<?php echo sprintf("%'.02d\n", $j); ?>" <?php if(($CountVol==1)&&(date("d", $Vol->date)==$j)) { echo "selected"; } ?>><?php echo sprintf("%'.02d\n", $j); ?></option>
    <?php } ?>
    </select>
    <select name="mois" class="SelectMini">
        <option value="NULL">--</option>
        <?php for($mo=1;$mo!=13;$mo++) { ?>
            <option value="<?php echo sprintf("%'.02d\n", $mo); ?>" <?php if(($CountVol==1)&&(date("m", $Vol->date)==$mo)) { echo "selected"; } ?>><?php echo sprintf("%'.02d\n", $mo); ?></option>
    <?php } ?>
    </select>
    <select name="annee" class="SelectMini">
        <option value="NULL">--</option>
        <?php for($a=2016;$a!=2027;$a++) { ?>
            <option value="<?php echo $a; ?>" <?php if(($CountVol==1)&&(date("Y", $Vol->date)==$a)) { echo "selected"; } ?>><?php echo $a; ?></option>
    <?php } ?>
    </select><BR /><BR />

    <span class="col_1" for="heure">Heure de prise en charge : </span>
    <select name="heure" class="SelectMini">
        <option value="NULL">--</option>
        <?php for($h=0;$h!=24;$h++) { ?>
            <option value="<?php echo sprintf("%'.02d\n", $h); ?>" <?php if(($CountVol==1)&&(date("H", $Vol->date)==$h)) { echo "selected"; } ?>><?php echo sprintf("%'.02d\n", $h); ?></option>
    <?php } ?>
    </select>
    <select name="min" class="SelectMini">
        <option value="NULL">--</option>
        <?php for($m=0;$m!=60;$m++) { ?>
            <option value="<?php echo sprintf("%'.02d\n", $m); ?>" <?php if(($CountVol==1)&&(date("i", $Vol->date)==$m)) { echo "selected"; } ?>><?php echo sprintf("%'.02d\n", $m); ?></option>
    <?php } ?>
    </select><BR /><BR />

    <textarea name="commentaire" placeholder="Commentaire" rows="8" cols="60"><?php if(isset($Prise->commentaire)) { echo $Prise->commentaire; } ?></textarea><BR /><BR />

    <input type="submit" name="Confirmer" value="Confirmer"/>
<?php } 
?>
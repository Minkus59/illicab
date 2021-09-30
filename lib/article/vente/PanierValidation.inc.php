<?php 
$Erreur=$_GET['erreur'];
$Valid=$_GET['valid'];

$ClientInfo=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Client WHERE hash=:hash");
$ClientInfo->BindParam(':hash', $SessionClient, PDO::PARAM_STR);
$ClientInfo->execute();
$Info=$ClientInfo->fetch(PDO::FETCH_OBJ);

if ($_SESSION['panierTrajet']==1) {
    $SelectTauxDesti=$cnx->prepare('SELECT * FROM '.$Prefix.'neuro_Destination WHERE libele=:libele');
    $SelectTauxDesti->BindParam(':libele', $_SESSION['destination'], PDO::PARAM_STR);
    $SelectTauxDesti->execute();
    $TauxDesti=$SelectTauxDesti->fetch(PDO::FETCH_OBJ);

    $SelectTauxPers=$cnx->prepare('SELECT * FROM '.$Prefix.'neuro_Personne WHERE quantite=:quantite');
    $SelectTauxPers->BindParam(':quantite', $_SESSION['passager'], PDO::PARAM_STR);
    $SelectTauxPers->execute();
    $TauxPers=$SelectTauxPers->fetch(PDO::FETCH_OBJ);
}
else {
    $SelectTauxDesti=$cnx->prepare('SELECT * FROM '.$Prefix.'neuro_DestinationPro WHERE libele=:libele');
    $SelectTauxDesti->BindParam(':libele', $_SESSION['destinationPro'], PDO::PARAM_STR);
    $SelectTauxDesti->execute();
    $TauxDesti=$SelectTauxDesti->fetch(PDO::FETCH_OBJ);

    $SelectTauxPers=$cnx->prepare('SELECT * FROM '.$Prefix.'neuro_Personne WHERE quantite=:quantite');
    $SelectTauxPers->BindParam(':quantite', $_SESSION['passagerPro'], PDO::PARAM_STR);
    $SelectTauxPers->execute();
    $TauxPers=$SelectTauxPers->fetch(PDO::FETCH_OBJ);
}
    
?>
<section>
<div id="Center">

<div id="bouton">
<div id="Cols4" class="boutonAfter">
<a href="<?php echo $Home; ?>/Reservation/" >Trajet</a>
</div>
<div id="Cols4" class="boutonAfter">
<a href="<?php echo $Home; ?>/Panier/Identification/">Identification</a>
</div>  
<div id="Cols4" class="boutonUp">
Validation
</div>
<div id="Cols4" class="boutonDown">
Confirmation
</div>
<div id="Cols4" class="boutonDown">
Paiement
</div>
</div>

<article id="Panier">
<?php 
if (isset($Erreur)) { echo "<font color='#FF0000'>".$Erreur."</font></p>"; } 
if (isset($Valid)) { echo "<font color='#095f07'>".$Valid."</font></p>"; }

//Ajouter l'adresse de livraison
?>
<H1>Validation de la commande N°: <?php echo $_SESSION['hash_commande']; ?></H1>

<H3>Vos informations de contact</H3>

<form action="<?php echo $Home; ?>/lib/script/vente/validation.php" method="POST">

<label class='col_1'>Adresse E-mail<font color='#FF0000'>*</font> :</label>
<input type="email" name="email" value="<?php echo $Info->email; ?>" required/> 
<br />

<label class="col_1" for="type">Civilité<font color='#FF0000'>*</font> :</label>
<select name="civilite" required>
<option value="" >--</option>
<option value="Mr" <?php if ($Info->titre=="Mr") { echo "selected"; } ?>>Mr</option>
<option value="Mme" <?php if ($Info->titre=="Mme") { echo "selected"; } ?>>Mme</option>
<option value="Mlle" <?php if ($Info->titre=="Mlle") { echo "selected"; } ?>>Mlle</option>
</select>
<br />

<label class='col_1'>Nom<font color='#FF0000'>*</font> :</label>
<input type="text" name="nom" value="<?php echo $Info->nom; ?>" required/> 
<br />

<label class="col_1">Prénom :</label>
<input type="text" name="prenom" value="<?php echo $Info->prenom; ?>"/>  
<br />

<label class='col_1'>Numéro de téléphone<font color='#FF0000'>*</font> :</label>
<input type="text" name="tel" value="<?php echo $Info->telephone; ?>" required/>   
<br /><br />

<div id="TrajetListe">
<H1><span>Trajet</span> aller</H1>

<?php
//MODULE CALCUL DE TRAJET

if ($_SESSION['panierTrajet']==1) {
    $Result=DistanceMatrix($_SESSION['depart'], $_SESSION['destination'], $KeyGoogleAPI, $TauxDesti->prix, $TauxPers->taux, $_SESSION['depart'], $_SESSION['destination']);
    echo "Nombre de passager : <b>".$_SESSION['passager']."</b><BR /><BR />";

    echo "De : <b>".$Result[0]."</b><BR />";
    echo "Vers : <b>".$Result[1]."</b><BR /><BR />";

    echo "Distance moyenne : <b>".$Result[2]."</b><BR />";
    echo "Temps de trajet moyen : <b>".$Result[3]."</b><BR /><BR />";
    echo "</div>";

    if ($_SESSION['trajet']==2) {
        $Result2=DistanceMatrix($_SESSION['destinationRetour'], $_SESSION['depart'], $KeyGoogleAPI, $TauxDesti->prix, $TauxPers->taux, $_SESSION['destinationRetour'], $_SESSION['depart']);
        echo "<div id='TrajetListe'>";
        echo "<H1><span>Trajet</span> retour</H1>";

        echo "De : <b>".$Result2[0]."</b><BR />";
        echo "Vers <b>: ".$Result2[1]."</b><BR /><BR />";

        echo "Distance moyenne : <b>".$Result2[2]."</b><BR />";
        echo "Temps de trajet moyen : <b>".$Result2[3]."</b><BR /><BR />";
        echo "</div>";
    }
    
    echo "<div id='Total'>";
    echo "Total : ";
    echo number_format($Result[4]+$Result2[4], 2,".", "")." € TTC";
    echo "</div>";
    ?> 
    <BR />
    <font color='#FF0000'>Veullez vérifier toutes les informations avant de valider votre commande</font>

    <p><input type="checkbox" name="valid1" required="required"/> J'ai vérifié l'exactitude des données ci-dessus</a><font color='#FF0000'>*</font> </p>
    <p><input type="checkbox" name="valid2" required="required"/> J'accepte les <a href="<?php echo $Home; ?>/Mentions-legales/" target="_blank">conditions d'utilisation</a><font color='#FF0000'>*</font> </p>

    <font color='#FF0000'>*</font> : Informations requises

    <div id="bouton">
    <div id="Cols3">

    <input type="hidden" name="DistanceAller" value="<?php echo $Result[2]; ?>"/>
    <input type="hidden" name="TempsAller" value="<?php echo $Result[3]; ?>"/>
    <input type="hidden" name="PrixAller" value="<?php echo $Result[4]; ?>"/>
    <?php if ($_SESSION['trajet']==2) { ?>
        <input type="hidden" name="DistanceRetour" value="<?php echo $Result2[2]; ?>"/>
        <input type="hidden" name="TempsRetour" value="<?php echo $Result2[3]; ?>"/>
        <input type="hidden" name="PrixRetour" value="<?php echo $Result2[4]; ?>"/>
    <?php } ?>

    <input class="Panier" type="submit" name="Valider" value="Valider ma demande"/>
    </form>
    </div>
    </div>

<?php } 
else { 
    $Result=DistanceMatrix($_SESSION['departPro'], $_SESSION['destinationPro'], $KeyGoogleAPI, $TauxDesti->prix, $TauxPers->taux, $_SESSION['departPro'], $_SESSION['destinationPro']);
    echo "<div id='CaseGauche'>";
    echo "Nombre de passager : <b>".$_SESSION['passagerPro']."</b><BR /><BR />";

    echo "De : <b>".$Result[0]."</b><BR />";
    echo "Vers : <b>".$Result[1]."</b><BR /><BR />";

    echo "Distance moyenne : <b>".$Result[2]."</b><BR />";
    echo "Temps de trajet moyen : <b>".$Result[3]."</b><BR /><BR />";
    echo "</div>
    <div id='CaseDroite'>";
    echo number_format($Result[4], 2,".", "")." €";
    echo "</div></div>";

    if ($_SESSION['trajetPro']==2) {
        $Result2=DistanceMatrix($_SESSION['destinationRetourPro'], $_SESSION['departPro'], $KeyGoogleAPI, $TauxDesti->prix, $TauxPers->taux, $_SESSION['destinationRetourPro'], $_SESSION['departPro']);
        echo "<div id='TrajetListe'>";
        echo "<H1><span>Trajet</span> retour</H1>";

        echo "<div id='CaseGauche'>";
        echo "De : <b>".$Result2[0]."</b><BR />";
        echo "Vers <b>: ".$Result2[1]."</b><BR /><BR />";

        echo "Distance moyenne : <b>".$Result2[2]."</b><BR />";
        echo "Temps de trajet moyen : <b>".$Result2[3]."</b><BR /><BR />";
        echo "</div>
        <div id='CaseDroite'>";
        echo number_format($Result2[4], 2,".", "")." €";
        echo "</div></div>";

        echo "<div id='PrixTotal'>";
        echo "<div id='CaseGauche'>";
        echo "<H1><span>Total</span> (aller / retour)</H1>";
        echo "</div>
        <div id='CaseDroite'>";
        echo number_format($Result[4]+$Result2[4], 2,".", "")." € TTC";
        echo "</div></div>";
    }
    ?> 
    <BR /> <BR />
    <font color='#FF0000'>Veullez vérifier toutes les informations avant de valider votre commande</font>

    <p><input type="checkbox" name="valid1" required="required"/> J'ai vérifié l'exactitude des données ci-dessus</a><font color='#FF0000'>*</font> </p>
    <p><input type="checkbox" name="valid2" required="required"/> J'accepte les <a href="<?php echo $Home; ?>/Mentions-legales/" target="_blank">conditions d'utilisation</a><font color='#FF0000'>*</font> </p>

    <font color='#FF0000'>*</font> : Informations requises

    <div id="bouton">
    <div id="Cols3">

    <input type="hidden" name="DistanceAller" value="<?php echo $Result[2]; ?>"/>
    <input type="hidden" name="TempsAller" value="<?php echo $Result[3]; ?>"/>
    <input type="hidden" name="PrixAller" value="<?php echo $Result[4]; ?>"/>
    <?php if (($_SESSION['trajet']==2)||($_SESSION['trajetPro']==2)) { ?>
        <input type="hidden" name="DistanceRetour" value="<?php echo $Result2[2]; ?>"/>
        <input type="hidden" name="TempsRetour" value="<?php echo $Result2[3]; ?>"/>
        <input type="hidden" name="PrixRetour" value="<?php echo $Result2[4]; ?>"/>
    <?php } ?>

    <input class="Panier" type="submit" name="Valider" value="Valider ma demande"/>
    </form>
    </div>
    </div>
<?php } ?>
</article>

</div>
</section>

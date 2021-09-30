<?php
$Annee=date('Y', time());
$AnneeSup=$Annee+4;
$mois=array('1' => 'Janvier', '2' => 'Fevrier', '3' => 'Mars', '4' => 'Avril', '5' => 'Mai', '6' => 'Juin', '7' => 'Juillet', '8' => 'Aout', '9' => 'Septembre', '10' => 'Octobre', '11' => 'Novembre', '12' => 'Décembre');
?>
<section>
<div id="Center">

<div id="Module">
<div id="Baniere">
<img class="plan1" src='<?php echo $Home; ?>/lib/img/Anime/plan1.png'/>
<img class="plan2" src='<?php echo $Home; ?>/lib/img/Anime/plan2.png'/>
<img class="plan3" src='<?php echo $Home; ?>/lib/img/Anime/plan3.png'/>
<img class="ArrierePlan" src='<?php echo $Home; ?>/lib/img/Anime/ArrierePlan.png'/>
</div>

<div id="Gauche">
<?php
$Erreur=$_GET['erreur'];
$Valid=$_GET['valid'];
if (isset($Erreur)) { echo "<font color='#FF0000'>".$Erreur."</font></p>"; } 
if (isset($Valid)) { echo "<font color='#095f07'>".$Valid."</font></p>"; } 
?>

<h1>Demande de <span>disponibilité</span></h1>

<form method="POST" id="Reservation" name="Reservation" action="<?php echo $Home; ?>/Reservation/">
    <div id="Groupe">
        Départ<BR />
        <input id="autocomplete" class="Module" type="text" name="depart" onFocus="geolocate()" value="<?php if (isset($_SESSION['depart'])) { echo $_SESSION['depart']; } ?>" required/>
    </div>

    <div id="Groupe">
        Destination<BR />
        <select class="Module" name="destination" required>
            <?php 
            $SelectDestination=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Destination");
            $SelectDestination->execute();

            echo '<option value="">-- Indiquez une destination --</option>';
            while ($destination=$SelectDestination->fetch(PDO::FETCH_OBJ)) { ?>
                <option value="<?php echo $destination->libele; ?>" <?php if ((isset($_SESSION['destination']))&&($_SESSION['destination']==$destination->libele)) { echo "selected"; } ?> ><?php echo $destination->libele; ?></option>
            <?php } ?>
        </select>
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
            <option value="1" >Aller Simple</option>
            <option value="2" >Aller / retour</option>
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

</div>

<div id="Droite">
<H1>Comment réserver <span>un trajet ?</span></H1>

Saisissez les informations de départ et d'arrivée. <br />
puis cliquez sur le bouton "<b>Calculer</b>".<br /><br />

Un aperçu de votre trajet ainsi que le prix vous sera communiqué,<br />
inscrivez-vous, puis confirmez votre trajet.<br /><br />

L'équipe Illicab vous enverra un e-mail de confirmation suite à votre demande de disponibilité.<br /><br />

Vous avez besoin d'<b>un trajet différent</b> ou <b>une demande spécifique</b> ?<br /><BR />
<u><b>Un seul numéro : </b></u><BR />
<img src='<?php echo $Home; ?>/lib/img/tel.png'/> <b><?php echo $Telephone; ?></b><BR />
<img src='<?php echo $Home; ?>/lib/img/mail.png'/> <?php echo $Destinataire; ?> <BR /><BR /><BR />
</div>

</div>

<?php
if ($Count>0) {

    while($Actu=$RecupArticle->fetch(PDO::FETCH_OBJ)) { 

        echo '
        <article>';

        echo $Actu->message;
        if (($Cnx_Admin==true)||($Cnx_Client==true)) { 
            echo '<a href="'.$Home.'/Admin/Article/Nouveau/?id='.$Actu->id.'"><img src="'.$Home.'/Admin/lib/img/modifier.png"></a><a href="'.$Home.'/Admin/Article/supprimer.php?id='.$Actu->id.'"><img src="'.$Home.'/Admin/lib/img/supprimer.png"></a>';
        } 
        echo '</article>';
    }
}
?>

<script>
function ChoixRetour() {
var select = document.getElementById('trajet');
var valeur = select.options[select.selectedIndex].value;
var divRetour = document.getElementById('Retour');

    if(valeur==2) {
        divRetour.style.height='130px';
    }
    if (valeur==1) {
        divRetour.style.height='0px';
    }
    if (valeur=="") {
        divRetour.style.height='0px';
    }
}
</script>

<script>
var autocomplete;

function initMap() {
  var input = document.getElementById('autocomplete');
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

  autocomplete.addListener('place_changed', fillInAddress);
}

function fillInAddress() {
  var place = autocomplete.getPlace();
}
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAsFH7oT4bv7FEqJ_A6QDVnb53GdFJ0pRM&signed_in=true&libraries=places&callback=initMap" async defer></script>
</div>

</section>

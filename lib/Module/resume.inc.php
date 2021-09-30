<?php
if (isset($_POST['Reserver'])) { 
    $Code_commande = md5(uniqid(rand(), true));
    $Hash_commande=substr($Code_commande, 0, 8);
    if (!isset($_SESSION['hash_commande'])) {
      $_SESSION['hash_commande']=$Hash_commande;
    }

    $_SESSION['depart']=$_POST['depart'];
    $_SESSION['destination']=$_POST['destination'];
    $_SESSION['passager']=$_POST['passager'];
    $_SESSION['trajet']=$_POST['trajet'];
    $_SESSION['destinationRetour']=$_POST['destinationRetour'];
    $_SESSION['panierTrajet']=1;
}
else {
  if (!isset($_SESSION['panierTrajet'])) {
    $Erreur="Veuillez remplir votre trajet avant de continuer !";
    header("location:".$Home."/?erreur=".urlencode($Erreur));
  }
}

$SelectTauxDesti=$cnx->prepare('SELECT * FROM '.$Prefix.'neuro_Destination WHERE libele=:libele');
$SelectTauxDesti->BindParam(':libele', $_SESSION['destination'], PDO::PARAM_STR);
$SelectTauxDesti->execute();
$TauxDesti=$SelectTauxDesti->fetch(PDO::FETCH_OBJ);

$SelectTauxPers=$cnx->prepare('SELECT * FROM '.$Prefix.'neuro_Personne WHERE quantite=:quantite');
$SelectTauxPers->BindParam(':quantite', $_SESSION['passager'], PDO::PARAM_STR);
$SelectTauxPers->execute();
$TauxPers=$SelectTauxPers->fetch(PDO::FETCH_OBJ);    

?>

<section>
<div id="Center">

<div id="bouton">
<div id="Cols4" class="boutonUp">
Trajet
</div>
<div id="Cols4" class="boutonDown">
Identification
</div>
<div id="Cols4" class="boutonDown">
Validation
</div>
<div id="Cols4" class="boutonDown">
Confirmation
</div>
<div id="Cols4" class="boutonDown">
Paiement
</div>
</div>

<?php
// MODULE ARTICLE
if ($Count>0) {

    while($Actu=$RecupArticle->fetch(PDO::FETCH_OBJ)) { 

        echo '<article>';

        echo $Actu->message;
        if (($Cnx_Admin==true)||($Cnx_Client==true)) { 
            echo '<a href="'.$Home.'/Admin/Article/Nouveau/?id='.$Actu->id.'"><img src="'.$Home.'/Admin/lib/img/modifier.png"></a><a href="'.$Home.'/Admin/Article/supprimer.php?id='.$Actu->id.'"><img src="'.$Home.'/Admin/lib/img/supprimer.png"></a>';
        } 
        echo '</article>';
    }
}
?>

<article>
<div id="Gauche" class="module">
<div id="Trajet">
<H1><span>Trajet</span> aller</H1>

<?php
//MODULE CALCUL DE TRAJET
$Result=DistanceMatrix($_SESSION['depart'], $_SESSION['destination'], $KeyGoogleAPI, $TauxDesti->prix, $TauxPers->taux, $_SESSION['depart'], $_SESSION['destination']);

echo "Nombre de passager : <b>".$_SESSION['passager']."</b><BR /><BR />";

echo "De : <b>".$Result[0]."</b><BR />";
echo "Vers : <b>".$Result[1]."</b><BR /><BR />";

echo "Distance moyenne : <b>".$Result[2]."</b><BR />";
echo "Temps de trajet moyen : <b>".$Result[3]."</b><BR /><BR />";
echo "</div>";

if ($_SESSION['trajet']==2) {
    echo "<div id='Trajet'>";
    echo "<H1><span>Trajet</span> retour</H1>";

    $Result2=DistanceMatrix($_SESSION['destinationRetour'], $_SESSION['depart'], $KeyGoogleAPI, $TauxDesti->prix, $TauxPers->taux, $_SESSION['destinationRetour'], $_SESSION['depart']);

    echo "De : <b>".$Result2[0]."</b><BR />";
    echo "Vers <b>: ".$Result2[1]."</b><BR /><BR />";

    echo "Distance moyenne : <b>".$Result2[2]."</b><BR />";
    echo "Temps de trajet moyen : <b>".$Result2[3]."</b><BR /><BR />";
    echo "</div>";
}

echo "<div id='Total'>";
echo "Total : ";
echo number_format($Result[4]+$Result2[4], 2,".", "")." €";
echo "</div>";

?> 
</div>

<div id="Droite" class="module">
<?php
//MODULE MINI MAP
$Depart=Geolocat($_SESSION['depart'], $KeyGoogleAPI);
$Destination=Geolocat($_SESSION['destination'], $KeyGoogleAPI);

if ($_SESSION['trajet']==2) {
    $DepartRetour=Geolocat($_SESSION['destinationRetour'], $KeyGoogleAPI);
    $DestinationRetour=Geolocat($_SESSION['depart'], $KeyGoogleAPI);
}
?>
<div id="map"></div>

<script>
function initMap() {
    var Depart = {lat: <?php echo $Depart[0]; ?>, lng: <?php echo $Depart[1]; ?>};
    var Destination = {lat: <?php echo $Destination[0]; ?>, lng: <?php echo $Destination[1]; ?>};

    var map = new google.maps.Map(document.getElementById('map'), {
        center: Destination,
        scrollwheel: true,
        zoom: 13
    });
    calculate(map);
    setMarkersDepart(map);
    setMarkersDestination(map);
    setMarkersDepartRetour(map);
}

function calculate(map) {
    origin      = {lat: <?php echo $Depart[0]; ?>, lng: <?php echo $Depart[1]; ?>}; // Le point départ
    destination = {lat: <?php echo $Destination[0]; ?>, lng: <?php echo $Destination[1]; ?>}; // Le point d'arrivé

    if(origin && destination){
        var directionsDisplay = new google.maps.DirectionsRenderer({
          suppressMarkers : true,
          map: map
        });

        var request = {
            origin      : origin,
            destination : destination,
            travelMode  : 'DRIVING' // Type de transport
        };

        var directionsService = new google.maps.DirectionsService(); // Service de calcul d'itinéraire

        directionsService.route(request, function(response, status){ // Envoie de la requéte pour calculer le parcours
            if(status == google.maps.DirectionsStatus.OK){
                directionsDisplay.setDirections(response); // Trace l'itinéraire sur la carte et les différentes étapes du parcours
            }
        });
    }  
}

function setMarkersDepart(map) {
    var beachesDepart = [
    ['Depart', <?php echo $Depart[0]; ?>, <?php echo $Depart[1]; ?>, 1],
    ];

  var image = {
    url: '/lib/img/depart.png',
    size: new google.maps.Size(50, 50),
    origin: new google.maps.Point(0, 0),
    anchor: new google.maps.Point(25, 50)
  };
  var shape = {
    coords: [1, 1, 1, 50, 48, 50, 48, 1],
    type: 'poly'
  };
  for (var i = 0; i < beachesDepart.length; i++) {
    var beach = beachesDepart[i];
    var marker = new google.maps.Marker({
      position: {lat: beach[1], lng: beach[2]},
      map: map,
      icon: image,
      shape: shape,
      title: beach[0],
      zIndex: beach[3]
    });
  }
}

function setMarkersDestination(map) {
    var beachesDestination = [
    ['Destination', <?php echo $Destination[0]; ?>, <?php echo $Destination[1]; ?>, 1]
    ];

  var image = {
    url: '/lib/img/Arrive.png',
    size: new google.maps.Size(50, 50),
    origin: new google.maps.Point(0, 0),
    anchor: new google.maps.Point(25, 50)
  };
  var shape = {
    coords: [1, 1, 1, 50, 48, 50, 48, 1],
    type: 'poly'
  };
  for (var i = 0; i < beachesDestination.length; i++) {
    var beach = beachesDestination[i];
    var marker = new google.maps.Marker({
      position: {lat: beach[1], lng: beach[2]},
      map: map,
      icon: image,
      shape: shape,
      title: beach[0],
      zIndex: beach[3]
    });
  }
}

function setMarkersDepartRetour(map) {
    var beachesDestination = [
    ['Arriver', <?php echo $DepartRetour[0]; ?>, <?php echo $DepartRetour[1]; ?>, 2]
    ];

  var image = {
    url: '/lib/img/departRetour.png',
    size: new google.maps.Size(50, 50),
    origin: new google.maps.Point(0, 0),
    anchor: new google.maps.Point(25, 50)
  };
  var shape = {
    coords: [1, 1, 1, 50, 48, 50, 48, 1],
    type: 'poly'
  };
  for (var i = 0; i < beachesDestination.length; i++) {
    var beach = beachesDestination[i];
    var marker = new google.maps.Marker({
      position: {lat: beach[1], lng: beach[2]},
      map: map,
      icon: image,
      shape: shape,
      title: beach[0],
      zIndex: beach[3]
    });
  }
}
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAsFH7oT4bv7FEqJ_A6QDVnb53GdFJ0pRM&signed_in=true&libraries=places&callback=initMap" async defer></script>
</div>

<div id="bouton">
<?php
if (isset($_SESSION['panierTrajet'])) {
  echo '<div id="Cols3"><a class="bouton" href="'.$Home.'/lib/script/vente/suppr_panier.php">Effacer le trajet</a></div>';
}
?>
<div id="Cols3">
<a class="bouton" href="<?php echo $Home; ?>/Panier/Identification/">Etape suivante</a>
</div>
</div>

</article>

</div>
</section>

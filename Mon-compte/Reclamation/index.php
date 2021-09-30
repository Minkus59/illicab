<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");  
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/log.inc.php");    

if ($Cnx_Client===false) {
  $Erreur="Vous devez être connecté pour accéder à cette page";
  header('location:'.$Home.'/Mon-compte/?erreur='.$Erreur);
}

$Erreur=$_GET['erreur'];
$Valid=$_GET['valid'];
$Now = time();

//Recuperation des info des la reclamation 
$Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Reclamation WHERE client=:client ORDER BY id DESC");
$Select->bindParam(':client', $SessionClient, PDO::PARAM_STR);
$Select->execute();
?>
<!DOCTYPE HTML>
<html>

<head>   

<title><?php echo $SOEPage->titre ?></title>
<meta name="category" content="<?php if ($SOEPage->nom=="/") { echo "Accueil"; } else { echo $SOEPage->nom; } ?>" />
<meta name="description" content="<?php echo $SOEPage->description ?>" />
<meta name="robots" content="index, follow"/>
<meta name="author" content="NeuroSoft Team"/>
<meta name="publisher" content="<?php echo $Publisher; ?>"/>
<meta name="reply-to" content="<?php echo $Destinataire; ?>"/>
<meta name="viewport" content="width=device-width, initial-scale=0.8" />

<link rel="shortcut icon" href="<?php echo $Home; ?>/lib/img/icone.ico" >
<link rel="stylesheet" type="text/css" media="screen AND (max-width: 480px)" href="<?php echo $Home; ?>/lib/css/misenpatel.css" />
<link rel="stylesheet" type="text/css" media="screen AND (min-width: 480px) AND (max-width: 960px)" href="<?php echo $Home; ?>/lib/css/misenpatab.css" />
<link rel="stylesheet" type="text/css" media="screen AND (min-width: 960px)" href="<?php echo $Home; ?>/lib/css/misenpapc.css" >

<script type="text/javascript" src="<?php echo $Home; ?>/lib/js/analys.js"></script>

<script language="JavaScript">

  //verif et creationd e la requete
	function createInstance()
	{
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
<center>
<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/header.inc.php"); ?>

<section>
<div id="Center">
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
if (isset($Erreur)) { echo "<p><font color='#FF0000'>".$Erreur."</font></p>"; }
if (isset($Valid)) { echo "<p><font color='#009900'>".$Valid."</font></p>"; }
?>

<p><H1>Réclamations</H1></p>

<b>Dans quelle cas utilisé les réclamations ? </b>

<p>Les réclamations sont réservées pour signaler tout manquement de la part de illicab.</p>

<a href="<?php echo $Home; ?>/Mon-compte/Reclamation/Nouveau/">Ajouter une nouvelle réclamation</a>

<p><H1>Liste des réclamations</H1></p>

<div id="Reclamation">
<table>
<tr><th>N° de réclamation</th><th>N° de trajet</th><th>Sujet</th><th>Date</th><th>Statue</th><th>Action</th></tr>

<?php
while($Info=$Select->fetch(PDO::FETCH_OBJ)) {
  $Select2=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Client WHERE hash=:hash");
  $Select2->bindParam(':hash', $Info->client, PDO::PARAM_STR);
  $Select2->execute();
  $Info2=$Select2->fetch(PDO::FETCH_OBJ); 
?>
   <tr <?php if ($Info->statue==1) { echo "class='rouge'"; } else { echo "class='vert'";} ?>>
   <td><?php echo $Info->hash; ?></td>
   <td><?php echo $Info->trajet; ?></td>
   
   <td class="left"><?php echo "<b>Sujet : ".$Info->sujet."</b></p>"; 
   
    $Hash=$Info->hash;

    $Select6=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Reclamation_Message WHERE hash=:hash");
    $Select6->bindParam(':hash', $Hash, PDO::PARAM_STR);
    $Select6->execute(); 
     
   while ($Info3=$Select6->fetch(PDO::FETCH_OBJ)) { ?>
               <script language="JavaScript">
                function submitForm<?php echo $Info3->id; ?>(element) { 
                    function storing(data) {
                    //envoi des element receptionné dans la div
                        var element = document.getElementById('<?php echo "Affichage".$Info3->id; ?>');
                        element.innerHTML = data;
                    } 
                    
                    var req =  createInstance();
                    //récuperation des champs du formulaire
                    var id = document.<?php echo "form_".$Info3->id; ?>.id.value;
                    //création >> nomChamp = nomVariable & nomChamp = nomVariable etc...
                    var data = "id=" + id;

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
                    
                    req.open("POST", "/Admin/Boutique/Client/SAV/NewReclam.php", true);
                    req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    //envoi
                    req.send(data);  
                }
        </script><?php
       
        if ($Info3->auteur=="Admin") { ?>
            <div id="<?php echo "Affichage".$Info3->id; ?>"><?php 
            if ($Info3->vu==1) { ?>
                <form name="form_<?php echo $Info3->id; ?>" action="" methode="POST">
                <input name="id" type="hidden" value="<?php echo $Info3->id; ?>"/>
                <input type="button" class="NewMess" name="lu" onClick="submitForm<?php echo $Info3->id; ?>()" title='Nouveau'/>
                </form><?php 
            } 
            else {
                echo "<img class='AncienMess' src='".$Home."/Admin/lib/img/AncienMess.png' title='Vu'/>";
            } ?>
            </div><?php
            
            echo "<b>Illicab</b> - ".nl2br($Info3->message)."</p> 
            Le ".date("d-m-Y / G:i:s", $Info3->created);
        }
        else {

            echo "<b>Vous</b> - ".nl2br($Info3->message)."</p> 
            Le ".date("d-m-Y / G:i:s", $Info3->created);
        }
    ?>
    <p><HR /></p>
    <?php
   }
   ?></td>
   
   <td><?php echo date("d-m-Y / G:i:s", $Info->created); ?></td><td><?php if ($Info->statue==1) { echo "Actif"; } else { echo "Cloturé";} ?></td>
   <td><?php if ($Info->statue==1) {
       echo '<a title="Répondre" href="'.$Home.'/Mon-compte/Reclamation/Visualisation/?id='.$Info->id.'"><img src="'.$Home.'/Admin/lib/img/repondre.png" alt="Répondre"></a>'; 
   }
   else {
       echo '<a title="Aperçu" href="'.$Home.'/Mon-compte/Reclamation/Visualisation/?id='.$Info->id.'"><img src="'.$Home.'/Admin/lib/img/apercu.png" alt="Aperçu"></a>';
   } ?></td></tr>
<?php
}
?>
</table>
</div>

</article>
</div>
</section>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>

</center>

</body>

</html>
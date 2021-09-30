<section>
<div id="Center">
 
 <?php 
$Erreur=$_GET['erreur'];
$Valid=$_GET['valid'];

$Client=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Client WHERE hash_client=:hash_client");
$Client->BindParam(':hash_client', $SessionClient, PDO::PARAM_STR);
$Client->execute();
$Info=$Client->fetch(PDO::FETCH_OBJ);

?>
<article>
<?php
if (isset($Erreur)) { echo "<font color='#FF0000'>".$Erreur."</font></p>"; } 
if (isset($Valid)) { echo "<font color='#095f07'>".$Valid."</font></p>"; } 

if ($_SESSION['Compte']=="Admin") { ?>

  <div id="info" class="gauche">
  <H1>Informations personnel</H1> </p>
  <?php echo "<b>Nom : </b>".$Info->nom." ".$Info->prenom."<BR />"; ?>
  
  <?php echo "<b>Téléphone : </b>".$Info->telephone."</p>"; ?>
  
  <?php echo "<b>Adresse : </b>".$Info->adresse.",<BR />"; ?>
  <?php echo $Info->cp." - "; ?>
  <?php echo $Info->ville."<BR />"; ?>
  <p><a href="<?php echo $Home; ?>/Mon-compte/Modification-client/">Modifier</a>
  </div>
  
  <div id="info" class="droite">
  <H1>Informations du compte</H1> </p>
  <?php echo "<b>E-mail : </b>".$Info->email."<BR />"; ?>
  <?php echo "<b>Mot de passe : </b>Vous seul le connaissez<BR />"; ?>
  <p><a href="<?php echo $Home; ?>/Mon-compte/Modification-compte/">Modifier</a>
  </div>

<?php
}    

if ($_SESSION['Compte']=="Personnel") { ?>

  <div id="info" class="gauche">
  <H1>Informations personnel</H1> </p>
  <?php echo "<b>Nom : </b>".$Info->nom."<BR />"; ?>
  <?php echo "<b>Prénom : </b>".$Info->prenom."</p>"; ?>
  
  <?php echo "<b>Téléphone : </b>".$Info->telephone."</p>"; ?>
  
  <?php echo "<b>Adresse : </b>".$Info->adresse.",<BR />"; ?>
  <?php echo $Info->cp." - "; ?>
  <?php echo $Info->ville."<BR />"; ?>
  <p><a href="<?php echo $Home; ?>/Mon-compte/Modification-client/">Modifier</a>
  </div>
  
  <div id="info" class="droite">
  <H1>Informations du compte</H1> </p>
  <?php echo "<b>E-mail : </b>".$Info->email."<BR />"; ?>
  <?php echo "<b>Mot de passe : </b>Vous seul le connaissez<BR />"; ?>
  <p><a href="<?php echo $Home; ?>/Mon-compte/Modification-compte/">Modifier</a>
  </div>

<?php
}
  
if ($_SESSION['Compte']=="Professionnel") { ?>

  <div id="info" class="gauche">
  <H1>Informations personnel</H1> </p>
  <?php echo "<b>Nom : </b>".$Info->nom."<BR />"; ?>
  <?php echo "<b>N° TVA : </b>".$Info->numero_tva."<BR />"; ?>
  
  <?php echo "<b>Téléphone </b>: ".$Info->telephone."</p>"; ?>
  
  <?php echo "<b>Adresse : </b></label>".$Info->adresse.",<BR />"; ?>
  <?php echo $Info->cp." - "; ?>
  <?php echo $Info->ville."<BR />"; ?>
  <p><a href="<?php echo $Home; ?>/Mon-compte/Modification-client/">Modifier</a>
  </div>
  
  <div id="info" class="droite">
  <H1>Informations du compte</H1> </p>
  <?php echo "<b>E-mail : </b>".$Info->email."<BR />"; ?>
  <?php echo "<b>Mot de passe : </b>Vous seul le connaissez<BR />"; ?>
  <p><a href="<?php echo $Home; ?>/Mon-compte/Modification-compte/">Modifier</a>
  </div>

<?php
}
  
if ($_SESSION['Compte']=="Partenaire") { ?>

  <div id="info" class="gauche">
  <H1>Informations personnel</H1> </p>
  <?php echo "<b>Nom : </b>".$Info->nom."<BR />"; ?>
  <?php echo "<b>Prénom : </b>".$Info->prenom."</p>"; ?>
  
  <?php echo "<b>Téléphone : </b>".$Info->telephone."</p>"; ?>
  
  <?php echo "<b>Adresse : </b>".$Info->adresse.",<BR />"; ?>
  <?php echo $Info->cp." - "; ?>
  <?php echo $Info->ville."<BR />"; ?>
  <p><a href="<?php echo $Home; ?>/Mon-compte/Modification-client/">Modifier</a>
  </div>
  
  <div id="info" class="droite">
  <H1>Informations du compte</H1> </p>
  <?php echo "<b>E-mail : </b>".$Info->email."<BR />"; ?>
  <?php echo "<b>Mot de passe : </b>Vous seul le connaissez<BR />"; ?>
  <p><a href="<?php echo $Home; ?>/Mon-compte/Modification-compte/">Modifier</a>
  </div>

<?php
}
?>

<script type="text/javascript" src="<?php echo $Home; ?>/lib/js/select_form.js"></script>
</article>

</div>
</section>
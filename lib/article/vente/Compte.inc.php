<?php 
$Erreur=$_GET['erreur'];
$Valid=$_GET['valid'];

$Statue=filter_input(INPUT_GET,'statue', FILTER_SANITIZE_STRIPPED);
$Statue=filter_var($Statue, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
?>
<section>
<div id="Center">

<div id="bouton">
<div id="Cols4" class="boutonAfter">
<a href="<?php echo $Home; ?>/Reservation/" >Trajet</a>
</div>
<div id="Cols4" class="boutonUp">
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

<article id="Panier">
<?php
if (isset($Erreur)) { echo "<font color='#FF0000'>".$Erreur."</font></p>"; } 
if (isset($Valid)) { echo "<font color='#095f07'>".$Valid."</font></p>"; } 

if ($Cnx_Client===false) { ?>

<div id="Form_Inscription">
    <H1 id="Inscription">Créer votre compte</H1></p>

    <H3>La création d'un compte est gratuite.</H3></p>

      <form name="form_inscription" id="form_inscription" action="<?php echo $Home; ?>/lib/script/compte/inscription.php" method="POST">

      <input type="hidden" name="statue" value="<?php echo $Statue; ?>"/> 
      <label class='col_1'>Adresse E-mail<font color='#FF0000'>*</font> :</label>
      <input type="email" name="email" value="<?php echo $_SESSION['email']; ?>" required/> 
      <br />
      <label class='col_1'>Créer un mot de passe<font color='#FF0000'>*</font> :</label>
      <input type="password" name="mdp" required/> 
      <br />
      <label class='col_1'>Confirmer le mot de passe<font color='#FF0000'>*</font> :</label>
      <input type="password" name="mdp2" required/>
      <br /><br />

      <label class="col_1" for="type">Civilité<font color='#FF0000'>*</font> :</label>
      <select name="civilite" required>
      <option value="" >--</option>
      <option value="Mr" <?php if ($_SESSION['civilite']=="Mr") { echo "selected"; } ?>>Mr</option>
      <option value="Mme" <?php if ($_SESSION['civilite']=="Mme") { echo "selected"; } ?>>Mme</option>
      <option value="Mlle" <?php if ($_SESSION['civilite']=="Mlle") { echo "selected"; } ?>>Mlle</option>
      </select>
      <br />
      <label class='col_1'>Nom<font color='#FF0000'>*</font> :</label>
      <input type="text" name="nom" value="<?php echo $_SESSION['nom']; ?>" required/> 
      <br />
      <label class="col_1">Prénom :</label>
      <input type="text" name="prenom" value="<?php echo $_SESSION['prenom']; ?>"/>  
      <br />
      <label class='col_1'>Numéro de téléphone<font color='#FF0000'>*</font> :</label>
      <input type="text" name="tel" value="<?php echo $_SESSION['tel']; ?>" required/>   
      <br /><br />
      <span class='col_1'></span>
      <input class="Panier" type="submit" name="Valider" value="M'inscrire"/>
      <br /><br />
      </form> 
   
      <font color='#FF0000'>*</font> : Informations requises

      <p><font color='#FF0000'>
      Les inscriptions sont fermées pour le moment.<BR />
      Elle seront disponibles prochainement !
      </font>
</div>

<div id="Form_Cnx">
    <p><H1>Déjà inscrit ?</H1></p>
    <form action="<?php echo $Home; ?>/lib/script/compte/cnx.php" method="POST">
    <label class='col_1'>E-mail<font color='#FF0000'>*</font> : </label>
    <input name="email" type="email"  required/>
    <br />
    <label class='col_1'>Mot de passe<font color='#FF0000'>*</font> : </label>
    <input name="mdp" type="password"  required/>
    <br /><br />
    <span class='col_1'></span>
    <input class="Panier" type="submit" name="OK" value="Connexion"/>
    </form><BR />
    <label class='col_1'></label><a href="<?php echo $Home; ?>/Securite/">Mot de passe oublié ?</a>

</div>
<?php
}
else {
echo "Vous êtes connecté, vous pouvez passer à l'étape suivante.";
}
?>

<script type="text/javascript" src="<?php echo $Home; ?>/lib/js/select_form.js"></script>

<div id="bouton">
<?php
if (isset($_SESSION['panierTrajet'])) {
  echo '
    <div id="Cols3">

    <a class="bouton" href="'.$Home.'/lib/script/vente/suppr_panier.php">Effacer le trajet</a>
    </div>';
}
?>
<div id="Cols3">
<a class="bouton" href="<?php echo $Home; ?>/Panier/Validation/">Etape suivante</a>
</div>
</div>

</article>

</div>
</section>

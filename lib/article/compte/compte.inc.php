<?php 
$Erreur=$_GET['erreur'];
$Valid=$_GET['valid'];

$ClientInfo=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Client WHERE hash=:hash");
$ClientInfo->BindParam(':hash', $SessionClient, PDO::PARAM_STR);
$ClientInfo->execute();
$Info=$ClientInfo->fetch(PDO::FETCH_OBJ);
?>

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
<img src="<?php echo $Home; ?>/lib/img/inscription.png"/>
<?php
}
else { ?>
    <div id="Form_Cnx">
    <H1>Vos informations personnelle</H1></p>

    <form name="form_modification" id="form_modification" action="<?php echo $Home; ?>/lib/script/compte/modificationClient.php" method="POST">

    <label class="col_1">Nom<font color='#FF0000'>*</font> :</label>
    <input type="text" name="nom" required="required" value="<?php echo $Info->nom; ?>"/> 
    <br />
    <label class="col_1">Prénom :</label>
    <input type="text" name="prenom" value="<?php echo $Info->prenom; ?>"/>  
    <br />
    <label class="col_1">Numéro de téléphone<font color='#FF0000'>*</font> :</label>
    <input type="text" name="tel" required="required" value="<?php echo $Info->telephone; ?>"/>   
    <br /><br />

    <span class="col_1"></span>
    <input type="submit" name="modifier" value="Modifier"/>
    </form> 
   
    <font color='#FF0000'>*</font> : Informations requises
    </div>

    <div id="Form_Cnx">
    <H1>Vos informations de connexion</H1>

    <form name="form_modification2" id="form_modification2" action="<?php echo $Home; ?>/lib/script/compte/modificationCompte.php" method="POST">
    <label class="col_1">Adresse E-mail<font color='#FF0000'>*</font> :</label>
    <input type="email" name="email" required="required" value="<?php echo $Info->email; ?>"/> 
    <br /><br />

    <span class="col_1"></span>
    <input type="submit" name="modifierEmail" value="Modifier"/>
    <br /><br />
    </form> 

    <form name="form_modification_mdp" id="form_modification_mdp" action="<?php echo $Home; ?>/lib/script/compte/modificationCompte.php" method="POST">
    <label class="col_1">Nouveau mot de passe<font color='#FF0000'>*</font> :</label>
    <input type="password" name="mdp" required="required"/>
    <br />
    <label class="col_1">Confirmer le mot de passe<font color='#FF0000'>*</font> :</label>
    <input type="password" name="mdp2" required="required"/>
    <br /><br />
    
    <span class="col_1"></span>
    <input type="submit" name="modifiermdp" value="Modifier"/>
    </form>

    <font color='#FF0000'>*</font> : Informations requises <BR /><BR />
    </div>

    <BR /><BR />
<a href="<?php echo $Home; ?>/lib/script/compte/deconnexion.php">Déconnexion</a>
<?php
}
?>
</article>

<?php
if ($Count>0) {

    while($Actu=$RecupArticle->fetch(PDO::FETCH_OBJ)) { 

        echo '
        <article>';

        echo $Actu->message;
        if ($Cnx_Admin==true) { 
            echo '<a href="'.$Home.'/Admin/Article/Nouveau/?id='.$Actu->id.'"><img src="'.$Home.'/Admin/lib/img/modifier.png"></a><a href="'.$Home.'/Admin/Article/supprimer.php?id='.$Actu->id.'"><img src="'.$Home.'/Admin/lib/img/supprimer.png"></a>';
        } 
        echo '</article>';
    }
}
?>

</div>
</section>
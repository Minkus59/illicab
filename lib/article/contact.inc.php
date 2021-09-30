<?php 
$Erreur=$_GET['erreur'];
$Valid=$_GET['valid'];

session_start();
?>

<section>
<div id="Center">
    
<article>
<?php 
if (isset($Erreur)) { echo "<font color='#FF0000'>".$Erreur."</font></p>"; } 
if (isset($Valid)) { echo "<font color='#00CC00'>".$Valid."</font></p>"; }
?>

<div id="Gauche">
<H1>Contact</H1>

Pour toutes questions :<BR /><BR />

Merci de bien vouloir préciser vos coordonnées et votre demande.<BR /><BR />  
<form name="form_contact" id="form_contact" action="<?php echo $Home; ?>/lib/script/contact.php" method="POST">

<input type="text" value="<?php if (isset($_SESSION['nom'])) { echo $_SESSION['nom']; } ?>" name="nom" placeholder="Nom / Prénom*" required="required"><BR />
<input type="text" value="<?php if (isset($_SESSION['tel'])) { echo $_SESSION['tel']; } ?>" name="tel" placeholder="Numéro de téléphone*" required="required"/><BR />
<input type="text" value="<?php if (isset($_SESSION['cp'])) { echo $_SESSION['cp']; } ?>" name="cp" placeholder="Code postal*" required="required"/><BR /><BR />
<input type="text" value="<?php if (isset($_SESSION['sujet'])) { echo $_SESSION['sujet']; } ?>" name="sujet" placeholder="Sujet*" required="required"/><BR />
<textarea cols="40" rows="10" name="message" placeholder="Message*" required="required"><?php if (isset($_SESSION['message'])) { echo $_SESSION['message']; } ?></textarea><BR />
<input type="email" value="<?php if (isset($_SESSION['email'])) { echo $_SESSION['email']; } ?>" name="email" placeholder="Votre adresse e-mail*" required="required"/><BR /><BR />
<input type="submit" name="Envoyer" value="Envoyer"/>

</form><BR /><BR />

<font color='#FF0000'>*</font> : Informations requises<BR /><BR />
</div>

<div id="Droite">
<a href='<?php echo $Home; ?>'><img src='<?php echo $ParamLogoFooter->logo; ?>'/></a><BR /><BR />
<img src='<?php echo $Home; ?>/lib/img/tel.png'/> <?php echo $Telephone; ?><BR />
<img src='<?php echo $Home; ?>/lib/img/mail.png'/> <?php echo $Destinataire; ?> <BR /><BR /><BR />

<img src='<?php echo $Home; ?>/lib/img/vehicule.png'/>
</div>
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


<section>
<div id="Center">
 
 <?php 
$Erreur=$_GET['erreur'];
$Valid=$_GET['valid'];
?>
<article>
<?php
if (isset($Erreur)) { echo "<font color='#FF0000'>".$Erreur."</font></p>"; } 
if (isset($Valid)) { echo "<font color='#095f07'>".$Valid."</font></p>"; } 
?>

<H1>Procédure de changement de mot de passe</H1></p>

<form id="form_email" action="<?php echo $Home; ?>/lib/script/compte/securite.php" method="POST">

<label class="col_1">Adresse E-mail :</label>
<input type="email" name="email"required="required"/>
<br /><br />

<span class="col_1"></span>
<input type="submit" name="Recevoir" value="Recevoir"/>
</form>
</article>

</div>
</section>
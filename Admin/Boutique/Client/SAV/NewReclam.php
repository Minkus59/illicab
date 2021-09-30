<?php 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");  
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/log.inc.php"); 

$Id=$_POST['id'];

$Update=$cnx->prepare("UPDATE ".$Prefix."neuro_Reclamation_Message SET vu='0' WHERE id=:id");
$Update->bindParam(':id', $Id, PDO::PARAM_INT);
$Update->execute();

echo "<img class='AncienMess' src='".$Home."/Admin/lib/img/AncienMess.png' title='Vu'/>";
?>
<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php"); 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");   
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/log.inc.php");   

unset($_SESSION['NeuroClient']);


if (isset($_SESSION['panier'])) {
   header("location:".$Home."/Panier/Identification/");
}
else {
     header("location:".$Home."/Mon-compte/");
}

?>
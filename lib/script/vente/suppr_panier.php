<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");  
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/log.inc.php"); 

if (isset($_SESSION['panierTrajet'])) {
  unset($_SESSION['panierTrajet']);

  unset($_SESSION['depart']);
  unset($_SESSION['destination']);
  unset($_SESSION['passager']);
  unset($_SESSION['trajet']);
  unset($_SESSION['destinationRetour']);

  unset($_SESSION['departPro']);
  unset($_SESSION['destinationPro']);
  unset($_SESSION['passagerPro']);
  unset($_SESSION['trajetPro']);
  unset($_SESSION['destinationRetourPro']);

  header("location:".$Home);
}
else {
  header("location:".$Home);
}
?>
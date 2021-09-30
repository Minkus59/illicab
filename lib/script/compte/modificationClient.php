<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php"); 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");   
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/log.inc.php");   

if (isset($_POST['modifier'])) {

   $Nom=filter_input(INPUT_POST,'nom', FILTER_SANITIZE_STRIPPED);
   $Nom=filter_var($Nom, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
   $Prenom=filter_input(INPUT_POST,'prenom', FILTER_SANITIZE_STRIPPED);
   $Prenom=filter_var($Prenom, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
   $Tel=filter_input(INPUT_POST,'tel', FILTER_SANITIZE_STRIPPED);
   $Tel=filter_var($Tel, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
   $Adresse=filter_input(INPUT_POST,'adresse', FILTER_SANITIZE_STRIPPED);
   $Adresse=filter_var($Adresse, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
   $Cp=filter_input(INPUT_POST,'cp', FILTER_SANITIZE_STRIPPED);
   $Cp=filter_var($Cp, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
   $Ville=filter_input(INPUT_POST,'ville', FILTER_SANITIZE_STRIPPED);
   $Ville=filter_var($Ville, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
   $Pays=filter_input(INPUT_POST,'pays', FILTER_SANITIZE_STRIPPED);
   $Pays=filter_var($Pays, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
   $Tva=filter_input(INPUT_POST,'tva', FILTER_SANITIZE_STRIPPED);
   $Tva=filter_var($Tva, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

   $Statue=$_SESSION['Compte'];

     if (strlen($Nom)<=2) { 
           $Erreur="Le nom doit etre saisie !<br />";
           header("location:".$Home."/Mon-compte/?erreur=".$Erreur."");
     }

     elseif (strlen($Tel)<=5) { 
           $Erreur="Le numéro de téléphone doit etre saisie !<br />";
           header("location:".$Home."/Mon-compte/?erreur=".$Erreur."");
     }
     else {
          if ($Statue=='Professionnel') {
            if (strlen($Adresse)<=2) { 
                  $Erreur="L'adresse doit etre saisie !<br />";
                  header("location:".$Home."/Mon-compte/?erreur=".$Erreur."");
            }
            
            elseif (strlen($Cp)<=2) { 
                  $Erreur="Le code postal doit etre saisie !<br />";
                  header("location:".$Home."/Mon-compte/?erreur=".$Erreur."");
            }
            
            elseif (strlen($Ville)<=2) { 
                  $Erreur="La ville doit etre saisie !<br />";
                  header("location:".$Home."/Mon-compte/?erreur=".$Erreur."");
            }
            
            elseif (strlen($Pays)<=2) { 
                  $Erreur="Le Pays doit etre saisie !<br />";
                  header("location:".$Home."/Mon-compte/?erreur=".$Erreur."");
            }
            else {
                $InsertUser=$cnx->prepare("UPDATE ".$Prefix."neuro_Client SET nom=:nom, prenom=:prenom, numero_tva=:numero_tva, telephone=:telephone , adresse=:adresse, cp=:cp, ville=:ville, pays=:pays WHERE hash=:hash");
                $InsertUser->bindParam(':nom', $Nom, PDO::PARAM_STR);
                $InsertUser->bindParam(':prenom', $Prenom, PDO::PARAM_STR);
                $InsertUser->bindParam(':numero_tva', $Tva, PDO::PARAM_STR);    
                $InsertUser->bindParam(':telephone', $Tel, PDO::PARAM_STR);
                $InsertUser->bindParam(':adresse', $Adresse, PDO::PARAM_STR);
                $InsertUser->bindParam(':cp', $Cp, PDO::PARAM_STR);
                $InsertUser->bindParam(':ville', $Ville, PDO::PARAM_STR);
                $InsertUser->bindParam(':pays', $Pays, PDO::PARAM_STR);  
                $InsertUser->bindParam(':hash', $SessionClient, PDO::PARAM_STR);  
                $InsertUser->execute();   
            }
          }
          
          elseif ($Statue=='Particulier') {
                $InsertUser=$cnx->prepare("UPDATE ".$Prefix."neuro_Client SET nom=:nom, prenom=:prenom, telephone=:telephone WHERE hash=:hash");
                $InsertUser->bindParam(':nom', $Nom, PDO::PARAM_STR);
                $InsertUser->bindParam(':prenom', $Prenom, PDO::PARAM_STR);
                $InsertUser->bindParam(':telephone', $Tel, PDO::PARAM_STR);
                $InsertUser->bindParam(':hash', $SessionClient, PDO::PARAM_STR);  
                $InsertUser->execute();   
          }

          else {
            $Erreur="Erreur !";
            header("location:".$Home."/Mon-compte/?erreur=".$Erreur."");
          }
          
          if ($InsertUser==false) {
            $Erreur="L'enregistrement des données à échouée, veuillez réessayer ultèrieurement !<br />";
            header("location:".$Home."/Mon-compte/?erreur=".$Erreur."");
          }
          else {
            $Valid="Enregistrement effectué avec succès !<br />";
            header("location:".$Home."/Mon-compte/?valid=".$Valid."");
          }
     }
}
else {
  header("location:".$Home."/Mon-compte/");
}
?>
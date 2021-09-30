<?php 
require($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php"); 
  
if ((isset($_POST['Envoyer']))&&($_POST['Envoyer']=="Envoyer")) {

   $Nom=FiltreText('nom');
   $Tel=FiltreTel('tel');
   $Cp=FiltreText('cp');
   $Sujet=FiltreText('sujet');
   $Message=FiltreText('message');
   $Email=FiltreEmail('email');

   session_start();

  if ($Nom[0]===false) {
    $Erreur=$Nom[1];
  }  
  else {
    $_SESSION['nom']=$Nom;
  } 
      
  if ($Tel[0]===false) {
    $Erreur=$Tel[1]; 
  }  
  else {
    $_SESSION['tel']=$Tel;
  } 
   
  if ($Cp[0]===false) {
    $Erreur=$Cp[1]; 
  }  
  else {
    $_SESSION['cp']=$Cp;
  } 
   
  if ($Sujet[0]===false) {
    $Erreur=$Sujet[1];
  }  
  else {
    $_SESSION['sujet']=$Sujet;
  } 
   
  if ($Message[0]===false) {
    $Erreur=$Message[1];
  }  
  else {
    $_SESSION['message']=$Message;
  }  
         
  if ($Email[0]===false) {
    $Erreur=$Email[1]; 
  }    
  else {
    $_SESSION['email']=$Email;
  }  
  
  if (!isset($Erreur)) {
    $Body="Message de : ".$Email."<BR />
    Nom : ".$Nom."<BR />
    Tel : ".$Tel."<BR />
    Code postal : ".$Cp."<BR />
    Sujet : ".$Sujet."<BR />
    <BR />
    Message : ".$Message."</p>";

    //Recuperation d'envoi
    $RecupEmail=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_parametre");
    $RecupEmail->execute();
    $InfoDesti=$RecupEmail->fetch(PDO::FETCH_OBJ);

    if (EnvoiNotification($Societe, $Serveur, $Destinataire, "illicab - Nouvelle demande de contact", $Body, $InfoDesti->email)==false) {
        $Erreur="L'e-mail n'a pu être envoyé, vérifiez que vous l'avez entré correctement !</p>";
        ErreurLog($Erreur);
        header('location:'.$Home.'/Contact/?erreur='.$Erreur);
    } 
    else {
      session_unset();
      session_destroy();
      $Erreur=urlencode("Votre message à bien été enregistré, il sera traité dans les meilleurs délais !");
      header('location:'.$Home.'/Contact/?erreur='.$Erreur);
    }
  }
  else {
    header('location:'.$Home.'/Contact/?erreur='.urlencode($Erreur));
  }
}
else {
  header("location:".$Home."/Contact/");
}
?>
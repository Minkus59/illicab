<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php"); 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");   
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/log.inc.php");   

if (isset($_POST['OK'])) {

   $Email=FiltreEmail('email');
   $Mdp=FiltreMDP('mdp');
   $Now=time();

   if ($Email[0]===false) {
       $Erreur=$Email[1]; 
       ErreurLog($Erreur);
       header("location:".$Home."/Mon-compte/?erreur=".urlencode($Erreur));
   }
   elseif ($Mdp[0]===false) {
       $Erreur=$Mdp[1]; 
       ErreurLog($Erreur);
       header("location:".$Home."/Mon-compte/?erreur=".urlencode($Erreur));
   }
   else {
       $Email=$Email;
       $Mdp=$Mdp;

       $VerifEmail=$cnx->prepare("SELECT (email) FROM ".$Prefix."neuro_Client WHERE email=:email");
       $VerifEmail->bindParam(':email', $Email, PDO::PARAM_STR);
       $VerifEmail->execute();
       $NbRowsEmail=$VerifEmail->rowCount();

       $RecupClient=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Client WHERE email=:email");
       $RecupClient->bindParam(':email', $Email, PDO::PARAM_STR);
       $RecupClient->execute();
       $RecupC=$RecupClient->fetch(PDO::FETCH_OBJ);

       if ($NbRowsEmail!=1) {
           $Erreur="Cette adresse E-mail ne correspond à aucun compte !<br />"; 
           ErreurLog($Erreur);
          header("location:".$Home."/Mon-compte/?erreur=".urlencode($Erreur));
       }
       else {
           $RecupCreated=$cnx->prepare("SELECT created FROM ".$Prefix."neuro_Client WHERE email=:email");
           $RecupCreated->bindParam(':email', $Email, PDO::PARAM_STR);
           $RecupCreated->execute();

            $DateCrea=$RecupCreated->fetch(PDO::FETCH_OBJ);
            $Salt=md5($DateCrea->created);
            $Mdp=md5($Mdp);
            $MdpCrypt=crypt($Mdp, $Salt);

           $Mdp=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Client WHERE mdp=:mdp AND email=:email");
           $Mdp->bindParam(':mdp', $MdpCrypt, PDO::PARAM_STR);
           $Mdp->bindParam(':email', $Email, PDO::PARAM_STR);
           $Mdp->execute();
           $nb_rows=$Mdp->rowCount();

           if ($nb_rows!=1) { 
               $Erreur="Le mot de passe ne correspond pas à cette adresse e-mail !<br />";
               ErreurLog($Erreur);
               header("location:".$Home."/Mon-compte/?erreur=".urlencode($Erreur));
           }
           elseif ($RecupC->activate!=1) {
                $Erreur="Votre compte n'a pas été activé !<br />";
                $Erreur.="Lors de votre inscription un e-mail vous a été envoyé<br />";
                $Erreur.="Veuillez valider votre adresse e-mail en cliquant sur le lien.<br />";
                $Erreur.="vous pouvais toujours recevoir le mail a nouveau en cliquant sur ' recevoir '<br />";
                $Erreur.="<form action='".$Home."/lib/script/compte/renvoi.php' method='post'/><input type='hidden' name='client' value='".$RecupC->hash."'/><input type='hidden' name='email' value='".$RecupC->email."'/><input type='submit' name='Recevoir' value='Recevoir'/></form></p>";
                ErreurLog($Erreur);
                header("location:".$Home."/Mon-compte/?erreur=".urlencode($Erreur));
            }
           else {
                $LastCnx=$cnx->prepare("UPDATE ".$Prefix."neuro_Client SET visited=:visited WHERE email=:email");
                $LastCnx->bindParam(':email', $Email, PDO::PARAM_STR);
                $LastCnx->bindParam(':visited', $Now, PDO::PARAM_STR);
                $LastCnx->execute();    
       
                session_start();
                $_SESSION['NeuroClient']=$RecupC->hash;
                $_SESSION['Compte']=$RecupC->groupe;

                if (isset($_SESSION['panierTrajet'])) {
                  header("location:".$Home."/Panier/Identification/");
                }
                else {
                  header("location:".$Home."/Mon-compte/");
                }
            }
        }
    }
}
else {
   header("location:".$Home."/Mon-compte/");
}

?>
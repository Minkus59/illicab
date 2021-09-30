<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php"); 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");     
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/log.inc.php");   

if (isset($_POST['Recevoir'])) {

   $Client=$_POST['client'];
   $Email=FiltreEmail('email');
   $Now=time();

   if ($Email[0]===false) {
       $Erreur=$Email[1];
   }
   else {
       $Email=$Email;

        $Body="Bonjour, Merci d'avoir cr&eacute;&eacute; votre compte client sur ".$Societe.".<BR /><BR />
            Veuillez cliquer sur le lien suivant pour valider votre inscription sur ".$Home.".<BR />                  
            <a href='".$Home."/Validation/?id=".$Client."&Valid=1'>Cliquez ici</a>
            <p>Conseils de s&eacute;curit&eacute; importants :</p>
            <ol>
            <li>Vos informations de compte doivent rester confidentielles.</li>
            <li>Ne les communiquez jamais &agrave; qui que ce soit.</li>
            <li>Changez votre mot de passe r&eacute;guli&egrave;rement.</li>
            <li>Si vous pensez que quelqu'un utilise votre compte ill&eacute;galement, veuillez nous pr&eacute;venir imm&eacute;diatement.</li>
            </ol>";

        if (EnvoiNotification($Societe, $Serveur, $Destinataire, "illicab - Validation d'inscription", $Body, $Email)==false) {
            $Erreur="L'e-mail n'a pu être envoyé, vérifiez que vous l'avez entré correctement !</p>";
            ErreurLog($Erreur);
            header("location:".$Home."/Mon-compte/?erreur=".urlencode($Erreur));
        }          
       else {
           $Valid="Un E-mail de confirmation vous a été envoyé à l'adresse suivante : ".$Email."<br />";
           $Valid.="Veuillez valider votre adresse e-mail avant de vous connecter !"; 
           header("location:".$Home."/Mon-compte/?valid=".urlencode($Valid));
        }
    }
}
else {
   header("location:".$Home."/Mon-compte/");
}

?>
<?php
require($_SERVER['DOCUMENT_ROOT']."/impinfbdd/OvH.inc.php");

try {
    $cnx = new PDO($Ovh_serv_bDd, $uTil_bDd_serv, $mDp_bDd_serv);
}
catch (PDOException $e) {
    die("Erreur de connexion à la base de donnée, veuillez réessayer ultèrieurement !");
}

function taille_champ($champ,$taille_min=0,$taille_max=0) {
    if(!isset($champ)) {
        return false;
  }
    elseif (strlen($champ)<$taille_min) {
        return false;
  }
    elseif(strlen($champ)>$taille_max) {
        return false;
  }
return true; 
}

function FiltreNum($name) {
  $string=filter_input(INPUT_POST,$name, FILTER_SANITIZE_STRIPPED);
  $string=filter_var($string, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
  $string=preg_replace(array('/,/'), '.', $string);
  
  if($string===false) { 
    $string=array(false, "Erreur, les caractères utilisés ne sont pas conforme");
    return $string;
  }
  else {
    return $string;
  }
}

function FiltreText($name) {
  $string=filter_input(INPUT_POST,$name, FILTER_SANITIZE_STRIPPED);
  $string=filter_var($string, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
  
  if($string===false) { 
    $string=array(false, urlencode("Erreur, les caractères utilisés ne sont pas conforme"));
    return $string;
  }
  else { 
    if (strlen(trim($string))<=1) {
      $string=array(false, "Certain champ doivent être saisie !");
      return $string;  
    }
    else {
      return $string;
    }  
  }
}

function FiltreMDP($name) {
  $string=filter_input(INPUT_POST,$name, FILTER_SANITIZE_STRIPPED);
  $string=filter_var($string, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
  
  if($string===false) { 
    $string=array(false, "Erreur, les caractères utilisés ne sont pas conforme");
    return $string;
  }
  else {  
    if (!taille_champ($string,8,25)) {
      $string=array(false, "Le mot de passe doit contenir entre 8 et 25 caractères !");
      return $string;  
    }
    elseif (!preg_match("#^[A-Z][A-z-._]+[0-9]+$#", $string)){ 
      $string=array(false, "Le mot de passe ne doit pas comporter d'espace, doit commencer par une majuscule et finir par des chiffres !");
      return $string;  
    }
    else {
      return $string;
    }  
  }
}

function FiltreEmail($name) {
  $string=filter_input(INPUT_POST,$name, FILTER_SANITIZE_STRIPPED);
  $string=filter_var($string, FILTER_SANITIZE_EMAIL);
  $string=filter_var($string, FILTER_VALIDATE_EMAIL);
  $string=strtolower($string);

  if($string===false) { 
    $string=array(false, "L'adresse e-mail n'est pas conforme !");
    return $string;
  }
  else {  
    if (!preg_match("#^[A-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $string)){ 
      $string=array(false, "L'adresse e-mail n'est pas conforme !");
      return $string; 
    } 
    else {
      return $string;
    }  
  }
}


function FiltreTel($name) {  
  $string=filter_input(INPUT_POST,$name, FILTER_SANITIZE_STRIPPED);
  $string=filter_var($string, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
  $string=preg_replace(array('/\s/','/-/'), '', $string);

  if($string===false) { 
    $string=array(false, "Erreur, les caractères utilisés ne sont pas conforme");
    return $string;
  }
  else {
    if(!preg_match("/^[0-9]{10}$/", $string)) {
      $string=array(false, "Le numéro de téléphone n'est pas valide !");
      return $string;
    }
    else {
      return $string;
    }  
  }
}

function FiltreTextGET($name) {
  $string=filter_input(INPUT_GET,$name, FILTER_SANITIZE_STRIPPED);
  $string=filter_var($string, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
  
  if($string===false) { 
    return false;  
  }
  else { 
    if (strlen($string)<=2) {
       return false; 
    }
    else {
      return $string;
    }  
  }
}

function Geolocat($Adress, $KeyGoogleAPI) {
    $Adress = preg_replace('#Ç#', 'C', $Adress);
    $Adress = preg_replace('#ç#', 'c', $Adress);
    $Adress = preg_replace('#è|é|ê|ë#', 'e', $Adress);
    $Adress = preg_replace('#È|É|Ê|Ë#', 'E', $Adress);
    $Adress = preg_replace('#à|á|â|ã|ä|å#', 'a', $Adress);
    $Adress = preg_replace('#@|À|Á|Â|Ã|Ä|Å#', 'A', $Adress);
    $Adress = preg_replace('#ì|í|î|ï#', 'i', $Adress);
    $Adress = preg_replace('#Ì|Í|Î|Ï#', 'I', $Adress);
    $Adress = preg_replace('#ð|ò|ó|ô|õ|ö#', 'o', $Adress);
    $Adress = preg_replace('#Ò|Ó|Ô|Õ|Ö#', 'O', $Adress);
    $Adress = preg_replace('#ù|ú|û|ü#', 'u', $Adress);
    $Adress = preg_replace('#Ù|Ú|Û|Ü#', 'U', $Adress);
    $Adress = preg_replace('#ý|ÿ#', 'y', $Adress);
    $Adress = preg_replace('#Ý#', 'Y', $Adress);
    $Adress = preg_replace('# #', '-', $Adress);

    $UrlGeocoding="https://maps.googleapis.com/maps/api/geocode/xml?address=".$Adress."&key=".$KeyGoogleAPI;

    $Xml=file_get_contents($UrlGeocoding);
    $Results = simplexml_load_string($Xml);

    if ($Results->status=="OK") {
        $Lat=$Results->result->geometry->location->lat;
        $Lng=$Results->result->geometry->location->lng;

        return array(0=>$Lat, 1=>$Lng, 2=>$Lat.",".$Lng);
    }
    else {
        if ($Results->status=="INVALID_REQUEST") {
            $Erreur="Geocoding - Veuillez préciser votre adresse, nous ne parvenons à vous trouver !<BR />";
            ErreurLog($Erreur);
            echo $Erreur;
            return 0;
        }
        elseif ($Results->status=="OVER_QUERY_LIMIT") {
            $Erreur="Geocoding - Une erreur est survenue mais rien de grave les quoata sont dépassé pour la journée, veuillez revenir demain !<BR />";
            ErreurLog($Erreur);
            echo $Erreur;
            return 0;
        }
        elseif ($Results->status=="REQUEST_DENIED") {
            $Erreur="Geocoding - Google a rejetez votre demande, veuillez contacter l'administrateur du site Internet !<BR />";
            ErreurLog($Erreur);
            //echo $Results->error_message;
            echo $Erreur;
            return 0;
        }
        elseif ($Results->status=="UNKNOWN_ERROR ") {
            $Erreur="Geocoding - Nous ne parvenons pas à trouver Google, veuillez revenir dans quelques minutes !<BR />";
            ErreurLog($Erreur);
            echo $Erreur;
            return 0;
        }
        else {
            $Erreur="Geocoding - Nous ne parvenons pas à vous trouver, veuillez réessayer !<BR />";
            ErreurLog($Erreur);
            echo $Erreur;
            return 0;
        }
    }
}

function DistanceMatrix($Depart, $Destination, $KeyGoogleAPI, $TauxDestiOk, $TauxPersOk, $NomDepart, $NomDestination) {
    $Depart=Geolocat($Depart, $KeyGoogleAPI);
    if ($Depart!=0) {
        $Destination=Geolocat($Destination, $KeyGoogleAPI);
        if ($Destination!=0) {
            $UrlGeocoding="https://maps.googleapis.com/maps/api/distancematrix/xml?key=".$KeyGoogleAPI."&origins=".$Depart[2]."&destinations=".$Destination[2]."&language=fr";

            $Xml=file_get_contents($UrlGeocoding);
            $Results = simplexml_load_string($Xml);

            if ($Results->status=="OK") {
                $Distance=$Results->row->element->distance->value;
                $DistanceText=$Results->row->element->distance->text;
                $TempsText=$Results->row->element->duration->text;

                $Distance=$Distance / 1000;
                $DistanceIllicab=$Distance * 2;
                $PrixDistance=$DistanceIllicab * $TauxDestiOk;
                $PrixPoidPersonne=$PrixDistance * $TauxPersOk;
                $PrixArondie=round($PrixPoidPersonne, 0, PHP_ROUND_HALF_DOWN);

                return array(0=>$NomDepart, 1=>$NomDestination, 2=>$DistanceText, 3=>$TempsText, 4=>money_format("%.2n", $PrixArondie));
            }
            else {
                if ($Results->status=="INVALID_REQUEST") {
                    $Erreur="DistanceMatrix - Veuillez préciser votre adresse !<BR />";
                    ErreurLog($Erreur);
                    echo $Erreur;
                }
                elseif ($Results->status=="OVER_QUERY_LIMIT") {
                    $Erreur="DistanceMatrix - Une erreur est survenue mais rien de grave les quoata sont dépassé pour la journée, veuillez revenir demain !<BR />";
                    ErreurLog($Erreur);
                    echo $Erreur;
                }
                elseif ($Results->status=="REQUEST_DENIED") {
                    $Erreur="DistanceMatrix - Google a rejetez votre demande, veuillez contacter l'administrateur du site Internet !<BR />";
                    ErreurLog($Erreur);
                    echo $Erreur;
                }
                else {
                    $Erreur="DistanceMatrix - Veuillez réessayer !<BR />";
                    ErreurLog($Erreur);
                    echo $Erreur;
                }
            }
        }
    }
} 

function EnvoiNotification($De, $Envoi, $Reception, $Titre, $Body, $Desti) {
    $Home="http://illicab.fr";

    $header = "MIME-Version: 1.0\n";
    $header .= "Content-Type:multipart/mixed; boundary=\"$boundary\"\n";
    $header .= "From: \"$De\"<$Envoi>\n";
    $header .= "Reply-to: <$Reception>\n";
    $header .= "\n";
    
    $message="Ce message est au format MIME.\n";
    
    $message.="--$boundary\n";
    $message.= "Content-Type: text/html; charset=utf-8\n";
    
    $message.="\n";
    $message.="<html><head><title>".$Titre."</title>
    </head><body>
    <table style='width: 800px;' cellspacing='0' cellpadding='0'>
    <tbody>
    <tr>
    <td style='background-color: #333333;' colspan='2'><a href='".$Home."/'><img src='".$Home."/lib/header/a1f15d5d2e26d23236d507d50e0b96a5Logo.png' alt='' /></a></td>
    <td style='background-color: #333333;'>&nbsp; <img src='".$Home."/lib/img/tel.png' alt='' width='30' height='30' /><span style='color: #ffffff; font-size: 24pt;'><strong>&nbsp;06 21 76 71 59</strong></span></td>
    </tr>
    <tr>
    <td style='width: 800px;' colspan='3'><br /><H1>".$Titre."</H1>
    ".$Body."
    <br /></td>
    </tr>
    <tr><br />
    <td style='width: 800px;' colspan='3'><font color='#ff0000'>Cet e-mail contient des informations confidentielles et / ou prot&eacute;g&eacute;es par la loi. Si vous n'en &ecirc;tes pas le v&eacute;ritable destinataire ou si vous l'avez re&ccedil;u par erreur, informez-en imm&eacute;diatement son exp&eacute;diteur et d&eacute;truisez ce message. La copie et le transfert de cet e-mail sont strictement interdits.</font><font color='#ff0000'>&nbsp;</font></td>
    <br /></tr>
    <tr style='background-color: #333333;'>
    <td><br /><p style='text-align: center;'><strong><span style='color: #53bfa9;'><a style='color: #53bfa9;' href='".$Home."/'><font size='4'>Trajet particulier</font></a></span></strong></p><br /></td>
    <td style='text-align: center;'><br /><strong><span style='color: #53bfa9;'><font color='#ff0000'>&nbsp;<a style='color: #53bfa9;' href='".$Home."/Trajet-professionnel/'><font size='4'>Trajet professionnel</font></a></font></span></strong><br /></td>
    <td style='text-align: center;'><br /><strong><span style='color: #53bfa9;'><font color='#ff0000'>&nbsp;<a style='color: #53bfa9;' href='".$Home."/BNS-Direct-Air/'><font size='4'>BNS DIRECT - AIR</font></a></font></span></strong><br /></td>
    </tr>
    <tr>
    <td><font color='#ff0000'><br /><br /></font></td>
    <td style='text-align: center;'><br /><font color='#ff0000'>&nbsp;&nbsp;<img src='".$Home."/lib/img/adresse.png' alt='' /> <span style='color: #000000;'>31 bis rue Jean Jaur&egrave;s, 59700 Marq en Baroeul</span></font><br /></td>
    <td><font color='#ff0000'><br /><br /></font></td>
    </tr>
    </tbody>
    </table>
    </body></html>";
    $message.="\n\n";
    
    $message.="--$boundary--\n";

    if (!mail($Desti, $Titre, $message, $header)) {
        return false;
    }
    else {   
        return true;
    }
}
?>
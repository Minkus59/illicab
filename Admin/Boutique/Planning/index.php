<?php 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");  
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/log.inc.php"); 

if ($Cnx_Admin===false) {
  header('location:'.$Home.'/Admin');
}

$Erreur=$_GET['erreur'];
$Valid=$_GET['valid'];
$SemaineActu=date("W", time());

if (isset($_POST['CodeSemaine'])) {
    $_SESSION['CodeSemaine']=$_POST['CodeSemaine'];
}
else {
    $_SESSION['CodeSemaine']=$SemaineActu;
}

class Planning {
	private $joursFr = Array(0=>'Dimanche', 1=>'Lundi', 2=>'Mardi', 3=>'Mercredi', 4=>'Jeudi', 5=>'Vendredi', 6=>'Samedi', 7=>'Dimanche');

	private $jourDebut; // jour de début du planning (0 a 7)
	private $jourFin; // jour de fin du planning

	private $heureDebut; // heure de début de chaque jour (en minutes)
	private $heureFin; // heure de fin de chaque jour (en minutes)

	private $pas; // durée d'une case (en minutes)
	private $minutesKeys;

	private $contenu; // contenu général du planning (tableau de PlanningCellule)

	private $tabSemaine; // stockage des données (tableau initialisé avec des cellules vides)

	const htmlSpace = '&nbsp;';
	const htmlEmptyCell = '<td>&nbsp;</td>';
	const htmlCellOpen = '<td>';
	const htmlCellClose = '</td>';
	const htmlRowOpen = '<tr>';
	const htmlRowClose = '</tr>';
	const htmlTableOpen = '<table class="tabPlanning">';
	const htmlTableClose = '</table>';

	const separateurHeure = 'h';

	public function __construct($jourDebut=1, $jourFin=7, $heureDebut=0, $heureFin=1440, $pas=30, $contenu = Array()){
    $this->jourDebut = $jourDebut;
    $this->jourFin = $jourFin;
    $this->heureDebut = $heureDebut;
    $this->heureFin = $heureFin;
    $this->pas = $pas;
    $this->contenu = $contenu;

    $this->initTableauSemaine($this->contenu);
    // $this->debugPHPArrays();
    $this->insererContenus($contenu);
	}

	/**
	* Génére un tableau dont les clés sont les heures de début de chaque case (en minutes)
	* Serviront a identifier facilement chaque case du planning
	* @return unknown_type
	*/
	private function genererMinutesKeys() {
        $keys = Array();
        for ($key=$this->heureDebut; $key<=$this->heureFin; $key+=$this->pas) {
        $keys[] = $key;
        }
        $this->keys = $keys;
        return $keys;
	}

	/**
	* Génére un tableau correspondant a un jour
	* @return unknown_type
	*/
	private function initTableauJour() {
        if ($this->pas != 0) {
        $numCells = ($this->heureDebut - $this->heureFin) / $this->pas;
        } else {
        echo 'pas == 0 !!';
        }
        $keys = $this->genererMinutesKeys();
        $tabJour = array_fill_keys($keys, self::htmlEmptyCell);
        return $tabJour;
	}

	private function initTableauSemaine() {
        $this->tabSemaine = Array();
        $tabJour = $this->initTableauJour();
        for($i=$this->jourDebut; $i<=$this->jourFin; $i++) {
        $this->tabSemaine[$i] = $tabJour;
        }
    }

	private function getNumeroCellule($minutesDebut, $minutesFin) {
        return ($minutesFin - $minutesDebut) / $this->pas;
	}

	/**
	* Insére tous les contenus de cellulés envoyés
	* @param $contenuPlanning
	* @return unknown_type
	*/
	private function insererContenus($contenuPlanning) {
        foreach ($contenuPlanning as $contenuCellule) {
        $this->insererContenu($contenuCellule);
        }
	}

	/**
	* Insére le contenu d'une cellule précise
	* @param $contenuCellule
	* @return unknown_type
	*/
	private function insererContenu($contenuCellule) {
        // ajout de la cellule fusionnée
        $duree = $this->getNumeroCellule($contenuCellule->heureDebut, $contenuCellule->heureFin);
        $contenu = $contenuCellule->contenu.'<br />';
        $contenu .= $this->convertMinutesEnHeuresMinutes($contenuCellule->heureDebut);
        $contenu .= ' - '.$this->convertMinutesEnHeuresMinutes($contenuCellule->heureFin);

        $this->tabSemaine[$contenuCellule->numJour][$contenuCellule->heureDebut] = $this->genererCelluleHTML($contenu, $duree, '', $contenuCellule->bgColor);

        // suppression du contenu suivant
        $key = $contenuCellule->heureDebut;
        for ($cpt = $duree-1; $cpt>0; $cpt--) {
        $key += $this->pas;
        $this->tabSemaine[$contenuCellule->numJour][$key] = ''; 
        }
	}

	/* Affichage */ 
	public function debugPHPArrays() {
        echo '<pre>';
        print_r($this->tabSemaine);
        echo '</pre>'; 
	}

	public function genererHtmlTable() {
        $htmlTable = self::htmlTableOpen;

        $htmlTable .= $this->genererBandeauJours();

        $key = $this->heureDebut;
        $keyEnd = $this->heureFin;
        for ($key; $key <= $keyEnd; $key+=$this->pas) {
        $htmlTable .= self::htmlRowOpen;
        $htmlTable .= '<td class="cellHour">'.$this->convertMinutesEnHeuresMinutes($key).'</td>';
        foreach ($this->tabSemaine as $tabHeures) {
        $htmlTable .= $tabHeures[$key];
        }
        $htmlTable .= self::htmlRowClose;
	}

	$htmlTable .= self::htmlTableClose;
    return $htmlTable;
	}

	public function afficherHtmlTable() {
    echo $this->genererHtmlTable();
	}

	private function genererBandeauJours() {
    $daysLine = self::htmlRowOpen;
    $daysLine .= $this->genererCelluleHTML(self::htmlSpace);
    $day = $this->jourDebut;
    while ($day <= $this->jourFin) {
      $daysLine .= $this->genererCelluleHTML($this->jourFr($day), '', 'cellDay');
      $day++;
    }
    $daysLine .= self::htmlRowClose;
    return $daysLine;
	}

	/**
	* Génére une ligne HTML contenant le libellé des jours utilisés dans le planning
	* @param $contenuCellule
	* @param $colspan
	* @param $class
	* @param $bgColor
	* @return unknown_type
	*/
	private function genererCelluleHTML($contenuCellule, $colspan = '', $class = '', $bgColor = '') {
	$celluleHTML = '<td';
	if (!empty($colspan)) 
    $celluleHTML .= ' rowspan="'.$colspan.'"'; 
	if (!empty($class)) 
    $celluleHTML .= ' class="'.$class.'"';
	if (!empty($bgColor)) 
    $celluleHTML .= ' bgcolor="'.$bgColor.'"';
    $celluleHTML .= '/>';
    $celluleHTML .= $contenuCellule;
    $celluleHTML .= '</td>';
    return $celluleHTML;
	}

	/**
	* Renvoie le libellé d'un jour en Franéais
	* @param $dayNum
	* @return unknown_type
	*/
	private function jourFr($dayNum) {
	return $this->joursFr[$dayNum];
	}

	private function convertMinutesEnHeuresMinutes($minutes) {
    $heure = floor($minutes / 60);
    $minutes = ($minutes % 60);
    $minutes = str_pad($minutes, 2, '0', STR_PAD_LEFT);
    return ($heure. self::separateurHeure .$minutes);
	}
}

class PlanningCellule {
	private $data;

	public function __construct($numJour, $heureDebut, $heureFin, $bgColor, $contenu) {
    $this->numJour = $numJour;
    $this->heureDebut = $heureDebut;
    $this->heureFin = $heureFin;
    $this->bgColor = $bgColor;
    $this->contenu = $contenu;
 }

 public function __set($name, $value) {
  if ($name == 'heureDebut' || $name == 'heureFin') {
    $tabHeure = explode(':', $value);
    $value = (int)$tabHeure[0];
    if ($tabHeure[1] == 30)
      $value += 0.5;
      $value = $value*60;
    }
  $this->data[$name] = $value;
 }

 public function __get($name) {
 if (array_key_exists($name, $this->data)) {
  return $this->data[$name];
 }

 $trace = debug_backtrace();
 trigger_error(
  'Propriété non-définie via __get(): ' . $name .
  ' dans ' . $trace[0]['file'] .
  ' à la ligne ' . $trace[0]['line'],
  E_USER_NOTICE);
  return null;
 }

 public function __toString() {
  $str = 'heure début : '.$this->heureDebut."<br />\n";
  $str .= 'heure fin : '.$this->heureFin."<br />\n";
  $str .= 'couleur : '.$this->bgColor."<br />\n";
  $str .= 'contenu : '.$this->contenu."<br />\n";
  return $str;
 }
}

Class PlanningMapper {
	// instance de la classe
	private static $instance;

	// Un constructeur privé ; empéche la création directe d'objet
	private function __construct() { }

	// La méthode singleton
	public static function getInstance() {
	if (!isset(self::$instance)) {
    $c = __CLASS__;
    self::$instance = new $c;
	}
	return self::$instance;
	}

	public function genererPlanningCellule($cours) {
    $contenuCellule = '<b>'.$cours->getAlphaFormateurs()->getPrenomFormateur().'</b><br />'.$cours->getAlphaNiveaux()->getLibelleNiveau();

    $planningContent = new PlanningCellule($cours->getJour(),
    $cours->getHeureDebut(),
    $cours->getHeureFin(),
    $cours->getAlphaNiveaux()->getCodeCouleur()
    ,$contenuCellule);

    return $planningContent;
	}

	public function __clone() {
	trigger_error('Le clônage n\'est pas autorisé.', E_USER_ERROR);
	}
}
?>

<!-- ************************************
*** Script réalisé par NeuroSoft Team ***
********* www.neuro-soft.fr *************
**************************************-->

<!doctype html>
<html>
<head>


<title>NeuroSoft Team - Accès PRO</title>
  
<meta name="robots" content="noindex, nofollow">

<meta name="author".content="NeuroSoft Team">
<meta name="publisher".content="Helinckx Michael">
<meta name="reply-to" content="contact@neuro-soft.fr">

<meta name="viewport" content="width=device-width" >                                                            

<link rel="shortcut icon" href="<?php echo $Home; ?>/Admin/lib/img/icone.ico">

<link rel="stylesheet" type="text/css" media="screen AND (max-width: 480px)" href="<?php echo $Home; ?>/Admin/lib/css/misenpatel.css" />
<link rel="stylesheet" type="text/css" media="screen AND (min-width: 480px) AND (max-width: 960px)" href="<?php echo $Home; ?>/Admin/lib/css/misenpatab.css" />
<link rel="stylesheet" type="text/css" media="screen AND (min-width: 960px)" href="<?php echo $Home; ?>/Admin/lib/css/misenpapc.css" >
</head>

<body>
<header>
<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/header.inc.php"); ?>
</header>

<section>
    
<nav>
<div id="MenuGauche">
<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/menu.inc.php"); ?>
</div>
</nav>

<article>
<?php if (isset($Erreur)) { echo "<font color='#FF0000'>".$Erreur."</font><BR /><BR />"; }
if (isset($Valid)) { echo "<font color='#009900'>".$Valid."</font><BR /><BR />"; } ?>

<form action="" method="POST">
<select name="CodeSemaine" onChange="this.form.submit()">
<option value="<?php echo $SemaineActu; ?>" ><?php echo "Semaine actuel : ".$SemaineActu; ?></option>
<?php 
for($CodeSdebut=1;$CodeSdebut<53;$CodeSdebut++) { ?>
  <option value="<?php echo $CodeSdebut; ?>" <?php if ($_SESSION['CodeSemaine']==$CodeSdebut) { echo "selected"; } ?> ><?php echo "Semaine ".$CodeSdebut; ?></option>
<?php
}
?>
</select>
</form>

<BR /><BR />

<?php
$Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Planning WHERE semaine=:semaine");
$Select->bindParam(':semaine', $_SESSION['CodeSemaine'], PDO::PARAM_STR);
$Select->execute();

while($Employer=$Select->fetch(PDO::FETCH_OBJ)) {
    $SelectLivreur=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Client WHERE hash=:hash");
    $SelectLivreur->bindParam(':hash', $Employer->user, PDO::PARAM_STR);
    $SelectLivreur->execute();
    $Livreur=$SelectLivreur->fetch(PDO::FETCH_OBJ);

    $contenusCellules[] = new PlanningCellule($Employer->jour, $Employer->Hdebut.':'.$Employer->Mdebut.':00',$Employer->Hfin.':'.$Employer->Mfin.':00', $Livreur->couleur, "<b>".$Livreur->nom." ".$Livreur->prenom."</b>");
}

$planning = new Planning(1, 7, 0, 1440, 30, $contenusCellules);
$planning->afficherHtmlTable();
?>

</article>
</section>
</div>
</CENTER>
</body>

</html>
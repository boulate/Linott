<?php include("connexion_base.php"); 

session_start();
$idUtilisateur			=	$_SESSION['idUtilisateurs'];
//$nomUtilisateur		=	$_SESSION['login']; 

$zone = "B";

$calendrier = new DOMDocument();
//Chargement du document
$calendrier->load("http://telechargement.index-education.com/vacances.xml");
$xpath = new DOMXPath($calendrier);

$zones = $xpath->query('//academies//academie');
$tableZones = array();
foreach ($zones as $key) 
{
	$zoneActuelle = $key->getAttribute('zone');

	if (in_array("$zoneActuelle", $tableZones)) 
	{

	}
	else
	{
		$tableZones[] = "$zoneActuelle";
	}
}

foreach ($tableZones as $zone) 
{
	//Requête Xpath : On cherche les tag "zone" avec une attribut "libelle" qui à pour valeur "B" , on récupère ensuite tous les enfant "vacances"
	$vacances= $xpath->query('//zone[@libelle=\''.$zone.'\']//vacances');

	foreach($vacances as $date)
	{
	  $dateDebut	=	$date->getAttribute('debut'); // Affichage de l'attribut début
	  $dateFin		=	$date->getAttribute('fin'); // Affichage de l'attribut début
	  
	  $dateDebut	=	date("Y-m-d", strtotime($dateDebut));
	  $dateFin	=	date("Y-m-d", strtotime($dateFin));


	  //echo "$zone,$dateDebut,$dateFin <br>";

		$requete_log = "INSERT IGNORE INTO zonesVacances (`zone`, `dateDebut`, `dateFin`) VALUES ('$zone', '$dateDebut', '$dateFin')";
		echo "$requete_log <br>";
		try
		{
			$reponse_log = $bdd->query($requete_log) or die('Erreur SQL !<br>' .$requete_log. '<br>'. mysql_error());
		}
		catch(Exception $e)
		{
			// En cas d'erreur précédemment, on affiche un message et on arrête tout
			die('Erreur : '.$e->getMessage());
		}
		$reponse_log->closeCursor();



		// 	echo "L'evenement $idEvenement a bien été modifié.";



	}
}


?>
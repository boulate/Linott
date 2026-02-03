<?php 
session_start();
include("connexion_base.php"); 

$loginSession		=	$_SESSION['login'];
$idUtilisateur		=	$_GET['utilisateur'];

function tableUtilisateurs($idUtilisateur)
{
	global $bdd;

	$sql		= "SELECT * FROM Utilisateurs where idUtilisateurs = $idUtilisateur";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());

	//$i=1;
	while ($donnees = $reponse->fetch())
	{
		// Declaration des variables
		$idUtilisateur		= $donnees['idUtilisateurs'];
		$loginUtilisateur	= $donnees['login'];
		$nom			= $donnees['nom'];
		$prenom		= $donnees['prenom'];
		$nbrHeuresContrat 	= $donnees['nbrHeuresSemaine'];
	}

	echo "$idUtilisateur,$loginUtilisateur, $nom,$prenom,$nbrHeuresContrat";
}
function tableHeureSup($idUtilisateur)
{
	global $bdd;

	$sql		= "SELECT sum(nbrHeureSup) as sommeHeuresSup FROM HeureSup where Utilisateurs_idUtilisateurs = $idUtilisateur";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());

	//$i=1;
	while ($donnees = $reponse->fetch())
	{
		// Declaration des variables
		$totalHeuresSupTravaillees	= $donnees['sommeHeuresSup'];
	}

	echo ",$totalHeuresSupTravaillees";
}
function tableRachatHeures($idUtilisateur)
{
		global $bdd;
		$sommeRachatHeures = 0;

	$sql		= "SELECT sum(nbr) as sommeRachatHeures FROM RachatHeures where Utilisateurs_idUtilisateurs = $idUtilisateur and YEAR(date) = YEAR(CURDATE())";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());

	//$i=1;
	while ($donnees = $reponse->fetch())
	{
		// Declaration des variables
		$sommeRachatHeures	= $donnees['sommeRachatHeures'] + $sommeRachatHeures;
	}

	echo ",$sommeRachatHeures";
}


tableUtilisateurs($idUtilisateur);
tableHeureSup($idUtilisateur);
tableRachatHeures($idUtilisateur);

// Sortie: Id,Login,Nom,Prenom,heures.contrat,heures.sup,heures.racheteesDansAnnee
// Exemple: 1,guillaume,Boulaton,Guillaume,39.00,3.75,0

?>

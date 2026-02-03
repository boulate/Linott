<?php 
session_start();
// Permet de mettre à jour les heures sup' si une ligne est supprimée.
$from = $_GET['from'];
if ($from == "xhrMajHeuresSup")
{

	include("connexion_base.php");

		$idUtilisateur		=	$_SESSION['idUtilisateurs'];
		$nomUtilisateur		=	$_SESSION['login'];
		$date				=	$_GET['date'];
#		$heureSup		=	$_GET['heureSup'];
		$total_journee		=	$_GET['total_journee'];
		$heureContrat		=	$_SESSION['nbr_heures'];

		//echo "*** Iduser: $idUtilisateur, nomuser: $nomUtilisateur, date: $date, heuresup: $heureSup, total_journée: $total_journee, heures contrat: $heureContrat *** ";
		majHeuresSup("xhrMajHeuresSup");
}




function majHeuresSup($page)
{
	//echo "je rentre dans maj heures sup.";
	global $bdd;
	$nombrePeriodesSemaine = 5;


if ( ($page == "valider_fiche") || ($page == "xhrMajHeuresSup") )
{
	global $idUtilisateur;
	global $nomUtilisateur;
	global $date;
	global $heureSup;
	global $heuresSupToSuppr;
	global $total_journee;
	global $heureContrat;
}

if ( $page == "renseigner_automatiquement_conge")
{
	global $idUtilisateurConge;
	global $loginUtilisateurConge;
	global $dateConge;
	global $totalJournee;
	global $nbrHeuresSemaine;
	global $periodeConge;

	if ( ($periodeConge == "MA") || ($periodeConge == "AM") )
	{
		//echo "je rentre dans le si periode conge.";
		$nombrePeriodesSemaine = 10;
	}

	$idUtilisateur = $idUtilisateurConge;
	$nomUtilisateur = $loginUtilisateurConge;
	$date 			= $dateConge;
	$heureSup 		= "";
	$heuresSupToSuppr = $totalJournee;
	// C'est une récup. Au niveau des heures sup' elle doit agir comme si le total de la journée était de 0 pour y soustraire les heures posées.
	$total_journee = $nbrHeuresSemaine/$nombrePeriodesSemaine;
	$heureContrat = $nbrHeuresSemaine;

}

if ( $page == "supprimer_automatiquement_conge")
{
	global $idUtilisateurConge;
	global $loginUtilisateurConge;
	global $dateConge;
	global $totalJournee;
	global $nbrHeuresSemaine;
	global $periodeConge;

	if ( ($periodeConge == "MA") || ($periodeConge == "AM") )
	{
		//echo "je rentre dans le si periode conge.";
		$nombrePeriodesSemaine = 10;
	}

	$idUtilisateur = $idUtilisateurConge;
	$nomUtilisateur = $loginUtilisateurConge;
	$date 			= $dateConge;
	$heureSup 		= "";
	$heuresSupToSuppr = $totalJournee;
	// C'est une récup. Au niveau des heures sup' elle doit agir comme si le total de la journée était de 0 pour y soustraire les heures posées.
	$total_journee = ( $nbrHeuresSemaine / $nombrePeriodesSemaine );
	
	$heureContrat = $nbrHeuresSemaine;

}

	// Je défini heureSup, attention à bien gérer les samedi et dimanche où il ne doit pas y avoir: " - ($heureContrat/$nombrePeriodesSemaine) "
	
	// On défini "jour de la semaine". strtotime permet de convertir notre date en "time" (secondes depuis la création d'Unix pour la function date.
	$jourDeLaSemaine = date('N', strtotime($date)); 
	if ( ($jourDeLaSemaine == 1) || ($jourDeLaSemaine == 2) || ($jourDeLaSemaine == 3) || ($jourDeLaSemaine == 4) || ($jourDeLaSemaine == 5) )
	{
		$heureSup = ( $total_journee - ($heureContrat/$nombrePeriodesSemaine) - $heuresSupToSuppr );	
	}
	if ( ($jourDeLaSemaine == 6) || ($jourDeLaSemaine == 7) )
	{
		$heureSup = ( $total_journee - $heuresSupToSuppr );	
	}



	//echo "*** Iduser: $idUtilisateur, nomuser: $nomUtilisateur, date: $date, heuresup: $heureSup, heureSupToSupr= $heuresSupToSuppr, total_journée= $total_journee, heures contrat: $heureContrat *** ";
	$ancien_nbr_heures_sup=0;
	$ancien_total_journee=0;
	// Si il y a déjà un calcul d'heure sup en base pour cette date, on le supprime pour ensuite le mettre à jour.
	$sql= " SELECT * FROM HeureSup WHERE Utilisateurs_idUtilisateurs = '$idUtilisateur' and date = '$date'";
	//echo " SELECT * FROM HeureSup WHERE Utilisateurs_idUtilisateurs = '$idUtilisateur' and date = '$date'";
	try
	{
		$reponse = $bdd->query($sql) or die('Erreur SQL !<br>' .$sql. '<br>'. mysql_error());
	}
	catch(Exception $e)
	{
		// En cas d'erreur précédemment, on affiche un message et on arrête tout
		die('Erreur : '.$e->getMessage());
	}
	$nbrReponse=0;
	while ($donnees = $reponse->fetch())
	{
		$nbrReponse++;
		$ancien_nbr_heures_sup=$donnees['nbrHeureSup'];
		$ancien_total_journee=$donnees['totalJournee'];
	}
	$reponse->closeCursor(); // Termine le traitement de la requête


	if ($nbrReponse > 0)
	{
		$sql= " DELETE FROM HeureSup WHERE Utilisateurs_idUtilisateurs = '$idUtilisateur' and date = '$date'";

		try
		{
			$reponse = $bdd->query($sql) or die('Erreur SQL !<br>' .$sql. '<br>'. mysql_error());
		}
		catch(Exception $e)
		{
			// En cas d'erreur précédemment, on affiche un message et on arrête tout
			die('Erreur : '.$e->getMessage());
		}								
	}


	if ( $page == "supprimer_automatiquement_conge")
	{
		//$heureSup 		= ( $ancien_nbr_heures_sup + $total_journee ) ;
		$total_journee  = ( $ancien_total_journee - $total_journee ) ;
	}
	if ($total_journee != 0)
	{
		$sql= "	INSERT INTO HeureSup (Utilisateurs_idUtilisateurs, Utilisateurs_login, date, nbrHeureSup, totalJournee) 
		VALUES 		('$idUtilisateur', '$nomUtilisateur', '$date', '$heureSup', '$total_journee')";

		//echo "$sql";

		try
		{
			$reponse = $bdd->query($sql) or die('Erreur SQL !<br>' .$sql. '<br>'. mysql_error());
		}
		catch(Exception $e)
		{
			// En cas d'erreur précédemment, on affiche un message et on arrête tout
			die('Erreur : '.$e->getMessage());
		}
	}

}

?>

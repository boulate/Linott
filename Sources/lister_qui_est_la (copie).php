<?php include("connexion_base.php"); 
session_start();

//include("checkAdmin.php");

//echo "debut script --- ";
//$message="";
	$idUtilisateurs		=	$_SESSION['idUtilisateurs'];
	$login				=	$_SESSION['login'];
	$groupes 			=	$_SESSION['groupes'];
	$mois				=	$_GET['mois'];
	$annee				=	$_GET['annee'];

	$tableIdGroupeSession		=	explode(",", $groupes);
	//print_r("groupes du l'utilisateur: $tableIdGroupeSession -----");
	//echo "groupes: $groupes --- ";

	// On selectionne d'abord les évenements
	$requete_select = 	"	SELECT distinct(idUtilisateurs) as idUtilisateurs, nom, prenom, date, id_utilisateurs_concernes, id_groupes_concernes 
								FROM Utilisateurs JOIN CalendrierConges
								WHERE active = 1
								AND type = 'absence'
								AND idUtilisateurs 
									NOT IN 	(
										SELECT Utilisateurs_idUtilisateurs FROM `CalendrierConges`
											WHERE MONTH(date) = 4 
											)
								ORDER BY date, nom
						";
	echo "<br />!!! $requete_select";
	try
	{
		$reponse_select = $bdd->query($requete_select) or die('Erreur SQL !<br>' .$requete_select. '<br>'. mysql_error());
	}
	catch(Exception $e)
	{
		// En cas d'erreur précédemment, on affiche un message et on arrête tout
		die('Erreur : '.$e->getMessage());
	}
	$tableIdPresent = array();
	while ($donnees = $reponse_select->fetch())
  	{
	 	$idUtilisateurBDD 				= $donnees['idUtilisateurs'];
	 	$nom						 	= $donnees['nom'];
	 	$prenom				 			= $donnees['prenom'];
	 	$date							= $donnees['date'];
	 	$listeUtilisateursConcernes 	= $donnees['id_utilisateurs_concernes'];
	 	$listeGroupesConcernes 			= $donnees['id_groupes_concernes'];

	 	$chaineInfo						= "$prenom;$nom;$date";

	 	// Si l'idUtilisateur de la session est différent de l'id en BDD lié à l'évènement, on le rajoute a la tableIdPresent
	 	if ($idUtilisateurs != $idUtilisateurBDD)
	 	{
			array_push($tableIdPresent, $chaineInfo);
		}
		
		// Pour chaque ID non présent dans la liste des personnes concernées, on ajoute à la tableIdPresent
		$tableIdUtilisateurs = explode(",", $listeUtilisateursConcernes);
		foreach ($tableIdUtilisateurs as $idTable)
		{
			if ($idUtilisateurBDD != $idUtilisateurs)
			{
				//echo "<br />push $chaineInfo";
				array_push($tableIdPresent, $chaineInfo);
			}
		}
		
		// On va lister les groupes présents à l'évènement, puis interroger la base sur chaque IDutilisateur de ces groupes. 
		$tableIdGroupesEvent = explode(",", $listeGroupesConcernes);
		// Pour chaque groupe lié à l'évenement
		$tableIdUserDansGroupe = array();
		foreach ($tableIdGroupesEvent as $idTable)
		{
			echo "<br>idTable = $idTable<br>";
			// On va chercher les ID concernés
			if ($idTable == "ALL")
			{
				$requete_groupes = 	"SELECT idUtilisateurs FROM Groupes";		
			}
			else if ($idTable != "")
			{
				$requete_groupes = "SELECT idUtilisateurs FROM Groupes WHERE id = $idTable";
			}
			
			if ($idTable != "")
			{
				echo " !!! $requete_groupes";
				try
				{
					$reponse_groupe = $bdd->query($requete_groupes) or die('Erreur SQL !<br>' .$requete_groupes. '<br>'. mysql_error());
				}
				catch(Exception $e)
				{
					// En cas d'erreur précédemment, on affiche un message et on arrête tout
					die('Erreur : '.$e->getMessage());
				}
				$tableIdPresent = array();
				while ($donnees = $reponse_groupe->fetch())
			  	{
				 	$idUtilisateursGroupe 				= $donnees['idUtilisateurs'];
				 	array_push($tableIdUserDansGroupe, $idUtilisateursGroupe);
				}
				$reponse_groupe->closeCursor();

				$ListeIdUtilisateursGroupe = implode(",", $idUtilisateursGroupe);
			}
		}
		echo "toto : $ListeIdUtilisateursGroupe";

	}
	$reponse_select->closeCursor();
	//print_r($tableIdPresent);

	$listeChaineInfo = implode(",", $tableIdPresent);
	//echo "$listeChaineInfo";

?>
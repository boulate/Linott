<?php include("connexion_base.php"); 
session_start();


///////////////////////////////////////////
// Comment marche ce script:
// - Je prends toutes les dates du mois
// - Pour chaque date je prends tous les événements
// - Pour chaque événement je prends les ID des personnes et groupes concernés (je sors les ID utilisateurs des groupes)
// - Je selectionne ensuite tous les utilisateurs qui ne SONT PAS dans cette liste finale.


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


	// On prend la liste des jours contenant des congés
	$requete_jour = "SELECT cdate FROM Calendar WHERE MONTH(cdate) = $mois and YEAR(cdate) = $annee AND DAYOFWEEK(cdate) <> 1 AND DAYOFWEEK(cdate) <> 7 ORDER BY cdate";
	//echo "<br />$requete_jour";
	try
	{
		$reponse_jour = $bdd->query($requete_jour) or die('Erreur SQL !<br>' .$requete_jour. '<br>'. mysql_error());
	}
	catch(Exception $e)
	{
		// En cas d'erreur précédemment, on affiche un message et on arrête tout
		die('Erreur : '.$e->getMessage());
	}
	while ($donnees_jours = $reponse_jour->fetch())
  	{
  		$dateJour = $donnees_jours['cdate'];
		$tableUtilisateurPrisACetteDate = array();
		$tableUtilisateurPrisACetteDateMatin = array();
		$tableUtilisateurPrisACetteDateAprem = array();


		// On selectionne ensuite les évenements du jour concerné
		$requete_event = 	"	SELECT utilisateurs_idUtilisateurs, periode, id_utilisateurs_concernes, id_groupes_concernes
								FROM CalendrierConges
								WHERE date = '$dateJour' AND ( (type = 'absence' AND valide = 'V') OR (type = 'event' AND indisponible = '1') )
							";
		//echo "<br>$requete_event";
		try
		{
			$reponse_event = $bdd->query($requete_event) or die('Erreur SQL !<br>' .$requete_event. '<br>'. mysql_error());
		}
		catch(Exception $e)
		{
			// En cas d'erreur précédemment, on affiche un message et on arrête tout
			die('Erreur : '.$e->getMessage());
		}
		$tableListeUtilisateursConcernes = array();
		while ($donnees = $reponse_event->fetch())
	  	{

		 	$idUtilisateurBDD 				= $donnees['utilisateurs_idUtilisateurs'];
		 	$periode						= $donnees['periode'];
		 	$listeUtilisateursConcernes 	= $donnees['id_utilisateurs_concernes'];
		 	$listeGroupesConcernes 			= $donnees['id_groupes_concernes'];

		 	//echo "--- $dateJour , $idUtilisateurBDD , $periode , $listeUtilisateursConcernes , $listeGroupesConcernes ";

		 	$listeUtilisateursGroupes = "";
		 	if ($listeGroupesConcernes != "")
		 	{
		 		// Si la liste des groupes contient "ALL" on prend tous les groupes et on les mets dans la variable
		 		if ($listeGroupesConcernes == "ALL")
		 		{
			 		$select_groupes_ALL = "SELECT id FROM Groupes";
				 	//echo "<br> $select_groupes_ALL";
					try
					{
						$reponse_groupes_ALL = $bdd->query($select_groupes_ALL) or die('Erreur SQL !<br>' .$select_groupes_ALL. '<br>'. mysql_error());
					}
					catch(Exception $e)
					{
						// En cas d'erreur précédemment, on affiche un message et on arrête tout
						die('Erreur : '.$e->getMessage());
					}
					$tableTousIdGroupes = array();
					while ($donnees_groupes = $reponse_groupes_ALL->fetch())
				  	{
				  		$idGroupe = $donnees_groupes['id'];
				  		
				  		array_push($tableTousIdGroupes, $idGroupe);


				  	}
				  	$reponse_groupes_ALL->closeCursor();

				  	$listeGroupesConcernes = implode(",", $tableTousIdGroupes);
		 		}



			 	$select_groupes = "SELECT idUtilisateurs FROM Groupes WHERE id IN ($listeGroupesConcernes)";
			 	//echo "<br>$select_groupes";
				try
				{
					$reponse_groupes = $bdd->query($select_groupes) or die('Erreur SQL !<br>' .$select_groupes. '<br>'. mysql_error());
				}
				catch(Exception $e)
				{
					// En cas d'erreur précédemment, on affiche un message et on arrête tout
					die('Erreur : '.$e->getMessage());
				}
				$tableIdUsersGroupe = array();
				while ($donnees_groupes = $reponse_groupes->fetch())
			  	{
			  		$idUsersGroupe = $donnees_groupes['idUtilisateurs'];

			  		if ($idUsersGroupe != "")
			  		{
			  			array_push($tableIdUsersGroupe, $idUsersGroupe);
			  		}

			  	}
			  	$reponse_groupes->closeCursor();

				$listeUtilisateursGroupes = implode(",", $tableIdUsersGroupe);
			}

	 		// Si la liste des utilisateurs contient "ALL" on prend tous les utilisateurs actifs et on les mets dans la variable
	 		if ($listeUtilisateursConcernes == "ALL")
	 		{
		 		$select_utilisateurs_ALL = "SELECT idUtilisateurs FROM Utilisateurs WHERE active = 1";
			 	//echo "<br> $select_utilisateurs_ALL";
				try
				{
					$reponse_utilisateurs_ALL = $bdd->query($select_utilisateurs_ALL) or die('Erreur SQL !<br>' .$select_utilisateurs_ALL. '<br>'. mysql_error());
				}
				catch(Exception $e)
				{
					// En cas d'erreur précédemment, on affiche un message et on arrête tout
					die('Erreur : '.$e->getMessage());
				}
				$tableTousIdUtilisateurs = array();
				while ($donnees_groupes = $reponse_utilisateurs_ALL->fetch())
			  	{
			  		$idUtilisateursAll = $donnees_groupes['idUtilisateurs'];
			  		
			  		array_push($tableTousIdUtilisateurs, $idUtilisateursAll);
			  	}
			  	$reponse_utilisateurs_ALL->closeCursor();

			  	$listeUtilisateursConcernes = implode(",", $tableTousIdUtilisateurs);
	 		}





			array_push($tableListeUtilisateursConcernes, $idUtilisateurBDD);
	
			if ($listeUtilisateursConcernes != "")
			{
				array_push($tableListeUtilisateursConcernes, $listeUtilisateursConcernes);
			}
			if ($listeUtilisateursGroupes != "")
			{
				array_push($tableListeUtilisateursConcernes, $listeUtilisateursGroupes);
			}

			$listeTousIdUtilisateurs = implode(",", $tableListeUtilisateursConcernes);
			//echo "--- $dateJour , $listeTousIdUtilisateurs";
			

			// Selon si la période est Matin, AM ou journée, je push dans la table correspondante la liste des utilisateurs concernés par l'event.
			if ($periode == "JO")
			{
				array_push(	$tableUtilisateurPrisACetteDate , "$listeTousIdUtilisateurs");
			}
			if ($periode == "MA")
			{
				array_push(	$tableUtilisateurPrisACetteDateMatin , "$listeTousIdUtilisateurs");
			}
			if ($periode == "AM")
			{
				array_push(	$tableUtilisateurPrisACetteDateAprem , "$listeTousIdUtilisateurs");				
			}


			//echo "liste ID: $listeTousIdUtilisateurs";
		}
		$reponse_event->closeCursor();

	$listeTousIdUtilisateursPrisDate 	  = implode(",", $tableUtilisateurPrisACetteDate);
	$listeTousIdUtilisateursPrisDateMatin = implode(",", $tableUtilisateurPrisACetteDateMatin);
	$listeTousIdUtilisateursPrisDateAprem = implode(",", $tableUtilisateurPrisACetteDateAprem);

	//echo "<br>liste de tous les utilisateurs pris en cette date du $dateJour: $listeTousIdUtilisateursPrisDate, le matin: $listeTousIdUtilisateursPrisDateMatin , l'aprem: $listeTousIdUtilisateursPrisDateAprem";

	$periodeUtilisateurOccupe = "JO";
	

	//echo "<br>--- Liste de tous les utilisateurs pris a cette date du $dateJour: $listeTousIdUtilisateursPrisDate --- $listeTousIdUtilisateursPrisDateMatin --- $listeTousIdUtilisateursPrisDateAprem ---<br>";
	if ($listeTousIdUtilisateursPrisDate != "")
	{
		$requete_utilisateurs_non_pris = "SELECT idUtilisateurs, login, nom, prenom, couleur FROM Utilisateurs WHERE idUtilisateurs IN ($listeTousIdUtilisateursPrisDate) AND active = 1 ORDER BY nom";
		$periodeUtilisateurOccupe = "JO";
		//echo "<br />requete_utilisateurs_non_pris: $requete_utilisateurs_non_pris";

		try
		{
			$reponse_utilisateurs_non_pris = $bdd->query($requete_utilisateurs_non_pris) or die('Erreur SQL !<br>' .$requete_utilisateurs_non_pris. '<br>'. mysql_error());
		}
		catch(Exception $e)
		{
			// En cas d'erreur précédemment, on affiche un message et on arrête tout
			die('Erreur : '.$e->getMessage());
		}
		$tableUtilisateursDispo = array();
		while ($donnees_dispo = $reponse_utilisateurs_non_pris->fetch())
	  	{
	  		$idUtilisateurDispo = $donnees_dispo['idUtilisateurs'];
	  		$loginUtilisateurDispo = $donnees_dispo['login'];	  		
	  		$nomUtilisateurDispo = $donnees_dispo['nom'];
	  		$prenomUtilisateurDispo = $donnees_dispo['prenom'];
	  		$couleurUtilisateurDispo = $donnees_dispo['couleur'];

	  		echo "$dateJour;$periodeUtilisateurOccupe;$idUtilisateurDispo;$loginUtilisateurDispo;$nomUtilisateurDispo;$prenomUtilisateurDispo;$couleurUtilisateurDispo///";
	  	}
	  	$reponse_utilisateurs_non_pris->closeCursor();

	}



	if ($listeTousIdUtilisateursPrisDateMatin != "")
	{
		$requete_utilisateurs_non_pris = "SELECT idUtilisateurs, login, nom, prenom, couleur FROM Utilisateurs WHERE idUtilisateurs IN ($listeTousIdUtilisateursPrisDateMatin) AND active = 1 ORDER BY nom";
		//echo "<br />requete_utilisateurs_non_pris: $requete_utilisateurs_non_pris";
	 	
	 	// Essai pour eviter le IF dans la while ci dessous
	 	$periodeUtilisateurOccupe = "MA";

		try
		{
			$reponse_utilisateurs_non_pris = $bdd->query($requete_utilisateurs_non_pris) or die('Erreur SQL !<br>' .$requete_utilisateurs_non_pris. '<br>'. mysql_error());
		}
		catch(Exception $e)
		{
			// En cas d'erreur précédemment, on affiche un message et on arrête tout
			die('Erreur : '.$e->getMessage());
		}
		$tableUtilisateursDispo = array();
		while ($donnees_dispo = $reponse_utilisateurs_non_pris->fetch())
	  	{
	  		$idUtilisateurDispo = $donnees_dispo['idUtilisateurs'];
	  		$loginUtilisateurDispo = $donnees_dispo['login'];	  		
	  		$nomUtilisateurDispo = $donnees_dispo['nom'];
	  		$prenomUtilisateurDispo = $donnees_dispo['prenom'];
	  		$couleurUtilisateurDispo = $donnees_dispo['couleur'];

	  		if (in_array("$idUtilisateurDispo", $tableUtilisateurPrisACetteDateMatin))
	  		{
	 			$periodeUtilisateurOccupe = "MA";
	  		}

	  		echo "$dateJour;$periodeUtilisateurOccupe;$idUtilisateurDispo;$loginUtilisateurDispo;$nomUtilisateurDispo;$prenomUtilisateurDispo;$couleurUtilisateurDispo///";
	  	}
	  	$reponse_utilisateurs_non_pris->closeCursor();

	}


	if ($listeTousIdUtilisateursPrisDateAprem != "")
	{
		$requete_utilisateurs_non_pris = "SELECT idUtilisateurs, login, nom, prenom, couleur FROM Utilisateurs WHERE idUtilisateurs IN ($listeTousIdUtilisateursPrisDateAprem) AND active = 1 ORDER BY nom";
		//echo "<br />requete_utilisateurs_non_pris: $requete_utilisateurs_non_pris";

	 	// Essai pour eviter le IF dans la while ci dessous
	 	$periodeUtilisateurOccupe = "AM";

		try
		{
			$reponse_utilisateurs_non_pris = $bdd->query($requete_utilisateurs_non_pris) or die('Erreur SQL !<br>' .$requete_utilisateurs_non_pris. '<br>'. mysql_error());
		}
		catch(Exception $e)
		{
			// En cas d'erreur précédemment, on affiche un message et on arrête tout
			die('Erreur : '.$e->getMessage());
		}
		$tableUtilisateursDispo = array();
		while ($donnees_dispo = $reponse_utilisateurs_non_pris->fetch())
	  	{
	  		$idUtilisateurDispo = $donnees_dispo['idUtilisateurs'];
	  		$loginUtilisateurDispo = $donnees_dispo['login'];	  		
	  		$nomUtilisateurDispo = $donnees_dispo['nom'];
	  		$prenomUtilisateurDispo = $donnees_dispo['prenom'];
	  		$couleurUtilisateurDispo = $donnees_dispo['couleur'];
	  		
	  		if (in_array("$idUtilisateurDispo", $tableUtilisateurPrisACetteDateAprem))
	  		{
	 			$periodeUtilisateurOccupe = "AM";
	  		}

	  		
	  		echo "$dateJour;$periodeUtilisateurOccupe;$idUtilisateurDispo;$loginUtilisateurDispo;$nomUtilisateurDispo;$prenomUtilisateurDispo;$couleurUtilisateurDispo///";
	  	}
	  	$reponse_utilisateurs_non_pris->closeCursor();

	}

	// else
	// {
	// 	$andNotIn = "";
	// 	if ($listeTousIdUtilisateursPrisDateMatin != "")
	// 	{
	// 		$andNotIn = "AND idUtilisateurs NOT IN ($listeTousIdUtilisateursPrisDateMatin) " ;
	// 	}
	// 	if ($listeTousIdUtilisateursPrisDateAprem != "")
	// 	{
	// 		$andNotIn = $andNotIn . " AND idUtilisateurs NOT IN ($listeTousIdUtilisateursPrisDateAprem) " ;
	// 	}

	// 	$requete_utilisateurs_non_pris = "SELECT idUtilisateurs, login, nom, prenom, couleur FROM Utilisateurs WHERE active = 1 $andNotIn";		

	// 	$periodeUtilisateurOccupe = "JO";
	// 	//echo "<br />requete_utilisateurs_non_pris: $requete_utilisateurs_non_pris";

	// 	try
	// 	{
	// 		$reponse_utilisateurs_non_pris = $bdd->query($requete_utilisateurs_non_pris) or die('Erreur SQL !<br>' .$requete_utilisateurs_non_pris. '<br>'. mysql_error());
	// 	}
	// 	catch(Exception $e)
	// 	{
	// 		// En cas d'erreur précédemment, on affiche un message et on arrête tout
	// 		die('Erreur : '.$e->getMessage());
	// 	}
	// 	$tableUtilisateursDispo = array();
	// 	while ($donnees_dispo = $reponse_utilisateurs_non_pris->fetch())
	//   	{
	//   		$idUtilisateurDispo = $donnees_dispo['idUtilisateurs'];
	//   		$loginUtilisateurDispo = $donnees_dispo['login'];	  		
	//   		$nomUtilisateurDispo = $donnees_dispo['nom'];
	//   		$prenomUtilisateurDispo = $donnees_dispo['prenom'];
	//   		$couleurUtilisateurDispo = $donnees_dispo['couleur'];

	//   		echo "$dateJour;$periodeUtilisateurOccupe;$idUtilisateurDispo;$loginUtilisateurDispo;$nomUtilisateurDispo;$prenomUtilisateurDispo;$couleurUtilisateurDispo///";
	//   	}
	//   	$reponse_utilisateurs_non_pris->closeCursor();
	// }
	}
	$reponse_jour->closeCursor();









?>
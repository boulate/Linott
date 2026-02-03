<?php 
session_start();
include("connexion_base.php"); 

$loginSession		=	$_SESSION['login'];
$idUtilisateur		=	$_SESSION['idUtilisateurs'];

//require("checkAdmin.php");

	///////////////////////////////////////////   ///////////////////////////////////////////
	$sql		= "SELECT valeur FROM Configuration where nom like 'mois_depart_annee_conge'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		// Declaration des variables
		$moisDepartAnneeConge		= $donnees['valeur'];
	}
	$reponse->closeCursor();


	///////////////////////////////////////////   ///////////////////////////////////////////
	$sql		= "SELECT valeur FROM Configuration where nom like 'nombre_jours_conges'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		// Declaration des variables
		$nbrJoursConges		= $donnees['valeur'];
	}
	$reponse->closeCursor();

	
	///////////////////////////////////////////   ///////////////////////////////////////////
	$sql		= "SELECT valeur FROM Configuration where nom like 'periode_gestion_RTT'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		// Declaration des variables
		$typeGestionRTT		= $donnees['valeur'];
	}
	$reponse->closeCursor();


	/////////////////////////////////////////// GESTION DU NOMBRE DE JOURS DE RTT  ///////////////////////////////////////////
	$sql		= "SELECT valeur FROM Configuration where nom like 'nombre_jours_RTT'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		// Declaration des variables
		$nbrJoursRTT		= $donnees['valeur'];
	}
	$reponse->closeCursor();

	
	///////////////////////////////////////////   ///////////////////////////////////////////
	$sql		= "SELECT valeur FROM Configuration where nom like 'mois_depart_annee_RTT'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		// Declaration des variables
		$moisDepartAnneeRTT		= $donnees['valeur'];
	}
	$reponse->closeCursor();
	
	
	///////////////////////////////////////////   ///////////////////////////////////////////
	$sql		= "SELECT valeur FROM Configuration where nom like 'mois_depart_decompte_heures'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		// Declaration des variables
		$moisDepartDecompteHeures		= $donnees['valeur'];
	}
	$reponse->closeCursor();


	///////////////////////////////////////////   ///////////////////////////////////////////
	$sql		= "SELECT valeur FROM Configuration where nom like 'activer_axe3'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		// Declaration des variables
		$activerAxe3		= $donnees['valeur'];

		if ( $activerAxe3 == 1 )
		{
			$activerAxe3 = "checked";
		}
		else
		{
			$activerAxe3 = "";
		}
	}
	$reponse->closeCursor();


	///////////////////////////////////////////   ///////////////////////////////////////////
	$sql		= "SELECT valeur FROM Configuration where nom like 'afficher_heures_sup'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		// Declaration des variables
		$afficherHeuresSup		= $donnees['valeur'];

		if ( $afficherHeuresSup == 1 )
		{
			$afficherHeuresSup = "checked";
		}
		else
		{
			$afficherHeuresSup = "";
		}
	}
	$reponse->closeCursor();


	///////////////////////////////////////////   ///////////////////////////////////////////
	$sql		= "SELECT valeur FROM Configuration where nom like 'afficher_codes_comptables_selection_axes'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		// Declaration des variables
		$afficherCodesComptablesSelectionAxes		= $donnees['valeur'];

		if ( $afficherCodesComptablesSelectionAxes == 1 )
		{
			$afficherCodesComptablesSelectionAxes = "checked";
		}
		else
		{
			$afficherCodesComptablesSelectionAxes = "";
		}
	}
	$reponse->closeCursor();


	///////////////////////////////////////////   ///////////////////////////////////////////
	$sql		= "SELECT valeur FROM Configuration where nom like 'afficher_codes_comptables_recapitulatif'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		// Declaration des variables
		$afficherCodesComptablesRecap		= $donnees['valeur'];

		if ( $afficherCodesComptablesRecap == 1 )
		{
			$afficherCodesComptablesRecap = "checked";
		}
		else
		{
			$afficherCodesComptablesRecap = "";
		}
	}
	$reponse->closeCursor();


	///////////////////////////////////////////   ///////////////////////////////////////////
	$sql		= "SELECT valeur FROM Configuration where nom like 'afficher_RTT'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		// Declaration des variables
		$afficherRTT		= $donnees['valeur'];

		if ( $afficherRTT == 1 )
		{
			$afficherRTT = "checked";
		}
		else
		{
			$afficherRTT = "";
		}
	}
	$reponse->closeCursor();


	///////////////////////////////////////////   ///////////////////////////////////////////
	$sql		= "SELECT valeur FROM Configuration where nom like 'afficher_conges'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		// Declaration des variables
		$afficherConges		= $donnees['valeur'];

		if ( $afficherConges == 1 )
		{
			$afficherConges = "checked";
		}
		else
		{
			$afficherConges = "";
		}
	}
	$reponse->closeCursor();


	///////////////////////////////////////////   ///////////////////////////////////////////
	$sql		= "SELECT valeur FROM Configuration where nom like 'axe2_exclus_totaux'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		// Declaration des variables
		$axe2_exclus_totaux		= $donnees['valeur'];
	}
	$reponse->closeCursor();


	///////////////////////////////////////////   ///////////////////////////////////////////	
	$sql		= "SELECT valeur FROM Configuration where nom like 'afficher_raccourcis_absences'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		// Declaration des variables
		$afficherRaccourcisAbsences		= $donnees['valeur'];

		if ( $afficherRaccourcisAbsences == 1 )
		{
			$afficherRaccourcisAbsences = "checked";
		}
		else
		{
			$afficherRaccourcisAbsences = "";
		}
	}
	$reponse->closeCursor();
	

	///////////////////////////////////////////   ///////////////////////////////////////////	
	$sql		= "SELECT valeur FROM Configuration where nom like 'afficher_jours_types'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		// Declaration des variables
		$afficherJoursTypes		= $donnees['valeur'];

		if ( $afficherJoursTypes == 1 )
		{
			$afficherJoursTypes = "checked";
		}
		else
		{
			$afficherJoursTypes = "";
		}
	}
	$reponse->closeCursor();


	///////////////////////////////////////////   ///////////////////////////////////////////
	$sql		= "SELECT valeur FROM Configuration where nom like 'zone_vacances'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		// Declaration des variables
		$zoneVacancesConfig		= $donnees['valeur'];
	}
	$reponse->closeCursor();


	///////////////////////////////////////////   ///////////////////////////////////////////
	$sql		= "SELECT valeur FROM Configuration where nom like 'afficher_calcul_rapide_journee'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		// Declaration des variables
		$afficherCalculRapideJournee		= $donnees['valeur'];

		if ( $afficherCalculRapideJournee == 1 )
		{
			$afficherCalculRapideJournee = "checked";
		}
		else
		{
			$afficherCalculRapideJournee = "";
		}
	}
	$reponse->closeCursor();
	
	
	///////////////////////////////////////////   ///////////////////////////////////////////
	$sql		= "SELECT valeur FROM Configuration where nom like 'afficher_total_annuel'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		// Declaration des variables
		$afficherTotalAnnuel		= $donnees['valeur'];

		if ( $afficherTotalAnnuel == 1 )
		{
			$afficherTotalAnnuel = "checked";
		}
		else
		{
			$afficherTotalAnnuel = "";
		}
	}
	$reponse->closeCursor();


	///////////////////////////////////////////   ///////////////////////////////////////////
	$sql		= "SELECT valeur FROM Configuration where nom like 'renseigner_automatiquement_conge_valide'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		// Declaration des variables
		$renseignerAutomatiquementCongeValide		= $donnees['valeur'];

		if ( $renseignerAutomatiquementCongeValide == 1 )
		{
			$renseignerAutomatiquementCongeValide = "checked";
		}
		else
		{
			$renseignerAutomatiquementCongeValide = "";
		}
	}
	$reponse->closeCursor();


	///////////////////////////////////////////   ///////////////////////////////////////////
	$sql		= "SELECT valeur FROM Configuration where nom like 'autoriser_admin_suppr_event'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		// Declaration des variables
		$autoriserAdminSupprEvent		= $donnees['valeur'];

		if ( $autoriserAdminSupprEvent == 1 )
		{
			$autoriserAdminSupprEvent = "checked";
		}
		else
		{
			$autoriserAdminSupprEvent = "";
		}
	}
	$reponse->closeCursor();



	?>
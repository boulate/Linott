<?php include("connexion_base.php"); 
session_start();

require("checkAdmin.php");
// On vérifie que "password" repect le format souhaité.
require("verifier_input_php.php");

	$idSession				=	$_SESSION['idUtilisateurs'];
	$login_session			=	$_SESSION['login'];
	$moisDepartAnneeConge		=	$_GET['moisDepartAnneeConge'];
	$nbrJoursConges			=	$_GET['nbrJoursConges'];
	$typeGestionRTT			=	$_GET['typeGestionRTT'];
	$nbrJoursRTT			=	$_GET['nbrJoursRTT'];
	$activerAxe3			=	$_GET['activerAxe3'];
	$afficherHeuresSup 		=	$_GET['afficherHeuresSup'];
	$afficherCodesComptablesRecap 			= $_GET['afficherCodesComptablesRecap'];
	$afficherCodesComptablesSelectionAxes 	= $_GET['afficherCodesComptablesSelectionAxes'];
	$afficherConges 						= $_GET['afficherConges'];
	$afficherRTT 			=	$_GET['afficherRTT'];
	$afficherCalculRapideJournee = $_GET['afficherCalculRapideJournee'];
	$afficherTotalAnnuel	=	$_GET['afficherTotalAnnuel'];
	$axe2_exclus_totaux		=	$_GET['axe2_exclus_totaux'];
	$afficherRaccourcisAbsences	=	$_GET['afficherRaccourcisAbsences'];
	$afficherJoursTypes		=	$_GET['afficherJoursTypes'];
	$moisDepartAnneeRTT		=	$_GET['moisDepartAnneeRTT'];
	$moisDepartDecompteHeures = $_GET['moisDepartDecompteHeures'];
	$zoneVacances			=	$_GET['zoneVacances'];
	$renseignerAutomatiquementCongeValide	=	$_GET['renseignerAutomatiquementCongeValide'];
	$autoriserAdminSupprEvent = $_GET['autoriserAdminSupprEvent'];



if (checkInput($moisDepartAnneeConge, "Mois") != "ok")
{
	echo checkInput($moisDepartAnneeConge, "Mois");
	exit;
}

if (checkInput($nbrJoursConges, "nbrJoursConges") != "ok")
{
	echo checkInput($nbrJoursConges, "nbrJoursConges");
	exit;
}

if (checkInput($moisDepartAnneeRTT, "Mois") != "ok")
{
	echo checkInput($moisDepartAnneeRTT, "Mois");
	exit;
}

if (checkInput($moisDepartDecompteHeures, "Mois") != "ok")
{
	echo checkInput($moisDepartDecompteHeures, "Mois");
	exit;
}

if (checkInput($typeGestionRTT) != "ok")
{
	echo checkInput($typeGestionRTT);
	exit;
}

if (checkInput($nbrJoursRTT, "nbrJoursRTT") != "ok")
{
	echo checkInput($nbrJoursRTT, "nbrJoursRTT");
	exit;
}


if (checkInput($activerAxe3, "caseACocher") != "ok")
{
	echo checkInput($activerAxe3, "caseACocher");
	exit;
}


if (checkInput($afficherHeuresSup, "caseACocher") != "ok")
{
	echo checkInput($afficherHeuresSup, "caseACocher");
	exit;
}

if (checkInput($afficherCodesComptablesRecap, "caseACocher") != "ok")
{
	echo checkInput($afficherCodesComptablesRecap, "caseACocher");
	exit;
}

if (checkInput($afficherCodesComptablesSelectionAxes, "caseACocher") != "ok")
{
	echo checkInput($afficherCodesComptablesSelectionAxes, "caseACocher");
	exit;
}

if (checkInput($afficherConges, "caseACocher") != "ok")
{
	echo checkInput($afficherConges, "caseACocher");
	exit;
}

if (checkInput($afficherRTT, "caseACocher") != "ok")
{
	echo checkInput($afficherRTT, "caseACocher");
	exit;
}

$axe2_exclus_totaux = str_replace(' ','',$axe2_exclus_totaux);
if (checkInput($axe2_exclus_totaux, "axe2_exclus_totaux") != "ok")
{
	echo checkInput($axe2_exclus_totaux, "axe2_exclus_totaux");
	exit;
}

if (checkInput($afficherRaccourcisAbsences, "caseACocher") != "ok")
{
	echo checkInput($afficherRaccourcisAbsences, "caseACocher");
	exit;
}

if (checkInput($afficherJoursTypes, "caseACocher") != "ok")
{
	echo checkInput($afficherJoursTypes, "caseACocher");
	exit;
}

if (checkInput($afficherCalculRapideJournee, "caseACocher") != "ok")
{
	echo checkInput($afficherCalculRapideJournee, "caseACocher");
	exit;
}

if (checkInput($afficherTotalAnnuel, "caseACocher") != "ok")
{
	echo checkInput($afficherTotalAnnuel, "caseACocher");
	exit;
}


if (checkInput($renseignerAutomatiquementCongeValide, "caseACocher") != "ok")
{
	echo checkInput($renseignerAutomatiquementCongeValide, "caseACocher");
	exit;
}


if (checkInput($autoriserAdminSupprEvent, "caseACocher") != "ok")
{
	echo checkInput($autoriserAdminSupprEvent, "caseACocher");
	exit;
}

if (checkInput($zoneVacances) != "ok")
{
	echo checkInput($zoneVacances);
	exit;
}

			$requete_modification = "UPDATE Configuration SET valeur = $moisDepartAnneeConge WHERE nom like 'mois_depart_annee_conge' ";
			//echo "$requete_modification";
			try
			{
				$reponse_modification = $bdd->query($requete_modification) or die('Erreur SQL !<br>' .$requete_modification. '<br>'. mysql_error());
			}
			catch(Exception $e)
			{
				// En cas d'erreur précédemment, on affiche un message et on arrête tout
				die('Erreur : '.$e->getMessage());
			}
			$reponse_modification->closeCursor();




			$requete_modification = "UPDATE Configuration SET valeur = $nbrJoursConges WHERE nom like 'nombre_jours_conges' ";
			//echo "$requete_modification";
			try
			{
				$reponse_modification = $bdd->query($requete_modification) or die('Erreur SQL !<br>' .$requete_modification. '<br>'. mysql_error());
			}
			catch(Exception $e)
			{
				// En cas d'erreur précédemment, on affiche un message et on arrête tout
				die('Erreur : '.$e->getMessage());
			}
			$reponse_modification->closeCursor();




			$requete_modification = "UPDATE Configuration SET valeur = '$typeGestionRTT' WHERE nom like 'periode_gestion_RTT' ";
			//echo "$requete_modification";
			try
			{
				$reponse_modification = $bdd->query($requete_modification) or die('Erreur SQL !<br>' .$requete_modification. '<br>'. mysql_error());
			}
			catch(Exception $e)
			{
				// En cas d'erreur précédemment, on affiche un message et on arrête tout
				die('Erreur : '.$e->getMessage());
			}
			$reponse_modification->closeCursor();




			$requete_modification = "UPDATE Configuration SET valeur = $nbrJoursRTT WHERE nom like 'nombre_jours_RTT' ";
			//echo "$requete_modification";
			try
			{
				$reponse_modification = $bdd->query($requete_modification) or die('Erreur SQL !<br>' .$requete_modification. '<br>'. mysql_error());
			}
			catch(Exception $e)
			{
				// En cas d'erreur précédemment, on affiche un message et on arrête tout
				die('Erreur : '.$e->getMessage());
			}
			$reponse_modification->closeCursor();



			$requete_modification = "UPDATE Configuration SET valeur = $activerAxe3 WHERE nom like 'activer_axe3' ";
			//echo "$requete_modification";
			try
			{
				$reponse_modification = $bdd->query($requete_modification) or die('Erreur SQL !<br>' .$requete_modification. '<br>'. mysql_error());
			}
			catch(Exception $e)
			{
				// En cas d'erreur précédemment, on affiche un message et on arrête tout
				die('Erreur : '.$e->getMessage());
			}
			$reponse_modification->closeCursor();



			$requete_modification = "UPDATE Configuration SET valeur = $afficherHeuresSup WHERE nom like 'afficher_heures_sup' ";
			//echo "$requete_modification";
			try
			{
				$reponse_modification = $bdd->query($requete_modification) or die('Erreur SQL !<br>' .$requete_modification. '<br>'. mysql_error());
			}
			catch(Exception $e)
			{
				// En cas d'erreur précédemment, on affiche un message et on arrête tout
				die('Erreur : '.$e->getMessage());
			}
			$reponse_modification->closeCursor();


			
			$requete_modification = "UPDATE Configuration SET valeur = $afficherCodesComptablesRecap WHERE nom like 'afficher_codes_comptables_recapitulatif' ";
			//echo "$requete_modification";
			try
			{
				$reponse_modification = $bdd->query($requete_modification) or die('Erreur SQL !<br>' .$requete_modification. '<br>'. mysql_error());
			}
			catch(Exception $e)
			{
				// En cas d'erreur précédemment, on affiche un message et on arrête tout
				die('Erreur : '.$e->getMessage());
			}
			$reponse_modification->closeCursor();



			$requete_modification = "UPDATE Configuration SET valeur = $afficherCodesComptablesSelectionAxes WHERE nom like 'afficher_codes_comptables_selection_axes' ";
			//echo "$requete_modification";
			try
			{
				$reponse_modification = $bdd->query($requete_modification) or die('Erreur SQL !<br>' .$requete_modification. '<br>'. mysql_error());
			}
			catch(Exception $e)
			{
				// En cas d'erreur précédemment, on affiche un message et on arrête tout
				die('Erreur : '.$e->getMessage());
			}
			$reponse_modification->closeCursor();



			$requete_modification = "UPDATE Configuration SET valeur = $afficherConges WHERE nom like 'afficher_conges' ";
			//echo "$requete_modification";
			try
			{
				$reponse_modification = $bdd->query($requete_modification) or die('Erreur SQL !<br>' .$requete_modification. '<br>'. mysql_error());
			}
			catch(Exception $e)
			{
				// En cas d'erreur précédemment, on affiche un message et on arrête tout
				die('Erreur : '.$e->getMessage());
			}
			$reponse_modification->closeCursor();



			$requete_modification = "UPDATE Configuration SET valeur = $afficherRTT WHERE nom like 'afficher_RTT' ";
			//echo "$requete_modification";
			try
			{
				$reponse_modification = $bdd->query($requete_modification) or die('Erreur SQL !<br>' .$requete_modification. '<br>'. mysql_error());
			}
			catch(Exception $e)
			{
				// En cas d'erreur précédemment, on affiche un message et on arrête tout
				die('Erreur : '.$e->getMessage());
			}
			$reponse_modification->closeCursor();



			$requete_modification = "UPDATE Configuration SET valeur = \"$axe2_exclus_totaux\" WHERE nom like 'axe2_exclus_totaux' ";
			//echo "$requete_modification";
			try
			{
				$reponse_modification = $bdd->query($requete_modification) or die('Erreur SQL !<br>' .$requete_modification. '<br>'. mysql_error());
			}
			catch(Exception $e)
			{
				// En cas d'erreur précédemment, on affiche un message et on arrête tout
				die('Erreur : '.$e->getMessage());
			}
			$reponse_modification->closeCursor();



			$requete_modification = "UPDATE Configuration SET valeur = $afficherRaccourcisAbsences WHERE nom like 'afficher_raccourcis_absences' ";
			//echo "$requete_modification";
			try
			{
				$reponse_modification = $bdd->query($requete_modification) or die('Erreur SQL !<br>' .$requete_modification. '<br>'. mysql_error());
			}
			catch(Exception $e)
			{
				// En cas d'erreur précédemment, on affiche un message et on arrête tout
				die('Erreur : '.$e->getMessage());
			}
			$reponse_modification->closeCursor();



			$requete_modification = "UPDATE Configuration SET valeur = $afficherJoursTypes WHERE nom like 'afficher_jours_types' ";
			//echo "$requete_modification";
			try
			{
				$reponse_modification = $bdd->query($requete_modification) or die('Erreur SQL !<br>' .$requete_modification. '<br>'. mysql_error());
			}
			catch(Exception $e)
			{
				// En cas d'erreur précédemment, on affiche un message et on arrête tout
				die('Erreur : '.$e->getMessage());
			}
			$reponse_modification->closeCursor();



			$requete_modification = "UPDATE Configuration SET valeur = $afficherCalculRapideJournee WHERE nom like 'afficher_calcul_rapide_journee' ";
			//echo "$requete_modification";
			try
			{
				$reponse_modification = $bdd->query($requete_modification) or die('Erreur SQL !<br>' .$requete_modification. '<br>'. mysql_error());
			}
			catch(Exception $e)
			{
				// En cas d'erreur précédemment, on affiche un message et on arrête tout
				die('Erreur : '.$e->getMessage());
			}
			$reponse_modification->closeCursor();


			$requete_modification = "UPDATE Configuration SET valeur = $afficherTotalAnnuel WHERE nom like 'afficher_total_annuel' ";
			//echo "$requete_modification";
			try
			{
				$reponse_modification = $bdd->query($requete_modification) or die('Erreur SQL !<br>' .$requete_modification. '<br>'. mysql_error());
			}
			catch(Exception $e)
			{
				// En cas d'erreur précédemment, on affiche un message et on arrête tout
				die('Erreur : '.$e->getMessage());
			}
			$reponse_modification->closeCursor();


			$requete_modification = "UPDATE Configuration SET valeur = $moisDepartAnneeRTT WHERE nom like 'mois_depart_annee_RTT' ";
			//echo "$requete_modification";
			try
			{
				$reponse_modification = $bdd->query($requete_modification) or die('Erreur SQL !<br>' .$requete_modification. '<br>'. mysql_error());
			}
			catch(Exception $e)
			{
				// En cas d'erreur précédemment, on affiche un message et on arrête tout
				die('Erreur : '.$e->getMessage());
			}
			$reponse_modification->closeCursor();



			$requete_modification = "UPDATE Configuration SET valeur = $moisDepartDecompteHeures WHERE nom like 'mois_depart_decompte_heures' ";
			//echo "$requete_modification";
			try
			{
				$reponse_modification = $bdd->query($requete_modification) or die('Erreur SQL !<br>' .$requete_modification. '<br>'. mysql_error());
			}
			catch(Exception $e)
			{
				// En cas d'erreur précédemment, on affiche un message et on arrête tout
				die('Erreur : '.$e->getMessage());
			}
			$reponse_modification->closeCursor();


			$requete_modification = "UPDATE Configuration SET valeur = '$zoneVacances' WHERE nom like 'zone_vacances' ";
			//echo "$requete_modification";
			try
			{
				$reponse_modification = $bdd->query($requete_modification) or die('Erreur SQL !<br>' .$requete_modification. '<br>'. mysql_error());
			}
			catch(Exception $e)
			{
				// En cas d'erreur précédemment, on affiche un message et on arrête tout
				die('Erreur : '.$e->getMessage());
			}
			$reponse_modification->closeCursor();	


			$requete_modification = "UPDATE Configuration SET valeur = '$renseignerAutomatiquementCongeValide' WHERE nom like 'renseigner_automatiquement_conge_valide' ";
			//echo "$requete_modification";
			try
			{
				$reponse_modification = $bdd->query($requete_modification) or die('Erreur SQL !<br>' .$requete_modification. '<br>'. mysql_error());
			}
			catch(Exception $e)
			{
				// En cas d'erreur précédemment, on affiche un message et on arrête tout
				die('Erreur : '.$e->getMessage());
			}
			$reponse_modification->closeCursor();			
			

			$requete_modification = "UPDATE Configuration SET valeur = '$autoriserAdminSupprEvent' WHERE nom like 'autoriser_admin_suppr_event' ";
			//echo "$requete_modification";
			try
			{
				$reponse_modification = $bdd->query($requete_modification) or die('Erreur SQL !<br>' .$requete_modification. '<br>'. mysql_error());
			}
			catch(Exception $e)
			{
				// En cas d'erreur précédemment, on affiche un message et on arrête tout
				die('Erreur : '.$e->getMessage());
			}
			$reponse_modification->closeCursor();	
	
///////////////////////// LOG /////////////////////////
			
			// On ecrit un log de ce qui se passe dans la table "Log".
			$requete_log = "INSERT INTO Log (`idLog`, `Utilisateurs_idUtilisateurs`, `Utilisateurs_login`, `Date`, `Type`, `Log`) VALUES (NULL, '$idSession', '$login_session', NOW(), 'modification de configuration', 'Mois de départ année comptable: $moisDepartAnneeConge, Nombre de jours de congés: $nbrJoursConges, periode de gestion des RTT: $typeGestionRTT, nombre de jours de RTT: $nbrJoursRTT, afficher heures supplementaires: $afficherHeuresSup, afficher codes comptables selection axes: $afficherCodesComptablesSelectionAxes, afficher codes comptables recapitilatifs: $afficherCodesComptablesRecap, activer axe3: $activerAxe3, afficher conges: $afficherConges, afficher RTT: $afficherRTT, axe2 exclus des totaux: $axe2_exclus_totaux, afficher raccourcis absences: $afficherRaccourcisAbsences, afficher jours types : $afficherJoursTypes, afficher calcul rapide journee: $afficherCalculRapideJournee, zone vacances: $zoneVacances')";
			//echo "$requete_log";
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



			echo "Les modifications de configuration ont bien été faites.";

?>
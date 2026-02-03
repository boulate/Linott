<?php include("connexion_base.php"); 
session_start();

//include("checkAdmin.php");

//echo "debut script --- ";
$message="";

	$idUtilisateur			=	$_SESSION['idUtilisateurs'];
	$login_session			=	$_SESSION['login'];

	$idEvenement				=	$_GET['idEvenement'];
	$queFaire					=	$_GET['queFaire'];

	$AncienneDescriptionEvent = $_GET['AncienneDescriptionEvent'];

	require("verifier_input_php.php");
	
	// if (checkInput($idAModifier, "id") != "ok")
	// {
	// 	echo checkInput($idAModifier, "id");
	// 	exit;
	// }

	
//	echo checkInput($login, "login");

	
			// On va chercher les propriétés de l'évènement.
			$requete_import_event = "SELECT `Utilisateurs_idUtilisateurs`, `Utilisateurs_login`, `date`, `periode`, `description` FROM CalendrierConges WHERE id = '$idEvenement'";
			echo "$requete_import_event\n";
			try
			{
				$reponse_import_event = $bdd->query($requete_import_event) or die('Erreur SQL !<br>' .$requete_import_event. '<br>'. mysql_error());
			}
			catch(Exception $e)
			{
				// En cas d'erreur précédemment, on affiche un message et on arrête tout
				die('Erreur : '.$e->getMessage());
			}
			while ($donnees = $reponse_import_event->fetch())
			{
				$idUtilisateurConge 	= $donnees['Utilisateurs_idUtilisateurs'];
				$loginUtilisateurConge 	= $donnees['Utilisateurs_login'];
				$dateConge 				= $donnees['date'];
				$periodeConge 			= $donnees['periode'];
				$descriptionConge		= $donnees['description'];
			}
			$reponse_import_event->closeCursor();


			// On va chercher l'idAxe1 du code 50 ( == congés et autre absences selon le README)
			$requete_import_idAxe1 = "SELECT `idAxe1` FROM Axe1 WHERE codeAxe1 = '50'";
			//echo "$requete_import_idAxe1";
			try
			{
				$reponse_import_idAxe1 = $bdd->query($requete_import_idAxe1) or die('Erreur SQL !<br>' .$requete_import_idAxe1. '<br>'. mysql_error());
			}
			catch(Exception $e)
			{
				// En cas d'erreur précédemment, on affiche un message et on arrête tout
				die('Erreur : '.$e->getMessage());
			}
			while ($donnees = $reponse_import_idAxe1->fetch())
			{
				$idAxe1Conge 	= $donnees['idAxe1'];
			}
			$reponse_import_idAxe1->closeCursor();	





			// On va chercher l'idAxe2 du type de congé
			$requete_import_idAxe2 = "SELECT `idAxe2` FROM Axe2 WHERE nomAxe2 = '$descriptionConge'";
			//echo "$requete_import_idAxe2";
			try
			{
				$reponse_import_idAxe2 = $bdd->query($requete_import_idAxe2) or die('Erreur SQL !<br>' .$requete_import_idAxe2. '<br>'. mysql_error());
			}
			catch(Exception $e)
			{
				// En cas d'erreur précédemment, on affiche un message et on arrête tout
				die('Erreur : '.$e->getMessage());
			}
			while ($donnees = $reponse_import_idAxe2->fetch())
			{
				$idAxe2Conge 	= $donnees['idAxe2'];
			}
			$reponse_import_idAxe2->closeCursor();		



			// On va chercher l'idAxe2 de "Récupération" car ce nom est défini dans le README
			$requete_import_idAxe2 = "SELECT `idAxe2` FROM Axe2 WHERE nomAxe2 = 'Récupération'";
			//echo "$requete_import_idAxe2";
			try
			{
				$reponse_import_idAxe2 = $bdd->query($requete_import_idAxe2) or die('Erreur SQL !<br>' .$requete_import_idAxe2. '<br>'. mysql_error());
			}
			catch(Exception $e)
			{
				// En cas d'erreur précédemment, on affiche un message et on arrête tout
				die('Erreur : '.$e->getMessage());
			}
			while ($donnees = $reponse_import_idAxe2->fetch())
			{
				$idAxe2Recup 	= $donnees['idAxe2'];
			}
			$reponse_import_idAxe2->closeCursor();	



			// On va chercher le nombre d'heures par semaine de la personne en congé
			$requete_import_nombre_heures_semaine = "SELECT `nbrHeuresSemaine` FROM Utilisateurs WHERE idUtilisateurs = '$idUtilisateurConge'";
			//echo "$requete_import_nombre_heures_semaine";
			try
			{
				$reponse_import_nombre_heures_semaine = $bdd->query($requete_import_nombre_heures_semaine) or die('Erreur SQL !<br>' .$requete_import_nombre_heures_semaine. '<br>'. mysql_error());
			}
			catch(Exception $e)
			{
				// En cas d'erreur précédemment, on affiche un message et on arrête tout
				die('Erreur : '.$e->getMessage());
			}
			while ($donnees = $reponse_import_nombre_heures_semaine->fetch())
			{
				$nbrHeuresSemaine 	= $donnees['nbrHeuresSemaine'];
			}
			$reponse_import_nombre_heures_semaine->closeCursor();			

///////////////////// Si on doit renseigner un congé ////////////////////////
			if ( $queFaire == "renseigner")
			{




				// On regarde que les lignes dans la feuille d'heures ne soient pas toutes déjà prises
				$tableNumerosLignesMatin = array();
				$tableNumerosLignesAprem = array();
				$requete_lignes_prises = "SELECT `numeroLigne` FROM Periodes WHERE Utilisateurs_idUtilisateurs = '$idUtilisateurConge' AND `date` = '$dateConge' ";
				//echo "$requete_lignes_prises";
				try
				{
					$reponse_lignes_prises = $bdd->query($requete_lignes_prises) or die('Erreur SQL !<br>' .$requete_lignes_prises. '<br>'. mysql_error());
				}
				catch(Exception $e)
				{
					// En cas d'erreur précédemment, on affiche un message et on arrête tout
					die('Erreur : '.$e->getMessage());
				}
				while ($donnees = $reponse_lignes_prises->fetch())
				{
					$numeroLigne 	= $donnees['numeroLigne'];

					if ($numeroLigne <= 6)
					{
						array_push($tableNumerosLignesMatin, "$numeroLigne");
					}
					if ($numeroLigne >= 7)
					{
						array_push($tableNumerosLignesAprem, "$numeroLigne");
					}

				}
				$reponse_lignes_prises->closeCursor();			

				// Si la taille des tables est de 6 ou plus (nombre de possibilités de renseignements) et que c'est une journée ou un MA/AM, on envoie une erreur et on ne fait rien.				
				// EDIT: Encore plus strict: Si il y a kkchose de renseigné dans la journée, on quitte tout.
				if ( (sizeof($tableNumerosLignesMatin) > 0) && ( ($periodeConge == "JO") || ($periodeConge == "MA") ) )
				{
					//echo "ATTENTION: Toutes les périodes du matin sont utilisées à cette date sur la fiche d'heures. L'absence ne peut y être renseignée automatiquement.";
					echo "ATTENTION: La fiche d'heure du $dateConge pour l'utilisateur $loginUtilisateurConge contient déjà des informations.\n\nPar conséquent cette absence que vous validez ne pourra pas être renseignée automatiquement dans la fiche d'heure.";
					exit(0);
				}
				else if ( (sizeof($tableNumerosLignesAprem) > 0) && ( ($periodeConge == "JO") || ($periodeConge == "AM") ) )
				{
					//echo "ATTENTION: Toutes les périodes de l'après midi sont utilisées à cette date sur la fiche d'heures. L'absence ne peut y être renseignée automatiquement.";
					echo "ATTENTION: La fiche d'heure du $dateConge pour l'utilisateur $loginUtilisateurConge contient déjà des informations.\n\nPar conséquent cette absence que vous validez ne pourra pas être renseignée automatiquement dans la fiche d'heure.";
					exit(0);
				}
				// Sinon on va renseigner le congé dans la premiere periode libre.
				else
				{
					$dureePeriode = $nbrHeuresSemaine/10;
					$finMatin = 8+$dureePeriode;
					$finAprem = 14+$dureePeriode;
					// Matin
					for ( $i = 1 ; $i <= 6 ; $i++)
					{
						// Mis en commentaire pour devenir plus strict: Si il y a kkchose de renseigné dans la journée, on ne renseigne pas automatiquement.
						if ( !in_array($i, $tableNumerosLignesMatin)  && ( ($periodeConge == "JO") || ($periodeConge == "MA") ) )
						{
							$requete_insert_matin = "INSERT INTO Periodes (`idHoraires`, `date`, `horaireDebut`, `horaireFin`, `totalHoraire`, `Utilisateurs_idUtilisateurs`, `Utilisateurs_login`, `Section_idSection`, `Axe1_idAxe1`, `Axe2_idAxe2`, `Axe3_idAxe3`, `numeroLigne`) VALUES 			('', '$dateConge', '8', '$finMatin', '$dureePeriode', '$idUtilisateurConge', '$loginUtilisateurConge', '1', '$idAxe1Conge', '$idAxe2Conge', '1', '$i')";
							//echo "\n$requete_insert_matin";
							try
							{
								$reponse_insert_matin = $bdd->query($requete_insert_matin) or die('Erreur SQL !<br>' .$requete_insert_matin. '<br>'. mysql_error());
							}
							catch(Exception $e)
							{
								// En cas d'erreur précédemment, on affiche un message et on arrête tout
								die('Erreur : '.$e->getMessage());
							}
							$reponse_insert_matin->closeCursor();


							//On a rentré le congé matin, on sort de la boucle
							break;
						}
					}
					// Aprem
					for ( $i = 7 ; $i <= 12 ; $i++)
					{
						if ( !in_array($i, $tableNumerosLignesAprem)  && ( ($periodeConge == "JO") || ($periodeConge == "AM") ) )
						{
							$requete_insert_aprem = "INSERT INTO Periodes 	(`idHoraires`, `date`, `horaireDebut`, `horaireFin`, `totalHoraire`, `Utilisateurs_idUtilisateurs`, `Utilisateurs_login`, `Section_idSection`, `Axe1_idAxe1`, `Axe2_idAxe2`, `Axe3_idAxe3`, `numeroLigne`) VALUES 			('', '$dateConge', '14', '$finAprem', '$dureePeriode', '$idUtilisateurConge', '$loginUtilisateurConge', '1', '$idAxe1Conge', '$idAxe2Conge', '1', '$i')";
							//echo "\n$requete_insert_aprem";
							try
							{
								$reponse_insert_aprem = $bdd->query($requete_insert_aprem) or die('Erreur SQL !<br>' .$requete_insert_aprem. '<br>'. mysql_error());
							}
							catch(Exception $e)
							{
								// En cas d'erreur précédemment, on affiche un message et on arrête tout
								die('Erreur : '.$e->getMessage());
							}
							$reponse_insert_aprem->closeCursor();

							//On a rentré le congé matin, on sort de la boucle
							break;
						}
					}

				}

				if ( ($periodeConge == "MA") || ($periodeConge == "AM") )
				{
					$totalJournee = $nbrHeuresSemaine/10;
				}
				if ( $periodeConge == "JO" )
				{
					$totalJournee = $nbrHeuresSemaine/5;
				}

				// Si c'est une récup qui est acceptée, on doit soustraire cette somme aux heures sup'
				if ( $idAxe2Conge == $idAxe2Recup)
				{
					//echo "\n je rentre dans le test";
					include("gestion_heures_sup.php");
					majHeuresSup("renseigner_automatiquement_conge");
				}

			}





///////////////////// Si on doit modifier un congé ////////////////////////
			if ( $queFaire == "modifier")
			{

				// On va chercher la ou les périodes à modifier
				//$requete_periodes_a_modif = "SELECT `idHoraires` FROM Periodes WHERE Axe2_idAxe2 = (SELECT `idAxe2` FROM Axe2 WHERE `nomAxe2` = '$AncienneDescriptionEvent' ) AND `Utilisateurs_idUtilisateurs` = '$idUtilisateurConge' AND `date` = '$dateConge' ";
				$requete_periodes_a_modif = "UPDATE Periodes SET `Axe2_idAxe2` = '$idAxe2Conge' WHERE Axe2_idAxe2 = (SELECT `idAxe2` FROM Axe2 WHERE `nomAxe2` = '$AncienneDescriptionEvent' ) AND `Utilisateurs_idUtilisateurs` = '$idUtilisateurConge' AND `date` = '$dateConge' ";
				//echo "ATTENTION : $requete_periodes_a_modif --- idAxe2Recup = $idAxe2Recup, idAxe2Conge = $idAxe2Conge, AncienneDescriptionEvent = $AncienneDescriptionEvent, periodeConge = $periodeConge";
				try
				{
					$reponse_periodes_a_modif = $bdd->query($requete_periodes_a_modif) or die('Erreur SQL !<br>' .$requete_periodes_a_modif. '<br>'. mysql_error());
				}
				catch(Exception $e)
				{
					// En cas d'erreur précédemment, on affiche un message et on arrête tout
					die('Erreur : '.$e->getMessage());
				}
				$reponse_periodes_a_modif->closeCursor();
				// Seul bémol: Si la personne défini en plus à la main un congé qui est le même que le type de congé validé, et que le type d'absence vient à être modifié dans la fenetre de préférence des absences, les 3 périodes vont changer.



				if ( ($periodeConge == "MA") || ($periodeConge == "AM") )
				{
					$totalJournee = $nbrHeuresSemaine/10;
				}
				if ( $periodeConge == "JO" )
				{
					$totalJournee = $nbrHeuresSemaine/5;
				}
				// Si c'était autre chose qu'une récup et que ca devient une récup, on doit soustraire cette somme aux heures sup'
				if ( ( $idAxe2Conge == $idAxe2Recup ) && ($AncienneDescriptionEvent != "Récupération") )
				{
					echo "\n je rentre dans le test et je rentre une récupération";
					include("gestion_heures_sup.php");
					majHeuresSup("renseigner_automatiquement_conge");
				}				

				// Si c'était une récup et qu'on la passe à autre chose, on doit ajouter cette somme aux heures sup'
				if ( ($idAxe2Conge != $idAxe2Recup ) && ($AncienneDescriptionEvent == "Récupération") )
				{
					echo "\n je rentre dans le test pour retirer une récup et rajouter heures sup': $totalHoraire";
					include("gestion_heures_sup.php");
					majHeuresSup("supprimer_automatiquement_conge");
				}


// PROBLEME: SI la personne modifie à la main une congée rentrée automatiquement et qu'on vient modifier le type de congé pour y mettre une récup, les heures soustraites des heures sup' (et donc égales à 1/5eme ou 1/10eme de semaine) ne seront peut être pas exates. 

			}




///////////////////// Si on doit supprimer un congé ////////////////////////
			if ( $queFaire == "supprimer")
			{
				$totalHoraire = 0;
				// On va chercher la periodeJournee du code 50 ( == congés et autre absences selon le README)
				//$requete_import_periodeJournee = "SELECT `idHoraires`, `date`, `horaireDebut`, `horaireFin`, `totalHoraire`, `Utilisateurs_idUtilisateurs`, `Axe2_idAxe2` FROM Periodes WHERE `codeAxe1` = '$idAxe2Conge' AND `Utilisateurs_idUtilisateurs` = '$idUtilisateurConge' AND `date` = '$dateConge' ";
				$requete_import_periodeJournee = "SELECT sum(totalHoraire) as sommeTotalHoraire FROM Periodes WHERE `Axe2_idAxe2` = '$idAxe2Conge' AND `Utilisateurs_idUtilisateurs` = '$idUtilisateurConge' AND `date` = '$dateConge' ";
				//echo "ATTENTION: $requete_import_periodeJournee";
				try
				{
					$reponse_import_periodeJournee = $bdd->query($requete_import_periodeJournee) or die('Erreur SQL !<br>' .$requete_import_periodeJournee. '<br>'. mysql_error());
				}
				catch(Exception $e)
				{
					// En cas d'erreur précédemment, on affiche un message et on arrête tout
					die('Erreur : '.$e->getMessage());
				}
				while ($donnees = $reponse_import_periodeJournee->fetch())
				{
					
					$totalHoraire 		= $donnees['sommeTotalHoraire'];

					// $idPeriode 		= $donnees['idPeriode'];
					// $date 			= $donnees['date'];
					// $horaireDebut 	= $donnees['horaireDebut'];
					// $horaireFin 	= $donnees['horaireFin'];
					// $totalHoraire 	= $donnees['totalHoraire'];
					// $idUtilisateurConge 	= $donnees['Utilisateurs_idUtilisateurs'];
					// $idAxe2 		= $donnees['Axe2_idAxe2'];
				}
				$reponse_import_periodeJournee->closeCursor();

				//echo "ATTENTION: periodeConge = $periodeConge";

				if ( ($periodeConge == "JO") || ($periodeConge == "MA") )
				{
					$requete_supression_conge = "DELETE FROM Periodes WHERE `horaireDebut` = '8' AND `Utilisateurs_idUtilisateurs` = '$idUtilisateurConge' AND `date` = '$dateConge' AND `Axe1_idAxe1` = '$idAxe1Conge' AND `Axe2_idAxe2` = '$idAxe2Conge' ";
					//echo "ATTENTION: $requete_supression_conge";
					try
					{
						$reponse_supression_conge = $bdd->query($requete_supression_conge) or die('Erreur SQL !<br>' .$requete_supression_conge. '<br>'. mysql_error());
					}
					catch(Exception $e)
					{
						// En cas d'erreur précédemment, on affiche un message et on arrête tout
						die('Erreur : '.$e->getMessage());
					}
					$reponse_supression_conge->closeCursor();
				}

				if ( ($periodeConge == "JO") || ($periodeConge == "AM") )
				{
					$requete_supression_conge = "DELETE FROM Periodes WHERE `horaireDebut` = '14' AND `Utilisateurs_idUtilisateurs` = '$idUtilisateurConge' AND `date` = '$dateConge' AND `Axe1_idAxe1` = '$idAxe1Conge' AND `Axe2_idAxe2` = '$idAxe2Conge' ";
					//echo "ATTENTION: $requete_supression_conge";
					try
					{
						$reponse_supression_conge = $bdd->query($requete_supression_conge) or die('Erreur SQL !<br>' .$requete_supression_conge. '<br>'. mysql_error());
					}
					catch(Exception $e)
					{
						// En cas d'erreur précédemment, on affiche un message et on arrête tout
						die('Erreur : '.$e->getMessage());
					}
					$reponse_supression_conge->closeCursor();
				}
				
				// Si c'est une récup qui est supprimée, on doit rajouter cette somme aux heures sup'
				if ( ($idAxe2Conge == $idAxe2Recup) && ($totalHoraire != 0) );
				{
					//echo "\n je rentre dans le test remonter heures sup'";
					include("gestion_heures_sup.php");
					majHeuresSup("supprimer_automatiquement_conge");
				}

			}
















			
			// // On ecrit un log de ce qui se passe dans la table "log".
			// $requete_log = "INSERT INTO log (`idlog`, `Utilisateurs_idUtilisateurs`, `Utilisateurs_login`, `Date`, `Type`, `log`) VALUES (NULL, '$idUtilisateur', '$login_session', NOW(), 'validation_conge', \"$requete_modification\")";
			// //echo "$requete_log";
			// try
			// {
			// 	$reponse_log = $bdd->query($requete_log) or die('Erreur SQL !<br>' .$requete_log. '<br>'. mysql_error());
			// }
			// catch(Exception $e)
			// {
			// 	// En cas d'erreur précédemment, on affiche un message et on arrête tout
			// 	die('Erreur : '.$e->getMessage());
			// }
			// $reponse_log->closeCursor();



			// echo "L'evenement idEvenement a bien été modifié.";
	


?>
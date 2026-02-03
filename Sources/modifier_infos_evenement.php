<?php include("connexion_base.php"); 
session_start();

//include("checkAdmin.php");

//echo "debut script --- ";
$message="";

	$idUtilisateur				=	$_SESSION['idUtilisateurs'];
	$login_session				=	$_SESSION['login'];

	$idEvenement				=	$_GET['idEvenement'];
	$idUtilisateurEvenement		=	$_GET['idUtilisateurEvenement'];
	$loginUtilisateurEvenement	=	$_GET['loginUtilisateurEvenement'];
	$dateEvenement				=	$_GET['dateEvenement'];
	$periodeEvenement			=	$_GET['periodeEvenement'];
	$typeEvenement				=	$_GET['typeEvenement'];
	$validation					=	$_GET['validation'];
	$bloquant					=	$_GET['bloquant'];
	$indisponible				=	$_GET['indisponible'];


 	$utilisateursConcernes		=	$_GET['utilisateursConcernes'];
 	$groupesConcernes			=	$_GET['groupesConcernes'];

	// En cas de modification de l'info event (venant par exemple de proprietes_evenement_calendrier.php), cette valeur sera renseignée
	$typeModif					=	$_GET['typeModif'];

	$modifDescriptionEvent		=	addslashes($_GET['modifDescriptionEvent']);

	// En cas de supprimer event, on va là.
	$supprimerEvent				=	$_GET['supprimerEvent'];


	// Si c'est un admin, on ajoute la possibilité de changement de validation
	$admin	=	$_SESSION['admin'];
	if ($admin == 1 )
	{
		$setValidation = ", valide = '$validation'";
	} 

	require("verifier_input_php.php");
	include("importer_configuration.php");

	// if (checkInput($idAModifier, "id") != "ok")
	// {
	// 	echo checkInput($idAModifier, "id");
	// 	exit;
	// }

	
//	echo checkInput($login, "login");
		if ( $typeModif == "event" )
		{
			// Si une difference entre la base et la page est présente, on lance la modif
			$requete_modification = "UPDATE CalendrierConges SET description = '$modifDescriptionEvent', bloquant = '$bloquant', indisponible = '$indisponible' WHERE id = '$idEvenement' ";
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
		}
		elseif ( $typeModif == "commentaire" ) 
		{
			$requete_modification = "UPDATE CalendrierConges SET commentaire = '$modifDescriptionEvent' WHERE id = '$idEvenement' ";
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
		}
		elseif ( $typeModif == "astreinte" ) 
		{
			$requete_modification = "UPDATE CalendrierConges SET description = '$modifDescriptionEvent' WHERE id = '$idEvenement' ";
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
		}
		elseif ( $supprimerEvent == 1 )
		{
			//echo "nous allons supprimer l'event.";

			// On verifie que la personne est bien la propriétaire de l'event.
			$requete_verification = "SELECT Utilisateurs_idUtilisateurs FROM CalendrierConges WHERE id = '$idEvenement'";
			//echo "$requete_verification";
			try
			{
				$reponse_verification = $bdd->query($requete_verification) or die('Erreur SQL !<br>' .$requete_verification. '<br>'. mysql_error());
			}
			catch(Exception $e)
			{
				// En cas d'erreur précédemment, on affiche un message et on arrête tout
				die('Erreur : '.$e->getMessage());
			}
				while ($donnees = $reponse_verification->fetch())
			{
				// Seul celui à qui appartient l'événement (ou un administrateur si l'option adéquate est cochée dans la configuration) peuvent supprimer un événement.
				if ( ($donnees['Utilisateurs_idUtilisateurs'] == $idUtilisateur) || ($admin == 1 && $autoriserAdminSupprEvent == "checked" ) )
				{
						$requete_suppression = "DELETE FROM CalendrierConges WHERE id = '$idEvenement' ";
						echo "$requete_suppression";
						try
						{
							$reponse_suppression = $bdd->query($requete_suppression) or die('Erreur SQL !<br>' .$requete_suppression. '<br>'. mysql_error());
						}
						catch(Exception $e)
						{
							// En cas d'erreur précédemment, on affiche un message et on arrête tout
							die('Erreur : '.$e->getMessage());
						}
						$reponse_suppression->closeCursor();
						echo "Évènement supprimé.";

				}
				else
				{	
					echo "Erreur: Vous ne pouvez pas supprimer un évènement qui ne vous appartient pas!";
					exit;
				}
			}
			$reponse_verification->closeCursor();

			exit;
		}
		elseif ( $typeModif == "utilisateursConcernes" ) 
		{
			$requete_modification = "UPDATE CalendrierConges SET id_utilisateurs_concernes = '$utilisateursConcernes', id_groupes_concernes = '$groupesConcernes'  WHERE id = '$idEvenement' ";
			echo "$requete_modification";
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
		}
		else
		{
			// Si une difference entre la base et la page est présente, on lance la modif
			$requete_modification = "UPDATE CalendrierConges SET Utilisateurs_idUtilisateurs = '$idUtilisateurEvenement', Utilisateurs_login = '$loginUtilisateurEvenement', `date` = '$dateEvenement', `periode` = '$periodeEvenement' , type = '$typeEvenement' $setValidation WHERE id = '$idEvenement' ";
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
		}
			
			
			// On ecrit un log de ce qui se passe dans la table "Log".
			$requete_log = "INSERT INTO Log (`idLog`, `Utilisateurs_idUtilisateurs`, `Utilisateurs_login`, `Date`, `Type`, `Log`) VALUES (NULL, '$idUtilisateur', '$login_session', NOW(), 'modification calendrier evenements', \"$requete_modification\")";
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



			echo "L'evenement $idEvenement a bien été modifié.";

?>
<?php include("connexion_base.php"); 
session_start();

//include("checkAdmin.php");

//echo "debut script --- ";
$message="";

	$idUtilisateur			=	$_SESSION['idUtilisateurs'];
	$login_session			=	$_SESSION['login'];

	$idEvenement				=	$_GET['idEvenement'];
	$idUtilisateurEvenement		=	$_GET['idUtilisateurEvenement'];
	$loginUtilisateurEvenement	=	$_GET['loginUtilisateurEvenement'];
	$dateEvenement				=	$_GET['dateEvenement'];
	$periodeEvenement			=	$_GET['periodeEvenement'];
	$typeEvenement				=	$_GET['typeEvenement'];
	$validation					=	$_GET['validation'];

	// Si c'est un admin, on ajoute la possibilité de changement de validation
	$admin	=	$_SESSION['admin'];
	if ($admin == 1 )
	{
		$setValidation = "valide = '$validation'";
	} 


	require("verifier_input_php.php");
	
	// if (checkInput($idAModifier, "id") != "ok")
	// {
	// 	echo checkInput($idAModifier, "id");
	// 	exit;
	// }

	
//	echo checkInput($login, "login");

	
			// Si une difference entre la base et la page est présente, on lance la modif
			$requete_modification = "UPDATE CalendrierConges SET $setValidation WHERE id = '$idEvenement' ";
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

			
			
			// On ecrit un log de ce qui se passe dans la table "Log".
			$requete_log = "INSERT INTO Log (`idLog`, `Utilisateurs_idUtilisateurs`, `Utilisateurs_login`, `Date`, `Type`, `Log`) VALUES (NULL, '$idUtilisateur', '$login_session', NOW(), 'validation_conge', \"$requete_modification\")";
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



			echo "L'evenement idEvenement a bien été modifié.";
	


?>
<?php include("connexion_base.php"); 
session_start();

	$from				=	$_GET['from'];
	$idSession			=	$_SESSION['idUtilisateurs'];
	$newPassword			=	$_GET['newPassword'];
	$login_session			=	$_SESSION['login'];
		
	if ($from == "preferences")
	{
		$idUtilisateur 			=	$idSession;
		$nom				=	$_SESSION['nom'];
		$prenom				=	$_SESSION['prenom'];
	}
	else
	{
		$idUtilisateur 			=	$_GET['idUtilisateur'];
		$loginUtilisateur		=	$_GET['loginUtilisateur'];
		$nom				=	$_GET['nomUtilisateur'];
		$prenom				=	$_GET['prenomUtilisateur'];
	}

// Si l'ID utilisateur à changer est différent de l'id de session, on vérifie les droits utilisateur.
if ($idUtilisateur != $idSession)
{
	require("checkAdmin.php");
}

// On vérifie que "password" repect le format souhaité.
require("verifier_input_php.php");

if (checkInput($newPassword, "password") != "ok")
{
	echo checkInput($newPassword, "password");
	exit;
}

//echo "debut script --- ";
//$message="";

			// Si une difference entre la base et la page est présente, on lance la modif
			$requete_modification = "UPDATE Utilisateurs SET motDePasse = md5('$newPassword') WHERE idUtilisateurs = '$idUtilisateur' ";
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

			
			
			// On ecrit un log de ce qui se passe dans la table "Log".
			$requete_log = "INSERT INTO Log (`idLog`, `Utilisateurs_idUtilisateurs`, `Utilisateurs_login`, `Date`, `Type`, `Log`) VALUES (NULL, '$idSession', '$login_session', NOW(), 'modification mot de passe', 'Modification du mot de passe de $loginUtilisateur')";
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

			////////////////////////////////////////// Je n'arrive pas à savoir pourquoi je ne peux pas mettre ca dans une fonction (ca ne marche pas).
			////////////////////////////////////////// Celui juste au dessus marche, mais pas celui en dessous. Meme en le sortant de la fonction (juste en definissant log et type_log dans des variable)
			// $style_log="modigi";
			// $log="$requete_modification";

			// // function creer_log($type_log, $log)
			// // {
		 	// 	//	$requete_log = "INSERT INTO Log (`idLog`, `Utilisateurs_idUtilisateurs`, `Utilisateurs_login`, `Date`, `Type`, `Log`) VALUES (NULL, '1', 'guillaume', NOW(), \"$type_log\", \"$log\" )";
		 	// 	$requete_log="select * from Log";
		 	// 		try
		 	// 		{
		 	// 			//$reponse_log = $bdd->query($requete_log) or die('Erreur SQL !<br>' .$requete_log. '<br>'. mysql_error());
		 	// 			echo "$requete_log";
		 	// 		}
		 	// 		catch(Exception $e)
		 	// 		{
		 	// 			// En cas d'erreur précédemment, on affiche un message et on arrête tout
		 	// 			die('Erreur : '.$e->getMessage());
		 	// 		}
		 	// 		$reponse_log->closeCursor();
			// // }


			// include("log.php");
			// creer_log("Modification utilisateur", "$requete_modification");


			echo "Le mot de passe de l'utilisateur $prenom $nom a bien été modifié.";

?>
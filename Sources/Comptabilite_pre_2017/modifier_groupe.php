<?php include("connexion_base.php"); 
session_start();

	require("checkAdmin.php");
	require("verifier_input_php.php");

	$idSession				=	$_SESSION['idUtilisateurs'];
	$login_session			=	$_SESSION['login'];
		

	$idGroupe 				=	$_GET['idGroupe'];
	$listeIdUtilisateurs	=	$_GET['listeIdUtilisateurs'];

	$supprimerGroupe		=	$_GET['supprimerGroupe'];

	$modifierNom			=	$_GET['modifierNom'];
	$nomGroupe				=	$_GET['nomGroupe'];
//echo "debut script --- ";
//$message="";
if (checkInput($idGroupe, "id") != "ok")
{
	echo checkInput($idGroupe, "id");
	exit;
}


if ($supprimerGroupe == "1")
{			// Si une difference entre la base et la page est présente, on lance la modif
			$requete_modification = "DELETE FROM Groupes WHERE id = '$idGroupe' ";
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

			echo "Le groupe a bien été supprimé.";

}
if ($modifierNom == "1")
{

		if (checkInput($nomGroupe, "nom") != "ok")
		{
			echo checkInput($nomGroupe, "nom");
			exit;
		}

			$sql_demande_nom_groupe	=	"SELECT nom from Groupes where id = '$idGroupe'";
			//echo "$sql_demande_nom_groupe";
			try
			{
				$reponse_nom_groupe = $bdd->query($sql_demande_nom_groupe) or die('Erreur SQL !<br>' .$sql_demande_nom_groupe. '<br>'. mysql_error());
			}
			catch(Exception $e)
			{
				// En cas d'erreur précédemment, on affiche un message et on arrête tout
				die('Erreur : '.$e->getMessage());
			}

			while ($donnees = $reponse_nom_groupe->fetch())
			{

				$nomBDD				=	$donnees['nom'];
			}
			$reponse_nom_groupe->closeCursor(); // Termine le traitement de la requête


			if ( $nomBDD == $nomGroupe )
			{
				echo "Erreur: Vous n'avez pas modifié le nom du groupe.";
			}
			else
			{
				// Si une difference entre la base et la page est présente, on lance la modif
				$requete_modification = "UPDATE Groupes SET nom = '$nomGroupe' WHERE id = '$idGroupe' ";
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

				echo "Le nom du groupe a bien été mis à jour.";
			}
}
else
{
			// Si une difference entre la base et la page est présente, on lance la modif
			$requete_modification = "UPDATE Groupes SET idUtilisateurs = '$listeIdUtilisateurs' WHERE id = '$idGroupe' ";
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

			echo "Le groupe a bien été mis à jour.";
}
			
			
			// On ecrit un log de ce qui se passe dans la table "Log".
			$requete_log = "INSERT INTO Log (`idLog`, `Utilisateurs_idUtilisateurs`, `Utilisateurs_login`, `Date`, `Type`, `Log`) VALUES (NULL, '$idSession', '$login_session', NOW(), 'modification de groupe', \"$requete_modification\")";
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



?>
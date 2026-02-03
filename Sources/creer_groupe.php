<?php include("connexion_base.php"); 
session_start();

require("checkAdmin.php");

//echo "debut script --- ";
$message="";

	$idUtilisateur				=	$_SESSION['idUtilisateurs'];
	$login_session				=	$_SESSION['login'];
	$nom					=	$_GET['nom'];

	
	require("verifier_input_php.php");
	

	if (checkInput($nom, "nom") != "ok")
	{
		echo checkInput($nom, "nom");
		exit;
	}


	// On test avant la création. Si le nom et le prénom du groupe à créer existent déjà en base, on ne lance pas de requete_creation
	$requete_test = "SELECT * from Groupes WHERE nom = '$nom' ";
	//echo "$requete_test";
	try
	{
		$reponse_test = $bdd->query($requete_test) or die('Erreur SQL !<br>' .$requete_test. '<br>'. mysql_error());
	}
	catch(Exception $e)
	{
		// En cas d'erreur précédemment, on affiche un message et on arrête tout
		die('Erreur : '.$e->getMessage());
	}

	// Si un nom de groupe ressort du test ci dessus, c'est qu'un groupe similaire existe déjà en base donc on annule la création.
	$unique = "oui";
	while ($donnees = $reponse_test->fetch())
  	{
		// $nomBase = $donnees['nom'];
		$unique = "non";
	}
	$reponse_test->closeCursor();


	if ($unique == "non")
	{
		echo "Un groupe ayant ce nom existe déjà.";
	} 
	if ($unique == "oui")
	{ 
			// Si ce groupe n'existe pas en base, on le créé
			$requete_creation = "INSERT INTO `Groupes` (`id` , `nom` , `idUtilisateurs`) VALUES (NULL , '$nom', '' )";
			//echo "$requete_creation";
			try
			{
				$reponse_creation = $bdd->query($requete_creation) or die('Erreur SQL !<br>' .$requete_creation. '<br>'. mysql_error());
			}
			catch(Exception $e)
			{
				// En cas d'erreur précédemment, on affiche un message et on arrête tout
				die('Erreur : '.$e->getMessage());
			}
			$reponse_creation->closeCursor();

			
			
			// On ecrit un log de ce qui se passe dans la table "Log".
			$requete_log = "INSERT INTO Log (`idLog`, `Utilisateurs_idUtilisateurs`, `Utilisateurs_login`, `Date`, `Type`, `Log`) VALUES (NULL, '$idUtilisateur', '$login_session', NOW(), 'création groupe', \"$requete_creation\")";
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


			echo "Le groupe $nom a bien été créé.\n\nVous pouvez maintenant aller y renseigner des utilisateurs.";
	}


?>
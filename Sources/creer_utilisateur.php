<?php include("connexion_base.php"); 
session_start();

require("checkAdmin.php");

//echo "debut script --- ";
$message="";

	$idUtilisateur				=	$_SESSION['idUtilisateurs'];
	$login_session				=	$_SESSION['login'];
	$login					=	strtolower($_GET['login']);
	$nom					=	$_GET['nom'];
	$prenom					=	$_GET['prenom'];
	$nbrHeures				=	$_GET['nbrHeures'];
	$nbrConges				= 	$_GET['nbrConges'];
	$nbrRTT					=	$_GET['nbrRTT'];
	$couleur				=	$_GET['couleur'];
	$admin					=	$_GET['admin'];
	$active					=	$_GET['active'];
	//$motDePasse				=	strtolower($nom);
	$motDePasse				=	$_GET['password'];


	// Si pas de nbrConges et nbrRTT renseignés, on prend ceux par défaut dans l'administration:
	if ($nbrConges == "")
	{
		/////////////////////////////////////////// GESTION DU NOMBRE DE JOURS DE RTT  ///////////////////////////////////////////
		$sql		= "SELECT valeur FROM Configuration where nom like 'nombre_jours_conges'";
		//echo "$sql";
		$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
		while ($donnees = $reponse->fetch())
		{
			// Declaration des variables
			$nbrConges		= $donnees['valeur'];
		}
		$reponse->closeCursor();
	}
	if ($nbrRTT == "")
	{
		/////////////////////////////////////////// GESTION DU NOMBRE DE JOURS DE RTT  ///////////////////////////////////////////
		$sql		= "SELECT valeur FROM Configuration where nom like 'nombre_jours_RTT'";
		//echo "$sql";
		$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
		while ($donnees = $reponse->fetch())
		{
			// Declaration des variables
			$nbrRTT		= $donnees['valeur'];
		}
		$reponse->closeCursor();
	}

	
	require("verifier_input_php.php");
	
	if (checkInput($login, "login") != "ok")
	{
		echo checkInput($login, "login");
		exit;
	}
	
	if (checkInput($motDePasse, "password") != "ok")
	{
		echo checkInput($motDePasse, "password");
		exit;
	}

	if (checkInput($prenom, "nom") != "ok")
	{
		echo checkInput($prenom, "nom");
		exit;
	}
	
	if (checkInput($nom, "nom") != "ok")
	{
		echo checkInput($nom, "nom");
		exit;
	}
	
	if (checkInput($nbrHeures, "heuresSemaine") != "ok")
	{
		echo checkInput($nbrHeures, "heuresSemaine");
		exit;
	}

	if (checkInput($nbrConges, "nbrConges") != "ok")
	{
		echo checkInput($nbrConges, "nbrConges");
		exit;
	}

	if (checkInput($nbrRTT, "nbrConges") != "ok")
	{
		echo checkInput($nbrRTT, "nbrConges");
		exit;
	}
	
	if (checkInput($couleur, "couleur") != "ok")
	{
		echo checkInput($couleur, "couleur");
		exit;
	}
	
	if (checkInput($admin, "caseACocher") != "ok")
	{
		echo checkInput($admin, "caseACocher");
		exit;
	}
	
	if (checkInput($active, "caseACocher") != "ok")
	{
		echo checkInput($active, "caseACocher");
		exit;
	}
	

	
	// Si aucune couleur n'est renseignée on met cette par défaut dans la base.
	if ($couleur == "")
	{
		$couleur = "White";
	}

	// On test avant la création. Si le nom et le prénom de la personnes à créer existent déjà en base, on ne lance pas de requete_creation
	$requete_test = "SELECT * from Utilisateurs WHERE login = '$login' OR (nom = '$nom' AND  prenom = '$prenom') ";
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


	// Si un utilisateur ressort du test ci dessus, c'est qu'un couple "nom/prénom" similaire existe déjà en base donc on annule la création.
	$unique = "oui";
	while ($donnees = $reponse_test->fetch())
  	{
		$loginBase = $donnees['login'];
		$unique = "non";
	}
	$reponse_test->closeCursor();


	if (	($unique == "non") && ($login == $loginBase) )
	{
		echo "Un utilisateur ayant le même login existe déjà.";
	} 
	if (	($unique == "non") && ($login != $loginBase) )
	{
		echo "Un utilisateur ayant le même couple \"Nom - Prénom\" existe déjà.";
	} 
	if ($unique == "oui")
	{ 
			// Si cet utilisateur n'existe pas en base, on le créé
			$requete_creation = "INSERT INTO `Utilisateurs` (`idUtilisateurs` , `nom` , `prenom` , `nbrHeuresSemaine` , `nbrConges`, `nbrRTT`, `login` , `motDePasse` , `couleur` , `admin` , `active`) VALUES (NULL , '$nom', '$prenom', '$nbrHeures', '$nbrConges', '$nbrRTT', '$login', md5('$motDePasse'), '$couleur', '$admin', '$active')";
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
			$requete_log = "INSERT INTO Log (`idLog`, `Utilisateurs_idUtilisateurs`, `Utilisateurs_login`, `Date`, `Type`, `Log`) VALUES (NULL, '$idUtilisateur', '$login_session', NOW(), 'création utilisateur', \"$requete_creation\")";
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
			// $log="$requete_creation";

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
			// creer_log("Modification utilisateur", "$requete_creation");


			echo "L'utilisateur $prenom $nom a bien été créé.\n\nSon login est \"$login\",\nSon mot de passe est \"$motDePasse\".";
	}


?>
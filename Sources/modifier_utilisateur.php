<?php include("connexion_base.php"); 
session_start();

include("checkAdmin.php");

//echo "debut script --- ";
$message="";

	$idUtilisateur			=	$_SESSION['idUtilisateurs'];
	$login_session			=	$_SESSION['login'];
	$idAModifier			=	$_GET['idAModifier'];
	$login				=	strtolower($_GET['login']);
	$prenom				=	$_GET['prenom'];
	$nom				=	$_GET['nom'];
	$nbrHeures			=	$_GET['nbrHeures'];
	$nbrConges			=	$_GET['nbrConges'];
	$nbrRTT				=	$_GET['nbrRTT'];
	$couleur			=	$_GET['couleur'];
	$admin				=	$_GET['admin'];
	$active				=	$_GET['active'];


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
	
	if (checkInput($idAModifier, "id") != "ok")
	{
		echo checkInput($idAModifier, "id");
		exit;
	}
	
	if (checkInput($login, "login") != "ok")
	{
		echo checkInput($login, "login");
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

	if (checkInput($nbrRTT, "nbrRTT") != "ok")
	{
		echo checkInput($nbrRTT, "nbrRTT");
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
	
//	echo checkInput($login, "login");

	
	// On test avant la modification. Si tous les elements de l'utilisateur à changer sont les memes, on ne lance pas la modif de requete_modification
	$requete_test = "SELECT * from Utilisateurs WHERE idUtilisateurs = '$idAModifier' AND  nom = '$nom' AND  prenom = '$prenom' AND  nbrHeuresSemaine = '$nbrHeures' AND nbrConges = '$nbrConges' AND nbrRTT = '$nbrRTT' AND  couleur = '$couleur' AND  login = '$login' AND  admin = '$admin' AND  active = '$active'";
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


	// Si un utilisateur ressort du test ci dessus, c'est qu'il n'y a aucune modif à faire car aucune différence entre la page en cours et la base.
	$difference = "presente";
	while ($donnees = $reponse_test->fetch())
  	{
  		$difference = "aucune";  	
		
		if(	($nom 		!= $donnees['nom'])
		||	($prenom 	!= $donnees['prenom'])
		||	($login 	!= $donnees['login'])	)
		{
			$difference 	 = "presente";
		}
	}
	$reponse_test->closeCursor();



	if ($difference == "aucune")
	{
		echo "Vous n'avez apporté aucune modification à cet utilisateur.";
	} 
	if ($difference == "presente")
	{ 
			// Si une difference entre la base et la page est présente, on lance la modif
			$requete_modification = "UPDATE Utilisateurs SET nom = '$nom', prenom = '$prenom', nbrHeuresSemaine = '$nbrHeures', nbrConges = '$nbrConges', nbrRTT = '$nbrRTT', login = '$login', couleur = '$couleur', admin = '$admin', active = '$active' WHERE idUtilisateurs = '$idAModifier' ";
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
			$requete_log = "INSERT INTO Log (`idLog`, `Utilisateurs_idUtilisateurs`, `Utilisateurs_login`, `Date`, `Type`, `Log`) VALUES (NULL, '$idUtilisateur', '$login_session', NOW(), 'modification utilisateur', \"$requete_modification\")";
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


			echo "L'utilisateur $prenom $nom a bien été modifié.";
	}


?>
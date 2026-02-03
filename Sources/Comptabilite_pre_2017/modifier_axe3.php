<?php include("connexion_base.php"); 
session_start();

include("checkAdmin.php");

//echo "debut script --- ";
$message="";

	$idUtilisateur			=	$_SESSION['idUtilisateurs'];
	$login_session			=	$_SESSION['login'];
	$idAxe3				=	$_GET['idAxe3'];
	$nomAxe3				=	$_GET['nomAxe3'];
	$active					=	$_GET['active'];

	
	require("verifier_input_php.php");
	if (checkInput($idAxe3, "id") != "ok")
	{
		echo checkInput($idAxe3, "id");
		exit;
	}
	if (checkInput($nomAxe3, "nomAxe3") != "ok")
	{
		echo checkInput($nomAxe3, "nomAxe3");
		exit;
	}
	if (checkInput($active, "caseACocher") != "ok")
	{
		echo checkInput($active, "caseACocher");
		exit;
	}
	
	
	
	//echo "active = $active --- ";

	// On test avant la modification. Si tous les elements de l'axe3 à changer sont les memes, on ne lance pas la modif de requete_modification
	$requete_test = "SELECT * from Axe3 WHERE idAxe3 = '$idAxe3' AND  nomAxe3 = '$nomAxe3' AND active = '$active'";
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


	// Si un axe3 ressort du test ci dessus, c'est qu'il n'y a aucune modif à faire car aucune différence entre la page en cours et la base.
	$difference = "presente";
	while ($donnees = $reponse_test->fetch())
  	{
  	
  	$difference = "aucune";
  	
  	  	if($nomAxe3 		!= $donnees['nomAxe3'])
		{
  			$difference = "presente";
  		}
  		
	}
	$reponse_test->closeCursor();



	if ($difference == "aucune")
	{
		echo "Vous n'avez apporté aucune modification au axe3 \"$nomAxe3\".";
	} 
	if ($difference == "presente")
	{ 
			// Si une difference entre la base et la page est présente, on lance la modif
			$requete_modification = "UPDATE Axe3 SET nomAxe3 = '$nomAxe3', active = '$active' WHERE idAxe3 = '$idAxe3' ";
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
			$requete_log = "INSERT INTO Log (`idLog`, `Utilisateurs_idUtilisateurs`, `Utilisateurs_login`, `Date`, `Type`, `Log`) VALUES (NULL, '$idUtilisateur', '$login_session', NOW(), 'modification axe3', \"$requete_modification\")";
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



			echo "L'axe3 \"$nomAxe3\" a bien été modifié.";
	}


?>
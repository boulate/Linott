<?php include("connexion_base.php"); 
session_start();

include("checkAdmin.php");

//echo "debut script --- ";
$message="";

	$idUtilisateur			=	$_SESSION['idUtilisateurs'];
	$login_session			=	$_SESSION['login'];
	$typeAxe			=	$_GET['typeAxe'];
	$nomAxe				=	$_GET['nomAxe'];
	$codeAxe			=	$_GET['codeAxe'];
	$idSection			=	$_GET['idSection'];

	require("verifier_input_php.php");
	if (checkInput($nomAxe, "nomAxe") != "ok")
	{
		echo checkInput($nomAxe, "nomAxe");
		exit;
	}
	if (checkInput($codeAxe, $typeAxe) != "ok")
	{
		echo checkInput($codeAxe, $typeAxe);
		exit;
	}
	if (checkInput($typeAxe, "typeAxe") != "ok")
	{
		echo checkInput($typeAxe, "typeAxe");
		exit;
	}
	if ( (checkInput($idSection, "id") != "ok") && ($typeAxe != "Section") )
	{
		echo checkInput($idSection, "id");
		exit;
	}
	
	
	// On test avant la modification. Si le code axe n'existe pas déjà
	$requete_test = "SELECT code$typeAxe from $typeAxe WHERE code$typeAxe = '$codeAxe'";
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
	}
	$reponse_test->closeCursor();



	if ($difference == "aucune")
	{
		echo "Un $typeAxe portant ce code existe déjà.";
	} 
	if ($difference == "presente")
	{ 
			// Si une difference entre la base et la page est présente, on lance la création
			// Si c'est un nouvel axe:
			if (strstr($typeAxe, "Axe"))
			{
				$requete_modification = "INSERT INTO $typeAxe (id$typeAxe, code$typeAxe ,nom$typeAxe, Section_idSection) VALUES (NULL , '$codeAxe', '$nomAxe' , '$idSection')";
			}
			// Si c'est une nouvelle section:
			if ($typeAxe == "Section")
			{
				$requete_modification = "INSERT INTO $typeAxe (id$typeAxe, code$typeAxe ,nom$typeAxe) VALUES (NULL , '$codeAxe', '$nomAxe')";
			}
			
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
			$requete_log = "INSERT INTO Log (`idLog`, `Utilisateurs_idUtilisateurs`, `Utilisateurs_login`, `Date`, `Type`, `Log`) VALUES (NULL, '$idUtilisateur', '$login_session', NOW(), 'creation $typeAxe', \"$requete_modification\")";
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


			if (strstr($typeAxe, "Axe"))
			{
				echo "L'$typeAxe \"$nomAxe\" a bien été créé.";
			}
			if ($typeAxe == "Section")
			{
				echo "La $typeAxe \"$nomAxe\" a bien été créée.";
			}
	}

?>
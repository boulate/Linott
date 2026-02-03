<?php include("connexion_base.php"); 
session_start();

include("checkAdmin.php");

//echo "debut script --- ";
$message="";

	$idUtilisateur			=	$_SESSION['idUtilisateurs'];
	$login_session			=	$_SESSION['login'];
	$nomAxe3			=	$_GET['nomAxe3'];
	$codeAxe3			=	$_GET['codeAxe3'];

	require("verifier_input_php.php");
	if (checkInput($nomAxe3, "nomAxe3") != "ok")
	{
		echo checkInput($nomAxe3, "nomAxe3");
		exit;
	}
	if (checkInput($codeAxe3, "Axe3") != "ok")
	{
		echo checkInput($codeAxe3, "Axe3");
		exit;
	}
	
	
	
	// On test avant la modification. Si l'axe3 n'existe pas déjà
	$requete_test = "SELECT * from Axe3 WHERE nomAxe3 = '$nomAxe3'";
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
		echo "Un axe3 portant ce nom existe déjà.";
	} 
	if ($difference == "presente")
	{ 
			// Si une difference entre la base et la page est présente, on lance la création
			$requete_modification = "INSERT INTO Axe3 (idAxe3, codeAxe3 ,nomAxe3 ,active)VALUES (NULL , '$codeAxe3', '$nomAxe3' , '1')";
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
			$requete_log = "INSERT INTO Log (`idLog`, `Utilisateurs_idUtilisateurs`, `Utilisateurs_login`, `Date`, `Type`, `Log`) VALUES (NULL, '$idUtilisateur', '$login_session', NOW(), 'creation axe3', \"$requete_modification\")";
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



			echo "L'axe3 \"$nomAxe3\" ayant le code \"$codeAxe3\" a bien été créé.";
	}


?>
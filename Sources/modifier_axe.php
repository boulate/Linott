<?php include("connexion_base.php"); 
session_start();

include("checkAdmin.php");

//echo "debut script --- ";
$message="";

	$idUtilisateur				=	$_SESSION['idUtilisateurs'];
	$login_session				=	$_SESSION['login'];
	$idAxe					=	$_GET['idAxe'];
	$nomAxe					=	$_GET['nomAxe'];
	$codeAxe				=	$_GET['codeAxe'];
	$typeAxe				=	$_GET['typeAxe'];
	
	require("verifier_input_php.php");
	if (checkInput($idAxe, "id") != "ok")
	{
		echo checkInput($idAxe, "id");
		exit;
	}
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
	
	
	if (strstr($typeAxe, "Axe"))
	{
		$idSection		=	$_GET['idSection'];
		$andSection 		= 	"AND Section_idSection = $idSection";
	}
	
	
	// On test avant la modification. Si tous les elements de l'axe à changer sont les memes, on ne lance pas la modif de requete_modification
	$requete_test = "SELECT * from $typeAxe WHERE id$typeAxe = '$idAxe' AND  nom$typeAxe = '$nomAxe' AND code$typeAxe = $codeAxe $andSection";
	//echo "$requete_test --- ";
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

  			if($nomAxe 		!= $donnees["nom$typeAxe"])
  			{
  				$difference = "presente";
  			}

	}
	$reponse_test->closeCursor();



	if ($difference == "aucune")
	{
		echo "Vous n'avez apporté aucune modification à l'élément \"$nomAxe\".";
	} 
	if ($difference == "presente")
	{ 
	
		// Si c'est un axe, on ajoute le "setSection" permettant de lier la section à l'axe.
		if (strstr($typeAxe, "Axe"))
		{
			$setSection 		= 	", Section_idSection = $idSection";
		}
		
			// Si une difference entre la base et la page est présente, on lance la modif
			$requete_modification = "UPDATE $typeAxe SET nom$typeAxe = '$nomAxe', code$typeAxe = '$codeAxe' $setSection WHERE id$typeAxe = '$idAxe' ";
	//		echo "$requete_modification --- ";
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
			$requete_log = "INSERT INTO Log (`idLog`, `Utilisateurs_idUtilisateurs`, `Utilisateurs_login`, `Date`, `Type`, `Log`) VALUES (NULL, '$idUtilisateur', '$login_session', NOW(), 'modification axe / session', \"$requete_modification\")";
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



			echo "Le l'axe \"$nomAxe\" a bien été modifié.";
	}


?>
<?php include("connexion_base.php"); 
require("verifier_input_php.php");

session_start();

	$idUtilisateur		=	$_SESSION['idUtilisateurs'];
	$nom_jour_type		=	$_GET['nom_jour_type'];
	for ($i = 1 ; $i <= 12 ; $i++)
	{
		$periode[$i] = str_replace('-', '_', $_GET['periode'.$i]);
		if (str_replace('_', '', $periode[$i]) == '') 
		{
			$periode[$i] = NULL;
		}
	}

	if (checkInput($nom_jour_type, "nom") != "ok")
	{
		echo checkInput($nom_jour_type, "nom");
		exit;
	}

	$requete_insert_jour_type = "INSERT INTO `JoursTypes` (`id`, `idUtilisateur`, `nom`, `periode1`, `periode2`, `periode3`, `periode4`, `periode5`, `periode6`, `periode7`, `periode8`, `periode9`, `periode10`, `periode11`, `periode12`) 
														VALUES ('', $idUtilisateur, '$nom_jour_type', '$periode[1]', '$periode[2]', '$periode[3]', '$periode[4]', '$periode[5]', '$periode[6]', '$periode[7]', '$periode[8]', '$periode[9]', '$periode[10]', '$periode[11]', '$periode[12]')";
	//echo "$requete_insert_jour_type";
	try
	{
		$reponse_insert_jour_type = $bdd->query($requete_insert_jour_type) or die('Erreur SQL !<br>' .$requete_insert_jour_type. '<br>'. mysql_error());
	}
	catch(Exception $e)
	{
		// En cas d'erreur précédemment, on affiche un message et on arrête tout
		die('Erreur : '.$e->getMessage());
	}
	$reponse_insert_jour_type->closeCursor();


	echo "*** Le jour type \"$nom_jour_type\" a bien été rajouté à votre liste.*** \n\n";
	echo "Veuillez raffraichir la page en cliquant sur le logo Linott pour le voir apparaitre.";


?>

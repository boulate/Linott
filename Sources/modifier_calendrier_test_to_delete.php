<?php include("connexion_base.php"); 
session_start();

include("checkAdmin.php");

//echo "debut script --- ";
$message="";

	$idUtilisateurs			=	$_SESSION['idUtilisateurs'];
	$login				=	$_SESSION['login'];
	$date				=	$_GET['date'];
	$type				=	$_GET['type'];
	$valide				=	$_GET['valide'];


	if ($admin != 1 )
	{
		$valide = "N";
	} 	



	// On test avant la modification. Si tous les elements de l'utilisateur à changer sont les memes, on ne lance pas la modif de requete_modification
	$requete_insert = "INSERT INTO CalendrierConges(id, Utilisateurs_idUtilisateurs, Utilisateurs_login, date, type, valide) VALUES ('', '$idUtilisateurs', '$login', '$date', '$type', '$valide')";
	echo "$requete_insert";
	try
	{
		$reponse_insert = $bdd->query($requete_insert) or die('Erreur SQL !<br>' .$requete_insert. '<br>'. mysql_error());
	}
	catch(Exception $e)
	{
		// En cas d'erreur précédemment, on affiche un message et on arrête tout
		die('Erreur : '.$e->getMessage());
	}

	while ($donnees = $reponse_insert->fetch())
  	{
	 	$idUtilisateur		=	$donnees['idUtilisateurs'];
		$difference = "aucune";
	}
	$reponse_insert->closeCursor();


	echo "ziofbzjecz";
	
?>
<?php include("connexion_base.php"); 
session_start();
//echo "debut script --- ";
$message="";

	$idUtilisateur		=	$_SESSION['idUtilisateurs'];
	$id_jour_type	=	$_GET['id_jour_type'];

	//echo "id: $id_fiche_a_suppr";


	$requete_suppression = "DELETE FROM JoursTypes where id = $id_jour_type and idUtilisateur = $idUtilisateur";
	try
	{
		$reponse_suppression = $bdd->query($requete_suppression) or die('Erreur SQL !<br>' .$requete_suppression. '<br>'. mysql_error());
	}
	catch(Exception $e)
	{
		// En cas d'erreur précédemment, on affiche un message et on arrête tout
		die('Erreur : '.$e->getMessage());
	}
	$reponse_suppression->closeCursor();

?>

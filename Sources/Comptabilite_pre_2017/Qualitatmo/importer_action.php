<?php include("connexion_base.php"); 
session_start();

//echo "debut script --- ";
$message="";

	$idUtilisateur			=	$_SESSION['idUtilisateurs'];
	$login_session			=	$_SESSION['login'];
	
	$idAction				=	$_GET['idAction'];
	$idFiche 				=	$_GET['idFiche'];

	$requete_insert_actions = "SELECT * FROM actions WHERE id_fiche_parent = $idFiche";
	//echo "$requete_insert_actions";
	try
	{
		$reponse_insert_actions = $bdd->query($requete_insert_actions) or die('Erreur SQL !<br>' .$requete_insert_actions. '<br>'. mysql_error());
	}
	catch(Exception $e)
	{
		// En cas d'erreur précédemment, on affiche un message et on arrête tout
		die('Erreur : '.$e->getMessage());
	}	
	// Si un axe3 ressort du test ci dessus, c'est qu'il n'y a aucune modif à faire car aucune différence entre la page en cours et la base.
	while ($donnees = $reponse_insert_actions->fetch())
  	{
		$id_action 					= $donnees['id'];
		$date_creation 				= $donnees['date_creation'];
		$id_fiche_parent 			= $donnees['id_fiche_parent'];
		$besoin_action_oui_non 		= $donnees['besoin_action_oui_non'];
		$besoin_actions 			= $donnees['besoin_actions'];
		$type_action_CPA 			= $donnees['type_action_CPA'];
		$responsable_action 		= $donnees['responsable_action'];
		$delai_action 				= $donnees['delai_action'];
		$date_realisation_action 	= $donnees['date_realisation_action'];
		$justificatifs 				= $donnees['justificatifs'];
		$efficacite 				= $donnees['efficacite'];
		$active 					= $donnees['active'];
		
		echo "$id_action;;;$date_creation;;;$id_fiche_parent;;;$besoin_action_oui_non;;;$besoin_actions;;;$type_action_CPA;;;$responsable_action;;;$delai_action;;;$date_realisation_action;;;$justificatifs;;;$efficacite;;;$active/ENDOFLINE/";;;

	}
	$reponse_insert_actions->closeCursor();

?>
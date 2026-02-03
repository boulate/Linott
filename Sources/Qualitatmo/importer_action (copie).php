<?php include("connexion_base.php"); 
session_start();

//echo "debut script --- ";
$message="";

	$idUtilisateur			=	$_SESSION['idUtilisateurs'];
	$login_session			=	$_SESSION['login'];
	
	$idAction				=	$_GET['idAction'];
	$idFicheParent 			=	$_GET['idFicheParent'];

	$numeroAction			=	$_GET['numeroAction'];
	$besoinActionOuiNon		=	$_GET['besoinActionOuiNon'];
	$CPA					=	$_GET['CPA'];
	$besoinActions			=	$_GET['besoinActions'];
	$responsableAction		=	$_GET['responsableAction'];
	$delaiAction			=	$_GET['delaiAction'];
	$dateRealisation		=	$_GET['dateRealisation'];
	$justificatifs 			= 	$_GET['justificatifs'];
	$efficaciteAction		=	$_GET['efficaciteAction'];
	$active					=	1;


	//echo "responsableAction = $responsableAction";

	// require("verifier_input_php.php");
	// if (checkInput($nomAxe3, "nomAxe3") != "ok")
	// {
	// 	echo checkInput($nomAxe3, "nomAxe3");
	// 	exit;
	// }
	// if (checkInput($codeAxe3, "Axe3") != "ok")
	// {
	// 	echo checkInput($codeAxe3, "Axe3");
	// 	exit;
	// }
	
	
	// Si l'action a déjà un ID, on vérifie qu'il y a bien eu changement avant de la modifier.
	if ( $idAction != "" )
	{
		$requete_diff = "SELECT * from actions WHERE id = $idAction";
		//echo "$requete_diff";
		try
		{
			$reponse_diff = $bdd->query($requete_diff) or die('Erreur SQL !<br>' .$requete_diff. '<br>'. mysql_error());
		}
		catch(Exception $e)
		{
			// En cas d'erreur précédemment, on affiche un message et on arrête tout
			die('Erreur : '.$e->getMessage());
		}	
		// Si un axe3 ressort du test ci dessus, c'est qu'il n'y a aucune modif à faire car aucune différence entre la page en cours et la base.
		while ($donnees = $reponse_diff->fetch())
	  	{
			$base_id 					=	$donnees['id'];
			$base_date_creation 			=	$donnees['date_creation'];
			$base_id_fiche_parent 		=	$donnees['id_fiche_parent'];
			$base_besoin_action_oui_non 	=	$donnees['besoin_action_oui_non'];
			$base_besoin_actions 		=	$donnees['besoin_actions'];
			$base_type_action_CPA 		=	$donnees['type_action_CPA'];
			$base_responsable_action 	=	$donnees['responsable_action'];
			$base_delai_action 			=	$donnees['delai_action'];
			$base_date_realisation_action =	$donnees['date_realisation_action'];
			$base_justificatifs 			=	$donnees['justificatifs'];
			$base_efficacite 			=	$donnees['efficacite'];
			$base_active 				=	$donnees['active'];

			if ( 	( $base_besoin_action_oui_non == $besoinActionOuiNon ) && ( $base_besoin_actions == $besoinActions ) && ( $base_type_action_CPA == $CPA ) && ( $base_responsable_action == $responsableAction ) && ( $base_delai_action == $delaiAction ) && ( $base_date_realisation_action == $dateRealisation ) && ( $base_justificatifs == $justificatifs ) && ( $base_efficacite == $efficaciteAction ) && ( $base_active == $active )	)
			{
				echo "L'action $idAction n'a pas changée.";
			}
			else
			{
				// On enregistre l'action
				$requete_update = "UPDATE actions SET besoin_action_oui_non = '$besoinActionOuiNon',  besoin_actions = '$besoinActions', type_action_CPA = '$CPA', responsable_action = '$responsableAction', delai_action = '$delaiAction', date_realisation_action = '$dateRealisation', justificatifs = '$justificatifs', efficacite = '$efficaciteAction', active = '$active' WHERE id = '$idAction' ";
				echo "$requete_update";
				try
				{
					$reponse_update = $bdd->query($requete_update) or die('Erreur SQL !<br>' .$requete_update. '<br>'. mysql_error());
				}
				catch(Exception $e)
				{
					// En cas d'erreur précédemment, on affiche un message et on arrête tout
					die('Erreur : '.$e->getMessage());
				}
				$reponse_update->closeCursor();
			}
		}
		$reponse_diff->closeCursor();

	}

if ( $idAction == "" )
{
	$requete_last_insert = "SELECT MAX(id_fiche) as maxi from fiche";
	//echo "$requete_last_insert";
	try
	{
		$reponse_last_insert = $bdd->query($requete_last_insert) or die('Erreur SQL !<br>' .$requete_last_insert. '<br>'. mysql_error());
	}
	catch(Exception $e)
	{
		// En cas d'erreur précédemment, on affiche un message et on arrête tout
		die('Erreur : '.$e->getMessage());
	}	
	// Si un axe3 ressort du test ci dessus, c'est qu'il n'y a aucune modif à faire car aucune différence entre la page en cours et la base.
	while ($donnees = $reponse_last_insert->fetch())
  	{
		$id_max = $donnees['maxi'];
	}
	$reponse_last_insert->closeCursor();

	$id_futur_fiche = $id_max + 1;



	echo "fiche parent: $idFicheParent";	
	if ( $idFicheParent != "" )
	{
		$id_futur_fiche = $idFicheParent;
	}


	// On enregistre l'action
	$requete_creation = "INSERT INTO actions 	(id, date_creation, id_fiche_parent, besoin_action_oui_non, besoin_actions, type_action_CPA, responsable_action, delai_action, date_realisation_action, justificatifs, efficacite, active) 
						VALUES 					('' , NOW(), '$id_futur_fiche' , '$besoinActionOuiNon', '$besoinActions', '$CPA', '$responsableAction', '$delaiAction', '$dateRealisation', '', '$efficaciteAction', '$active')";
	echo "$requete_creation";
	try
	{
		$reponse_modification = $bdd->query($requete_creation) or die('Erreur SQL !<br>' .$requete_creation. '<br>'. mysql_error());
	}
	catch(Exception $e)
	{
		// En cas d'erreur précédemment, on affiche un message et on arrête tout
		die('Erreur : '.$e->getMessage());
	}
	$reponse_modification->closeCursor();
}




			

?>
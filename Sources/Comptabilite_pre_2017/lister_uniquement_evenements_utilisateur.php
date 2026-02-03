<?php include("connexion_base.php"); 
session_start();

//include("checkAdmin.php");

//echo "debut script --- ";
//$message="";
	$idUtilisateurs		=	$_SESSION['idUtilisateurs'];
	$login				=	$_SESSION['login'];
	$groupes 			=	$_SESSION['groupes'];
	$mois				=	$_GET['mois'];
	$annee				=	$_GET['annee'];

	$tableIdGroupeSession		=	explode(",", $groupes);
	//print_r("groupes du l'utilisateur: $tableIdGroupeSession -----");
	//echo "groupes: $groupes --- ";

	// On selectionne d'abord les évenements
	$requete_select = "select id, type, Utilisateurs_idUtilisateurs, id_utilisateurs_concernes, id_groupes_concernes from CalendrierConges WHERE MONTH(date) = '$mois' AND YEAR(date) = '$annee'";
	//echo "$requete_select";
	try
	{
		$reponse_select = $bdd->query($requete_select) or die('Erreur SQL !<br>' .$requete_select. '<br>'. mysql_error());
	}
	catch(Exception $e)
	{
		// En cas d'erreur précédemment, on affiche un message et on arrête tout
		die('Erreur : '.$e->getMessage());
	}
	$tableIdConges = array();
	while ($donnees = $reponse_select->fetch())
  	{
	 	$idConge 						= $donnees['id'] ;
	 	$idUtilisateurBDD 				= $donnees['Utilisateurs_idUtilisateurs'];
	 	$type							= $donnees['type'];
	 	$listeUtilisateursConcernes 	= $donnees['id_utilisateurs_concernes'];
	 	$listeGroupesConcernes 			= $donnees['id_groupes_concernes'];
	 	
	 	// Si l'idUtilisateur de la session est égal à l'id en BDD lié à l'évènement et que ce n'est pas une astreinte (car ce qui importe sur les astreintes c'est la personne d'astreinte et pas celle qui la crée), on le rajoute a la tableidconges
	 	if ( ($idUtilisateurs == $idUtilisateurBDD) && ( $type != "astreinte") )
	 	{
			array_push($tableIdConges, $idConge);
		}
		
		// Si dans la liste des utilisateurs concernés par l'évènement on trouve l'id de l'utilisateur en cours, on le rajoute à la tableidconges
		$tableIdUtilisateurs = explode(",", $listeUtilisateursConcernes);
		foreach ($tableIdUtilisateurs as $idTable)
		{
			if ($idTable == $idUtilisateurs)
			{
				array_push($tableIdConges, $idConge);
			}
			if ($idTable == "ALL")
			{
				array_push($tableIdConges, $idConge);	
			}
		}
		
		// Si dans la liste des groupes concernés par l'événement, on trouve un groupe dans lequel est inscrit l'utilisateur, on met l'évenement dans la tableidconges
		$tableIdGroupesEvent = explode(",", $listeGroupesConcernes);
		// Pour chaque groupe lié à l'évenement
		foreach ($tableIdGroupesEvent as $idTable)
		{
			// et pour chaque groupe de la session utilisateur
			foreach ($tableIdGroupeSession as $idGroupeSession)
				// Si l'id du groupe de l'évenement est égal à l'id du coupe de l'utilisateur, on l'ajoute à la tableidconges
				if ($idTable == $idGroupeSession)
				{
					array_push($tableIdConges, $idConge);
				}
				if ($idTable == "ALL")
				{
					array_push($tableIdConges, $idConge);	
				}
			}

	}
	$reponse_select->closeCursor();
	//print_r($tableIdConges);

	$listeIdConges = implode(",", $tableIdConges);
	echo "$listeIdConges";

?>
<?php include("connexion_base.php"); 
session_start();

//include("checkAdmin.php");

//echo "debut script --- ";
//$message="";
	$idUtilisateurs		=	$_SESSION['idUtilisateurs'];
	$login				=	$_SESSION['login'];
	$mois				=	$_GET['mois'];
	$annee				=	$_GET['annee'];

// Permet de trier les sorties vers le calendrier. D'abord les events, puis les conges de l'utilisateur en cours, puis les congés des autres.
lancer_requete("AND type = 'astreinte'");
lancer_requete("AND type = 'event'");
lancer_requete("AND Utilisateurs_idUtilisateurs = $idUtilisateurs AND type = 'absence'");
lancer_requete("AND Utilisateurs_idUtilisateurs != $idUtilisateurs AND type = 'absence'");

function lancer_requete($andRequete)
{
	global $bdd;

	global $idUtilisateurs;
	global $login;
	global $mois;
	global $annee;
	
	// On selectionne d'abord les évenements
	//$requete_select = "select * from CalendrierConges where MONTH(date) = $mois";
	//$requete_select = "select id, Utilisateurs_idUtilisateurs, Utilisateurs_login, date, type, valide, description, bloquant, couleur from CalendrierConges, Utilisateurs where MONTH(date) = $mois AND YEAR(date) = $annee AND CalendrierConges.Utilisateurs_idUtilisateurs = Utilisateurs.idUtilisateurs $andRequete";
	$requete_select = "select id, Utilisateurs_idUtilisateurs, Utilisateurs_login, date, periode, type, valide, description, bloquant, couleur from CalendrierConges, Utilisateurs where MONTH(date) = $mois AND YEAR(date) = $annee AND CalendrierConges.Utilisateurs_idUtilisateurs = Utilisateurs.idUtilisateurs $andRequete";
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

	while ($donnees = $reponse_select->fetch())
  	{
	 	$idConge = $donnees['id'] ;
	 	$idUtilisateursConge = $donnees['Utilisateurs_idUtilisateurs'] ;
	 	$loginUtilisateursConge = $donnees['Utilisateurs_login'] ;
	 	$date = $donnees['date'] ;
	 	$periode = $donnees['periode'];
	 	$type = $donnees['type'] ;
	 	$valide = $donnees['valide'] ;
	 	$description = $donnees['description'] ;
	 	$bloquant = $donnees['bloquant'] ;

	 	$couleur = $donnees['couleur'];

	 	if ($type == "astreinte")
	 	{
	 		$description = obtenir_nom($description);
	 	}
	 	
		echo "$idConge;$idUtilisateursConge;$loginUtilisateursConge;$couleur;$date;$periode;$type;$valide;$description;$bloquant//";	
	}
	$reponse_select->closeCursor();
}

function obtenir_nom($idUtilisateur)
{
	global $bdd;

	$requete_nom = "SELECT nom, prenom FROM Utilisateurs WHERE idUtilisateurs = '$idUtilisateur' ";
	//echo "$requete_nom";
	try
	{
		$reponse_nom = $bdd->query($requete_nom) or die('Erreur SQL !<br>' .$requete_nom. '<br>'. mysql_error());
	}
	catch(Exception $e)
	{
		// En cas d'erreur précédemment, on affiche un message et on arrête tout
		die('Erreur : '.$e->getMessage());
	}

	while ($donnees = $reponse_nom->fetch())
  	{
	 	$nom = $donnees['nom'] ;
	 	$prenom = $donnees['prenom'][0] ;

	 	return "$prenom.$nom";
	}
	$reponse_nom->closeCursor();
}

function vacances()
{

	global $bdd;

	$requete_nom = "SELECT cdate FROM `Calendar` , zonesVacances WHERE cdate >= dateDebut AND cdate < dateFin AND zone = 'B' AND YEAR( cdate ) = '2014' AND MONTH( cdate ) = '04'";
	//echo "$requete_nom";
	try
	{
		$reponse_nom = $bdd->query($requete_nom) or die('Erreur SQL !<br>' .$requete_nom. '<br>'. mysql_error());
	}
	catch(Exception $e)
	{
		// En cas d'erreur précédemment, on affiche un message et on arrête tout
		die('Erreur : '.$e->getMessage());
	}

	while ($donnees = $reponse_nom->fetch())
  	{
	 	$date = $donnees['cdate'] ;

		echo "0;0;Vacances;RED;$date;JO;vacances;V;vacances;0//";	
	}
	$reponse_nom->closeCursor();

}

?>
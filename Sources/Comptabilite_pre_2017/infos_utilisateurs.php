<?php include("connexion_base.php"); 
session_start();

//include("checkAdmin.php");

//echo "debut script --- ";
//$message="";
	$idUtilisateurs		=	$_SESSION['idUtilisateurs'];
	$login				=	$_SESSION['login'];
	$groupes 			=	$_SESSION['groupes'];
	$demande			=	$_GET['demande'];



if ($demande == "nombre_actifs")
{
	$requete_select = "select count(idUtilisateurs) as total FROM Utilisateurs WHERE active = 1";
	//echo "$requete_select<br>";
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
  		echo $donnees['total'];
	}
	$reponse_select->closeCursor();
}
if ($demande == "infos_users_actifs")
{
	$requete_select = "select idUtilisateurs, nom, prenom, login, couleur FROM Utilisateurs WHERE active = 1 ORDER BY nom";
	//echo "$requete_select<br>";
	try
	{
		$reponse_select = $bdd->query($requete_select) or die('Erreur SQL !<br>' .$requete_select. '<br>'. mysql_error());
	}
	catch(Exception $e)
	{
		// En cas d'erreur précédemment, on affiche un message et on arrête tout
		die('Erreur : '.$e->getMessage());
	}
	$tableIdUtilisateurs = array();
	while ($donnees = $reponse_select->fetch())
  	{
  		$id 	= $donnees['idUtilisateurs'];
  		$nom 	= $donnees['nom'];
  		$prenom = $donnees['prenom'];
  		$login 	= $donnees['login'];
  		$couleur = $donnees['couleur'];


  		array_push($tableIdUtilisateurs, "$id;$nom;$prenom;$login;$couleur");
	}
	$reponse_select->closeCursor();

	$listeIdUtilisateurs = implode(",", $tableIdUtilisateurs);
	echo "$listeIdUtilisateurs";
}














?>
<?php 
session_start();
include("connexion_base.php"); 

$loginSession		=	$_SESSION['login'];
$idUtilisateur		=	$_SESSION['idUtilisateurs'];

$type				=	$_GET['type'];




$sql		= "SELECT preferences_masque_id_axe1, preferences_masque_id_axe2, preferences_masque_id_axe3, afficher_astreintes FROM Utilisateurs where idUtilisateurs = $idUtilisateur";
//echo "$sql";
$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());

//$i=1;
while ($donnees = $reponse->fetch())
{
	// Declaration des variables
	$preferences_masque_id_axe1			= $donnees['preferences_masque_id_axe1'];
	$preferences_masque_id_axe2			= $donnees['preferences_masque_id_axe2'];
	$preferences_masque_id_axe3	 		= $donnees['preferences_masque_id_axe3'];
	$preferences_afficher_astreintes	= $donnees['afficher_astreintes'];
}

// A traiter plus tard, modifier l'appel des prefs axes pour leur ajouter un type.
if ($type == "afficher_axes")
{
	echo "$preferences_masque_id_axe1;$preferences_masque_id_axe2;$preferences_masque_id_axe3";
}

if ($type == "afficher_astreintes")
{
	echo "$preferences_afficher_astreintes";
}


?>

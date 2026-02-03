<?php 
session_start();
include("connexion_base.php"); 

$loginSession		=	$_SESSION['login'];
$idUtilisateur		=	$_SESSION['idUtilisateurs'];

$type				=	$_GET['type'];
$valeur				=	$_GET['valeur'];

// La deuxieme condition empeche un utilisateur de se modifier lui même son profil pour se mettre admin.
if ( ( $type != "") && ( $type != "admin" ) )
{
	$sql		= "UPDATE Utilisateurs SET afficher_astreintes = $valeur WHERE idUtilisateurs = $idUtilisateur";
	echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());

	while ($donnees = $reponse->fetch())
	{

	}
}

?>

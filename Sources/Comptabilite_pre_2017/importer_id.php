<?php 
session_start();
include("connexion_base.php"); 

$loginSession		=	$_SESSION['login'];
$idUtilisateur		=	$_SESSION['idUtilisateurs'];

//require("checkAdmin.php");

	///////////////////////////////////////////   ///////////////////////////////////////////
	$sql		= "SELECT idAxe1 FROM Axe1 where codeAxe1 like '50'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		// Declaration des variables
		$idAxe1Code50	= $donnees['idAxe1'];
	}
	$reponse->closeCursor();


	///////////////////////////////////////////   ///////////////////////////////////////////
	$sql		= "SELECT idAxe2 FROM Axe2 where codeAxe2 like '5000'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		// Declaration des variables
		$idAxe2Code5000	= $donnees['idAxe2'];
	}
	$reponse->closeCursor();
	

	?>

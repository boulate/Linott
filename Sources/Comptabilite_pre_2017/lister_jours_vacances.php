<?php 
session_start();
include("connexion_base.php"); 

$loginSession		=	$_SESSION['login'];
$idUtilisateur		=	$_SESSION['idUtilisateurs'];

$zone = $_GET['zone'];
$mois = $_GET['mois'];
$annee = $_GET['annee'];

//require("checkAdmin.php");

	///////////////////////////////////////////   ///////////////////////////////////////////
	$sql		= "SELECT cdate FROM `Calendar` , zonesVacances WHERE cdate >= dateDebut AND cdate < dateFin AND zone = '$zone' AND YEAR( cdate ) = '$annee' AND MONTH( cdate ) = '$mois' ";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		// Declaration des variables
		$date		= $donnees['cdate'];
		echo "$date;";
	}
	$reponse->closeCursor();

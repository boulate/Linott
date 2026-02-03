<?php include("connexion_base.php");
session_start();
//include("checkAdmin.php"); 


//echo "debut de la page <br><br>";
$idUtilisateur		=	$_SESSION['idUtilisateurs'];
$nomUtilisateur		=	$_SESSION['login']; 
//echo "nombre de periodes=$nombre_periodes </br><br>";
$idEvenement 		=	$_GET['idEvenement'];

$select		= "SELECT * FROM CalendrierConges where id = '$idEvenement'";
$reponse = $bdd->query($select) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());

//$i=1;
while ($donnees = $reponse->fetch())
{
	// Declaration des variables
	$idEvenementBDD=$donnees['id'];
	$idUtilisateurEvenement = $donnees['Utilisateurs_idUtilisateurs'];
	$loginUtilisateurEvenement = $donnees['Utilisateurs_login'];
	$dateEvenement = $donnees['date'];
	$periodeEvenement = $donnees['periode'];
	$typeEvenement = $donnees['type'];
	$validation = $donnees['valide'];
	$description = $donnees['description'];
	$bloquant = $donnees['bloquant'];


	echo "$idEvenement;";
	echo "$idUtilisateurEvenement;";
	echo "$loginUtilisateurEvenement;";
	echo "$dateEvenement;";
	echo "$periodeEvenement;";
	echo "$typeEvenement;";				
	echo "$validation;";
	echo "$description;";
	echo "$bloquant";
}



?>

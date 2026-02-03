
<?php include("connexion_base.php"); 
session_start(); ?>
<?php require("checkAdmin.php"); ?>

<?php
	$id_session		=	$_SESSION['idUtilisateurs'];
	$login_session		=	$_SESSION['login'];
	$loginUtilisateur	=	$_GET['loginUtilisateur'];
	$idUtilisateur		=	$_GET['idUtilisateur'];
	$nbrHeuresRachat	=	$_GET['nbr'];

require("verifier_input_php.php");
if (checkInput($idUtilisateur, "id") != "ok")
{
	echo checkInput($idUtilisateur, "id");
	exit;
}
if (checkInput($loginUtilisateur, "login") != "ok")
{
	echo checkInput($loginUtilisateur, "login");
	exit;
}
if (checkInput($nbrHeuresRachat, "rachatHeures") != "ok")
{
	echo checkInput($nbrHeuresRachat, "rachatHeures");
	exit;
}

if ($idUtilisateur !="" && $nbrHeuresRachat != "")
{
	$requete_rachat = "INSERT INTO RachatHeures (date, nbr, Utilisateurs_idUtilisateurs, Utilisateurs_login) values (CURDATE(), $nbrHeuresRachat, $idUtilisateur, '$loginUtilisateur')";
	//echo "$requete_rachat";	
	try
	{
		$reponse_rachat = $bdd->query($requete_rachat) or die('Erreur SQL !<br>' .$requete_rachat. '<br>'. mysql_error());
	}
	catch(Exception $e)
	{
		// En cas d'erreur précédemment, on affiche un message et on arrête tout
		die('Erreur : '.$e->getMessage());
	}
	$reponse_rachat->closeCursor();
	
	
	
	
	// On ecrit un log de ce qui se passe dans la table "Log".
	$requete_log = "INSERT INTO Log (`idLog`, `Utilisateurs_idUtilisateurs`, `Utilisateurs_login`, `Date`, `Type`, `Log`) VALUES (NULL, '$id_session', '$login_session', NOW(), 'rachat heures', \"$requete_rachat\")";
	//echo "$requete_log";
	try
	{
		$reponse_log = $bdd->query($requete_log) or die('Erreur SQL !<br>' .$requete_log. '<br>'. mysql_error());
	}
	catch(Exception $e)
	{
		// En cas d'erreur précédemment, on affiche un message et on arrête tout
		die('Erreur : '.$e->getMessage());
	}
	$reponse_log->closeCursor();
	
	
	

	echo "Prise en compte du rachat de $nbrHeuresRachat heure(s) à $loginUtilisateur effectuée.";
}
?>

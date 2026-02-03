<?php
session_start();
include("connexion_base.php");

	$loginToVerif = strtolower($_POST['login']);
	$passToVerif = ($_POST['password']);
	$hashPassToVerif = md5($passToVerif);

$erreur_login="vide";

// J'importe ma page php permettant de vérifier les inputs (simpliste mais réduit un peu les risques d'injections sql, etc.)
require("verifier_input_php.php");
if ( checkInput($loginToVerif, "login") != "ok" )
{
	erreur_login(checkInput($loginToVerif, "login"));
}
if ( checkInput($passToVerif, "password") != "ok" )
{
	erreur_login(checkInput($passToVerif, "password"));
}


	$sql = "SELECT * FROM Utilisateurs WHERE login = '$loginToVerif'" ;
	//echo "sql: $sql";
	try
	{
		$reponse_login = $bdd->query($sql) or die('Erreur SQL !<br>' .$sql. '<br>'. mysql_error());
	}
	catch(Exception $e)
	{
		// En cas d'erreur précédemment, on affiche un message et on arrête tout
		die( 'Erreur au moment du login : '.$e->getMessage() );
	}


	while ($donnees = $reponse_login->fetch())
	{

		$idUtilisateurs		=	$donnees['idUtilisateurs'];
		$nom			=	$donnees['nom'];
		$prenom			=	$donnees['prenom'];
		$nbr_heures		=	$donnees['nbrHeuresSemaine'];
		$nbrConges		=	$donnees['nbrConges'];
		$nbrRTT			=	$donnees['nbrRTT'];
		$login			=	$donnees['login'];
		$pass			=	$donnees['motDePasse'];
		$admin			=	$donnees['admin'];
		$active			=	$donnees['active'];
		$couleur		=	$donnees['couleur'];

		if ( ($loginToVerif == $login) && ($hashPassToVerif == $pass) )
		{

			if ( ($active == 1) || ($login == "admin") ) 
			{	
				$message_accueil		= "Bonjour $prenom";
				$_SESSION['idUtilisateurs'] 	= $idUtilisateurs;
				$_SESSION['nom'] 		= $nom;
				$_SESSION['prenom'] 		= $prenom;
				$_SESSION['nbr_heures'] 	= $nbr_heures;
				$_SESSION['nbrConges']	= $nbrConges;
				$_SESSION['nbrRTT']		= $nbrRTT;
				$_SESSION['login'] 		= $login;
				$_SESSION['admin'] 		= $admin;
				$_SESSION['couleur']	= $couleur;


				#echo "$idUtilisateurs, $nom, $prenom, $nbr_heures, $login, $pass";

				$_SESSION['groupes']	=	obtenir_groupes($idUtilisateurs);

				header('Location: compta.php'); 
				exit;
			}
			else
			{
				erreur_login("Ce compte est désactivé.");
			}
	
		}
		else
		{
			erreur_login("Mauvais login / mot de passe.");
		}
	}
	
	
// Si erreur login est égal à "vide" (et donc qu'il n'est pas passé dans la moulinette d'en haut), on lance une erreur de login.
if ($erreur_login == "vide")
{
    erreur_login("Mauvais login / mot de passe.");
}


function erreur_login($erreur)
{
		header("Location: index.php?error=$erreur");
		exit;
}

function obtenir_groupes($idSession)
{
	global $bdd;
	$tableGroupesUser = array();

	$sqlGroupes = "SELECT * FROM Groupes" ;
	// echo "sqlGroupes: $sqlGroupes"; // Debug désactivé
	try
	{
		$reponse_Groupes = $bdd->query($sqlGroupes) or die('Erreur SQL !<br>' .$sqlGroupes. '<br>'. mysql_error());
	}
	catch(Exception $e)
	{
		// En cas d'erreur précédemment, on affiche un message et on arrête tout
		die( 'Erreur au moment du login : '.$e->getMessage() );
	}
	while ($donneesGroupes = $reponse_Groupes->fetch())
	{
		$idGroupe					=	$donneesGroupes['id'];
		$nom						=	$donneesGroupes['nom'];
		$listeUtilisateurs			=	$donneesGroupes['idUtilisateurs'];

		$tableIdsGroupeEnCours		=	explode(",", $listeUtilisateurs);
		foreach ($tableIdsGroupeEnCours as $idEnCours) 
		{
			if ($idEnCours == $idSession) 
			{
				array_push($tableGroupesUser, $idGroupe);
			}	
		}

	}

	$listeGroupesUser = implode(",", $tableGroupesUser);
	return "$listeGroupesUser";
}




?>






</body>

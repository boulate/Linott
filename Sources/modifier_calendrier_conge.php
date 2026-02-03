<?php require("connexion_base.php"); 
session_start();

//require("checkAdmin.php");
require("verifier_input_php.php");

//echo "debut script --- ";
$message="";

	$idUtilisateurs		=	$_SESSION['idUtilisateurs'];
	$login				=	$_SESSION['login'];
	$prenom				=	$_SESSION['prenom'];
	$nom				=	$_SESSION['nom'];
	$date				=	$_GET['date'];
	$periode			=	$_GET['periodeEvenement'];
	$type				=	$_GET['typeEvenement'];
	$valide				=	$_GET['valide'];
	$description		=	addslashes($_GET['description']);
	$bloquant			=	$_GET['bloquant'];
	$indisponible		=	$_GET['indisponible'];

	$listeUtilisateursConcernes = "";
	$listeGroupesConcernes = "";
	if ( $type == "event" )
	{
		$listeUtilisateursConcernes	=	$_GET['listeUtilisateursConcernes'];
		$listeGroupesConcernes 		= 	$_GET['listeGroupesConcernes'];
	}
	if ( $type == "absence" )
	{
		$listeUtilisateursConcernes	=	$idUtilisateurs;		
	}
	if ( $type == "astreinte" )
	{
		$listeUtilisateursConcernes	=	$description;		
	}

	$initialeNom		=	$nom;


	if (checkInput($type, "typeEvenementCalendrier") != "ok")
	{
		echo checkInput($type, "typeEvenementCalendrier");
		exit;
	}

	$admin	=	$_SESSION['admin'];
	// if ( $admin != 1 )
	// {
	// 	$valide = "N";
	// } 	

	// On défini $andPeriode qui verifiera si la période demandée n'est pas bloquée.
	$andPeriode = "" ;
	if ( $periode == "MA" )
	{
		 $andPeriode = "AND ( periode = '$periode' OR periode = 'JO' )";
	}
	if ( $periode == "AM" )
	{
		 $andPeriode = "AND ( periode = '$periode' OR periode = 'JO' )";
	}
	if ( $periode == "JO" )
	{
		 $andPeriode = "AND ( periode = '$periode' OR periode = 'MA' OR periode = 'AM' )";
	}

	$texte = "" ;
	if ( $type == "absence" && $admin != 1)
	{
		// On fait attention à ce que ce jour ne soit pas bloqué par un évènement bloquant.
		$requete_test_blocage = "SELECT * FROM CalendrierConges WHERE date = '$date' AND bloquant = '1' $andPeriode ";
		//echo "$requete_test_blocage";
		try
		{
			$reponse_test_blocage = $bdd->query($requete_test_blocage) or die('Erreur SQL !<br>' .$requete_test_blocage. '<br>'. mysql_error());
		}
		catch(Exception $e)
		{
			// En cas d'erreur précédemment, on affiche un message et on arrête tout
			die('Erreur : '.$e->getMessage());
		}
		while ($donnees = $reponse_test_blocage->fetch() )
	  	{
	  		$description = "";
	  		$description = $donnees['description'];

	  		$dateLisible = date("d/m/Y", strtotime($date));

			$texte = "$dateLisible: L'évènement \"$description\" a été défini comme bloquant.\n\nVous ne pouvez donc pas faire de demande d'absence sur cette période."	;
		}
		$reponse_test_blocage->closeCursor();
	}


	if ( $texte == "" )
	{
		// On test avant la modification. Si tous les elements de l'utilisateur à changer sont les memes, on ne lance pas la modif de requete_modification
		$requete_insert = "INSERT INTO CalendrierConges(id, Utilisateurs_idUtilisateurs, Utilisateurs_login, date, periode, type, valide, description, indisponible, bloquant, id_utilisateurs_concernes, id_groupes_concernes, date_creation) VALUES ('', '$idUtilisateurs', '$login', '$date', '$periode', '$type', '$valide', '$description', '$indisponible', '$bloquant', '$listeUtilisateursConcernes', '$listeGroupesConcernes', NOW() )";
		echo "$requete_insert";
		try
		{
			$reponse_insert = $bdd->query($requete_insert) or die('Erreur SQL !<br>' .$requete_insert. '<br>'. mysql_error());
		}
		catch(Exception $e)
		{
			// En cas d'erreur précédemment, on affiche un message et on arrête tout
			die('Erreur : '.$e->getMessage());
		}
	}
	if ( $texte != "" )
	{
		echo "$texte";
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
<head><!-- Permet de se connecter à la base Mysql-->


<script>

function closeWindows()
{
	document.location.href='preferences.php';
}
</script> 
</head>

<body onLoad="closeWindows();">

<?php include("connexion_base.php"); 
session_start();

$message="";

$idUtilisateur		=	$_SESSION['idUtilisateurs'];

$type_preference	=	$_GET['pref'];

function update_pref_axe1($idUtilisateur)
{
	global $bdd;
	$insert = "";
	
	// Je vais chercher tous les id axes existants
	$reponse_tous = $bdd->query('SELECT idAxe1 FROM Axe1');
	
	// Je les mets dans un tableau
	$tableauTousId = array();
	while ($donnees = $reponse_tous->fetch())
	{
		$id = $donnees['idAxe1'];
		array_push($tableauTousId, $id);
	}

	// Je vais interroger les checkbox correspondantes
	foreach ($tableauTousId as $id)
	{
		// Je controle leur présence
		$test = $_POST[$id];
		// Si ils ne sont pas cochés, j'affiche leur id
		if (!isset($test))
		{
			if ($insert != "")
			{
				$insert = "$insert,$id";
			}
			if ($insert == "")
			{
				$insert = "$id";	
			}

		}
	
	}
	echo "$insert";
	$reponse_tous->closeCursor();
	
	// Mise à jour de la base de données
	$maj_pref = "UPDATE Utilisateurs SET preferences_masque_id_axe1 = \"$insert\" WHERE idUtilisateurs = $idUtilisateur";
	try
	{
		$reponse_update = $bdd->query($maj_pref) or die('Erreur SQL !<br>' .$sql. '<br>'. mysql_error());
	}
	catch(Exception $e)
	{
		// En cas d'erreur précédemment, on affiche un message et on arrête tout
		die('Erreur : '.$e->getMessage());
	}
	$reponse_update->closeCursor();
}

function update_pref_axe2($idUtilisateur)
{
	global $bdd;
	$insert = "";
	
	// Je vais chercher tous les id axes existants
	$reponse_tous = $bdd->query('SELECT idAxe2 FROM Axe2');
	
	// Je les mets dans un tableau
	$tableauTousId = array();
	while ($donnees = $reponse_tous->fetch())
	{
		$id = $donnees['idAxe2'];
		array_push($tableauTousId, $id);
	}

	// Je vais interroger les checkbox correspondantes
	foreach ($tableauTousId as $id)
	{
		// Je controle leur présence
		$test = $_POST[$id];
		// Si ils ne sont pas cochés, j'affiche leur id
		if (!isset($test))
		{
			if ($insert != "")
			{
				$insert = "$insert,$id";
			}
			if ($insert == "")
			{
				$insert = "$id";	
			}

		}
	
	}
	echo "$insert";
	$reponse_tous->closeCursor();
	
	// Mise à jour de la base de données
	$maj_pref = "UPDATE Utilisateurs SET preferences_masque_id_axe2 = \"$insert\" WHERE idUtilisateurs = $idUtilisateur";
	try
	{
		$reponse_update = $bdd->query($maj_pref) or die('Erreur SQL !<br>' .$sql. '<br>'. mysql_error());
	}
	catch(Exception $e)
	{
		// En cas d'erreur précédemment, on affiche un message et on arrête tout
		die('Erreur : '.$e->getMessage());
	}
	$reponse_update->closeCursor();
}

function update_pref_axe3($idUtilisateur)
{
	global $bdd;
	$insert = "";
	
	
	// Je vais chercher tous les id axe3 existants
	$reponse_tous = $bdd->query('SELECT idAxe3 FROM Axe3 where active=1');
	
	// Je les mets dans un tableau
	$tableauTousId = array();
	while ($donnees = $reponse_tous->fetch())
	{
		$id = $donnees['idAxe3'];
		array_push($tableauTousId, $id);
	}

	// Je vais interroger les checkbox correspondantes
	foreach ($tableauTousId as $id)
	{
		// Je controle leur présence
		$test = $_POST[$id];
		// Si ils ne sont pas cochés, j'affiche leur id
		if (!isset($test))
		{
			if ($insert != "")
			{
				$insert = "$insert,$id";
			}
			if ($insert == "")
			{
				$insert = "$id";	
			}

		}
	}
	echo "$insert";
	$reponse_tous->closeCursor();
	
	// Mise à jour de la base de données
	$maj_pref = "UPDATE Utilisateurs SET preferences_masque_id_axe3 = \"$insert\" WHERE idUtilisateurs = $idUtilisateur";
	try
	{
		$reponse_update = $bdd->query($maj_pref) or die('Erreur SQL !<br>' .$sql. '<br>'. mysql_error());
	}
	catch(Exception $e)
	{
		// En cas d'erreur précédemment, on affiche un message et on arrête tout
		die('Erreur : '.$e->getMessage());
	}
	$reponse_update->closeCursor();
}



if ($type_preference == "axe1")
{
	update_pref_axe1($idUtilisateur);
}
if ($type_preference == "axe2")
{
	update_pref_axe2($idUtilisateur);
}
if ($type_preference == "axe3")
{
	update_pref_axe3($idUtilisateur);
}
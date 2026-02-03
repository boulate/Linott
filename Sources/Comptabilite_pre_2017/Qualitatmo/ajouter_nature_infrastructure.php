<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<HTML xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >

<?php session_start(); ?>

<HEAD>

<?php // Permet de rediriger vers l'acceuil si utilisateur non enregistré.
	$prenom = $_SESSION['prenom'];
	if (!$prenom)
	{
		header('Location: index.php'); 
	}
?>

<TITLE>Comptabilite analytique</TITLE>
	
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<!-- Mon thème -->
	<link rel="stylesheet" href="style.css" />

	<!-- Integration de jquery calendar http://jqueryui.com/datepicker/ -->
	<link rel="stylesheet" href="CSS/jquery-ui.css" />
	<script src="jquery-1.8.3.js"></script>
	<script src="jquery-ui.js"></script>

	<link rel="stylesheet" href="CSS/Delta/css/normalise.css"> 
	<link rel="stylesheet" href="CSS/Delta/theme/jquery-ui.css">
	<script src="CSS/Delta/js/modernizr-2.0.6.min.js"></script>
	<link rel="icon" type="image/png" href="favicon.png" />

</HEAD>

<BODY>

<?php

include("connexion_base.php");



	$type="Infrastructure";
	$new_nature = $_POST['new_nature_infrastructure'];
	$createur = "guillaume";
	$date = date("Y-m-d");

	//echo "BDD = $bdd</br>";
	//echo "new_nature= $new_nature<br />";
	//echo "type= $type<br />";
	//echo "createur= $createur<br />";
	//echo "date= $date";



//	Envoi de la requete
$sql= "INSERT INTO nature (Nature, Type, Createur, Date) VALUES ('$new_nature', '$type', '$createur', '$date')";
//$bdd->query($sql) or die('Erreur SQL !<br>' .$sql. '<br>'. mysql_error());

try
{
    // Insérez ici toutes vos requêtes SQL
$bdd->query($sql);
    
}
catch(Exception $e)
{
    die('Erreur : '.$e->getMessage()); // Affichage des erreurs éventuelles
}

print("<center>L'entree <b>$nom_ajout</b> a bien ete ajoutee.</center><br />");


?>


<!-- <META HTTP-EQUIV="Refresh" CONTENT="2;URL=index.php">Redirection ... -->
<center> Vous pouvez fermer cette fenêtre. <br><br>Pensez à <b>rafraichir votre formulaire</b> afin de pouvoir sélectionner cette nouvelle entrée (touche F5).</center>

</BODY>

</HTML>
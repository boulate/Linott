<?php

try
{
//	On se connecte à la base MySQL
	$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
	$bdd = new PDO('mysql:host=localhost;dbname=qualitatmo', 'atmo', 'atmo', $pdo_options);
}
catch(Exception $e)
{
//	En cas d'erreur précédemment, on affiche un message et on arrête tout
	die('Erreur : '.$e->getMessage());
}

	$nom_ajout = $_POST['new_type_materiel'];
	$createur = "guillaume";
	$date = date("Y-m-d");

	//echo "BDD = $bdd</br>";
	//echo "new_nature= $new_nature<br />";
	//echo "type= $type<br />";
	//echo "createur= $createur<br />";
	//echo "date= $date";



//	Envoi de la requete
$sql= "INSERT INTO type_materiel (Nom_type, Createur, Date) VALUES ('$nom_ajout', '$createur', '$date')";
$bdd->query($sql) or die('Erreur SQL !<br>' .$sql. '<br>'. mysql_error());


print("<center>L'entree $nom_ajout a bien ete ajoutee.</center><br />");


?>


<META HTTP-EQUIV="Refresh" CONTENT="2;URL=index.php">Redirection ...

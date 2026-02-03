<?php
include("Configuration/bdd.php");

	try
	{
		// On se connecte à la base MySQL, l'option SET NAMES utf8 permet de gerer les accents.
		$pdo_options = array(
			    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
			        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
				  );

//	    $bdd = new PDO('mysql:host=localhost;dbname=Linott', 'linott', 'infolibre-dijon.fr', $pdo_options);
            $bdd = new PDO("mysql:host=$host_BDD;dbname=$name_BDD", "$login_BDD", "$password_BDD", $pdo_options);

	    //mysql_query("SET NAMES 'utf8'");
	// mysql_query("SET NAMES 'utf8'"); // Commenté: orphelin, PDO gère déjà UTF-8 ligne 8

////	On se connecte à la base MySQL
//	$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
//	$bdd = new PDO('mysql:host=localhost;dbname=Linott', 'atmo', 'atmo', $pdo_options);


	}
	catch(Exception $e)
	{
	    // En cas d'erreur précédemment, on affiche un message et on arrête tout
	    die('Erreur : '.$e->getMessage());
	}
?>

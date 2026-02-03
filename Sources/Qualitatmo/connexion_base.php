 <?php
 
	try
	{
	//	On se connecte à la base MySQL
		$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
		$bdd = new PDO('mysql:host=localhost;dbname=qualitatmo', 'linott', 'infolibre-dijon.fr', $pdo_options);
	}
	catch(Exception $e)
	{
	//	En cas d'erreur précédemment, on affiche un message et on arrête tout
		die('Erreur : '.$e->getMessage());
	}
?>

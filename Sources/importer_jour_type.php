<?php include("connexion_base.php");
session_start();
//include("checkAdmin.php"); 


//echo "debut de la page <br><br>";
$idUtilisateur		=	$_SESSION['idUtilisateurs'];
$nomUtilisateur		=	$_SESSION['login']; 
$id_jour_type		=	$_GET['idJourType'];

$select		= "SELECT * FROM JoursTypes where idUtilisateur = '$idUtilisateur' AND id = '$id_jour_type'";
//echo $select;
$reponse = $bdd->query($select) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());

//$i=1;
while ($donnees = $reponse->fetch())
{
	// Declaration des variables
	$id=$donnees['id'];
	$nom=$donnees['nom'];

	for ($i = 1 ; $i <= 12 ; $i++)
	{
		$periode[$i] = $donnees['periode'.$i];
		//echo "$periode[$i];";
		if($periode[$i] != '')
		{

			list($de, $a, $idAxe1, $idAxe2, $idAxe3) = split("_", $periode[$i]);

			$selectAxe1		= "SELECT nomAxe1 FROM Axe1 WHERE idAxe1 = $idAxe1";
			$reponseAxe1 	= $bdd->query($selectAxe1) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
			$nomAxe1 		= $reponseAxe1->fetch()['nomAxe1'];

			$selectAxe2		= "SELECT nomAxe2 FROM Axe2 WHERE idAxe2 = $idAxe2";
			$reponseAxe2 	= $bdd->query($selectAxe2) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
			$nomAxe2 		= $reponseAxe2->fetch()['nomAxe2'];

			$selectAxe3		= "SELECT nomAxe3 FROM Axe3 WHERE idAxe3 = $idAxe3";
			$reponseAxe3 	= $bdd->query($selectAxe3) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
			$nomAxe3 		= $reponseAxe3->fetch()['nomAxe3'];

			echo "$i; $de; $a; $idAxe1; $nomAxe1; $idAxe2; $nomAxe2; $idAxe3; $nomAxe3;/EOV";
		}

	}
	
}



?>

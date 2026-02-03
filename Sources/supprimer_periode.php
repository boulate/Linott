<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
<head><!-- Permet de se connecter à la base Mysql-->
<script>
function message()
{
	message = document.getElementById("message").value;
	if (message != "")
	{
		alert(message);
	}
	closeWindows();
}

function closeWindows()
{
	window.parent.opener.location.reload();window.close();
}
</script> 
</head>

<body onLoad="message();">

<?php include("connexion_base.php"); 
session_start();
//echo "debut script --- ";
$message="";

	$idUtilisateur		=	$_SESSION['idUtilisateurs'];
	$id_periode_a_suppr	=	$_GET['id_periode_a_suppr'];

	//echo "id: $id_fiche_a_suppr";


	$requete_suppression = "DELETE FROM Periodes where idHoraires = $id_periode_a_suppr and Utilisateurs_idUtilisateurs = $idUtilisateur";
	try
	{
		$reponse_suppression = $bdd->query($requete_suppression) or die('Erreur SQL !<br>' .$requete_suppression. '<br>'. mysql_error());
	}
	catch(Exception $e)
	{
		// En cas d'erreur précédemment, on affiche un message et on arrête tout
		die('Erreur : '.$e->getMessage());
	}
	$reponse_suppression->closeCursor();
?>

message();

</body>

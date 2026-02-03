<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
<?php session_start(); ?>

<head>
<?php
	// Permet de rediriger vers l'acceuil si utilisateur non enregistré.
	$prenom = $_SESSION['prenom'];
	if (!$prenom)
	{
		header('Location: ../index.php'); 
	} 
	
	$idSession = $_SESSION['idUtilisateurs'];
?>


	<title>Linott: Créer une fiche action</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	

	<!-- Integration de jquery calendar http://jqueryui.com/datepicker/ -->
	<link rel="stylesheet" href="../CSS/jquery-ui.css" />
	<script src="../jquery-1.8.3.js"></script>
	<script src="../jquery-ui.js"></script>

	<link rel="stylesheet" href="../CSS/Delta/css/normalise.css"> 
	<link rel="stylesheet" href="../CSS/Delta/theme/jquery-ui.css">
	<script src="../CSS/Delta/js/modernizr-2.0.6.min.js"></script>
	<link rel="icon" type="image/png" href="../favicon.png" />
	
	<!-- Mon thème -->
	<link rel="stylesheet" href="../style.css" />

<script>
	
function retourFiche()
{
	location.href="fiches_actions.php";
}

function demarrage()
{
	change_couleur_bouton_menu_general();
}



</script>
</head>

<body onLoad="demarrage();">
<!-- Table donnant la mise en page globale de la page. Va jusqu'en bas -->
<table width=100% id=tableGlobale border=0>
 <tr>
  <td></td>
  <td id="tableGlobale">


	<!-- Permet de se connecter à la base Mysql-->
	<?php include("connexion_base.php");
		$loginSession	=	$_SESSION['login'];
		$prenomSession	=	$_SESSION['prenom'];
		$nomSession		=	$_SESSION['nom'];
		$idSession		=	$_SESSION['idUtilisateurs']
	?>
	
<?php include("../menu_general.php") ?>

	<br/>
	<br/>
<table width=100% align=right>
	<tr>
		<td>
				<?php include("menu.php"); ?>
				<br />

		</td>
	</tr>
</table>
	<!-- Table permettant les bords à 3% -->
	<table border=0 width=100%>
	 <tr>
	  <td width=3%></td>
	  <td>
		<form method="post" action="valider_formulaire.php" target="_blank">
		
		<div id="header">
			<?php include("header.php"); ?>
		</div>





		<div id="corps">
			<!-- SECTION Qui quand -->
			<table border=0 align=center TABLE WIDTH=100%>
				<tr>
					<th align=left width=37%>
						<?php echo "Rédacteur: $prenomSession $nomSession" ?>
					</th>
					</td>

					<td align=center width=20%>
							<h4>Date d'émission: 
							<?php 
								$dateEmission=date("d.m.Y");
								echo"<br />$dateEmission </h4>";
							?>
					</td>
					<td width=43%>
					</td>
				</tr>
			</table>

			<table width=100%>
				<!-- SECTION nature -->
				<tr align=center>
				 <td>
					<table border=0 width=100%>
					 <tr>
					  <th id="ongletVertFavoris" colspan=2>Nature:</th>
					  <td></td>
					 </tr>
					</table>
						
					<?php include("nature.php"); ?>
				 </td>	
				</tr>
					




				<!-- SECTION description -->
				<tr border=0 align=center width=100%>
				 <td><br /><br />
					<table border=0 width=100%>
					 <tr>
					  <th id="ongletBleuFavoris" colspan=2>Description:</th>
					  <td></td>
					 </tr>
					</table>
					<?php include("description.php"); ?>
					<?php include("materiel.php"); ?>
				 </td>
				</tr>




				<!-- SECTION Analyse action -->
				<tr border=0 align=center width=100%>
				 <td><br /><br />
					<table border=0 width=100%>
					 <tr>
					  <th id="ongletVertFavoris" colspan=2>Analyse action:</th>
					  <td></td>
					 </tr>
					</table>
					<?php include("analyse_action.php"); ?>
				 </td>
				</tr>



			</table>
		</div>

		<p align=right><br>
			<input type="submit" value="Valider fiche" onclick="enregistrer_actions();"/>
		</p>
		</form>
		
	<!-- Fin de la table permettant les bords à 3% --> 
	  </td>
	  <td width=3%></td>
	 </tr>
	</table>
<!-- Fin de la table de mise en page globale -->
  </td>
  <td></td>
 </tr>
</table>
</body>
</html>


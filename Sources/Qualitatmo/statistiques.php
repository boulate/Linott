<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
<?php session_start(); ?>
<head>
	<title>Gestion des fiches de conformite: statistique des fiches actions</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
		<!-- Mon thème -->
	<link rel="stylesheet" href="../style.css" />

	<!-- Integration de jquery calendar http://jqueryui.com/datepicker/ -->
	<link rel="stylesheet" href="../CSS/jquery-ui.css" />
	<script src="../jquery-1.8.3.js"></script>
	<script src="../jquery-ui.js"></script>

	<link rel="stylesheet" href="../CSS/Delta/css/normalise.css"> 
	<link rel="stylesheet" href="../CSS/Delta/theme/jquery-ui.css">
	<script src="../CSS/Delta/js/modernizr-2.0.6.min.js"></script>
	<link rel="icon" type="image/png" href="../favicon.png" />	

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
<table id="tableGlobale" width=100%>
<tr>
<td></td>
<td id="tableGlobale">
	<!-- Permet de se connecter à la base Mysql-->
	<?php include('connexion_base.php');
		$loginSession	=	$_SESSION['login'];
		$prenomSession	=	$_SESSION['prenom'];
		$nomSession		=	$_SESSION['nom'];
		$idSession		=	$_SESSION['idUtilisateurs']
	?>
	<!-- mise en place des variables -->
	<!-- Nombre de fiches -->
	<?php
	$requete = "SELECT count(id_fiche) from fiche";
	$reponse = $bdd->query($requete) or die('Erreur SQL au moment de la selection de * from fiche!<br>' .$sql. '<br>'. mysql_error());

	while ($donnees = $reponse->fetch())
		{
			$nbr_fiches = $donnees['count(id_fiche)'];
		}
	?>

	<!-- Nombre de fiches dysfonctionnement -->
	<?php
	$requete = "SELECT count(id_fiche) from fiche where type_fiche_action like \"Dysfonctionnement\"";
	$reponse = $bdd->query($requete) or die('Erreur SQL au moment de la selection de * from fiche!<br>' .$sql. '<br>'. mysql_error());

	while ($donnees = $reponse->fetch())
		{
			$nbr_fiches_dysfonctionnement = $donnees['count(id_fiche)'];
		}
	?>

	<!-- Nombre de fiches amelio -->
	<?php
	$requete = "SELECT count(id_fiche) from fiche where type_fiche_action like \"Amelioration\"";
	$reponse = $bdd->query($requete) or die('Erreur SQL au moment de la selection de * from fiche!<br>' .$sql. '<br>'. mysql_error());

	while ($donnees = $reponse->fetch())
		{
			$nbr_fiches_amelioration = $donnees['count(id_fiche)'];
		}
	?>

	<!-- Nombre de fiches de non conformite -->
	<?php
	$requete = "SELECT count(id_fiche) from fiche where action_sur_produit like \"oui\" or action_sur_produit like \"non\"";
	$reponse = $bdd->query($requete) or die('Erreur SQL au moment de la selection de * from fiche!<br>' .$sql. '<br>'. mysql_error());

	while ($donnees = $reponse->fetch())
		{
			$nbr_nc = $donnees['count(id_fiche)'];
		}
	?>


	<!-- Nombre de fiches de NC majeures -->
	<?php
	$requete = "SELECT count(id_fiche) from fiche where action_sur_produit like \"oui\"";
	$reponse = $bdd->query($requete) or die('Erreur SQL au moment de la selection de * from fiche!<br>' .$sql. '<br>'. mysql_error());

	while ($donnees = $reponse->fetch())
		{
			$nbr_nc_majeures = $donnees['count(id_fiche)'];
		}
	?>

	<!-- Nombre de fiches de NC mineures -->
	<?php
	$requete = "SELECT count(*) from fiche where action_sur_produit like \"non\"";
	$reponse = $bdd->query($requete) or die('Erreur SQL au moment de la selection de * from fiche!<br>' .$sql. '<br>'. mysql_error());

	while ($donnees = $reponse->fetch())
		{
			$nbr_nc_mineures = $donnees['count(id_fiche)'];
		}
	?>

	<!-- Nombre de fiches non cloturees -->
	<?php
	$requete = "SELECT count(id_fiche) from fiche where date_cloture like \"\"";
	$reponse = $bdd->query($requete) or die('Erreur SQL au moment de la selection de * from fiche!<br>' .$sql. '<br>'. mysql_error());

	while ($donnees = $reponse->fetch())
		{
			$nbr_fiches_non_cloturees = $donnees['count(id_fiche)'];
		}
	?>

	<!-- Nombre de fiches cloturees -->
	<?php
	$requete = "SELECT count(id_fiche) from fiche where date_cloture > 0";
	$reponse = $bdd->query($requete) or die('Erreur SQL au moment de la selection de * from fiche!<br>' .$sql. '<br>'. mysql_error());

	while ($donnees = $reponse->fetch())
		{
			$nbr_fiches_cloturees = $donnees['count(id_fiche)'];
		}
	?>

<?php include("../menu_general.php") ?>

	<br/>
	<br/>
    <!-- Table permettant les bords à 3% -->
    <table border=0 width=100%>
   		<tr>
    		<td width=3%>
    		</td>
    		
    		<td>
				<table border=0 width=100%>
					<tr>
						<td>
							<table border=0 align=right width=100%>
								<tr>
									<td>
										<?php include("menu.php"); ?>
									</td>
								</tr>
							</table>
							<!-- Table permettant les bords à 3% -->
							<table border=0 width=100%>
								<tr>
									<td width=3%>
									</td>
									
									<td>
										<br />
										<br />
										<br />
										<table border=0 width=100%>
											<tr>
												<th id="ongletVertFavoris" colspan=2>Statistiques:</th>
												<td></td>
											</tr>
										</table>
										<table id="tableOngletsVert" border=0 width=100%>
											<tr>
												<td>
												</td>
											</tr>
											<tr>
												<td>
												</td>

												<td width=20%>
													<?php echo "nombre de fiches";?>
												</td>
												<td width=10%>
													<?php echo $nbr_fiches;?>
												</td>
												<td>
													<?php echo "(Dysfonctionnement: $nbr_fiches_dysfonctionnement, amélioration: $nbr_fiches_amelioration)";?>
												</td>
											</tr>


											<tr>
												<td>
												</td>
												
												<td>
													<?php echo "nombre de non conformitees";?>
												</td>
												<td text=red>
													<?php echo $nbr_nc;?>
												</td>
												<td>
													<?php echo "(Majeures: $nbr_nc_majeures, mineures: $nbr_nc_mineures)";?>
												</td>
											</tr>

											<tr>
												<td>
												</td>

												<td>
													<?php echo "nombre de fiches non cloturees";?>
												</td>
												<td color=red>
													<?php echo $nbr_fiches_non_cloturees;?>
												</td>
												<td>
													<?php echo "(Cloturees: $nbr_fiches_cloturees)";?>
												</td>
											</tr>
											<tr>
												<td>
												</td>
											</tr>
										</table>




    <!-- Fin de la table permettant les bords à 3% --> 
				    				</td>

				    				<td width=3%>
				    				</td>
				    			</tr>
							</table>

						</td>
					</tr>
				</table>

			</td>
		</tr>
	</table>

<!-- Fin de la table de mise en page globale -->
		</td>

		<td>
		</td>
	</tr>
</table>
</body>

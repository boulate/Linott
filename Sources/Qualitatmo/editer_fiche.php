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


	<title>Linott: Modifier une fiche action</title>
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
	importer_action();
}

</script>

</head>

<body onLoad="demarrage();">

<?php
						// Recuperation du nom de la fiche a modifier dans l'url
						$nom_fiche=$_GET['nom'];					
						include("connexion_base.php");
						// Je selectionne ma fiche:
						$fiche_en_cours = $bdd->query("SELECT * FROM fiche WHERE nom LIKE '$nom_fiche'");

					//Recuperation de toutes les donnees de fiche_en_cours.
							while ($donnees = $fiche_en_cours->fetch())
							{
							// Declaration des variables
							$id_fiche = $donnees['id_fiche'];
							$nom = $donnees['nom'];
							$date = $donnees['date'];
							$num_fiche_jour = $donnees['num_fiche_jour'];
							$type_fiche_action = $donnees['type_fiche_action'];
							$redacteur = $donnees['redacteur'];
							$id_nature = $donnees['id_nature'];
							$noms_natures = $donnees['noms_natures'];
							$date_anomalie = $donnees['date_anomalie'];
							$marque_appareil = $donnees['marque_appareil'];
							$site = $donnees['site'];
							$type_appareil = $donnees['type_appareil'];
							$materiel_logiciel = $donnees['materiel_logiciel'];
							$numero_serie = $donnees['numero_serie'];
							$faits = $donnees['faits'];
							$causes = $donnees['causes'];
							$consequences = $donnees['consequences'];
							$actions_court_terme = $donnees['actions_court_terme'];
							$commentaire_actions_court_terme = $donnees['commentaire_actions_court_terme'];
							$incidence_qualite = $donnees['incidence_qualite'];
							$commentaire_indice_qualite = $donnees['commentaire_indice_qualite'];
							$action_sur_produit = $donnees['action_sur_produit'];

							$consequences_produit = $donnees['consequences_produit'];
							$consequences_satisfaction_client = $donnees['consequences_satisfaction_client'];
							$consequences_diffusion = $donnees['consequences_diffusion'];
							$information_responsable = $donnees['information_responsable'];
							$date_information_responsable = $donnees['date_information_responsable'];
							$information_client = $donnees['information_client'];
							$date_information_client = $donnees['date_information_client'];
							$poursuite_travaux = $donnees['poursuite_travaux'];
							$autorite_poursuite_travaux = $donnees['autorite_poursuite_travaux'];
							$besoin_action_oui_non = $donnees['besoin_action_oui_non'];
							$besoin_actions = $donnees['besoin_actions'];

							// Plus là depuis version 2.0
							//$commentaire_action_sur_produit = $donnees['commentaire_action_sur_produit'];
							$type_action_CPA = $donnees['type_action_CPA'];
							$responsable_action = $donnees['responsable_action'];
							$delai_action = $donnees['delai_action'];
							$date_realisation_action = $donnees['date_realisation_action'];
							$justificatifs = $donnees['justificatifs'];
							$efficacite = $donnees['efficacite'];
							$date_cloture = $donnees['date_cloture'];
							$visa_responsable=$donnees['visa_responsable'];
							$visa_direction=$donnees['visa_direction'];
							$date_derniere_modification=$donnees['date_derniere_modification'];
							$nom_derniere_modification=$donnees['nom_derniere_modification'];
							}
							?>


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

	  
	  	<div id="menu">
			<?php include("menu.php"); ?>
			<br />
		</div>

	<!-- Table permettant les bords à 3% -->
	<table border=0 width=100%>
	 <tr>
	  <td width=3%></td>
	  <td>


<form method="post" action="modifier_fiche.php?nom=<?php echo $nom_fiche ?>" target="_blank">
<input type="hidden" id="idFiche" value="<?php echo $id_fiche ?>">

<div id="header">
	<?php include("header_modif.php"); ?>
</div>

<!-- <div id="menu">
	<?php include("menu.php"); ?>
</div> -->


<div id="corps">
		<!-- SECTION Qui quand -->
		<table border=0 align=center TABLE WIDTH=100%>
			<tr>
				<th align=left width=37%>
					<?php echo "Rédacteur: $redacteur" ?>
				</th>

				<td align=center width=20%>
						<h4>Date d'émission: 
						<?php 
							$dateEmission=date("d.m.Y", strtotime($date));
							echo"<br />$dateEmission </h4>";
						?>
				</td>
				<td width=43% align=right>
					<?php
						if ($date_derniere_modification == "") 
						{
							$texte_modif="La fiche n'a encore jamais été modifiée.";
						}
						else	
						{
							$texte_modif="Derniere modification le $date_derniere_modification par $nom_derniere_modification";
						}
						echo "<br /><br />$texte_modif";
						
					?>
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
						
					<?php include("nature_modif.php"); ?>
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
					<?php include("description_modif.php"); ?>
					<?php include("materiel_modif.php"); ?>
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
					<?php include("analyse_action_modif.php"); ?>
				 </td>
				</tr>

	</table>
<table width=100% border=0>
<tr>	
	<td align=right>
		<br>
		<input type="submit" value="Modifier la fiche"  onclick="enregistrer_actions();"/>
	</form>
	</td>	
</tr>
<tr>
	<td align=center>
		<br>
		<form method="post" action="imprimer_fiche.php?nom=<?php echo $nom_fiche?>" target="_blank">
		<input type="submit" value="Imprimer"/>
	</td>
</tr>
</table>

</div>



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


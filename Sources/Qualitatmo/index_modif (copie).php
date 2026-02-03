<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >

<head>
	<title>Gestion des fiches de conformite</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

</head>

<body>

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
							$derniere_modification=$donnees['derniere_modification'];
							$nom_derniere_modification=$donnees['nom_derniere_modification'];
							}
							?>


<form method="post" action="modifier_fiche.php?nom=<?php echo $nom_fiche ?>">


<div id="header">
	<?php include("header_modif.php"); ?>
</div>

<div id="menu">
	<?php include("menu.php"); ?>
</div>


<div id="corps">
	<!-- SECTION Qui quand -->
	<table border=5 align=center TABLE WIDTH=100%>
			<tr>
			<td width=85%>
				<!-- Gestion des Rédacteurs -->
				<?php			
				$reponse_utilisateurs = $bdd->query('SELECT * FROM utilisateurs ORDER BY username');
				?>

				<label for="redacteur">Rédacteur: </label>
				<select name="redacteur" id="ID_user">			
				<?php			
				while ($donnees = $reponse_utilisateurs->fetch())
				{
				$nom_utilisateur=$donnees['username'];
				$id_utilisateur=$donnees['ID_users'];
					// si le "en cours" = "celui en base", alors selected="selected"
					if ($nom_utilisateur == $redacteur) {
						$selected = "selected=\"selected\"";
									}
					else {
						$selected = "";
						}

					?>
	<!--				<option value="<?php echo $nom_utilisateur ?>"><?php echo $nom_utilisateur ?></option> -->
		   			<option value="<?php echo $nom_utilisateur ?>"<?php echo $selected ?>><?php echo "$nom_utilisateur" ?></option>
				<?php
				}
				$reponse_utilisateurs->closeCursor(); // Termine le traitement de la requête
				?>
				</select>
			
					<?php
						if ($derniere_modification == "$null") {
							$texte_modif="La fiche n'a encore jamais été modifiée.";
							}
						else	{
							$texte_modif="Derniere modification le $derniere_modification";
							}
					
					?>

				<?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $texte_modif" ?>
			</td>


			
			<td>
				Date d'émission: <?php 
							$dateEmission=date("d.m.Y");
							echo"<br>$dateEmission";
						?>
			</td>
	
		</tr>


		<!-- SECTION nature -->
		<tr align=center bgcolor=silver>
			
			<td colspan=2>
				<h3>Nature:</h3>
				<?php include("nature_modif.php"); ?>
			</td>
		</tr>
			

		<!-- SECTION materiel -->
		<tr>
			<td colspan=2>
			<?php include("materiel_modif.php"); ?>
			<td>
		</tr>


		<!-- SECTION description -->
		<tr>
			<td colspan=2>
			<?php include("description_modif.php"); ?>
			<td>
		</tr>


		<!-- SECTION Analyse action -->
		<tr>
			<td colspan=2>
			<?php include("analyse_action_modif.php"); ?>
			<td>
		</tr>

	</table>
<table>
<tr>
	<td width=90%>
	<p align=center><br>
		<input type="submit" value="Modifier la fiche créée par <?php echo $redacteur ?>"/>
	</p>
	</form>
	</td>

	<td>
	<p align=right>
		<form method="post" action="imprimer_fiche.php?nom=<?php echo $nom_fiche?>" target="_blank">
		<input type="submit" value="Imprimer cette fiche"/>
	</p>
	</td>
</tr>
</table>

</div>



</form>
</body>
</html>


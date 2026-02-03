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
							$commentaire_action_sur_produit = $donnees['commentaire_action_sur_produit'];
							$besoin_actions = $donnees['besoin_actions'];
							$type_action_CPA = $donnees['type_action_CPA'];
							$responsable = $donnees['responsable'];
							$delai = $donnees['delai'];
							$realisation = $donnees['realisation'];
							$justificatifs = $donnees['justificatifs'];
							$efficacite = $donnees['efficacite'];
							$cloture = $donnees['cloture'];
							$visa_responsable=$donnees['visa_responsable'];
							$visa_direction=$donnees['visa_direction'];
							$derniere_modification=$donnees['derniere_modification'];
							}
							?>


<form method="post" action="modifier_fiche.php?nom=<?php echo $nom_fiche ?>">


<div id="header">
	<?php include("header_modif.php"); ?>
</div>

<!-- <div id="menu">
	<?php include("menu.php"); ?>
</div> -->


<div id="corps">
	<!-- SECTION Qui quand -->
	<table border=5 align=center TABLE WIDTH=100%>
		<tr>
			<td width=85%>
				<!-- Gestion des Rédacteurs -->
				<b>Redacteur:</b> <?php echo $redacteur ?>
			
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
				<b>Date d'émission:</b> <?php 
							$dateEmission=date("d.m.Y");
							echo"<br>$dateEmission";
						?>
			</td>
	
		</tr>


		<!-- SECTION nature -->
		<table border=1 width=100%>
			<tr bgcolor=silver>
				<td align=center>
				<h3>Nature(s) du problème:</h3><br>
				<?php echo " $noms_natures" ?>
				<br>
				</td>
			</tr>
		</table>
		<!-- SECTION materiel -->
		<table width=100% border=1><tr><td>
			<table width=100% CELLPADDING=5>
					<tr>
						<td><b>Date d'anomalie:</b> <?php echo "$date_anomalie" ?></td>
						<td><b>Marque:</b> <?php echo "$marque_appareil" ?></td>
						<td><b>Site:</b> <?php echo "$site" ?></td>

					</tr>
					<tr>
						<td><b>Type:</b> <?php echo "$type_appareil" ?></td>
						<td><b>Matériel:</b> <?php echo "$materiel_logiciel" ?></td>
						<td><b>Numéro de série:</b> <?php echo "$numero_serie" ?></td>
					</tr>
			</table></td></tr>
		</table>
		<!-- SECTION description -->

				<table width=100% border=1>
					<tr><td><table width=100% CELLPADDING=5>
						<tr><td align=left width=30%><b>Faits:</b></td><td><?php echo $faits ?></td></tr>
						<tr><td><b>Causes:</b></td><td><?php echo $causes ?></td></tr>
						<tr><td><b>Conséquences:</b></td><td><?php echo $consequences ?></td></tr>
						<tr><td><b>Action(s) à court terme:</b></td><td><?php echo $actions_court_terme ?></td></tr>
						<tr><td><b>Commentaire Action(s):</b></td><td><?php echo $commentaire_actions_court_terme ?></td></tr>
					</table></td></tr>
				</table>



		<!-- SECTION Analyse action -->
			<table bgcolor=silver width=100% border=1>
				<tr>
					<td width=50%><?php echo "<b>Incidence:</b> $incidence_qualite" ?><br><br><?php echo $commentaire_indice_qualite ?></td>
					<td><?php echo "<b>Action sur le produit/service:</b> $action_sur_produit"?><br><br><?php echo $commentaire_action_sur_produit ?></td>
				</tr>
			</table>
			<table bgcolor=silver width=100% border=1>
				<tr>
					<td width=50%><b>Besoin d'actions:</b><br><br><?php echo $besoin_actions?> </td>
					<td width=17%><b>Type C/P/A:</b> <br><br><?php echo $type_action_CPA?> </td>
					<td width=16%><b>Responsable:</b> <br><br><?php echo $responsable?> </td>
					<td width=17%><b>Delai previsionnel:</b> <br><br><?php echo $delai?> </td>
				</tr>
			</table>
			<table bgcolor=silver width=100% border=1>
				<tr>
					<td width=50%><b>Verification de l'efficacite des actions menees:</b><br><br><?php echo $efficacite?> </td>
					<td width=17%><b>Cloture le:</b> <br><br><?php echo $cloture?> </td>
					<td width=16%><b>Visa du responsable:</b> <br><br><?php echo $visa_responsable?> </td>
					<td width=17%><b>Visa de la direction*:</b><br><br><?php echo $visa_direction?> </td>
				</tr>
			</table>

	</table>

</div>



</form>
</body>
</html>


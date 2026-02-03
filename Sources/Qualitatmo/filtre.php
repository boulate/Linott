<!--Tableau permettant de filtrer les donnees : $champ, $operateur, $critere-->
<form method="post" action="fiches_actions.php" enctype="multipart/form-data">
<table border=0 id='tableOngletsBleu'>
	<tr>
		<td width=5%>
		</td>
		<td width=10% align=center>
			Critère : 
		</td>

		<td> <!--Affichage des noms de champ dans un menu deroulant -->
			<?php 
				$select_colonnes = "SHOW COLUMNS FROM fiche";
				$reponse_colonnes = $bdd->query($select_colonnes) or die('Erreur SQL au moment de la recuperation du nom des colonnes!<br>' .$sql. '<br>'. mysql_error());
			?>
			<label for="champ"></label>
			<select name="champ" id="id_colonne">		
				<?php			
				while ($donnees_colonnes = $reponse_colonnes->fetch())
				{
					$nom_colonne=$donnees_colonnes['Field'];

					$nom_lisible = $nom_colonne;

					// Le IF ci dessous me permet de choisir le champ par defaut que je veux dans mon menu deroulant.
					if ($nom_colonne == "id_fiche") 							{ continue; }
					if ($nom_colonne == "nom") 									{ $nom_lisible = "Référence fiche"; }
					if ($nom_colonne == "date") 								{ $nom_lisible = "Date d'émission"; }
					if ($nom_colonne == "num_fiche_jour") 						{ continue; }
					if ($nom_colonne == "type_fiche_action") 					{ $nom_lisible = "Type de fiche"; }
					if ($nom_colonne == "id_redacteur") 						{ continue; }
					if ($nom_colonne == "redacteur") 							{ $nom_lisible = "Rédacteur"; }
					if ($nom_colonne == "id_nature") 							{ continue; }
					if ($nom_colonne == "noms_natures") 						{ $nom_lisible = "Sous processus"; }
					if ($nom_colonne == "types_natures") 						{ $nom_lisible = "Processus concernés"; }
					if ($nom_colonne == "date_anomalie") 						{ $nom_lisible = "Date d'apparition'"; }
					if ($nom_colonne == "marque_appareil") 						{ $nom_lisible = "Marque de l'appareil"; }
					if ($nom_colonne == "site") 								{ $nom_lisible = "Site concerné"; }
					if ($nom_colonne == "type_appareil") 						{ $nom_lisible = "Type d'appareil"; }
					if ($nom_colonne == "materiel_logiciel") 					{ $nom_lisible = "Nom du matériel/logiciel"; }
					if ($nom_colonne == "numero_serie") 						{ $nom_lisible = "Numéro de série"; }
					if ($nom_colonne == "faits") 								{ $nom_lisible = "Faits"; }
					if ($nom_colonne == "causes") 								{ $nom_lisible = "Causes"; }
					if ($nom_colonne == "consequences") 						{ $nom_lisible = "Conséquences"; }
					if ($nom_colonne == "actions_court_terme") 					{ $nom_lisible = "Actions curative"; }
					if ($nom_colonne == "commentaire_actions_court_terme") 		{ $nom_lisible = "Commentaires actions curatives"; }
					if ($nom_colonne == "incidence_qualite") 					{ $nom_lisible = "Type de non conformité"; }
					if ($nom_colonne == "commentaire_indice_qualite")			{ $nom_lisible = "Commentaire sur la non conformité"; }
					if ($nom_colonne == "action_sur_produit") 					{ $nom_lisible = "Action sur le produit"; }
					if ($nom_colonne == "commentaire_action_sur_produit")		{ continue; }
					if ($nom_colonne == "consequences_produit") 				{ $nom_lisible = "Conséquences sur le produit"; }
					if ($nom_colonne == "consequences_satisfaction_client") 	{ $nom_lisible = "Conséquences sur la satisfaction client"; }
					if ($nom_colonne == "consequences_diffusion") 				{ $nom_lisible = "Conséquences sur la diffusion"; }
					if ($nom_colonne == "information_responsable") 				{ $nom_lisible = "Information responsable"; }
					if ($nom_colonne == "date_information_responsable") 		{ $nom_lisible = "Date d'information du responsable"; }
					if ($nom_colonne == "information_client") 					{ $nom_lisible = "Information client"; }
					if ($nom_colonne == "date_information_client") 				{ $nom_lisible = "Date d'information du client"; }
					if ($nom_colonne == "poursuite_travaux") 					{ $nom_lisible = "Poursuite des travaux"; }
					if ($nom_colonne == "autorite_poursuite_travaux") 			{ $nom_lisible = "Personne ayant autorisé la poursuite des travaux"; }
					if ($nom_colonne == "besoin_action_oui_non") 				{ continue; }
					if ($nom_colonne == "besoin_actions") 						{ continue; }
					if ($nom_colonne == "type_action_CPA") 						{ continue; }
					if ($nom_colonne == "responsable_action") 					{ continue; }
					if ($nom_colonne == "delai_action") 						{ continue; }
					if ($nom_colonne == "date_realisation_action") 				{ continue; }
					if ($nom_colonne == "justificatifs") 						{ continue; }
					if ($nom_colonne == "efficacite") 							{ continue; }
					if ($nom_colonne == "date_cloture") 						{ $nom_lisible = "Date de clôture"; }
					if ($nom_colonne == "visa_responsable") 					{ $nom_lisible = "Visa du responsable"; }
					if ($nom_colonne == "visa_direction") 						{ $nom_lisible = "Visa de la Direction"; }
					if ($nom_colonne == "date_derniere_modification") 			{ $nom_lisible = "Date de dernière modification"; }
					if ($nom_colonne == "nom_derniere_modification") 			{ $nom_lisible = "Personne ayant fait la dernière modif"; }
					if ($nom_colonne == "active") 								{ continue; }
					if ($nom_colonne == "id_fiche") 							{ $nom_lisible = "IDfiche"; }




				if ($nom_colonne == redacteur) {				
					?>
			   			<option value="<?php echo $nom_colonne ?>" selected="selected"><?php echo $nom_lisible ?></option>	
					<?php
				}
				else {
					?>
			   			<option value="<?php echo $nom_colonne ?>"><?php echo $nom_lisible ?></option>	
					<?php	
				}
				}
				$reponse_colonnes->closeCursor(); // Termine le traitement de la requête
			?>
			</select>
		</td>

		<td>
			<!--Affichage de tous les operateurs dans un menu deroulant-->
			<label for="operateur"></label>
			<select name="operateur" id="operateur">
				<option value="=">=</option>
				<option value=">">></option>
				<option value=">=">>=</option>
				<option value="<"><</option>
				<option value="<="><=</option>
				<option value="like" selected="selected">contient</option>
				<option value="avg">moyenne</option>
				<option value="sum">somme</option>
			</select>
		</td>

		<td>
			<!-- zone de texte de comparaison -->
				<input type="text" size=16 name="critere">
		</td>

		<td>
			    <input type="submit" value="Valider">   
		</td>

		<td width=3%>
		</td>
	</tr>

	<tr>

		<td colspan=7 align=center>
			<br />
			<!-- Gestion des Rédacteurs -->
			<?php		
				$reponse_utilisateurs = $bdd->query('SELECT * FROM Linott.Utilisateurs ORDER BY prenom');
			?>
			<label for="redacteur">Voir les fiches non cloturées de : </label>
			<select name="redacteur" id="ID_user">			
			<?php			
			echo "<option value='' selected> </option>";
			while ($donnees = $reponse_utilisateurs->fetch())
			{
					$nom_utilisateur=$donnees['prenom'] . " " . $donnees['nom'];
					$id_utilisateur=$donnees['idUtilisateurs'];


		           			echo "<option value='$nom_utilisateur'> $nom_utilisateur </option>";
			}
			$reponse_utilisateurs->closeCursor(); // Termine le traitement de la requête

			?>

			</select>			

			<input type="submit" value="Afficher"> 
		
		</td>

	</tr>
</table>

</form>

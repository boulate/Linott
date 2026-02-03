<!--Tableau permettant de filtrer les donnees : $champ, $operateur, $critere-->
<form method="post" action="afficher_fiches_choisies.php" enctype="multipart/form-data">
<table border=0 bgcolor=red>
	<tr>
		<td>
			Selectionnez un critère : 
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
				// Le IF ci dessous me permet de choisir le champ par defaut que je veux dans mon menu deroulant.
				if ($nom_colonne == redacteur) {				
					?>
			   			<option value="<?php echo $nom_colonne ?>" selected="selected"><?php echo $nom_colonne ?></option>	
					<?php
				}
				else {
					?>
			   			<option value="<?php echo $nom_colonne ?>"><?php echo $nom_colonne ?></option>	
					<?php	
				}
				}
				$reponse_colonnes->closeCursor(); // Termine le traitement de la requête
			?>
			</select>
		</td>
		<td>
			<p> <!--Affichage de tous les operateurs dans un menu deroulant-->
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
			</p>
		</td>
		<td>
			<!-- zone de texte de comparaison -->
				<input type="text" size=16 name="critere">
			        <input type="submit" value="Valider">   
			
		</td>
	</tr>
</table>
</form>

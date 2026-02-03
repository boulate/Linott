<table border=0 width=100%>
	<tr>
		<td width=14%>
		</td>
		
		<td align=center>

			<table border=0>
			 <tr>
			  <th align=center colspan=2>
						<?php					
							include("connexion_base.php");
						?>

					<h3>Modification de la fiche d'action :<br></h3>
					<?php
						// Affichage du nom de la fiche à modifier
						echo "$nom_fiche";
						// *** Recuperation et affichage de type_action: ***   
						// On récupère tout le contenu de la table type_action
					?>
				</th>
			
			  </tr>
			  <tr>

			  <td width=25%>
			  </td>

			  <td align=left>
					<!-- On affiche chaque entrée une à une pour type_action -->
					<?php
						$reponse_type_action = $bdd->query('SELECT * FROM type_action');				
						echo "<br />";
						while ($donnees = $reponse_type_action->fetch())
						{
						$nom_type_action=$donnees['type_action'];
						$id_type_action=$donnees['ID_type_action'];
				// si le "en cours" = "celui en base", alors selected="selected"
							if ($nom_type_action == $type_fiche_action) {
								$selected = "checked=\"checked\"";
											}
							else {
								$selected = "";
								}
					?>
						<input type="radio" name="type_action_cochee" value="<?php echo $nom_type_action ?>" id="<?php echo $nom_type_action ?>" <?php echo $selected ?>>
						<label for="<?php echo $nom_type_action ?>"><?php echo $nom_type_action ?></label><br>
					<?php
						}
						$reponse_type_action->closeCursor(); // Termine le traitement de la requête
					?>
			  </td>
			 </tr>
			</table>
		</td>
		<td width=20% align=center>
			<p><br />
				<h3>EN-QUA-702</h3>
				version 2.0</br>
				Nombre de pages: 1
			</p>
		</td>
	</tr>
</table>

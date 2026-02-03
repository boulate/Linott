<table border=0 width=100%>
	<tr>
		<td width=14%>
		</td>
		
		<td align=center>

			<table border=0>
			 <tr>
			  <th align=left>


			<!-- On affiche chaque entrée une à une pour type_action -->
			Fiche Action :
			  </th>
			  </tr>
			  <tr>
			  <td align=left>
				<?php
					// *** Recuperation et affichage de type_action: ***   
					// On récupère tout le contenu de la table type_action
					$reponse_type_action = $bdd->query('SELECT * FROM type_action');	
				?>
				<?php
					while ($donnees = $reponse_type_action->fetch())
					{
					$nom_type_action=$donnees['type_action'];
					$id_type_action=$donnees['ID_type_action'];
				?>
					<input type="radio" name="type_action_cochee" value="<?php echo $nom_type_action ?>" id="<?php echo $nom_type_action ?>">
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
			<p>
				<h3>EN-QUA-702</h3>
				version 2.0</br>
				Nombre de pages: 1
			</p>
		</td>
	</tr>
</table>

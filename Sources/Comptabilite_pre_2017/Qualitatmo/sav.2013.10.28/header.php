
	<table border=3 TABLE WIDTH=75% align=center>
		<tr>
			<td width=15% align=center>
				<img src="LOGO_atmo.png" alt="Logo ATMOSF'air" width=180> 
			</td>

			<td width=65%>
				<table border=0 width=100%> 
				<tr>					
					<td width=70% align=center>
						<?php					
						try
						{
						//	On se connecte à la base MySQL
							$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
							$bdd = new PDO('mysql:host=localhost;dbname=qualitatmo', 'atmo', 'atmo', $pdo_options);
						}
						catch(Exception $e)
						{
						//	En cas d'erreur précédemment, on affiche un message et on arrête tout
							die('Erreur : '.$e->getMessage());
						}
						?>
					<h3>Fiche d'action :</h3>
					<?php
						// *** Recuperation et affichage de type_action: ***   
						// On récupère tout le contenu de la table type_action
						$reponse_type_action = $bdd->query('SELECT * FROM type_action');
						
					?>
					</td>
				</tr>
				</table>
				<table border=0 width=100%>
				<tr>
					<td width=40%></td>
					<td align=left>
					<!-- On affiche chaque entrée une à une pour type_action -->
				
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
					
					</p>
				</tr>
				</table>
			</td>

			<td width=20% align=center>
				<p>
					<h3>EN-QUA-003</h3>
					version 1.0</br>
					Nombre de pages:
				</p>
			</td>
		</tr>
	</table>
<p><br/></p>

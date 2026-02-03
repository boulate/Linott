<?php
include("connexion_base.php");
?>

<table border=0 width=100% id='tableBleu'>
	<tr>
		<td width=25%>
			<!-- Gestion des sites -->
			<?php>			
			$reponse_site = $bdd->query('SELECT * FROM site ORDER BY Nom_site');

			?>
			<label for="site">Site : </label>
			<select name="site" id="id_site">			
			<?php			
			while ($donnees = $reponse_site->fetch())
			{
			$nom_site=$donnees['Nom_site'];
			$id_site=$donnees['ID_site'];
			?>
           			<option value="<?php echo $nom_site ?>"><?php echo $nom_site ?></option>	
			<?php
			}
			$reponse_site->closeCursor(); // Termine le traitement de la requête
			?>
			</select>		
		</td>

		<td width=20%>
					<!-- Formulaire pour ajouter un element a site -->
			<form method="post" action="ajouter_site.php" enctype="multipart/form-data" target="_blank">
				<input type="text" size=16 name="new_site">
			        <input type="submit" value="Créer site">   
			</form>
		</td>

		<td width=10%>
		</td>

		<td width=25%>
			<!-- Gestion de la marque du materiel -->
			<?php>			
			$reponse_marque = $bdd->query('SELECT * FROM marque ORDER BY Nom_marque');

			?>
			Marque : 
			<select name="marque" id="marque">			
			<?php			
			while ($donnees = $reponse_marque->fetch())
			{
			$nom_marque=$donnees['Nom_marque'];
			$id_marque=$donnees['ID_marque'];
			?>
           			<option value="<?php echo $nom_marque ?>"><?php echo $nom_marque ?></option>	
			<?php
			}
			$reponse_marque->closeCursor(); // Termine le traitement de la requête
			?>
			</select>
		</td>


		<td width=20%>
			<!-- Formulaire pour ajouter un element a marque -->
			<form method="post" action="ajouter_marque.php" enctype="multipart/form-data" target="_blank">
				<input type="text" size=16 name="new_marque">
			        <input type="submit" value="Créer marque">   
			</form>
		</td>


	</tr>

	<tr>	
		<td>
			<!-- Gestion du materiel / logiciel -->
			<?php>			
			$reponse_materiel = $bdd->query('SELECT * FROM materiel ORDER BY Nom_materiel');

			?>
			<label for="materiel">Materiel / logiciel : </label>
			<select name="materiel" id="id_materiel">			
			<?php			
			while ($donnees = $reponse_materiel->fetch())
			{
			$nom_materiel=$donnees['Nom_materiel'];
			$id_materiel=$donnees['ID_materiel'];
			?>
           			<option value="<?php echo $nom_materiel ?>"><?php echo $nom_materiel ?></option>	
			<?php
			}
			$reponse_materiel->closeCursor(); // Termine le traitement de la requête
			?>
			</select>		
		</td>


		<td>
			<!-- Formulaire pour ajouter un element a materiel -->
			<form method="post" action="ajouter_materiel.php" enctype="multipart/form-data" target="_blank">
				<input type="text" size=16 name="new_materiel">
			    <input type="submit" value="Créer materiel">   
			</form>
		</td>

		<td>
		</td>

		<td>
			<!-- Gestion du type_materiel -->
			<?php>			
			$reponse_type = $bdd->query('SELECT * FROM type_materiel ORDER BY Nom_type');

			?>
			<label for="type">Type : </label>
			<select name="type" id="id_type">			
			<?php			
			while ($donnees = $reponse_type->fetch())
			{
			$nom_type=$donnees['Nom_type'];
			$id_type=$donnees['ID_type'];
			?>
           			<option value="<?php echo $nom_type ?>"><?php echo $nom_type ?></option>	
			<?php
			}
			$reponse_type->closeCursor(); // Termine le traitement de la requête
			?>
			</select>
		</td>

		<td>
			<!-- Formulaire pour ajouter un element a type_materiel -->
			<form method="post" action="ajouter_type_materiel.php" enctype="multipart/form-data" target="_blank">
				<input type="text" size=16 name="new_type_materiel">
			    <input type="submit" value="Créer type">   
			</form>
		</td>

	</tr>

	<tr>
		<td>	
		</td>

		<td>
		</td>

		<td>
		</td>

		<td> <!-- Gestion du numero de serie -->
			<?php>			
			$reponse_num_serie = $bdd->query('SELECT * FROM num_serie ORDER BY Num_serie');

			?>
			<label for="num_serie">N° de serie : </label>
			<select name="num_serie" id="id_num_serie">			
			<?php			
			while ($donnees = $reponse_num_serie->fetch())
			{
			$num_serie=$donnees['Num_serie'];
			$id_num_serie=$donnees['ID_num_serie'];
			?>
           			<option value="<?php echo $num_serie ?>"><?php echo $num_serie ?></option>	
			<?php
			}
			$reponse_num_serie->closeCursor(); // Termine le traitement de la requête
			?>
			</select>
		</td>
		

		<td>
			<!-- Formulaire pour ajouter un element a materiel -->
			<form method="post" action="ajouter_num_serie.php" enctype="multipart/form-data" target="_blank">
				<input type="text" size=16 name="new_num_serie">
			        <input type="submit" value="Créer n° série">   
			</form>
		</td>

	</tr>
</table>


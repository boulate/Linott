<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >

	<head>
		<title>Gestion des fiches de conformite</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	</head>

<body>

<?php
include("connexion_base.php");

// changement de la variable globale id_nature en ids_natures pour eviter les confusions
$ids_natures=$id_nature
?>

<!-- Je ferme la balise form pour eviter un bug qui fait que mon bouton "ajouter une nature mesurage" renvoi la valeur de "valider la fiche". -->
</form>

<!-- SECTION COMPTE RENDU -->
<table border=0 width=100% id='tableOngletsVert'>
	<tr>
		<th width=25%>Mesurage :</th>
		<th width=25%>Infrastructure :</th>
		<th width=25%>Etude :</th>
		<th width=25%>Modelisation :</th>
	</tr>

		<tr>
			<td>
				<p>
					<!--  <h4>Mesurage :</h4>  -->
					<?php
					// *** Recuperation et affichage de NATURE: ***   
					// On récupère tout le contenu de la table nature qu'on trie par nom
					$reponse_mesurage = $bdd->query('SELECT * FROM nature WHERE Type=\'Mesurage\' ORDER BY Nature');
					?>
					<!-- On affiche chaque entrée une à une pour Nature -->
						 
	
					<?php
					while ($donnees = $reponse_mesurage->fetch())
					{
					$nom_nature=$donnees['Nature'];
					$id_nature=$donnees['ID_nature'];
							// si le "en cours" = "celui en base", alors selected="selected"
							$string_test_id="-$id_nature-";
							$id_nature_similaire = strpos($ids_natures,$string_test_id);


							if ($id_nature_similaire === false) {
								$selected = "";
											}
							else {
								$selected = "checked=\"checked\"";
								}
					?>
						<input type="checkbox" name="nature_cochee[]" value="<?php echo $id_nature ?>" id="<?php echo $nom_nature ?>" <?php echo $selected?>>
						<label for="<?php echo $nom_nature ?>"><?php echo $nom_nature ?></label><br>
					<?php
					}
					$reponse_mesurage->closeCursor(); // Termine le traitement de la requête
					?>


					<!-- Formulaire pour ajouter un element a mesurage -->
					</br>Ajouter un élément:<br>
					<form method="post" action="ajouter_nature_mesurage.php" enctype="multipart/form-data" target="_blank">
						<input type="text" size=16 name="new_nature_mesurage">
					        <input type="submit" value="Ajouter">   
					</form>


				</p>
			</td>
				
			<td>
				<p>
					<!--  <h4>Infrastructure :</h4>  -->	
					<?php
					// *** Recuperation et affichage de NATURE: ***   
					// On récupère tout le contenu de la table nature qu'on trie par nom
					$reponse_infrastructure = $bdd->query('SELECT * FROM nature WHERE Type=\'Infrastructure\' ORDER BY Nature');
					?>

					<!-- On affiche chaque entrée une à une pour Nature -->
					<?php
					while ($donnees = $reponse_infrastructure->fetch())
					{
					$nom_nature=$donnees['Nature'];
					$id_nature=$donnees['ID_nature'];
							// si le "en cours" = "celui en base", alors selected="selected"
							$string_test_id="-$id_nature-";
							$id_nature_similaire = strpos($ids_natures,$string_test_id);


							if ($id_nature_similaire === false) {
								$selected = "";
											}
							else {
								$selected = "checked=\"checked\"";
								}
					?>
						<input type="checkbox" name="nature_cochee[]" value="<?php echo $id_nature ?>" id="<?php echo $nom_nature ?>" <?php echo $selected?>>
						<label for="<?php echo $nom_nature ?>"><?php echo $nom_nature ?></label><br>
					<?php
					}
					$reponse_infrastructure->closeCursor(); // Termine le traitement de la requête
					?>

					<!-- Formulaire pour ajouter un element a infrastructure -->
					</br>Ajouter un élément:<br>
					<form method="post" action="ajouter_nature_infrastructure.php" enctype="multipart/form-data" target="_blank">
						<input type="text" size=16 name="new_nature_infrastructure">
					        <input type="submit" value="Ajouter">   
					</form>		
					

				</p>
			</td>

			<td>
				<p>
					<!--  <h4>Etude :</h4>  -->	
					<?php
					// *** Recuperation et affichage de NATURE: ***   
					// On récupère tout le contenu de la table nature qu'on trie par nom
					$reponse_etude = $bdd->query('SELECT * FROM nature WHERE Type=\'Etude\' ORDER BY Nature');
					?>

					<!-- On affiche chaque entrée une à une pour Nature -->
					<?php
					while ($donnees = $reponse_etude->fetch())
					{
					$nom_nature=$donnees['Nature'];
					$id_nature=$donnees['ID_nature'];
					// si le "en cours" = "celui en base", alors selected="selected"
							$string_test_id="-$id_nature-";
							$id_nature_similaire = strpos($ids_natures,$string_test_id);


							if ($id_nature_similaire === false) {
								$selected = "";
											}
							else {
								$selected = "checked=\"checked\"";
								}
					?>
						<input type="checkbox" name="nature_cochee[]" value="<?php echo $id_nature ?>" id="<?php echo $nom_nature ?>" <?php echo $selected?>>
						<label for="<?php echo $nom_nature ?>"><?php echo $nom_nature ?></label><br>
					<?php
					}
					$reponse_etude->closeCursor(); // Termine le traitement de la requête
					?>		
					

					<!-- Formulaire pour ajouter un element a etude -->
					</br>Ajouter un élément:<br>
					<form method="post" action="ajouter_nature_etude.php" enctype="multipart/form-data" target="_blank">
						<input type="text" size=16 name="new_nature_etude">
					        <input type="submit" value="Ajouter">   
					</form>


				</p>
			</td>

			<td>
				<p>
					<!--  <h4>Modelisation :</h4>  -->	
					<?php
					// *** Recuperation et affichage de NATURE: ***   
					// On récupère tout le contenu de la table nature qu'on trie par nom
					$reponse_modelisation = $bdd->query('SELECT * FROM nature WHERE Type=\'Modelisation\' ORDER BY Nature');
					?>

					<!-- On affiche chaque entrée une à une pour Nature -->
					<?php
					while ($donnees = $reponse_modelisation->fetch())
					{
					$nom_nature=$donnees['Nature'];
					$id_nature=$donnees['ID_nature'];
					// si le "en cours" = "celui en base", alors selected="selected"
							$string_test_id="-$id_nature-";
							$id_nature_similaire = strpos($ids_natures,$string_test_id);


							if ($id_nature_similaire === false) {
								$selected = "";
											}
							else {
								$selected = "checked=\"checked\"";
								}
					?>
						<input type="checkbox" name="nature_cochee[]" value="<?php echo $id_nature ?>" id="<?php echo $nom_nature ?>" <?php echo $selected?>>
						<label for="<?php echo $nom_nature ?>"><?php echo $nom_nature ?></label><br>
					<?php
					}
					$reponse_modelisation->closeCursor(); // Termine le traitement de la requête
					?>		
					

					<!-- Formulaire pour ajouter un element a modelisation -->
					</br>Ajouter un élément:<br>
					<form method="post" action="ajouter_nature_modelisation.php" enctype="multipart/form-data" target="_blank">
						<input type="text" size=16 name="new_nature_modelisation">
					        <input type="submit" value="Ajouter">   
					</form>

				</p>
			</td>

	</tr>


</table>
<br />
<table border=0 width=100% id='tableVert'>
	<tr>
			<th width=25%>Secretariat/Comptabilite :</th>
			<th width=25%>Qualite :</th>
			<th width=25%>Information/Alerte :</th>
			<th width=25%>Direction :</th>
	</tr>

	<tr>


			<td>
				<p>
					 <!--  <h4>Secretariat/comptabilite :</h4>  -->	
				
					<?php
					// *** Recuperation et affichage de NATURE: ***   
					// On récupère tout le contenu de la table nature qu'on trie par nom
					$reponse_secretariat_comptabilite = $bdd->query('SELECT * FROM nature WHERE Type=\'Secretariat_comptabilite\' ORDER BY Nature');
					?>
					

					<!-- On affiche chaque entrée une à une pour Nature -->
					<?php
					while ($donnees = $reponse_secretariat_comptabilite->fetch())
					{
					$nom_nature=$donnees['Nature'];
					$id_nature=$donnees['ID_nature'];
					// si le "en cours" = "celui en base", alors selected="selected"
							$string_test_id="-$id_nature-";
							$id_nature_similaire = strpos($ids_natures,$string_test_id);


							if ($id_nature_similaire === false) {
								$selected = "";
											}
							else {
								$selected = "checked=\"checked\"";
								}
					?>
						<input type="checkbox" name="nature_cochee[]" value="<?php echo $id_nature ?>" id="<?php echo $nom_nature ?>" <?php echo $selected?>>
						<label for="<?php echo $nom_nature ?>"><?php echo $nom_nature ?></label><br>
					<?php
					}
					$reponse_secretariat_comptabilite->closeCursor(); // Termine le traitement de la requête
					?>

					<!-- Formulaire pour ajouter un element a secretariat_comptabilite -->
					</br>Ajouter un élément:<br>
					<form method="post" action="ajouter_nature_secretariat_comptabilite.php" enctype="multipart/form-data" target="_blank">
						<input type="text" size=16 name="new_nature_secretariat_comptabilite">
					        <input type="submit" value="Ajouter">   
					</form>
			
				</p>
			</td>

			<td>
					<p>
					<!--  <h4>Qualite :</h4>  -->
					
					<?php
					// *** Recuperation et affichage de NATURE: ***   
					// On récupère tout le contenu de la table nature qu'on trie par nom
					$reponse_qualite = $bdd->query('SELECT * FROM nature WHERE Type=\'Qualite\' ORDER BY Nature');
					?>

					<!-- On affiche chaque entrée une à une pour Nature -->
					<?php
					while ($donnees = $reponse_qualite->fetch())
					{
					$nom_nature=$donnees['Nature'];
					$id_nature=$donnees['ID_nature'];
					// si le "en cours" = "celui en base", alors selected="selected"
							$string_test_id="-$id_nature-";
							$id_nature_similaire = strpos($ids_natures,$string_test_id);


							if ($id_nature_similaire === false) {
								$selected = "";
											}
							else {
								$selected = "checked=\"checked\"";
								}
					?>
						<input type="checkbox" name="nature_cochee[]" value="<?php echo $id_nature ?>" id="<?php echo $nom_nature ?>" <?php echo $selected?>>
						<label for="<?php echo $nom_nature ?>"><?php echo $nom_nature ?></label><br>
					<?php
					}
					$reponse_qualite->closeCursor(); // Termine le traitement de la requête
					?>

					<!-- Formulaire pour ajouter un element a qualite -->
					</br>Ajouter un élément:<br>
					<form method="post" action="ajouter_nature_qualite.php" enctype="multipart/form-data" target="_blank">
						<input type="text" size=16 name="new_nature_qualite">
						<input type="submit" value="Ajouter">   
					</form>
			
					
				</p>

			</td>

			<td>
					<p>
					<!--  <h4>Information_alerte :</h4>  -->
					
					<?php
					// *** Recuperation et affichage de NATURE: ***   
					// On récupère tout le contenu de la table nature qu'on trie par nom
					$reponse_information_alerte = $bdd->query('SELECT * FROM nature WHERE Type=\'Information_alerte\' ORDER BY Nature');
					?>

					<!-- On affiche chaque entrée une à une pour Nature -->
					<?php
					while ($donnees = $reponse_information_alerte->fetch())
					{
					$nom_nature=$donnees['Nature'];
					$id_nature=$donnees['ID_nature'];
					// si le "en cours" = "celui en base", alors selected="selected"
							$string_test_id="-$id_nature-";
							$id_nature_similaire = strpos($ids_natures,$string_test_id);


							if ($id_nature_similaire === false) {
								$selected = "";
											}
							else {
								$selected = "checked=\"checked\"";
								}
					?>
						<input type="checkbox" name="nature_cochee[]" value="<?php echo $id_nature ?>" id="<?php echo $nom_nature ?>" <?php echo $selected?>>
						<label for="<?php echo $nom_nature ?>"><?php echo $nom_nature ?></label><br>
					<?php
					}
					$reponse_information_alerte->closeCursor(); // Termine le traitement de la requête
					?>
					
					<!-- Formulaire pour ajouter un element a information_alerte -->
					</br>Ajouter un élément:<br>
					<form method="post" action="ajouter_nature_information_alerte.php" enctype="multipart/form-data" target="_blank">
						<input type="text" size=16 name="new_nature_information_alerte">
						<input type="submit" value="Ajouter">   
					</form>
			
				</p>

			</td>


			<td>
					<p>
					<!--  <h4>Direction :</h4>  -->
					
					<?php
					// *** Recuperation et affichage de NATURE: ***   
					// On récupère tout le contenu de la table nature qu'on trie par nom
					$reponse_direction = $bdd->query('SELECT * FROM nature WHERE Type=\'Direction\' ORDER BY Nature');
					?>

					<!-- On affiche chaque entrée une à une pour Nature -->
					<?php
					while ($donnees = $reponse_direction->fetch())
					{
					$nom_nature=$donnees['Nature'];
					$id_nature=$donnees['ID_nature'];
					// si le "en cours" = "celui en base", alors selected="selected"
							$string_test_id="-$id_nature-";
							$id_nature_similaire = strpos($ids_natures,$string_test_id);


							if ($id_nature_similaire === false) {
								$selected = "";
											}
							else {
								$selected = "checked=\"checked\"";
								}
					?>
						<input type="checkbox" name="nature_cochee[]" value="<?php echo $id_nature ?>" id="<?php echo $nom_nature ?>" <?php echo $selected?>>
						<label for="<?php echo $nom_nature ?>"><?php echo $nom_nature ?></label><br>
					<?php
					}
					$reponse_direction->closeCursor(); // Termine le traitement de la requête
					?>
			
					<!-- Formulaire pour ajouter un element a direction -->
					</br>Ajouter un élément:<br>
					<form method="post" action="ajouter_nature_direction.php" enctype="multipart/form-data" target="_blank">
						<input type="text" size=16 name="new_nature_direction">
						<input type="submit" value="Ajouter">   
					</form>
			
					
				</p>

			</td>


		</tr>


	</table>

</body>
</html>


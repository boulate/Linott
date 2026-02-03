<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >

<head>
	<title>Gestion des fiches de conformite: afficher les fiches</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
		<!-- Mon thème -->
	<link rel="stylesheet" href="../style.css" />

	<!-- Integration de jquery calendar http://jqueryui.com/datepicker/ -->
	<link rel="stylesheet" href="../CSS/jquery-ui.css" />
	<script src="../jquery-1.8.3.js"></script>
	<script src="../jquery-ui.js"></script>

	<link rel="stylesheet" href="../CSS/Delta/css/normalise.css"> 
	<link rel="stylesheet" href="../CSS/Delta/theme/jquery-ui.css">
	<script src="../CSS/Delta/js/modernizr-2.0.6.min.js"></script>
	<link rel="icon" type="image/png" href="../favicon.png" />
	
</head>

<body>
<div id="header">
	<?php include("header_afficher_fiches.php"); ?>
</div>

<div id="menu">
	<?php include("menu.php"); ?>
</div>

<div id="corps">
<!-- Connection a la base de donnee -->
<?php
include("connexion_base.php");
?>



<?php include("filtre.php"); ?>



<table border=1 align=center tr height=1>
	<thead class="entetetable" align=center bgcolor=silver>
		<td bgcolor=#7FC6BC>nom</td>
		<td bgcolor=#7FC6BC>date</td>
		<td bgcolor=#7FC6BC>types_natures</td>
		<td bgcolor=#7FC6BC>cloture</td>
		<td bgcolor=#7FC6BC>type_fiche_action</td>
		<td bgcolor=#7FC6BC>incidence_qualite</td>
		<td bgcolor=#7FC6BC>commentaire_indice_qualite</td>
		<td bgcolor=#7FC6BC>redacteur</td>
		<td bgcolor=#7FC6BC>noms_natures</td>
		<td bgcolor=#7FC6BC>date_anomalie</td>
		<td bgcolor=#7FC6BC>site</td>
		<td bgcolor=#7FC6BC>type_appareil</td>
		<td bgcolor=#7FC6BC>marque_appareil</td>
		<td bgcolor=#7FC6BC>materiel_logiciel</td>
		<td bgcolor=#7FC6BC>numero_serie</td>
		<td>faits</td>
		<td>causes</td>
		<td>consequences</td>
		<td>actions_court_terme</td>
		<td>action_sur_produit</td>
		<td>commentaire_action_sur_produit</td>
		<td>besoin_actions</td>
		<td>type_action_CPA</td>
		<td>responsable</td>
		<td>delai</td>
		<td>realisation</td>
		<td>justificatifs</td>
		<td>efficacite</td>
		<td>visa_responsable</td>
		<td>visa_direction</td>
		<td>num_fiche_jour</td>
		<td>id_nature</td>
	</thead>


	<?php 
//Recuperation de toutes les donnees de fiche.
$select_all = "SELECT * FROM fiche ORDER BY id_fiche DESC";
$reponse = $bdd->query($select_all) or die('Erreur SQL au moment de la selection de * from fiche!<br>' .$sql. '<br>'. mysql_error());
		while ($donnees = $reponse->fetch())
		{
		// Declaration des variables
		$nom = $donnees['nom'];
		$date = $donnees['date'];
		$num_fiche_jour = $donnees['num_fiche_jour'];
		$type_fiche_action = $donnees['type_fiche_action'];
		$redacteur = $donnees['redacteur'];
		$id_nature = $donnees['id_nature'];
		$noms_natures = $donnees['noms_natures'];
		$types_natures = $donnees['types_natures'];
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

		?>
		<!-- Reseignement de chaque colonne. -->

		<tr align=center>	
			<td><?php echo "<A HREF=\"editer_fiche.php?nom=$nom\">$nom</A>"?> </td>
			<td><?php echo $date ?></td>
			<td><?php echo $types_natures ?></td>
			<td><?php echo $cloture ?></td>
			<td><?php echo $type_fiche_action ?></td>
			<td><?php echo $incidence_qualite ?></td>
			<td><?php echo $commentaire_indice_qualite ?></td>
			<td><?php echo $redacteur ?></td>
			<td NOWRAP align=left><?php echo $noms_natures ?></td>
			<td><?php echo $date_anomalie ?></td>
			<td><?php echo $site ?></td>
			<td><?php echo $type_appareil ?></td>
			<td><?php echo $marque_appareil ?></td>
			<td><?php echo $materiel_logiciel ?></td>
			<td><?php echo $numero_serie ?></td>
			<td><?php echo $faits ?></td>
			<td><?php echo $causes ?></td>
			<td><?php echo $consequences ?></td>
			<td><?php echo $actions_court_terme ?></td>
			<td><?php echo $action_sur_produit ?></td>
			<td><?php echo $commentaire_action_sur_produit ?></td>
			<td><?php echo $besoin_actions ?></td>
			<td><?php echo $type_action_CPA ?></td>
			<td><?php echo $responsable ?></td>
			<td><?php echo $delai ?></td>
			<td><?php echo $realisation ?></td>
			<td><?php echo $justificatifs ?></td>
			<td><?php echo $efficacite ?></td>
			<td><?php echo $visa_responsable ?></td>
			<td><?php echo $visa_direction ?></td> 
			<td><?php echo $num_fiche_jour ?></td>
			<td><?php echo $id_nature ?></td>
		</tr>
		<?php
		}
		$reponse->closeCursor(); // Termine le traitement de la requête
	?>
</table>


</div>
</body>
</html>

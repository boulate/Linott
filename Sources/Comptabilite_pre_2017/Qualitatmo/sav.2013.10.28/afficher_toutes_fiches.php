<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >

<head>
	<title>Gestion des fiches de conformite: afficher les fiches</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
</head>

<body>
<!-- Connection a la base de donnee -->
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



<?php include("filtre.php"); ?>



<table border=1 align=center>
	<tr align=center bgcolor=silver>
		<td>nom</td>	<td>date</td>	<td>num_fiche_jour</td>		<td>type_fiche_action</td>	<td>redacteur</td>	<td>id_nature</td>	<td>noms_natures</td>	<td>marque_appareil</td>	<td>site</td>	<td>type_appareil</td>	<td>materiel_logiciel</td>	<td>numero_serie</td>	<td>faits</td>	<td>causes</td>	<td>consequences</td>	<td>actions_court_terme</td>	<td>incidence_qualite</td>	<td>commentaire_indice_qualite</td>	<td>action_sur_produit</td>	<td>commentaire_action_sur_produit</td>	<td>besoin_actions</td>	<td>type_action_CPA</td>	<td>realisateur</td>	<td>delai</td>	<td>realisation</td>	<td>justificatifs</td>	<td>efficacite</td>	<td>cloture</td>	<td>visa_responsable</td>	<td>visa_direction</td>
	</tr>
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
		$realisateur = $donnees['realisateur'];
		$delai = $donnees['delai'];
		$realisation = $donnees['realisation'];
		$justificatifs = $donnees['justificatifs'];
		$efficatite = $donnees['efficatite'];
		$cloture = $donnees['cloture'];
		$visa_responsable=$donnees['visa_responsable'];
		$visa_direction=$donnees['visa_direction'];

		?>
			<tr align=center>	<td><?php echo $nom ?></td>	<td><?php echo $date ?></td>	<td><?php echo $num_fiche_jour ?></td>		<td><?php echo $type_fiche_action ?></td>	<td><?php echo $redacteur ?></td>	<td><?php echo $id_nature ?></td>	<td NOWRAP align=left><?php echo $noms_natures ?></td>	<td><?php echo $marque_appareil ?></td>	<td><?php echo $site ?></td>	<td><?php echo $type_appareil ?></td>	<td><?php echo $materiel_logiciel ?></td>	<td><?php echo $numero_serie ?></td>	<td><?php echo $faits ?></td>	<td><?php echo $causes ?></td>	<td><?php echo $consequences ?></td>	<td><?php echo $actions_court_terme ?></td>	<td><?php echo $incidence_qualite ?></td>	<td><?php echo $commentaire_indice_qualite ?></td>	<td><?php echo $action_sur_produit ?></td>	<td><?php echo $commentaire_action_sur_produit ?></td>	<td><?php echo $besoin_actions ?></td>	<td><?php echo $type_action_CPA ?></td>	<td><?php echo $realisateur ?></td>	<td><?php echo $delai ?></td>	<td><?php echo $realisation ?></td>	<td><?php echo $justificatifs ?></td>	<td><?php echo $efficacite ?></td>	<td><?php echo $cloture ?></td> <td><?php echo $visa_responsable ?></td>	<td><?php echo $visa_direction ?></td> </tr>
		<?php
		}
		$reponse->closeCursor(); // Termine le traitement de la requête
	?>
</table>



</body>
</html>

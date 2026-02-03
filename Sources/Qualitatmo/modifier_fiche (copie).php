<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >

<head>
	<title>Gestion des fiches de conformite</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

</head>

<body>
<div id="menu">
	<?php include("menu.php"); ?>
</div>


<div id="corps">
<?php
include("connexion_base.php");

// Nom de la fiche:
// Recuperation du nom de la fiche a modifier dans l'url
$nom_fiche=$_GET['nom'];
echo "<br>- Nom de la fiche: <br> $nom_fiche <br>";

// Indique la date de creation de la fiche:
$date=date("Y-m-d");
echo "<br>- Date de creation de la fiche:<br> $date <br>";

// Indique le type d'action qui a ete coche
$type_action = ($_POST['type_action_cochee']);
echo "<br>- ID de(s) type(s) d'action(s) coche(s) : <br>";
echo "$type_action";
echo "<br>";


// Indique le redacteur qui a ete renseigne
$redacteur=$_POST['redacteur'];
echo "<br>- Redacteur de la fiche: <br> $redacteur <br>";


// Indique l'ID de la nature qui a ete cochee
echo "<br>- ID de la Nature de la fiche : <br>";
if(isset($_POST['nature_cochee']))
{
	foreach($_POST['nature_cochee'] AS $nom=>$last_id_nature)
	$id_nature = "-$last_id_nature-$id_nature";	
}
echo "$id_nature ";
echo "<br>";

// Indique la/les nature(s) cochee(s)
$requete_id_nature_or = "ID_nature = 0";
if(isset($_POST['nature_cochee']))
{
	foreach($_POST['nature_cochee'] AS $nom=>$last_id_nature)
	{
	$requete_id_nature_or = "$requete_id_nature_or or ID_nature = $last_id_nature";
	}	
}
	$requete_nom_nature = "SELECT * from nature WHERE $requete_id_nature_or";
	$reponse_nom_nature = $bdd->query($requete_nom_nature) or die('Erreur SQL !<br>' .$sql. '<br>'. mysql_error());


// On affiche tous les nom de natures correspondant a leurs ID (tant qu'il y en a).
while ($donnees_nom_nature = $reponse_nom_nature->fetch())
{
		$last_nom_nature = $donnees_nom_nature['Nature'];
		$noms_natures = "$last_nom_nature<br>$noms_natures";
}
echo "<br>- Les noms de nature sont:<br>$noms_natures ";


// Indique le/les types de nature(s) cochee(s)
$requete_id_nature_or = "ID_nature = 0";
if(isset($_POST['nature_cochee']))
{
	foreach($_POST['nature_cochee'] AS $nom=>$last_id_nature)
	{
	$requete_id_nature_or = "$requete_id_nature_or or ID_nature = $last_id_nature";
	}	
}
	$requete_type_nature = "SELECT * from nature WHERE $requete_id_nature_or";
	$reponse_type_nature = $bdd->query($requete_type_nature) or die('Erreur SQL !<br>' .$sql. '<br>'. mysql_error());

// On affiche tous les nom de natures correspondant a leurs ID (tant qu'il y en a).
while ($donnees_type_nature = $reponse_type_nature->fetch())
{
		$last_type_nature = $donnees_type_nature['Type'];
		$types_natures = "$last_type_nature<br>$types_natures";
}
echo "<br>- Les types de natures sont:<br>$types_natures ";


// Date de la non conformite
$date_anomalie=$_POST['date_anomalie'];
echo "<br>- Date de l'anomalie: <br> $date_anomalie<br>";

// Marque de l'appareil
$marque=$_POST['marque'];
echo "<br>- Marque de l'appareil : <br> $marque <br>";

// Site
$site=$_POST['site'];
echo "<br>- Site concerne : <br> $site <br>";


// Type d'appareil
$type=$_POST['type'];
echo "<br>- Type de l'appareil : <br> $type <br>";


// Materiel / logiciel
$materiel=$_POST['materiel'];
echo "<br>- Materiel concerne : <br> $materiel <br>";


// Numero de serie
$num_serie=$_POST['num_serie'];
echo "<br>- Numero de serie de l'appareil : <br> $num_serie <br>";


// Description des faits
$faits=addslashes($_POST['description_faits']);
echo "<br>- Description des Faits : <br> $faits <br>";

$causes=addslashes($_POST['description_causes']);
echo "<br>- Description des causes : <br> $causes <br>";

$consequences=addslashes($_POST['description_consequences']);
echo "<br>- Description des consequences : <br> $consequences <br>";

$actions_court_terme=addslashes($_POST['actions_court_terme']);
echo "<br>- Actions a court terme : <br> $actions_court_terme <br>";

$commentaire_actions_court_terme=addslashes($_POST['commentaire_actions_court_terme']);
echo "<br>- Commentaire actions a court terme : <br> $commentaire_actions_court_terme <br>";

// incidence sur la qualite
$incidence_qualite=$_POST['incidence_qualite'];
echo "<br>- Incidence sur la qualite : <br> $incidence_qualite <br>";

// Commentaire_indice_qualite
$commentaire_indice_qualite=addslashes($_POST['commentaire_indice_qualite']);
echo "<br>- Commentaire_indice_qualite : <br> $commentaire_indice_qualite <br>";

// Action sur produit
$action_sur_produit=$_POST['action_sur_produit'];
echo "<br>- action_sur_produit : <br> $action_sur_produit <br>";

// Commentaire action sur produit
$commentaire_action_sur_produit=addslashes($_POST['commentaire_action_sur_produit']);
echo "<br>- Commentaire de l'action_sur_produit : <br> $commentaire_action_sur_produit <br>";

// Actions
$actions=addslashes($_POST['besoin_actions']);
echo "<br>- Actions realisees: <br> $actions <br>";

// C, P ou A?
$cpa=$_POST['CPA'];
echo "<br>- Type d'action (Corrective, Preventive, Amelioration): <br> $cpa <br>";

// responsable
$responsable=$_POST['responsable'];;

// Delai previsionnel
$delai=$_POST['delai'];;

// Realisation
$realisation=$_POST['realisation'];;


// Elements justificatifs
$justificatifs=addslashes($_POST['elements_justificatifs']);
echo "<br>- Elements justificatifs : <br> $justificatifs <br>";

// Verification de l'efficacite des actions menees
$efficacite=addslashes($_POST['efficacite_action']);
echo "<br>- Verification de l'efficacite des actions menees : <br> $efficacite <br>";

// Cloture le :
$cloture=$_POST['cloture'];;
echo "<br>- Cloture le : <br> $cloture <br>";

// Visas
$visa_responsable=$_POST['visa_responsable'];
$visa_direction=$_POST['visa_direction'];
echo "<br>- Visas : <br> $visa_responsable, $visa_direction <br>";

// Derniere modification
$derniere_modification=date("Y-m-d à H:i");
echo "<br>- Derniere modification : <br> $derniere_modification <br>";




// Renseignement de la fiche en base.
// Envoi de la requete
$sql= "
UPDATE fiche
SET
type_fiche_action='$type_action',
redacteur='$redacteur',
id_nature='$id_nature',
noms_natures='$noms_natures',
types_natures='$types_natures',
date_anomalie='$date_anomalie',
marque_appareil='$marque',
site='$site',
type_appareil='$type',
materiel_logiciel='$materiel',
numero_serie='$num_serie',
faits='$faits',
causes='$causes',
consequences='$consequences',
actions_court_terme='$actions_court_terme',
commentaire_actions_court_terme='$commentaire_actions_court_terme',
incidence_qualite='$incidence_qualite',
commentaire_indice_qualite='$commentaire_indice_qualite',
action_sur_produit='$action_sur_produit',
commentaire_action_sur_produit='$commentaire_action_sur_produit',
besoin_actions='$actions',
type_action_CPA='$cpa',
responsable='$responsable',
delai='$delai',
realisation='$realisation',
justificatifs='$justificatifs',
efficacite='$efficacite',
cloture='$cloture',
visa_responsable='$visa_responsable',
visa_direction='$visa_direction',
derniere_modification='$derniere_modification'
WHERE nom like '$nom_fiche'
";

$bdd->query($sql) or die('Erreur SQL !<br>' .$sql. '<br>'. mysql_error());

print("<center><h1>La fiche \"$nom_fiche\" a bien ete modifiee.<h1></center><br />");
?>

</div>
</body>
</html>

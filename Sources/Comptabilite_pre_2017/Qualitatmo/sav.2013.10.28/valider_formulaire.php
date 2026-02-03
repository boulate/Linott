<?php
echo "Connexion a la base de donnee...<br><br>";
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
	$id_nature = "$last_id_nature-$id_nature";	
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



// Date de la non conformite


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
echo "<br> Description des Faits : <br> $faits <br>";

$causes=addslashes($_POST['description_causes']);
echo "<br>- Description des causes : <br> $causes <br>";

$consequences=addslashes($_POST['description_consequences']);
echo "<br>- Description des consequences : <br> $consequences <br>";

$actions_court_terme=addslashes($_POST['actions_court_terme']);
echo "<br>- Actions a court terme : <br> $actions_court_terme <br>";


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

// Realisateur
$realisateur=$_POST['realisateur'];;

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

// Visas
$visa_responsable=$_POST['visa_responsable'];
$visa_direction=$_POST['visa_direction'];



// Nom de la fiche:
$requete_fiche_jour="SELECT * from fiche WHERE date LIKE \"$date\" ORDER BY num_fiche_jour";
$reponse_fiche_jour = $bdd->query($requete_fiche_jour) or die('Erreur SQL !<br>' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse_fiche_jour->fetch())
	{
	$num_fiche_jour=$donnees['num_fiche_jour'];
	}
$nouveau_num_fiche_jour = $num_fiche_jour+1;
echo "<br>- Nouveau numero de fiche :<br> $nouveau_num_fiche_jour<br>";
$date_nom_fiche = date("Ymd");
$heure_nom_fiche = date("H:i");
$nom_fiche = "$date_nom_fiche-$heure_nom_fiche-$nouveau_num_fiche_jour";
echo "<br>- Nom de la fiche: <br> $nom_fiche <br>";


// Renseignement de la fiche en base.
// Envoi de la requete
$sql= "INSERT INTO fiche (nom,		date,		num_fiche_jour,			type_fiche_action,		redacteur,	id_nature,	noms_natures,			marque_appareil,	site,		type_appareil,	materiel_logiciel,	numero_serie,	faits,		causes,		consequences,		actions_court_terme,	incidence_qualite,	commentaire_indice_qualite,		action_sur_produit,	commentaire_action_sur_produit,	besoin_actions,	type_action_CPA,	realisateur,	delai,		realisation,	justificatifs,		efficacite,	cloture,	visa_responsable,	visa_direction) 
VALUES 			 ('$nom_fiche',	'$date',	'$nouveau_num_fiche_jour',	'$type_action',			'$redacteur',	'$id_nature',	'$noms_natures',		'$marque',		'$site',	'$type',	'$materiel',		'$num_serie',	'$faits',	'$causes',	'$consequences',	'$actions_court_terme',	'$incidence_qualite',	'$commentaire_indice_qualite',	'$action_sur_produit',	'$commentaire_action_sur_produit',	'$actions',		'$cpa',			'$realisateur',	'$delai',	'$realisation',	'$justificatifs',	'$efficacite',	'$cloture', '$visa_responsable',	'$visa_direction')";

$bdd->query($sql) or die('Erreur SQL !<br>' .$sql. '<br>'. mysql_error());

print("<center><h1>La fiche \"$nom_fiche\" a bien ete cree.<h1></center><br />");
?>

